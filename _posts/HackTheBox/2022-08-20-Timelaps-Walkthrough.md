# HackTheBox Timelapse Walkthrough

<img src="/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/Timelapse.png" style="zoom:50%;" />

## Recon

First, let's start with nmap port scanning.

![nmap-results](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/nmap-results.png)

We can see port 445 is open so let's check the shared folders.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/smbclient-test.png)

So We have list of folders and we have access to `Shares` folder with the `anonymous` user.

Let's check the `Shares` folder and download all file in it.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/smbclient-extract.png)

When we unzip `winrm_backup.zip`, we can that it's password protected file.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/unzip-faild.png)

Let's get password hash with `zip2john` and crack it with `hashcat`

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/john.png)

Let's unzip `winrm_backup.zip` again with this password.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/unzip-password.png)

## Shell as legacyy

Now we have `legacyy_dev_auth.pfx`, so let's extract `crt` and `key` files.

> A PFX file indicates a certificate in PKCS#12 format; it contains the  certificate, the intermediate authority certificate necessary for the  trustworthiness of the certificate, and the private key to the  certificate. Think of it as an archive that stores everything you need  to deploy a certificate.

Again we have password protected file, let's decrypt it.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/pfx-protected.png)

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/pfx2john.png)

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/john-pfx.png)

Now let's extract `crt` and `key` files.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/private&certificate.png)

We can login to box using `evil-winrm` with `key` and `certificate`.

Let's read `user.txt`.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/user.txt.png)

## Shell as svc_deploy

Running [WinPEAS](https://github.com/carlospolop/PEASS-ng/tree/master/winPEAS), we found interesting file called `C:\Users\legacyy\AppData\Roaming\Microsoft\Windows\Powershell\PSReadLine\ConsoleHost_history.txt`, let's check it.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/WinPEAS.bat.png)

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/svc_delopy-history.png)

We can run this commands, upload `nc.exe` and get a shell as `svc_deploy` user.

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/history-commands-shell.png)

![](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/nc-shell-svc.png)

## Shell as root

When we run `whoami /all` we can find that in `LAPS_Readers` group.

![laps-group](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/laps-group.png)

> LAPS allows you to manage the local Administrator password (which is randomised, unique, and changed regularly) on domain-joined computers. These passwords are centrally stored in Active Directory and restricted to authorised users using ACLs.

After search about this group and exploit it, We can excute this command to get  `Administrator` password.

![administrator-password](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/administrator-password.png)

Now let's login to box with `Administrator` user and read `root.txt`.

![root.txt](/home/emperor10/Downloads/Pentesting/Machines/HTB-MACHINES/timelapse/Writeup/root.txt.png)