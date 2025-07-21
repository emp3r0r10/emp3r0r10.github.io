---
title: "BsidesSF CTF 2025 Writeup"
classes: wide
header:
  teaser: /assets/images/ctf_writeups/Bsides_CTF/Bsides_Logo.png
ribbon: blue
description: "Hello, This is my writeup for BsidesSF CTF, In this writeup I will walk you through most web challenges that I solved. I will also show you how I solved another categories, so let's get started."
categories:
  - CTF Writeups
toc: true
---


Hello, This is my writeup for BsidesSF CTF, In this writeup I will walk you through most web challenges that I solved. I will also show you how I solved another categories, so let's get started.

## Challenge 1 (detector - web)

We can see the challenge provides us with the flag path, the website link with source code.

![Challenge_1](/assets/images/ctf_writeups/Bsides_CTF/Challenge_1.png)

The website is simple as it takes an IP address and ping on it.

![Solution_1.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.1.png)

So, let's check the source code and examine how it works.

![Solution_1.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.6.png)

![Solution_1.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.5.png)

We can see above that it checks for the range of each octet in an IP address (this is a client side validation), but in the `php` code it passes the the IP directly to `system()` function without validation, so an attacker can inject a vaild IP range then execute system commands.

![Solution_1.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.3.png)

![Solution_1.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.4.png)





As we have the flag path already, let's read it.

![Solution_1.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_1.2.png)

## Challenge 2 (detector-2 - web)

This the improved version of the pervious challenge, so let's access the website.

![Challenge_11](/assets/images/ctf_writeups/Bsides_CTF/Challenge_11.png)

![Solution_11.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.1.png)

We can see above the challenge has the same website like previous challenge, so let's check the source code.

![Solution_11.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.3.png)

![Solution_11.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.2.png)

The challenge has an small validation on server side which puts the IP address into double quotes and prevents from escaping by checking if the user-input contains double quotes, it returns an error.

As the IP will be injected into `system()` function and executed by `bash`, we can bypass the validation using `127.0.0.1$(Command)`.

The bash will run the command inside `$(Command)`and Replace `$(Command)` with the output of the command.

![Solution_11.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.4.png)

![Solution_11.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.5.png)

 The flag path is provided, so let's read the flag.

![Solution_11.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_11.6.png)

## Challenge 3 (go-git-it - web)

We can notice from the challenge name that our goal is to access `.git` directory.

![Challenge_2](/assets/images/ctf_writeups/Bsides_CTF/Challenge_2.png)

We can see it's a static website, so let's try to access `/.git`

![Solution_2.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.1.png)

We can see the directory is not found, weird!, let's back to the home page and inspect the page source.

![Solution_2.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.4.png)

We can see a comment which tells us `don't forget to unlist the git directory`, so let's try `/git`.

![Solution_2.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.2.png)

And we can access it successfully.

![Solution_2.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.3.png)



Now let's download it using [GitTools](https://github.com/internetwache/GitTools) especially `git-dumper`.

![Solution_2.7](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.7.png)

Let's explore the history of commits using `git log`.

![Solution_2.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.5.png)

As the first commit has a note of `remove sensitive information`, let's see it using `git show <commit>`.

![Solution_2.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_2.6.png)

## Challenge 4 (hangman-one - web)

![Challenge_3](/assets/images/ctf_writeups/Bsides_CTF/Challenge_3.png)

We can see a login page and signup pages, so let's create an account and login.

![Solution_3.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.1.png)

We can see that the our goal is to guess flag and we have 4 times to wrong then gameover.

![Solution_3.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.2.png)

Acutually I tried to find something to make the guess process easier but I couldn't, so my approach is:

1. Create an account and login
2. Try characters and note the wrong one
3. Repeat until I git the flag.

The image below from admin account as I tried to login as `admin:admin` and found that his guesses are saved.

![Solution_3.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.3.png)

![Solution_3.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.4.png)

![Solution_3.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.6.png)

![Solution_3.8](/assets/images/ctf_writeups/Bsides_CTF/Solution_3.8.png)

## Challenge 5 (your-browser-hates-you - web)

The challenge tells us that the URL will not work as it has something wrong in SSL certificate.

![Challenge_5](/assets/images/ctf_writeups/Bsides_CTF/Challenge_5.png)

If we try it in the browser, it's not working.

![Solution_5.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_5.1.png)

But we can try to open it using `curl -k <url>` and get the flag.

> The `-k` (or `--insecure`) flag tells `curl` ignore certificate validation. 

![Solution_5.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_5.2.png)

## Challenge 6 (web-tutorial-1 - web)

In this challenge, we need to exploit XSS steal the flag from admin

![Challenge_7](/assets/images/ctf_writeups/Bsides_CTF/Challenge_7.png)

We can a 3 hints, let's see them

![Solution_7.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_7.1.png)



![Solution_7.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_7.2.png)

Let's try to trigger an XSS for testing the vulnerability.

![Solution_7.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_7.3.png)

Now our approach is:

1. Use JavaScript (`XMLHttpRequest` or `fetch`) to make a request to `/xss-one-flag`. This fetches the flag from the Admin's session.
2. Send the response from the request to attacker machine (I will use `webhook`).

![Solution_7.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_7.5.png)

![Solution_7.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_7.4.png)

## Challenge 7 (hidden-reports - web)



![Challenge_8](/assets/images/ctf_writeups/Bsides_CTF/Challenge_8.png)

If we visit a website, we can see that we need to enter a valid password (Authorization Code) to get the flag. But there is an interesting note tells us to avoid using `'` which indicates to `SQL Injection`.

![Solution_8.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_8.1.png)

We can see that the single quote `'` breaks the SQL query. So, we can bypass the query using `' OR 1=1 --`

`'` The end of the query.

`OR 1=1` which always true.

`--` to comment the reset of query.

![Solution_8.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_8.2.png)

![Solution_8.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_8.5.png)

## Challenge 8 (sighting - web)

![Challenge_10](/assets/images/ctf_writeups/Bsides_CTF/Challenge_10.png)

The website has an upload feature, but when I tried to upload an image, I can't see where it uploaded. So, I opened one of the above images (already exists) in new tab.

![Solution_10.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_10.1.png)

We can see images will be in `uploads/` directory, but if we try to access our uploaded image, it gives us not found.

![Solution_10.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_10.2.png)

So, the file upload is a rabbit hole, but `file` parameter is interesting and may indicate to `Local File Inclusion (LFI)`.

Let's try to access `../../../../etc/passwd`.

![Solution_10.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_10.3.png)

Now let's read the flag from the provided path `/flag.txt`.

![Solution_10.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_10.4.png)

## Challenge 9 (taxonomy - web)

![Challenge_12](/assets/images/ctf_writeups/Bsides_CTF/Challenge_12.png)

The website shows us a list of users and some data belong to them. This belongs to `SQL Injection`. Why I said that? because I suppose that the data returned from database. It also maybe a static and written by developer.

![Solution_12.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_12.1.png)

At first let's try to inject `test'`, and as expected it returned a SQL error.

![Solution_12.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_12.2.png)

We need to bypass the query to read the flag as we do before.

![Solution_12.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_12.3.png)



But the above query will not work correctly, so we need to track the SQL query and close each part using `%' OR 1=1 ) --`.

`%` Complete the LIKE.
`'` Close the ' string.
`OR 1=1` Always true.
`)` Close the ( group.
`--` Comment out everything else.

Now the `WHERE` clause is always true because of `OR 1=1`. We can see all data from the database.

![Solution_12.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_12.5.png)

## Challenge 10 (evidence - web)

The challenge gives us a small hint about XML entities which is interesting and indicate to `XXE Injection`.

![Challenge_13](/assets/images/ctf_writeups/Bsides_CTF/Challenge_13.png)

The website shows us an upload feature and the allowed extension is XML

![Solution_13.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_13.2.png)

Let's try to upload a normal XML file.

![Solution_13.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_13.6.png)

Everything looks normal, so let's read the source code.

![Solution_13.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_13.1.png)

The above code is vulnerable to `XXE Injection` because:

- `libxml_disable_entity_loader(false);` Allows us to load external entities.

  We re-enable entity loading, which allows the processing of external entities in the XML input.

- - Expand entities (`LIBXML_NOENT`).
  - Load external DTDs (`LIBXML_DTDLOAD`).

- User-Controlled Input:
   You take a file uploaded by the user and directly load it into the `DOMDocument` without any validation or sanitization.

So, as it loads external entities we can read internal files us. Let's try to read `/etc/passwd` to confirm that it works.

![Solution_13.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_13.4.png)

Now we can read `/flag.txt`.

![Solution_13.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_13.5.png)

## Challenge 11 (dating - web)

In this challenge the goal is to gain RCE and read the flag.

![Challenge_14](/assets/images/ctf_writeups/Bsides_CTF/Challenge_14.png)

The website takes some parameters to create a dragon profile.

![Solution_14.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.1.png)

![Solution_14.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.2.png)

Let's navigate to the source code directly to have a better understanding.



> **Wikipedia**
>
> A **Jakarta Servlet**, formerly **Java Servlet** is a [Java](https://en.wikipedia.org/wiki/Java_(programming_language)) [software component](https://en.wikipedia.org/wiki/Software_component) that extends the capabilities of a [server](https://en.wikipedia.org/wiki/Server_(computing)). Although servlets can respond to many types of requests, they most commonly implement [web containers](https://en.wikipedia.org/wiki/Web_container) for hosting [web applications](https://en.wikipedia.org/wiki/Web_application) on [web servers](https://en.wikipedia.org/wiki/Web_server) and thus qualify as a server-side servlet [web API](https://en.wikipedia.org/wiki/Web_API). Such web servlets are the [Java](https://en.wikipedia.org/wiki/Java_(software_platform)) counterpart to other [dynamic web content](https://en.wikipedia.org/wiki/Dynamic_web_page) technologies such as [PHP](https://en.wikipedia.org/wiki/PHP) and [ASP.NET](https://en.wikipedia.org/wiki/ASP.NET).

The code above sends a POST  request to `/ProfileServlet`:

1. It reads the **raw POST body** as a binary input stream.
2. It **deserializes** that input using `XMLDecoder`.
3. `XMLDecoder` expects **XML formatted Java objects**.
4. It reads an object (`dragonData`) from the XML.
5. It **responds** by printing `Profile received for: {dragonData}`.

The issue here is that If an attacker sends malicious XML, they can create dangerous objects that:

- Execute arbitrary code
- Read files
- Write files
- Run system commands (if gadget chains exist)

![Solution_14.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.3.png)

After searching for an exploit for this situation, I found this exploit from [Exploit-DB](https://www.exploit-db.com/exploits/39438).

Let's test it first by sending a request to webhook.

![Solution_14.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.4.png)

![Solution_14.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.5.png)

Now we need to get a shell and for this, we can use ngrok as an IP and port to listen on and use `nc` to get a reverse shell. 

![Solution_14.7](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.7.png)

![Solution_14.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.6.png)



Finally let's read the flag.

![Solution_14.8](/assets/images/ctf_writeups/Bsides_CTF/Solution_14.8.png)

## Challenge 12 (pathing - web)

The website tells us the flag is in the following path `../../../../../../../../flag.txt` which indicates to `Path Traversal`.

![Challenge_15](/assets/images/ctf_writeups/Bsides_CTF/Challenge_15.png)

If we go to the website, it shows us the path will be inserted into the URL directly without parameters.

![Solution_15.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_15.1.png)

But we can't access it using a browser because the browser automatically normalize the URL path before sending it.

![Solution_15.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_15.2.png)

![Solution_15.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_15.3.png)

So, we can bypass it by sending the request using burp suite and get the flag.

![Solution_15.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_15.4.png)

## Challenge 13 (hoard - web)

![Challenge_16](/assets/images/ctf_writeups/Bsides_CTF/Challenge_16.png)

The website shows us some user inputs, so let's check the source code directly.

![Solution_16.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_16.1.png)

It just sends a POST request with some data but the interesting part is `shell_exec()` which tells us if the `hoardType` is equal to `artifact`, it passes the reset of parameters into `shell_exec()`.

So, we need to escape from single quotes and execute system commands to read the flag using `'; cat /flag.txt ; #`.

**Note:** In the first time I used `//` but it's not working, so I replace it with `#`.

![Solution_16.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_16.2.png)

![Solution_16.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_16.3.png)

![Solution_16.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_16.4.png)

## Challenge 14 (meow - terminal)

This challenge is like a warm up to the terminal category which our goal is to read the flag using the provided flag path.

![Challenge_4](/assets/images/ctf_writeups/Bsides_CTF/Challenge_4.png)

Just navigate to the website and read the flag. Piece of Cake!

![Solution_4](/assets/images/ctf_writeups/Bsides_CTF/Solution_4.png)

## Challenge 15 (toothless - forensics)

Move to another category which is `forensics`, and the challenge provide us with `.pcap` file

![Challenge_6](/assets/images/ctf_writeups/Bsides_CTF/Challenge_6.png)

Let's open it in `wireshark` and analyze the packets.

![Solution_6.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_6.1.png)

We can see that the packets are `ICMP`, so we need to check all packets and capture the one that contains `Data` as we see above.

Let's decode the value of `Data` and get the flag.

![Solution_6.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_6.2.png)

## Challenge 16 (dragon-name - mobile)

The last challenge is a mobile challenge and it provides us with the `.apk` file, so, let's download and insert it into `Jadx-GUI`. 

![Challenge_9](/assets/images/ctf_writeups/Bsides_CTF/Challenge_9.png)

We can see  in `AndroidManifest.xml` that the app contains 3 activities. Let's start by analyzing `MainActivity` 

![Solution_9.1](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.1.png)

The below function is the most interesting part in `MainActivity`.

We can see the flag consists of 5 parts:

1. Part #1: The `rot13` of `PGS` string
2. Part #2: The `bas64` decoding of `dzNhaw==` string
3. Part #3: `T0`
4. Part #4: This part will be found in `res/values/strings.xml` file 
5. Part #5: `Typ3`

![Solution_9.2](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.2.png)

![Solution_9.3](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.3.png)

![Solution_9.4](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.4.png)

![Solution_9.5](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.5.png)

The final flag will be:

![Solution_9.6](/assets/images/ctf_writeups/Bsides_CTF/Solution_9.6.png)

