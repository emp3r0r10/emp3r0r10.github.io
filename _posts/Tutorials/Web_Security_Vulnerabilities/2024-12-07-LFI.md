---
title: "Web Security Vulnerabilities - Local File Inclusion (LFI)"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/LFI/LFI-Cover.png
ribbon: red
description: "Local File Inclusion also known as LFI is a web security vulnerability that allows an attacker to include files from the server's filesystem through a web browser. This occurs when a web application dynamically includes files based on user input without proper validation."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/LFI.png" alt="LFI" style="zoom:80%;" />


## What is Local File Inclusion?

Local File Inclusion also known as LFI is a web security vulnerability that allows an attacker to include files from the server's filesystem through a web browser. This occurs when a web application dynamically includes files based on user input without proper validation.

**Example:**

Consider an application that includes files based on user input using the following URL:

`http://example.com/?page=index.php`

The application code:

```php
<?php
    $file = $_GET['file'];
    if(isset($file))
    {
        include('pages/$file');
    }
    else
    {
        include('index.php');
    }
?>
```

In this code, the `$file` parameter is used directly in the `include` function without any validation. An attacker can exploit this vulnerability by supplying a malicious file path, such as `/etc/passwd`, to access sensitive system files:

`http://example.com/?page=../../etc/passwd`

> ../../ used to back two directories

This would include the contents of the `/etc/passwd` file in the web page, potentially exposing sensitive information.

## What is the impact of LFI?

The impact of local file inclusion depends on the server files. An attacker can read or access sensitive files such as configuration files or log files, and potentially achieve Remote Code Execution (RCE) if they can include and execute malicious code.

## How to find LFI?

To find LFI vulnerabilities, focus on parameters that allow file inclusion. Some common parameters to test:

- `page`
- `file`
- `path`
- `include`
- `show`
- `locate`
- `download`
- `view`
- `site`

## How to test LFI?

After finding parameters that takes files as input, it's time to test for LFI. There are some steps you can follow to get better understanding of how application handles input file:

### Manual

1. **Directory Traversal:** Attempt to traverse directories to access sensitive files.

   ```
   /etc/passwd
   ../../../etc/passwd
   ../../../../var/www/html/index.php
   ```

2. **URL Encoding:**  Encodes characters in the URL.

   ```
   ../../../etc/passwd --> %2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd
   ```

   

3. **Use Null Byte to bypass file  extensions: **Use `%00` to terminate file extensions in PHP.

   ```
   ../../etc/passwd%00
   ../../etc/passwd%00.jpg
   ../../etc/passwd%00.php
   ```

4. **PHP Wrappers: **PHP stream wrappers can help access file contents in alternative formats:

   ```
   php://filter/convert.base64-encode/resource=index.php
   ```

   Decode the response to analyze sensitive data.

   > Another useful wrappers:
   >
   > - [file://](https://www.php.net/manual/en/wrappers.file.php) — Access local filesystem
   >
   > - [http://](https://www.php.net/manual/en/wrappers.http.php) — Access HTTP(s) URLs
   >
   > - [ftp://](https://www.php.net/manual/en/wrappers.ftp.php) — Access FTP(s) URLs
   >
   > - [php://](https://www.php.net/manual/en/wrappers.php.php) — Access various I/O streams
   >
   > - [zlib://](https://www.php.net/manual/en/wrappers.compression.php) — Compression Streams
   >
   > - [data://](https://www.php.net/manual/en/wrappers.data.php) — Data (RFC 2397)
   >
   > - [glob://](https://www.php.net/manual/en/wrappers.glob.php) — Find pathnames matching pattern
   >
   > - [phar://](https://www.php.net/manual/en/wrappers.phar.php) — PHP Archive
   >
   > - [ssh2://](https://www.php.net/manual/en/wrappers.ssh2.php) — Secure Shell 2
   >
   > - [rar://](https://www.php.net/manual/en/wrappers.rar.php) — RAR
   >
   > - [ogg://](https://www.php.net/manual/en/wrappers.audio.php) — Audio streams
   >
   > - [expect://](https://www.php.net/manual/en/wrappers.expect.php) — Process Interaction Streams

5. **Log Poisoning**: If an application writes user-controlled input into log files, attackers can inject PHP code into log files and 

   Access the log via LFI:

   ```
   ../../var/log/access.log?cmd=id
   ```

5. **Obfuscation:** Use techniques to disguise the payload

   ```
   ..//..//..//etc/passwd
   ..%2f%2f..%2f%2f..%2f%2fetc/passwd
   ```

### Automation

1. Use `Burp Intruder`/`Gobuster` to fuzz file paths

## How to prevent LFI

To prevent Local File Inclusion (LFI) vulnerabilities, the most effective approach is to avoid passing file names via user input entirely. However, if you must pass file names in user input, you can take the following measures:

1. **Input Validation:** Never trust user input. Before including files based on user input, ensure that the input is sanitized and validated to prevent malicious file inclusions.
2. **Use Whitelists:** Maintain whitelists of allowed files that can be included. This approach restricts the files that users can access and helps prevent unauthorized file inclusions.
3. **Limit File Permissions:** Set appropriate permissions for files and directories on the server to restrict access. This helps prevent attackers from accessing sensitive files even if they manage to exploit an LFI vulnerability.
4. **Error Handling:** Disable detailed error messages to avoid exposing sensitive server paths.

By implementing these measures, you can effectively prevent LFI vulnerabilities in your web application.

## Time to practice

Now let's practice on some challenge.

### Challenge #1

> **Challenge available files:**
>
> 1. home.php
> 2. about.php

So, the first challenge that I created is simple as we can see that we are asked to enter a file name. 

![Challenge_Intro](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_1/Challenge_Intro.png)

The website shows an error: `Invalid Page`.

![Challenge_Test](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_1/Challenge_Test.png)

Let's try to access `home.php` which is provided in the challenge description.

![Challenge_home_page](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_1/Challenge_home_page.png)

Now here is `Local File Inclusion` comes in, as we have parameter called `file` and it takes file name, It may be vulnerable to LFI. Let's try accessing files on the server, like `/etc/passwd`.

We can see the contents of the `/etc/passwd` file, indicating an Local File Inclusion vulnerability.

![Challenge_exploit](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_1/Challenge_exploit.png)

### Challenge #2

In the second challenge, we are asked to enter a file path.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_intro.png)

Attempting to access `index.php` yields an error, indicating the file path should start with `/var/www/html/`.

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_test.png)

Let's try to access `index.html` with the required path results in a file not found error.

The `page` parameter may be vulnerable to LFI. We attempt to escape the `/var/www/html/` directory.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_test_2.png)

The `page` parameter may be vulnerable to LFI. So, the first step to access files on the server is to escape from the required directory `/var/www/html/`.

Step-by-step directory traversal reveals we can escape only up to the `/html/` directory.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_test_3.png)

![Challenge_test_4](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_test_4.png)

We can use obfuscation techniques to bypass validation. Some applications block `../../../../`, but may not recognize `..//`. Both `../` and `..//` move up one directory level, but `..//` obfuscates the payload.

> Obfuscation in the context of security exploits refers to the practice of disguising or altering an attack payload to bypass security mechanisms such as filters or intrusion detection systems. By modifying the appearance of the payload, an attacker can sometimes evade basic security checks that look for well-known patterns or strings associated with attacks.

By bypassing directory restrictions, we can read files on the server.

![Challenge_test_5](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_test_5.png)

![Challenge_exploit](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_3/Challenge_exploit.png)

### Challenge #3

Third Challenge is provided from [rootme](https://www.root-me.org/en/Challenges/Web-Server/Local-File-Inclusion-Double-encoding), the goal is to find a password in a file on the server.

![Challenge_intro_1](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_intro_1.png)

The website has two pages: `CV` and `Contact`.

![Challenge_intro_2](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_intro_2.png)

If we try to access any of them, we can observe that there is a `page` parameter with file name as value. So, the first thing we can try is testing for `Local File Inclusion`.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_try.png)

If we try to access `/etc/passwd`, it shows us the following error:

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_test.png)

Let's back to test each character to determine what cause this error. When we try to go back one directory using `../` it shows us the same error.

![Challenge_2](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_2.png)

So, the website filter using `../`. Let's intercept the request to burp and try to bypass this filter using URL encoding.

![Challenge_url_endcode](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_url_endcode.png)

We can see above, this is not working, but double URL encoding is working.

![Challenge_double_url_endcode](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_double_url_endcode.png)

If we try to access any file, we can observe that the file name is concatenated to `$filename.inc.php`.

![Challenge_url_endcode_2](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_url_endcode_2.png)

So, we can try to access `/etc/passwd` and bypass the reset of filename using `nullbyte` technique, but it's not working here.

![Challenge_etc_passwd](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_etc_passwd.png)

So, we can think of another approach to access the source code of files. We can try PHP wrapper.

> PHP wrappers are used to encode different PHP streams. Whenever you access a webpage the data is sent to you as a stream as opposed to downloading the entire file first before opening it. A wrapper can be used to tell the stream how to handle specific protocols encoding. So instead outputing the output with its default encoding a wrapper can be used to encode the stream with say base64 encoding instead. This post will focus on using PHPs base64 encode wrapper.

We can use PHP wrapper as it encodes the source code. The server does not interpret the file but instead outputs the encoded stream instead. As we know the default page in the challenge is `home` so let's try to access it's source code.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_exploit_1.png)

We can see above the server respond with base64 encoded source code, so let's decode it using `CyberChef`.

We can see there is a hidden file called `conf.inc.php`. and as we know that the filename ends with `.inc.php`, so let's try to access it using PHP wrapper.

![Challenge_exploit_2](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_exploit_2.png)

We can see above the server respond with base64 encoded source code successfully, so let's decode it using `CyberChef`.

![Challenge_exploit_3](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_exploit_3.png)

Finally we get the flag.

![Challenge_exploit_4](/assets/images/tutorials/Web_Security_Vulnerabilities/LFI/Challenge_2/Challenge_exploit_4.png)

## Resources

[Medium - What is LFI?](https://medium.com/@errorfiathck/what-is-lfi-local-file-inclusion-vulnerability-c9372e25e389)

[BrightSec - LFI](https://brightsec.com/blog/local-file-inclusion-lfi/#:~:text=Local File Inclusion is an,files on a web server)

[OWASP Testing for LFI]([https://owasp.org/www-project-web-security-testing-guide/v42/4-Web_Application_Security_Testing/07-Input_Validation_Testing/11.1-Testing_for_Local_File_Inclusion#:~:text=Local%20file%20inclusion%20(also%20known,procedures%20implemented%20in%20the%20application)](https://owasp.org/www-project-web-security-testing-guide/v42/4-Web_Application_Security_Testing/07-Input_Validation_Testing/11.1-Testing_for_Local_File_Inclusion#:~:text=Local file inclusion (also known,procedures implemented in the application))

[invicti - LFI](https://www.invicti.com/blog/web-security/insecure-direct-object-reference-vulnerabilities-idor/)

[bugcrowd - LFI](https://www.bugcrowd.com/blog/how-to-find-idor-insecure-direct-object-reference-vulnerabilities-for-large-bounty-rewards/)

## Conclusion

In conclusion, LFI are a serious security issue that can allow attackers to take full control of a web server. Developers and Administrators should take steps to prevent these types of attacks by implementing proper security measures and best practices.

Hope you enjoy this guide! Thanks for reading.
