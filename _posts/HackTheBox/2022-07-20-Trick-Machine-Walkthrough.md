---
title: "Trick Machine Writeup"
classes: wide
header:
  teaser: /assets/images/hackthebox/Trick-Writeup/Trick.png
ribbon: red
description: "Trick is easy machine"
categories:
  - HackTheBox
toc: false
---

<img src="/assets/images/hackthebox/Trick-Writeup/Trick.png" alt="Trick" style="zoom:50%;" />

## Recon

First, let's start with nmap port scanning.

![](/assets/images/hackthebox/Trick-Writeup/nmap-results.png)

We can see that port 80 is open so let's check the running web service.

![](/assets/images/hackthebox/Trick-Writeup/trick-first-domain.png)

We can see port 53 is open, so let's try zone transfer. it shows the following results:

![](/assets/images/hackthebox/Trick-Writeup/dig-results.png)

Discover the `preprod-payroll.trick.htb` subdomain.

I tried admin/admin as the login creds but it didn't work, so I tried basic SQL injection ` admin' or 1=1 -- -` in the username field and it successfully bypassed the login page.

![](/assets/images/hackthebox/Trick-Writeup/bypass-login.png)

![](/assets/images/hackthebox/Trick-Writeup/page-parameter.png)

The `page` parameter looks interesting. we can try `local file inclusion` in it with `../../../../../../../../etc/passwd`, but not working.

After some checks I found that the value of page parameter is `users` ,`home`, so we can assume that there is a php by default in code.

So we can try `php://filter/convert.base64-encode/resource=index` and it shows the following results:

![](/assets/images/hackthebox/Trick-Writeup/LFI.png)

decode it from base64 and show the following results:

`index`

![](/assets/images/hackthebox/Trick-Writeup/index.png)

`login`

![](/assets/images/hackthebox/Trick-Writeup/login.png)

Notice the included file `./db_connect.php`, let’s try to read it.

![](/assets/images/hackthebox/Trick-Writeup/db_connect.png)

I tried to ssh with this creds, but not working.

## Shell as michael

mmmmmm! Let's do subdomain enumeration with `wfuzz`,but no results.

Let's do it again but with wordlist begin with `preprod` word. and it show new subdomain:

![](/assets/images/hackthebox/Trick-Writeup/third-domain.png)

Dicover it I found `local file inclusion` also in it. let's try to read:

`/etc/passwd`

![etc-passwd](/assets/images/hackthebox/Trick-Writeup/etc-passwd.png)

`/home/mishael/.ssh/id_rsa`

![](/assets/images/hackthebox/Trick-Writeup/id_rsa-michael.png)

> Remember to change the permissions of the `id_rsa` file to 600

Now we can ssh into box with `michael` user and read `user.txt`.

![](/assets/images/hackthebox/Trick-Writeup/user.txt.png)

## Shell as root

Running `sudo -l` , we can see that `michael` user can restart fail2ban service as root user without password.

![](/assets/images/hackthebox/Trick-Writeup/sudo.png)

But what is fail2ban? `fail2ban is an intrusion prevention software framework that protects computer servers from brute-force attacks.`

I found a [post](https://youssef-ichioui.medium.com/abusing-fail2ban-misconfiguration-to-escalate-privileges-on-linux-826ad0cdafb7) while searching for exploit for it showing that I can modify `/etc/fail2ban/action.d/iptables-multiport.conf` and put my payload in `actionban` variable.

But when I try to do it I don't have permission to modify it, so I can delete it and put it again with my payload.

![](/assets/images/hackthebox/Trick-Writeup/actionban.png)

![](/assets/images/hackthebox/Trick-Writeup/bash.png)

> +s is the `setuid` bit, which tells the OS to execute that program  with the userid of its owner.  This is typically used with files owned  by root to allow normal users to execute them as root.

Then use hydra to make failed login attempts to get banned.

After being banned,we can excute `/bin/bash` as root.	

![](/assets/images/hackthebox/Trick-Writeup/hydra.png)

![](/assets/images/hackthebox/Trick-Writeup/root.txt.png)

