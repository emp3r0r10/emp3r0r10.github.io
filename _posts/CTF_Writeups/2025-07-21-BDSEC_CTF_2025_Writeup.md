---
title: "BDSEC CTF 2025 Writeup"
classes: wide
header:
  teaser: /assets/images/ctf_writeups/BDSec_CTF/BDSEC_logo.png
ribbon: blue
description: "The challenge description gives us some hints and an overview of the challenge:"
categories:
  - CTF Writeups
toc: true
---

## Evil File reader - Web

![Challenge_1](/assets/images/ctf_writeups/BDSec_CTF/Challenge_1.png)

The challenge description gives us some hints and an overview of the challenge:

1. Name of challenge indicates to path traversal or local file inclusion.
2. The last paragraph gives us a hint about characters and they act differently which is interesting.

let's navigate to challenge URL. 

![Solution_1.1](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.1.png)

We can see there is an input to enter a filename and the content is retrieved in the page as below image.

![Solution_1.2](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.2.png)

We can try to read system file such as `/etc/passwd` and `/proc/self/enivron`.

![Solution_1.3](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.3.png)

![Solution_1.4](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.4.png)

As expected the website is vulnerable to path traversal, but If we try to read `flag.txt` it gives us an error message.

![Solution_1.5](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.5.png)

After trying some encoding techniques such URL encoding, it fails.

If we back to last paragraph of the challenge, it indicates to that we need to change a single character but make sure it is treated as the intended character. This can be done using `unicode` characters.

> Unicode is a universal character encoding standard that assigns a unique number (code point) to every character, enabling computers to represent and manipulate text from diverse languages and scripts. It provides a consistent way to handle text across different platforms, programs, and languages, eliminating the confusion caused by the many different character encodings that existed previously.

So, I searched for `unicode` characters and found this list from [wikipedia](https://en.wikipedia.org/wiki/List_of_Unicode_characters).

I spent some time to understand it more and find a character that looks same a `flag.txt` but with different `unicode` or visually identical but different.

Finally I found that character similar to letter `a` but different in `unicode`.

![Solution_1.7](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.7.png)

![Solution_1.6](/assets/images/ctf_writeups/BDSec_CTF/Solution_1.6.png)

## Special Access - Web

![Challenge_2](/assets/images/ctf_writeups/BDSec_CTF/Challenge_2.png)

The challenge description indicate to that we need to break access controls to get the flag.

I navigated to the challenge link and found a login an register pages. So, let's create an account.

![Solution_2.1](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.1.png)

After that I redirected to the dashboard, but you can notice that the role is `user` which is interesting.

![Solution_2.2](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.2.png)

So, the approach now is to change this role from `user` to `admin` to read the flag as there is no more functionalities in the website.

If we navigate to profile page, we can see that we can edit our profile.

![Solution_2.3](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.3.png)

Let's try to update password and intercept the request to Burp repeater.

![Solution_2.4](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.4.png)

We can see above that the response gives us `true` and a successful message.

What happens if we add another parameter to change the role like `role:admin`.

![Solution_2.5](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.5.png)

We can see above it gives us `true` and the role is changed in the website also.

![Solution_2.6](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.6.png)

Back to the dashboard and read the flag.

![Solution_2.7](/assets/images/ctf_writeups/BDSec_CTF/Solution_2.7.png)

## Yeti Killer - Web

![Challenge_3](/assets/images/ctf_writeups/BDSec_CTF/Challenge_3.png)

The challenge description tells us that there is a feature to convert plain text into `YAML`.

Let's download files and check them.

![Solution_3.0](/assets/images/ctf_writeups/BDSec_CTF/Solution_3.0.png)

![Solution_3.1](/assets/images/ctf_writeups/BDSec_CTF/Solution_3.1.png)

We can see in the above image includes the following:

1. send a `POST` request `/` with `command` parameter.
2. `yaml.load(req.body)` Parses the incoming YAML string to a JavaScript object.
3. Extracts the value of `command`.
4. If the `command` contains a something from the blacklist, It returns an error.
5. The command is executed on the server shell.
6. Output or errors are sent back to the client.

The challenge URL redirect us to the `HTML` page.

![Solution_3.8](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.8.png)

As we see above the the YAML input is converted into JSON format.

so we can do the same using `command` parameter as we see below.

![Solution_3.9](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.9.png)

If we send a request with `{ command: "id" }` or `{ command: "ls" }`, it will executed successfully.

![Solution_3.3](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.3.png)

![Solution_3.4](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.4.png)

If we send `{ command: "cat server.js" }`, it shows us an error as it is a blacklisted value.

![Solution_3.5](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.5.png)

As we need to read `flag.txt` file, we can't read it directly. So we need to convert the payload into `base64` and execute it using `sh -c '<PAYLOAD>'`.

To do this, we need to use `echo`, but it's blocked. So, we can use it's alternative: `printf` like the following:

![Solution_3.6](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.6.png)

Now let's read the flag.

![Solution_3.7](C:\Users\abdel\Desktop\BDSec_CTF\Solution_3.7.png)