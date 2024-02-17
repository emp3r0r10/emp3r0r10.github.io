# Drive Machine Writeup

<img src="C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Drive.png" alt="Drive" style="zoom:50%;" />

Drive is HackTheBox Hard Linux Machine which starts with a website that I can upload, store, edit, and share files. as well as adding groups and showing reports. First I'll register an account and upload a file. I'll try to reverse it and find an `IDOR` vulnerability. Then I'll brute force the file's ids using burp intruder and found login credentials to SSH into machine. I'll forward port `3000` and discover `Gitea` service. I'll extract database from `backups` folder and read `db.sqlite3` file. I'll reverse engineer a binary file and read source code using `IDA`. Finally I'll exploit `Remote Code Execution` using `load_extension` library in `sqlite3`.

## Recon

As always let's start with nmap port scanning.

![nmap_results](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\nmap_results.png)

`-sC` for default scripts scan.

`-sV` for enumerate version of services.

`-oN` for output.

We have 2 open ports 22 (SSH), 80 (HTTP) and 1 filtered port that is `3000`. The host is running on `ubuntu` OS and `nginx 1.18` web server.

There’s a redirect on port 80 to `drive.htb`, so let's add it to `/etc/hosts` and check it.

We can see we have option to register an account and login, so let's register an account and access the website.

![Doodle_website](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_website.png)

After checking the website, we can upload, edit, reserve, and share files. We can also add user groups and show reports. lot of features we need to test.

![Doodle_home_page](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_home_page.png)

So First let's try upload a file.

> MIME type (also known as a Multipurpose Internet Mail Extension) is a standard that indicates the format of a file. It is a fundamental characteristic of a digital resource that influences its ability to be accessed and used over time.
>
> For example:  text/plain

![Doodle_upload](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_upload.png)

![Doodle_show_file](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_show_file.png)

If we try to abuse upload functionality, it isn't vulnerable.

Let's show our file and intercept the request in burp. We can see file id looks interesting.

![GetFileDetail](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\GetFileDetail.png)

So, let's send the request to intruder and try to fuzz file id.

![GetFileDetail_Failed](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\GetFileDetail_Failed.png)

We can see above there are various file ids with different status code `401` which means it's unauthorized to access. So let's continue check other functionalities.

If we click `Reverse` we can edit that file.

![Doodle_edit_file](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_edit_file.png)

Let's intercept the request to burp and analyze it.

## Shell as Martin

![burp-idor-error](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\burp-idor-error.png)

If we try to change the file id to any pervious id which gives us unauthorized, we can access these files which is `IDOR` vulnerability.

> **Insecure direct object reference also known as idor** occur when an application provides direct access to objects based on user-supplied input. As a result of this vulnerability attackers can bypass authorization and access resources in the system directly, for example database records or files.

**ID:** `98`

![Doodle_Hi](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_Hi.png)

**ID:**  `99`

![Doodle_security_announce](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_security_announce.png)

**ID:**  `100`

![Welcome_to_Doodle_Grive](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Welcome_to_Doodle_Grive.png)

**ID:**  `101`

![Doodle_database_backup](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_database_backup.png)

**ID:**  `79`

![Doodle_software_engineer](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_software_engineer.png)

As we see above `79` ID leaks sensitive information.

If we try to SSH with `martin:Xk4@KjyrYv8t194L!`, we will login.

![martin_shell](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\martin_shell.png)

## Shell as Tom

When we do some manual enumeration on machine, we can find `backups` directory which looks interesting. 

![sqlite_7z_backups](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\sqlite_7z_backups.png)

Let's transfer files to our local and try to unzip it.

We can see zipped files are protected and `martin` password also not working.

![faild_unzip](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\faild_unzip.png)

Let's do more enumeration and we can see the following open ports which port `3000` looks interesting.

![netstat](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\netstat.png)

So, let's forward this port and access it.

![ssh-tunnel](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\ssh-tunnel.png)

We can see it's `Gitea` website. If we try to login with with `martin` credential, we can access it.

![gitea_page](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\gitea_page.png)

![Doodle_source](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\Doodle_source.png)

As we see above it's `DoodleGrive` repository, let's check `db_backup.sh`.

![db_backup_password](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\db_backup_password.png)

Now we have zip password, so let's extract zip files in `backups` folder one by one it gives us four different hashes for `tom` user.

![tom_hash1](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\tom_hash1.png)

![tom_hash2](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\tom_hash2.png)

![tom_hash3](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\tom_hash3.png)

![tom_hash4](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\tom_hash4.png)

Let's try to crack these hashes with `hashcat`.

![hashcat_payload](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\hashcat_payload.png)

It shows us the following results:

![hashcat_results](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\hashcat_results.png)

If we try to SSH with `tom` user and these passwords, `tom:johnmayer7` will work.

Let's read `user.txt`.

![user_flag](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\user_flag.png)

## Shell as root

We can see we have an interesting file called `doodleGrive-cli` which used to monitoring server status.

![README](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\README.png)

As we see below, we need username and password to run this file.

![doodleGrive-cli_faild_login](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_faild_login.png)

So let's transfer it to our local machine and reverse engineering using `IDA`.

We can see login credentials in the main function of the binary code. 

![doodleGrive-cli_main](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_main.png)

let's login and explore `doodleGrive-cli` binary file.

![doodleGrive-cli_test](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_test.png)

So let's look around main function in `IDA`.

We can see it calls different function in each case.

Function #1: `main_menu()`

![doodleGrive-cli_main_menu](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_main_menu.png)

Function #2: `show_users_list()`

This function use select query to display users data.

![doodleGrive-cli_show-users-list](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_show-users-list.png)

Function #3: `show_groups_list()`

This function use select query to display users group data.

![doodleGrive-cli_show-groups-list](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_show-groups-list.png)

Function #4: `show_server_status()`

This function runs `server-health-chech.sh` file as `www-data` user.

![doodleGrive-cli_show-server-status](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_show-server-status.png)

Function #5: `show_server_log()`

This function displays the last `1000` line of `access.log` file.

![doodleGrive-cli_show-server-logs](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_show-server-logs.png)

As we can see above all functions are normal, but the function below looks interesting.

Function #6: `activate_user_account`

This function use update query to set `is_active` column to value `1` where `username` is specified by user. 

![doodleGrive-cli_activate_users](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_activate_users.png)

We can see above the function runs `sqlite3` and accepts user input in `username` which indicates to `sqlite` injection.

If we search for `sqlite injection`, we can find some tricks in [PayloadAllTheThings](https://github.com/swisskyrepo/PayloadsAllTheThings/blob/master/SQL Injection/SQLite Injection.md).

According PayloadAllTheThings, we can use [load_extension](https://www.sqlite.org/c3ref/load_extension.html) function to call other file from directory and execute a `Remote Code Execution`.

> The load_extension(X,Y) function loads [SQLite extensions](https://www.sqlite.org/loadext.html) out of the shared library file named X using the entry point Y. The result of load_extension() is always a NULL. If Y is omitted then the default entry point name is used. The load_extension() function raises an exception if the extension fails to load or initialize correctly.
>
> The load_extension() function will fail if the extension attempts to modify or delete an SQL function or collating sequence. The extension can add new functions or collating sequences, but cannot modify or delete existing functions or collating sequences because those functions and/or collating sequences might be used elsewhere in the currently running SQL statement. To load an extension that changes or deletes functions or collating sequences, use the [sqlite3_load_extension()](https://www.sqlite.org/c3ref/load_extension.html) C-language API.
>
> For security reasons, extension loading is disabled by default and must be enabled by a prior call to [sqlite3_enable_load_extension()](https://www.sqlite.org/c3ref/enable_load_extension.html).

> **Remote Code Execution also known as RCE** attacks allow an attacker to remotely execute malicious code on a computer. The impact of an RCE vulnerability can range from malware execution to an attacker gaining full control over a compromised machine.

Now we know the first parameter in load_extension() function is a shared library, let's search for [Shared Library Exploit](https://tbhaxor.com/exploiting-shared-library-misconfigurations/).

The only command we are using is `chmod` to change the `suid` of `bash` file.

First we need to write a malicious script with our payload, then we need to compile it.

![root_shell](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\root_shell.png)

Now let's call it using `load_extension()`, but because the query inside string according with `IDA` that we’ve analyze before, we must close

the quotes to separate between string and function using `"+load_extension()+"`.

![doodleGrive-cli_test_shell](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\doodleGrive-cli_test_shell.png)

As we see above the binary removes letter `.`, `/`.

If we back to `IDA`, we will notice the `sanitize_string()` function.

So let's check what this function does.

![santize_code](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\santize_code.png)

as we see it filter the following bad characters from string. 

![bad_chars](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\bad_chars.png)

We can bypass filtered characters by using `char()` function.

> The char(X1,X2,...,XN) function returns a string composed of characters having the unicode code point values of integers X1 through XN.

But If we call the `shell` file, it shows us the following error:

![limition_bypass](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\limition_bypass.png)

I think there is a limitation on the file name, so we can make the file name one character.

So let's convert `shell.c` to `a.c` and compile it again.

![edit_shell](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\edit_shell.png)

We need to convert the file name to `ascii` and call it again.

![text-to-ascii](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\text-to-ascii.png)

![final_exploit](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\final_exploit.png)

Now Let’s run `/bin/bash` and use the `-p` option to run it in privileged mode to be able to read `root.txt`.

![root.txt](C:\Users\abdel\Documents\Pentesting\Machines\HackTheBox\HTB-Machines\Rooted\drive\Writeup\root_flag.png)
