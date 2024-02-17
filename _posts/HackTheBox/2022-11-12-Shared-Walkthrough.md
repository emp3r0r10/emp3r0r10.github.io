---
title: "Shared Machine Writeup"
classes: wide
header:
  teaser: /assets/images/hackthebox/Shared-Writeup/Shared.png
ribbon: red
description: "Shared is medium linux machine that involves exploiting SQL injection, ipython and redis."
categories:
  - HackTheBox
toc: false
---

<img src="/assets/images/hackthebox/Shared-Writeup/Shared.png" alt="Shared" style="zoom:50%;" />

Shared is medium linux machine that involves exploiting SQL injection, ipython and redis.

## Recon

First, let's start with nmap port scanning.

![nmap](/assets/images/hackthebox/Shared-Writeup/nmap.png)

We can see that port 80 is open, so let's check the running web service.

![first-domain](/assets/images/hackthebox/Shared-Writeup/first-domain.png)

We have some categories, let's check one of them.

![tabs](/assets/images/hackthebox/Shared-Writeup/tabs.png)

If we click on any item, we can add it to cart and proceed to checkout.

![add-to-cart](/assets/images/hackthebox/Shared-Writeup/add-to-cart.png)

We can see that we got redirected to another subdomain.

![second-domain](/assets/images/hackthebox/Shared-Writeup/second-domain.png)

Let's intercept the above request in burp repeater.

![sqli](/assets/images/hackthebox/Shared-Writeup/sqli.png)

This `Custom_cart` cookie parameter shown above is interesting. When decoded we can see that it's a map corresponding to the `Product` name and its quantity.

## Shell as james_mason

we can try SQL injection in product key, it turned out to be vulnerable.

![test-sqli](/assets/images/hackthebox/Shared-Writeup/test-sqli.png)

Let's get the database name:

![database()](/assets/images/hackthebox/Shared-Writeup/database().png)

Getting`table_name` from `checkout` database:

![table_name](/assets/images/hackthebox/Shared-Writeup/table_name.png)

Getting `username` from `user` table:

![username](/assets/images/hackthebox/Shared-Writeup/username.png)

Getting `password` from `user` table:

![password](/assets/images/hackthebox/Shared-Writeup/password.png)

The password Looks like md5 hash, so let's try to decrypt it.

![crackstation](/assets/images/hackthebox/Shared-Writeup/crackstation.png)

Now we can ssh with `james_mason:Soleil101` credentials, but we unfortunately we didn't find `user.txt` file in this user's folder.

![james-shell](/assets/images/hackthebox/Shared-Writeup/james-shell.png)

I found another user called `dan_smith` in `/home` directory.

## Shell as dan_smith

Running [linPEAS](https://github.com/carlospolop/PEASS-ng/tree/master/linPEAS) didn't give any useful information.

Running [pspy](https://github.com/DominicBreuker/pspy), we can see that `ipython` gets executed every minute under the user with uid 1001 (which is `dan_smith`).

> pspy is a command line tool designed to snoop on processes without need for root permissions. It allows you to see commands run by other users, cron jobs, etc. as they execute.

![pspy](/assets/images/hackthebox/Shared-Writeup/pspy.png)

> IPython is a command shell for interactive computing in multiple  programming languages, originally developed for the Python programming  language, that offers introspection, rich media, shell syntax, tab  completion, and history

Let's search for an exploit for ipython, I found this one [CVE-2022-21699](https://github.com/advisories/GHSA-pq7m-3gw7-gq5x).

![exploit-ipython](/assets/images/hackthebox/Shared-Writeup/exploit-ipython.png)

We can read `dan_smith`'s private key at `/tmp/key`.

![id_rsa-dan_smith](/assets/images/hackthebox/Shared-Writeup/id_rsa-dan_smith.png)

Now we can ssh as `dan_smith` and read `user.txt`.

![user.txt](/assets/images/hackthebox/Shared-Writeup/user.txt.png)

## Shell as root

Let's check the groups we are a member of, and what we can run under these groups.

I found `sysadmin` group which looks interesting.

![priv2](/assets/images/hackthebox/Shared-Writeup/priv2.png)

Now let's run this redis_connector binary and how it goes.

> Redis is an in-memory data structure store, used as a distributed, in-memory key–value database, cache and message broker, with optional  durability. Redis supports different kinds of abstract data structures,  such as strings, lists, maps, sets, sorted sets, HyperLogLogs, bitmaps,  streams, and spatial indices.

![redis-test](/assets/images/hackthebox/Shared-Writeup/redis-test.png)

It seems that we need a password to login, so let's transfer this binary to our machine, setup a netcat listener and run it again.

![scp](/assets/images/hackthebox/Shared-Writeup/scp.png)

It shows string that looks like a password (and it is).

Note that default port for redis is 6378.

![nc-password](/assets/images/hackthebox/Shared-Writeup/nc-password.png)

After some research for redis exploits, I found this [post](https://thesecmaster.com/how-to-fix-cve-2022-0543-a-critical-lua-sandbox-escape-vulnerability-in-redis/) which goes through a critical Lau sandbox escape vulnerability In redis (CVE-2022-0543).

Let's setup a netcat listener to get a reverse shell and exploit this vulnerability to read finally `root.txt` 

![redis-exploit](/assets/images/hackthebox/Shared-Writeup/redis-exploit.png)

![root.txt](/assets/images/hackthebox/Shared-Writeup/root.txt.png)