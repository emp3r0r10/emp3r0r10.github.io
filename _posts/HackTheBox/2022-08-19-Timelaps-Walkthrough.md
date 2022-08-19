---
title: "Faculty Machine Writeup"
classes: wide
header:
  teaser: /assets/images/hackthebox/Timelapse-Writeup/Faculty.png
ribbon: red
description: "Timelapse is an easy windows machine that involves smb enumeration, password hash cracking, and exploitation of weak active directory configuration."
categories:
  - HackTheBox
toc: false
---

<img src="/assets/images/hackthebox/Faculty-Writeup/Faculty.png" style="zoom:50%;" />

## Recon
# HackTheBox Timelapse Walkthrough

<img src="/assets/images/hackthebox/Timelapse-Writeup/Timelapse.png" style="zoom:50%;" />

Timelapse is an easy windows machine that involves smb enumeration, password hash cracking, and exploitation of weak active directory configuration.

## Recon

First, let's start with nmap port scanning.

![nmap-results](/assets/images/hackthebox/Timelapse-Writeup/nmap-results.png)

We can see port 445 (smb) is open so let's check the shared folders that have `anonymous` access.

![](/assets/images/hackthebox/Timelapse-Writeup/smbclient-test.png)

We have access to `Shares` folder, so Let's try to list and download its content.

![](/assets/images/hackthebox/Timelapse-Writeup/smbclient-extract.png)

If we try to unzip `winrm_backup.zip`, we can see that it's password protected.

![](/assets/images/hackthebox/Timelapse-Writeup/unzip-faild.png)

Let's get password hash with `zip2john` and crack it with `hashcat`.

![](/assets/images/hackthebox/Timelapse-Writeup/john.png)

Now we can unzip the successfully.

![](/assets/images/hackthebox/Timelapse-Writeup/unzip-password.png)

## Shell as legacyy

We got`legacyy_dev_auth.pfx`, so let's extract the embedded certificate and key files.

> A PFX file indicates a certificate in PKCS#12 format; it contains the  certificate, the intermediate authority certificate necessary for the  trustworthiness of the certificate, and the private key to the  certificate. Think of it as an archive that stores everything you need  to deploy a certificate.

But we need a password to decrypt and extract the files.

![](/assets/images/hackthebox/Timelapse-Writeup/pfx-protected.png)

Again we can use `john` to crack the password hash.

![](/assets/images/hackthebox/Timelapse-Writeup/pfx2john.png)

![](/assets/images/hackthebox/Timelapse-Writeup/john-pfx.png)

Now let's extract the `crt` and `key` files.

![](/assets/images/hackthebox/Timelapse-Writeup/private&certificate.png)

We can now login to the box using `evil-winrm` tool  with our `key` and `certificate`.

![](/assets/images/hackthebox/Timelapse-Writeup/user.txt.png)

## Shell as svc_deploy

Running [WinPEAS](https://github.com/carlospolop/PEASS-ng/tree/master/winPEAS), I found an interesting file called `C:\Users\legacyy\AppData\Roaming\Microsoft\Windows\Powershell\PSReadLine\ConsoleHost_history.txt` (which stores the powershell commands history), so let's check it.

![](/assets/images/hackthebox/Timelapse-Writeup/WinPEAS.bat.png)

![](/assets/images/hackthebox/Timelapse-Writeup/svc_delopy-history.png)

We can run the exact same commands to authenticate as `svc_deploy`, replace `whoami` command with a powershell cradle to download `nc.exe` to the target machine so we can get a shell as `svc_deploy` user.

![](/assets/images/hackthebox/Timelapse-Writeup/history-commands-shell.png)

![](/assets/images/hackthebox/Timelapse-Writeup/nc-shell-svc.png)

## Shell as root

If we run `whoami /all`, we see that we are a member of  `LAPS_Readers` group.

![laps-group](/assets/images/hackthebox/Timelapse-Writeup/laps-group.png)

> LAPS allows you to manage the local Administrator password (which is randomized, unique, and changed regularly) on domain-joined computers. These passwords are centrally stored in Active Directory and restricted to authorized users using ACLs.

After some searching I tried to enumerate the active directory computer properties to look for `ms-Mcs-AdmPwd` field (which contains clear-text password).

##### ![administrator-password](/assets/images/hackthebox/Timelapse-Writeup/administrator-password.png)

Now we can login to the box with `Administrator` user and read `root.txt`.

![root.txt](/assets/images/hackthebox/Timelapse-Writeup/root.txt.png)