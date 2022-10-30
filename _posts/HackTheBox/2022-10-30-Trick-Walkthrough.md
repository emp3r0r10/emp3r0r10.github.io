---
title: "Trick Machine Writeup"
classes: wide
header:
  teaser: /assets/images/hackthebox/Trick-Writeup/Trick.png
ribbon: red
description: "Trick is an easy linux machine that involves exploiting SQL injection, LFI and fail2ban service."
categories:
  - HackTheBox
toc: false
---

<img src="/assets/images/hackthebox/Trick-Writeup/Trick.png" alt="Trick" style="zoom:50%;" />

Trick is an easy linux machine that involves exploiting SQL injection, LFI and fail2ban service.

## Recon

First, let's start with nmap port scanning.

![](/assets/images/hackthebox/Trick-Writeup/nmap-results.png)

We can see that port 80 is open so let's check the running web service.

![](/assets/images/hackthebox/Trick-Writeup/trick-first-domain.png)

We can see that port 53 is open, so let's try zone transfer to enumerate DNS domains. It shows the following results:

![dig-results](/assets/images/hackthebox/Trick-Writeup/dig-results.png)

Now let's check `preprod-payroll.trick.htb` subdomain.

I tried `admin/admin` as the login creds but it didn't work, so I tried basic SQL injection ` admin' or 1=1 -- -` in the username field and it successfully bypassed the login page.

![](/assets/images/hackthebox/Trick-Writeup/bypass-login.png)

![](/assets/images/hackthebox/Trick-Writeup/page-parameter.png)

The `page` parameter looks interesting. we can try local file inclusion using `../../../../../../../../etc/passwd`, but it didn't work either.

After some playing with the`page` parameter, I assumed that the server-side code appends `.php` to the page name in oder to include it.

So we can try `php://filter/convert.base64-encode/resource=index` and it shows the following results:

![](/assets/images/hackthebox/Trick-Writeup/LFI.png)

We can base64 decode the returned result to view the source code.

`index.php`

![](/assets/images/hackthebox/Trick-Writeup/index.png)

`login.php`

![](/assets/images/hackthebox/Trick-Writeup/login.png)

Notice the included file `./db_connect.php`, let’s try to read it.

![](/assets/images/hackthebox/Trick-Writeup/db_connect.png)

I tried to ssh with these creds, but it didn't work.

## Shell as michael

I tried to do subdomain enumeration with `wfuzz`, but I got no results.

So let's do it again but now we perpend the word `preprod` to our wordlist, and it shows a new subdomain.

![](/assets/images/hackthebox/Trick-Writeup/third-domain.png)

After checking `preprod-marketing` subdomain, I found that it's vulnerable to `local file inclusion`.  let's try to read some files.

`/etc/passwd`

![etc-passwd](/assets/images/hackthebox/Trick-Writeup/etc-passwd.png)

`/home/mishael/.ssh/id_rsa`

![](/assets/images/hackthebox/Trick-Writeup/id_rsa-michael.png)

> Now remember to change the permissions of the `id_rsa` file to 600

Now we can dump the private key and ssh into the box as `michael` user and read `user.txt`.

![](/assets/images/hackthebox/Trick-Writeup/user.txt.png)

## Shell as root

Running `sudo -l` , we can see that `michael` user can restart `fail2ban` service as `root` user without a password.

![](/assets/images/hackthebox/Trick-Writeup/sudo.png)

But what is fail2ban? 

> fail2ban is an intrusion prevention software framework that protects computer servers from brute-force attacks.

I found a [blogpost](https://youssef-ichioui.medium.com/abusing-fail2ban-misconfiguration-to-escalate-privileges-on-linux-826ad0cdafb7) while searching for exploits for this service and it shows that I can modify `/etc/fail2ban/action.d/iptables-multiport.conf` and insert my payload in `actionban` variable.

But when I tried to do it I didn't have the permission to modify the file (write-protected), but I can delete/overwrite it.

![](/assets/images/hackthebox/Trick-Writeup/actionban.png)

> +s is the `setuid` bit, which tells the OS to execute that program  with the userid of its owner.  This is typically used with files owned  by root to allow normal users to execute them as root.

So I had to move the file to `/tmp` directory, add my payload and overwrite the original file. 

![](/assets/images/hackthebox/Trick-Writeup/bash.png)

Finally I used `hydra` to make failed login attempts and get banned.

After geting banned, we can execute `/bin/bash` as root.	

![](/assets/images/hackthebox/Trick-Writeup/hydra.png)

![](/assets/images/hackthebox/Trick-Writeup/root.txt.png)

