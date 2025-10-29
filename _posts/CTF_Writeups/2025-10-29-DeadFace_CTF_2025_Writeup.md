---
title: "# DEADFACE CTF 2025 - Hack The Night Writeup"
classes: wide
header:
  teaser: /assets/images/ctf_writeups/DeadFace_CTF/DeadFace_CTF_Logo.jpg
ribbon: blue
description: "Hello everyone, this my writeup for `Hack The Night` in DEADFACE CTF 2025. It covers a website and our goal is to retrieve the 10 flags for the 10 challenges. So, let's get started."
categories:
  - CTF Writeups
toc: true
---

Hello everyone, this my writeup for `Hack The Night` in DEADFACE CTF 2025. It covers a website and our goal is to retrieve the 10 flags for the 10 challenges. So, let's get started.

![Solved_Challenges](/assets/images/ctf_writeups/DeadFace_CTF/Solved_Challenges.png)

## Challenge #1: The Source of Our Troubles

![Challenge_1](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_1.png)

The story is that the DEADFAC compromised Night Veil University's student portal Website (NVU) and we should explore the website to find 10 flags.

So, let's go to website and explore it.

![Solution_1.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_1.1.png)

We can see it's a normal website for announcements, courses, researches and news, with a login feature.

The challenge name relates to the source code, so let's examine it.

![Solution_1.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_1.2.png)

> deadface{v13w_s0urc3_4lw4ys_f1rst}

## Challenge #2: Hidden Paths

![Challenge_2](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_2.png)

The challenge description refers to web crawlers, which indicates the existence of `/robots.txt`. Let's try it.

> **Web crawlers**, also known as web spiders, bots, or robots, are automated programs used to browse the web and collect information from websites. Their primary purpose is to index content for search engines, which helps users find relevant information when they perform searches. Here’s a bit more detail on how they work and their uses.
>
> `robots.txt` file on a website to see if they are allowed to access certain pages. This helps site owners manage and control how their site is crawled.

![Solution_2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_2.png)

We can see there are some endpoints, so take note of them as they may help us later.

> deadface{r0b0ts_txt_r3v34ls_h1dd3n_p4ths}

## Challenge #3: Console Chaos

![Challenge_3](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_3.png)

The challenge name contains the keyword *console* and the description refers to browser tools, so let's check the console on the home page.

![Solution_3](/assets/images/ctf_writeups/DeadFace_CTF/Solution_3.png)

Don't forget to note the two endpoints shown above.

> deadface{c0ns0l3_l0gs_4r3_y0ur_fr13nd}

## Challenge #4: Stick to the Script

![Challenge_4](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_4.png)

The challenge description tells us there is a secret in obfuscated code on the website.

If we analyze the home page source code, we can see a `script.js` file which may contain obfuscated data.

![Solution_4.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_4.1.png)

Let's check it.

![Solution_4.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_4.2.png)

We can see in the image above that there is base64-encoded text. Let's decrypt it.

![Solution_4.3](/assets/images/ctf_writeups/DeadFace_CTF/Solution_4.3.png)

> deadface{j4v4scr1pt_c4n_h1d3_s3cr3ts}

## Challenge #5: Pest Control

![Challenge_5](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_5.png)

The description of the challenge tells us that NVU has an API and DEADFACE abused it to gain configuration information to attack NVU.

If you remember in the third challenge, there are two endpoints related to the API leaked in the console and we noted them earlier.

Notice that one of them contains the word `config`, which indicates configuration. Let's check it.

![Solution_5](/assets/images/ctf_writeups/DeadFace_CTF/Solution_5.png)

> deadface{4p1_d3bug_3xp0sur3_l34ks}

## Challenge #6: Access Granted

![Challenge_6](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_6.png)

![Solution_6.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_6.1.png)

The website contains a login page and provides us with some credentials. Let's try the student account credentials.

![Solution_6.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_6.2.png)

We are logged in as a normal user, but the challenge tells us we need to gain authenticated access to the website like DEADFACE did, so let's try `SQL Injection` in the username.

![Solution_6.3](/assets/images/ctf_writeups/DeadFace_CTF/Solution_6.3.png)

![Solution_6.4](/assets/images/ctf_writeups/DeadFace_CTF/Solution_6.4.png)

> deadface{sql_1nj3ct10n_byp4ss_4uth}

## Challenge #7: Reverse Course

![Challenge_7](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_7.png)

The challenge tells us there is an account called *emergency admin* with admin privileges that NVU removed. However, there is still some data related to the account somewhere in the web app.

If you remember in `/robots.txt` there is a `/backup` endpoint which may contain old or removed data, so let's check it.

![Solution_7.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_7.1.png)

As expected there is a SQL file, let's download and read it.

![Solution_7.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_7.2.png)

> deadface{EmergencyAccess2025!}

## Challenge #8: Not-So-Public Domain

![Challenge_8](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_8.png)

DEADFACE was able to retrieve a hidden announcement and our goal is to access it.

If we go back to the third challenge again, we can observe another API endpoint. Let's explore what it does.

![Solution_8.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_8.1.png)

We can see it returns announcement data according to the `q` parameter's value. What happens if we inject a single quote (`'`)?

![Solution_8.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_8.2.png)

Okay, it's a SQL error which is interesting. Let's try to retrieve all columns and data using `' OR 1=1 -- -`.

![Solution_8.3](/assets/images/ctf_writeups/DeadFace_CTF/Solution_8.3.png)

> deadface{h1dd3n_4nn0unc3m3nts_r3v34l_s3cr3ts}

## Challenge #9: Classified

![Challenge_9](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_9.png)

Like the previous challenge, this one involves a leaked confidential research.

To search for researches, we can use `/api/search.php`, but to know the exact URL we need to examine `search.php`.

![Search_Feature](/assets/images/ctf_writeups/DeadFace_CTF/Search_Feature.png)

So, it's like announcements but change the type to `research`.

![Solution_9.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_9.1.png)

![Solution_9.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_9.2.png)

As we see above, testing SQL here is also worked like the previous challenge and we got the flag.

> deadface{cl4ss1f13d_r3s34rch_unh4ck4bl3}

## Challenge #10: The Invisible Man

### Intended Way


![Challenge_10](/assets/images/ctf_writeups/DeadFace_CTF/Challenge_10.png)

The final challenge tells us the flag is located in a user not displayed on the website.

The admin panel shows us 15 users only, but if we go back to `/api/debug.php` when we access the config.

![Debug_Mode](/assets/images/ctf_writeups/DeadFace_CTF/Debug_Mode.png)

We have options to list all users also, so let's do it.

![List_Of_Users_1](/assets/images/ctf_writeups/DeadFace_CTF/List_Of_Users_1.png)

![List_Of_Users_2](/assets/images/ctf_writeups/DeadFace_CTF/List_Of_Users_2.png)

The last user (`backup_admin`) is a hidden one. We can login with it using the `SQL Injection` found on the login page.

![backup_admin_dash_profile](/assets/images/ctf_writeups/DeadFace_CTF/backup_admin_dash_profile.png)

The flag is somewhere in this account, so let's go to `/admin.php`.

![Admin_Dashboard_1](/assets/images/ctf_writeups/DeadFace_CTF/Admin_Dashboard_1.png)

We can notice a `view Details` beside each user in the above list. Let's click on each one to examine the behavior.

 ![Solution_10.1.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.1.1.png)

![Solution_10.2.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.2.2.png)

It returns users data according to the user ID, so what happens, if we change the value of `view_user` from `1` to `16` (`backup_admin` ID) as it the sixteenth user from the above list.

![Solution_10.5.5](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.5.5.png)

### Unintended Way

If we back to admin dashboard, we can see a feature to ping a host, let's try and intercept the request to burp.

![Solution_10.1](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.1.png)

![Solution_10.2](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.2.png)

As we see above, it is vulnerable to command injection which leads to RCE.

Let's do `ls` to list the files.

![Solution_10.3](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.3.png)

If we read `admin.php`, we can see the flag.

![Solution_10.4](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.4.png)

![Solution_10.5](/assets/images/ctf_writeups/DeadFace_CTF/Solution_10.5.png)

> deadface{1ns3cur3_d1r3ct_0bj3ct_r3f3r3nc3}