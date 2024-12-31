---
title: "Web Security Vulnerabilities - Server Side Template Injection (SSTI)"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/SSTI-Cover.png
ribbon: red
description: "Server-Side Template Injection, also known as SSTI, is a web security vulnerability that allows an attacker to inject malicious code into a template. This code is written using the native template syntax, which is then executed on the server."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/SSTI.png" alt="SSTI" style="zoom:100%;" />

## Table of Contents
  - [What is Server Side Template Injection?](#what-is-server-side-template-injection)
  - [What is the Impact of SSTI?](#what-is-the-impact-of-ssti)
  - [How to Find SSTI?](#how-to-find-ssti)
    - [Detect](#detect)
    - [Identify](#identify)
    - [Exploit](#exploit)
  - [How to Prevent SSTI?](#how-to-prevent-ssti)
  - [Time to Practice](#time-to-practice)
  - [Resources](#resources)
  - [Conclusion](#conclusion)
  - [Final Words](#final-words)

## What is Server Side Template Injection?

Server-Side Template Injection, also known as SSTI, is a web security vulnerability that allows an attacker to inject malicious code into a template. This code is written using the native template syntax, which is then executed on the server.

SSTI vulnerabilities arise when an application includes user input directly in the template without proper validation, instead of passing it as data to be safely rendered on the server.

> Template engines are designed to generate web pages by combining fixed templates with volatile data. Templates also enable fast rendering of the server-side data that needs to be passed to the application. The template engine replaces the variables in a template file with actual values and displays these values to the client.
>
> For example: jinja and twig

**Example:**

Suppose we have an application that allows a user to log in and displays their username using the `Jinja2` template engine in Python.

The `HTML` code looks like:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Server-Side Template Injection Example</title>
</head>
<body>
    <h1>Welcome, {{ name }}!</h1>
</body>
</html>
```

 The `Flask` route that renders this template looks like:

```python
from flask import Flask, render_template, request

app = Flask(__name__)

@app.route('/')
def index():
    name = request.args.get('name', 'Guest')
    return render_template('index.html', name=name)
```

Here, the `name` variable is taken from the query string parameter `name` and rendered directly in the template without sanitization, allowing attackers to inject template expressions. If an attacker submits a malicious input like `{{ 7 * 7 }}` as the `name` parameter in the URL, like:

```
GET /index.html/?name={{ 7 * 7 }}
HOST: example.com
```

The template engine will evaluate this expression as `49` and render the page with `Welcome, 49!`, which indicates an SSTI vulnerability.

## What is the impact of SSTI?

The impact of an SSTI vulnerability depends on the template engine and how the website uses it. These vulnerabilities often have a critical impact, as they can result in Remote Code Execution (RCE). Even without code execution, the attacker may be able to read sensitive data on the server.

## How to find SSTI?

There are three main steps to finding an SSTI vulnerability:

1. Detect
2. Identify
3. Exploit

### Detect

The first step in exploiting an SSTI vulnerability is detecting whether an application is vulnerable. The simplest way to find an SSTI vulnerability is by fuzzing the template with special characters commonly used in template expressions, such as `${{<%[%'"}}%`. If the server raises an exception or returns an error, this might indicate the presence of a vulnerability.

### Identify

The second step is identifying what is the template the application uses. Submitting an invalid syntax might reveal the template engine due to an error message returned by the server, Alternatively, you can inject arbitrary mathematical operations using syntax from different template engines and observe whether they are successfully evaluated.

The image below represents a decision tree to help in this process:

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/SSTI_Identify.png" alt="SSTI_Identify" style="zoom:50%;" />

> It is important to note that sometimes one payload can successfully return different results in two or more templates. Therefore, it is essential not to jump to conclusions based on a single successful response.

### Exploit

After detecting the vulnerability and identifying the template engine, it's time to exploit it. The best approach is to read the documentation of the template engine to understand its basic syntax and built-in methods, which will help in exploiting the server.

## How to prevent SSTI?

The most effective way to prevent SSTI attacks is not to include user input directly in the template engine without validation or sanitization. However, if you must include user input, you can use logic-less template engines like `Mustache`, which completely separate code interpretation from visual representation.

## Time to practice

Now let's practice and understand the vulnerability in simulating real-scenarios. 

At first glance, we can see that the website is missing the `name` parameter. Let's send a request and include this parameter.

![Challenge_Overview](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Overview.png)

If we inject random text into the `name` parameter, we can see it reflected on the web page.

![](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Check.png)

Let's inject some special characters to observe how the website processes them.

![Screenshot_2024-04-26_09_19_29](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Screenshot_2024-04-26_09_19_29.png)

As we can see in the image above, the website reflects special characters without validation. So we can try testing for XSS.

![Challenge_Xss](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Xss.png)

And yes, we successfully triggered XSS😁. But wait, is the website vulnerable only to XSS? Why not try testing for other vulnerabilities?

Let's try testing for SSTI. There are many payloads to try, so let's first try `{{7*7}}`. If it's vulnerable, it should return a result.

![Challenge_Test](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Test.png)

And we can see the payload rendered as `49`, indicating to an SSTI vulnerability.

> You can get payloads from [HackTricks ](https://book.hacktricks.xyz/pentesting-web/ssti-server-side-template-injection)or [PayloadAllTheThings](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/Server%20Side%20Template%20Injection)

Now that we know there's an SSTI vulnerability and the template engine is `Jinja2` (a Python template engine), let's proceed.

Everything in Python is an object, and each object has a class from which it is instantiated.

First, we need to call `''.__class__.__base__`:

- `''`: This is an empty string literal.
- `.__class__`: This is a special attribute that returns the class of an object. So, `''.__class__` returns the class of the empty string, which is the `str` class in this case.
- `.__base__`: This is another special attribute that returns the base class of a class. Since `str` is a built-in type in Python, its base class is the `object` class. Therefore, `''.__class__.__base__` returns the `object` class, which is the base class of the `str` class.

![Challenge_Class](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Class.png)

Next, we need to call the `subclasses()` method to return a list of subclasses of the `str` class.

![Challenge_Subclasses](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Subclasses.png)

We have many `subclasses`. What we need is to choose a subclass that imports the `sys` module to execute commands. There are many subclasses; we will use the `IncrementalEncoder` class.

![Challenge_Encoder_class](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Encoder_class.png)

Let's access the `IncrementalEncoder` class using `''.__class__.__base__.__subclasses__()[127].__init__`.

![Challenge_Encoder_init](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Encoder_init.png)

Now that we're in the class file, let's access the modules in the class, and we'll find `sys`.

![Challenge_Encoder_globals](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Encoder_globals.png)

Once we're in the `sys` module, we can access the `os` module and use it to execute system commands.

![Challenge_Sys](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_Sys.png)

![Challenge_OS](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_OS.png)

The final payload will be:

`{''.__class__.__base__.__subclasses__()[127].__init__.__globals__['sys'].modules['os'].popen('ls').read()}`.

> Python **Popen** is a class within the subprocess module that allows us to spawn new processes, connect to their input/output/error pipes, and obtain their return codes. It enables Python programs to run shell commands, system commands, and other external processes directly from within the script.

`read()` reads the output and returns it as a string.

> The Python subprocess module is a tool that allows you to run other programs or commands from your Python code. It can be used to open new programs, send them data and get results back.

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_directories.png" alt="Challenge_directories" style="zoom:200%;" />

Finally, let's read `flag.txt`.

![Challenge_flag](/assets/images/tutorials/Web_Security_Vulnerabilities/SSTI/Challenge/Challenge_flag.png)

> The final tip is not always stop at just XSS.

## Resources

- [PortSwigger - SSTI](https://portswigger.net/web-security/server-side-template-injection)
- [OWASP - Testing for SSTI](https://owasp.org/www-project-web-security-testing-guide/v41/4-Web_Application_Security_Testing/07-Input_Validation_Testing/18-Testing_for_Server_Side_Template_Injection)

- [HackTricks - SSTI](https://book.hacktricks.xyz/pentesting-web/ssti-server-side-template-injection)

- [PayloadsAllTheThings - SSTI](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/Server Side Template Injection)

## Conclusion

In today's tutorial, we talked about SSTI and how to find it with its impact and prevention. We also practice on a detailed lab (designed by me). Hope you enjoy reading this guide on Server-Side Template Injection (SSTI). I hope you found the information helpful and are better equipped to identify and exploit SSTI vulnerabilities.

## Final Words

This is the last part of Web Security Vulnerabilities tutorial, hope you learn something from this tutorial.

If you found this tutorial helpful or not, you can follow me and give your feedback on [twitter](https://x.com/emp3r0r10) to improve myself in the future tutorials.

Keep Going. Thanks for reading.
