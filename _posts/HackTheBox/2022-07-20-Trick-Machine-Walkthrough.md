  GNU nano 6.0                                                      2022-07-10-Faculty-Machine-Walkthrough.md                                                               
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

<img src="/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/Trick.png" alt="Trick" style="zoom:50%;" />

## Recon

First, let's start with nmap port scanning.

![nmap-results](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/nmap-results.png)

We can see that port 80 is open so let's check the running web service.

![trick-first-domain](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/trick-first-domain.png)

We can see port 53 is open, so let's try zone transfer. it shows the following results:

![dig-results](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/dig-results.png)

Discover the `preprod-payroll.trick.htb` subdomain.

I tried admin/admin as the login creds but it didn't work, so I tried basic SQL injection ` admin' or 1=1 -- -` in the username field and it successfully bypassed the login page.

![bypass-login](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/bypass-login.png)

![LFI](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/page-parameter.png)

The `page` parameter looks interesting. we can try `local file inclusion` in it with `../../../../../../../../etc/passwd`, but not working.

After some checks I found that the value of page parameter is `users` ,`home`, so we can assume that there is a php by default in code.

So we can try `php://filter/convert.base64-encode/resource=index` and it shows the following results:

![LFI](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/LFI.png)

decode it from base64 and show the following results:

`index`

![index](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/index.png)

`login`

![login](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/login.png)

Notice the included file `./db_connect.php`, let’s try to read it.

![db_connect](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/db_connect.png)

I tried to ssh with this creds, but not working.

## Shell as michael

mmmmmm! Let's do subdomain enumeration with `wfuzz`,but no results.

Let's do it again but with wordlist begin with `preprod` word. and it show new subdomain:

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/third-domain.png)

Dicover it I found `local file inclusion` also in it. let's try to read:

`/etc/passwd`

![etc-passwd](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/etc-passwd.png)

`/home/mishael/.ssh/id_rsa`

![id_rsa-michael](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/id_rsa-michael.png)

> Remember to change the permissions of the `id_rsa` file to 600

Now we can ssh into box with `michael` user and read `user.txt`.

![user.txt](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/user.txt.png)

## Shell as root

Running `sudo -l` , we can see that `michael` user can restart fail2ban service as root user without password.

![sudo](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/sudo.png)

But what is fail2ban? `fail2ban is an intrusion prevention software framework that protects computer servers from brute-force attacks.`

I found a [post](https://youssef-ichioui.medium.com/abusing-fail2ban-misconfiguration-to-escalate-privileges-on-linux-826ad0cdafb7) while searching for exploit for it showing that I can modify `/etc/fail2ban/action.d/iptables-multiport.conf` and put my payload in `actionban` variable.

But when I try to do it I don't have permission to modify it, so I can delete it and put it again with my payload.

![actionban](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/actionban.png)

![bash](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/bash.png)

> +s is the `setuid` bit, which tells the OS to execute that program  with the userid of its owner.  This is typically used with files owned  by root to allow normal users to execute them as root.

Then use hydra to make failed login attempts to get banned.

After being banned,we can excute `/bin/bash` as root.	

![hydra](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/hydra.png)

![root.txt](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/trick/Writeup/root.txt.png)

