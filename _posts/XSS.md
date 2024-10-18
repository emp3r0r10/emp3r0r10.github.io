---
title: "Web Security Vulnerabilities - Cross Site Scripting (XSS)"
classes: wide
header:
  teaser: /assets/images/tutorials/Web_Security_Vulnerabilities/XSS/XSS_Cover_1.png
ribbon: red
description: "Cross-Site Scripting, also known as XSS, is a web security vulnerability that allows attackers to inject malicious scripts into web pages viewed by other users. These scripts typically execute in the victim’s browser, leading to security breaches like account compromise, data theft, or website defacement."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/XSS.png" alt="XSS" style="zoom: 100%;" />

## Table of Contents
  - [What is XSS?](#what-is-xss)
  - [Types of XSS](#types-of-xss)
    - [Reflected XSS](#reflected-xss)
    - [Stored XSS](#stored-xss)
    - [DOM-Based XSS](#dom-based-xss)
    - [Blind XSS](#blind-xss)
  - [What is the Impact of XSS?](#what-is-the-impact-of-xss)
  - [How to Find XSS?](#how-to-find-xss)
    - [Manual Testing](#manual-testing)
    - [Automated Testing](#automated-testing)
  - [How to Prevent XSS Attacks?](#how-to-prevent-xss-attacks)
  - [Time to Practice](#time-to-practice)
    - [Challenge #1](#challenge-1)
    - [Challenge #2](#challenge-2)
    - [Challenge #3](#challenge-3)
    - [Challenge #4](#challenge-4)
    - [Challenge #5](#challenge-5)
    - [Challenge #6](#challenge-6)
    - [Challenge #7](#challenge-7)
  - [Resources](#resources)
  - [Conclusion](#conclusion)

## What is XSS?

Cross-Site Scripting, also known as XSS, is a web security vulnerability that allows attackers to inject malicious scripts into web pages viewed by other users. These scripts typically execute in the victim’s browser, leading to security breaches like account compromise, data theft, or website defacement.

XSS occurs when user input is accepted and reflected by the website without proper validation or sanitization. It allows attackers to impersonate victims and perform actions like stealing sensitive data or injecting malicious content into the webpage.

## Types of XSS

- Reflected XSS
- Stored XSS
- DOM-Based XSS
- Blind XSS

#### Reflected XSS

Reflected XSS occurs when an attacker injects malicious code into a request, and the code is reflected in the response in an unsafe manner.

**Example:**

When you search for something on a website, the original request might look like this: 

```
GET /search.html?q=test HTTP/1.1
```

And the response from the website might return your input like this:

```html
<h1>
    Your Seach: test
</h1>
```

If a malicious payload is injected, it might be returned and executed by the website.

#### Stored XSS

Stored XSS, also known as Persistent XSS, occurs when an attacker injects malicious code that is stored on the server of the website. This means the code will be executed on every visit without requiring the submission of the payload again.

**Example**:

Assume you are commenting on a blog post. The request might look like this:

```
POST /post/comment HTTP/1.1 
Host: example.com
Content-Length: 100 

postId=1&comment=It's+amazting&name=test&email=test@example.com
```

The comment will be stored on the server and displayed whenever any user visits the post.

If you inject a payload, it will be executed every time the page loads.

#### DOM-Based XSS

DOM-based XSS arises when malicious code is injected into the Document Object Model (DOM) of a web page. The vulnerability exploits the client-side JavaScript code, typically by manipulating variables, URL fragments, or other elements of the DOM.   

**Example:**

In some applications, when you enter your name, the website might save your input in the DOM like this:

```javascript
<script> 
    var name="test";
</script>
```

If an attacker injects malicious scripts into the variable, they would be executed in the context of the application, leading to XSS.

#### Blind XSS

Blind XSS, also known as Stored Blind XSS, occurs when an attacker injects malicious code into a vulnerable web application, but the injected code does not appear in the immediate response to the attacker. Instead, the injected code is stored on the server and later executed in a different context, which could be in another part of the same application or even in a different application altogether.

## What is the Impact of XSS?

The impact of XSS depends on how and where the XSS vulnerability is exploited within the application.

- If an attacker compromises a user with `admin` privileges, the impact will be critical.
- Attackers can steal session cookies, gaining unauthorized access to the victim's account.
-  If the XSS only causes a reflected alert popup and does not execute harmful scripts, the impact is considered low. It mainly affects user experience and indicates a security flaw.
- XSS allows the attacker to craft convincing phishing attacks by injecting fake content into a legitimate website.

## How to Find XSS?

### Manual Testing

To test if an application is vulnerable to Cross-Site Scripting (XSS), you should test all user inputs on the website and focus on:

1. **What is reflected?**

   Check if the (full user input or part of it) is reflected in the response from the server, either immediately or after some processing.
2. **How is it reflected?**

   Understand how the user input is reflected in the response. Is it reflected in HTML, JavaScript, or even CSS without escaping, what encoding used, or is it sanitized before displaying. 
3. **Where is it reflected?**

   Determine where the user input is reflected in the response. It could be in a URL parameter, a form field, a cookie, or other parts of the request or response. Some areas to focus on:

   - HTML tags
   - JavaScript code (`<script>`)
   - attribute values (like `href`, `src`, `onclick`)
   - URLs
   - Cookies
   - Dynamic content loaded via AJAX or APIs
   - Filename when upload a file
   - Search Bar
   - Login pages
   - Comment section
   - Error messages
4. **How the website deals with your input?**

   Test how the website handles different user inputs, such as special characters, JavaScript code, or HTML tags. See if the website filters or escapes user input to prevent XSS attacks.

5. Use [XSS Cheat sheet](https://portswigger.net/web-security/cross-site-scripting/cheat-sheet) for bypassing filters and WAFs.

> **Tip:** Always test on different browsers as XSS vulnerabilities can behave differently across browsers.

### Automated Testing

While I prefer manual testing for vulnerabilities, some scenarios require automation to make the work easier.

Here are some XSS automation tools you can use:

**XSSer**

- XSSer is an automated XSS vulnerability detection tool. It automatically identifies and exploits XSS vulnerabilities using different payloads.

**XSStrike**

- XSStrike is a powerful XSS detection tool that can automatically generate payloads and test for reflected and stored XSS vulnerabilities.

**XSSHunter**

- XSSHunter  is used for testing `blind XSS` vulnerabilities, where the attacker cannot see the output directly. It helps set up payloads that can trigger when executed on other users' browsers, sending a notification to the attacker.

**Netsparker**

- Netsparker is an automated web application security scanner that helps identify and prioritize security vulnerabilities, including XSS. It provides a user-friendly interface for both beginners and experienced security professionals.

## How to prevent XSS attacks?

Preventing XSS vulnerabilities involves combination of the following measures:

1. **Filtering user input:** Never trust user input, always perform validation and sanitation on input from untrusted sources.

2. **Encoding:** Encode user input before incorporating it into response.

3. **Disable Inline JavaScript Execution:** use the following header to make the browser to block pages if XSS is detected:

   `X-XSS-Protection: 1; mode=block`.

4. **Use `HTTPOnly` ** attribute on cookies to prevent JavaScript access to sensitive session cookies.

5. **content Security Policy (CSP):** Implement a strong Content Security Policy to restrict the types of content that can be executed within your application.

   > **Content security policy (CSP)** is the last line of defense against cross-site scripting. If your XSS prevention fails, you can use CSP to mitigate XSS by restricting what an attacker can do.
   >
   > **CSP** lets you control various things, such as whether external scripts can be loaded and whether inline scripts will be executed. To deploy CSP you need to include an HTTP response header called `Content-Security-Policy` with a value containing your policy.

   > **Same Origin Policy (SOP)**

## Time to practice

Now let's practice what we've learned and see how we can find XSS in real-world scenarios.

We'll practice on 7 labs that I made. Let's start with the first challenge.

### Challenge #1

In the first challenge, we are asked to execute `alert("XSS_Challenge_1")` and are provided with a `GET` parameter called `name`.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_intro.png)

Let's use this parameter normally and type some random text.

We can observe that our text is reflected on the web page.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_try.png)

Next, let's try to inject some special characters to determine how the website handles user input.

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_test_1.png)

As we can see above, the website returns user input in the response without any validation.

So, let's intercept the request with Burp Suite and experiment with it.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_test_2.png)

In the image above, I tried to inject a `<script>` tag, but it seems the website filters it. Let's try injecting other tags like `<img>`. The website reflects this as normal input, which indicates the presence of XSS.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_test_3.png)

Now, let's try to execute XSS using this payload: `<img src=x onerror=alert(10)>`.

We successfully solved the first lab.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_exploit_1.png)

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_exploit_1.1.png)

**`<script>` tag bypass**

As we saw earlier, the website filters out `<script>` tags, but we can bypass this filter using `<scrscriptipt>`.

This technique works if the application removes the `<script>` tag only once.

![Challenge_bypass_filter](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_bypass_filter.png)

As we can see above, it's working, and we can execute `alert(10)`.

![Challenge_exploit_2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_exploit_2.png)

Let's solve the challenge and `alert("XSS_Challenge_1")`.

![Challenge_exploit_2.2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_1\Challenge_exploit_2.2.png)

### Challenge #2

The second Challenge provides us with `img` parameter to enter image name.![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_2\Challenge_intro.png)

Let's type an existing image name and determine how it works.

![](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_2\Challenge_try.png)

As we see above, it just displays an image., so let's look at source code to identify where user input is inserted.

We can see image name is inserted in `src` attribute. 

![Challeneg_source_code](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_2\Challeneg_source_code.png)

So to execute `XSS`, we need to escape `src` attribute. Let's do so using: `test.jpg' onmouseover='alert(10)`.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_2\Challenge_exploit_1.png)

As the payload was accepted, let's solve the second challenge and alert `XSS_Challenge_2`.

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_2\Challenge_exploit_1.1.png)

The second challenge is done. Let's move to the next.

### Challenge #3

The third challenge shows us the `Guest Book` page with `Name` and `Message` parameters.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_intro.png)

Let's type random text in both parameters, and see how the website handles it.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_try.png)

![Challenge_Source_code](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_Source_code.png)

As we see above it is store our input and display it in the web page. so let's intercept the request to burp and try to exploit it.

![Challenge_exploit_fail](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_exploit_fail.png)

If we inject some special characters and `<script>` tag, we can see that the website store it without validation or filteration.

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_exploit_1.1.png)

Now let's exploit it and execute `alert(10)`.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_exploit_1.png)

![Challenge_exploit_1.2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_3\Challenge_exploit_1.2.png)

### Challenge #4

This challenge from `Idek` CTF is special as it has a new technique that I'm not used before. Let's look at it.

![ ](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_4\Challenge_intro.png)

Source Code:

![image-20240829004938090](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_4\Sourec_Code.png)

We can see above, the page takes `GET` parameter called `name` and applies some filters to it.

If the value of `name` parameter contains `\n`, `\r`, `\t`, `/` and ` " "`, then it replace it with empty string.

Let's see these filters in the page.

**First try:** `<script></script>`

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_4\Challenge_test_1.png)

**Second try:** `<img src=x onerror=alert(10)>`

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_4\Challenge_test_2.png)

We can see above how filters will prevent our payload.

If we search in google for `XSS without spaces`, we will find this payload: `<img/src/onerror=alert(1)>`.

But this will not work because it contains `/`. So, let's do another simple Google search: `XSS with no spaces and slashes`.

We can found this [technique](https://security.stackexchange.com/questions/47684/what-is-a-good-xss-vector-without-forward-slashes-and-spaces), actually I'm not using this before, so you can search for it. 

![proof](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_8\proof.png)

Now, If we try the above payload, it will work successfully.

![Challenge_exploit](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_4\Challenge_exploit.png)

### Challenge #5

In the fifth challenge, We can see a search page. Let's try to break it.

As we learned above the first step is to use the functionality as normal user.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_5\Challenge_try.png)

We can see above, the page just reflect user input into the page. So let's test for special characters and we can see that it doesn't filter the output which is a sign to XSS.

![Challenge_test](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_5\Challenge_test.png)

Let's try to break it and inject XSS payload: `<script>alert(10)</script>`

![Challenge_exploit](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_5\Challenge_exploit.png)

### Challenge #6

In the sixth challenge, the website goal is to print the name in the web page with special font and provides us with `name` parameter.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_intro.png)

So, let's type random text like `HackerOne` and we can see that the name is reflected in the web page.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_try.png)

If we inject some special characters, the website reflected it like the first challenge.

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_1.png)

If we try to exploit it again using `<script>`, we will see that the website filters it.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_2.png)

Also if we try to bypass `<script>` tag using `<scrscriptipt>`, it won't work.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_3.png)

So, let's move to another tags like `<img>` or `<svg>` and both of them worked.

![Challenge_test_4](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_4.png)

![Challenge_test_5](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_5.png)

Now let's exploit `XSS` using `svg` tag this time.

unfortunately the website shows us: `Hack Detected`.

![Challenge_test_6](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_test_6.png)

It may be filter some event handlers, so let's try to use another one like: `onmouseover` and it worked.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_exploit_1.png)

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_6\Challenge_exploit_1.1.png)

### Challenge #7

This challenge like the previous challenge which also print the name in the web page with special font and provides us with `name` parameter. so let's check it and see what is different.

![Challenge_intro](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_intro.png)

Again let's inject some random text and special characters.

![Challenge_try](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_try.png)

![Challenge_test_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_test_1.png)

We can see above the website reflects user input without validation on special characters.

So, let's inject `<script>` tag, we can see the website accepts it without validation.

![Challenge_test_2](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_test_2.png)

Let's try to execute `alert(10)` to solve the challenge.

![Challenge_test_3](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_test_3.png)

Unfortunately, we can't use `alert` as it filtered. 

We can use another functions like `confirm` and `prompt`, but all of them not working.

![Challenge_test_4](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_test_4.png)

![Challenge_test_5](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_test_5.png)

But we can bypass this filter and execute `alert()` using `eval()` and separate `alert` into 2 pieces.

It is worked and we solve the lab.

> The eval() function evaluates JavaScript code represented as a string and returns its completion value.

![Challenge_exploit_1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_exploit_1.png)

![Challenge_exploit_1.1](/assets/images/tutorials/Web_Security_Vulnerabilities/XSS/Challenge_7\Challenge_exploit_1.1.png)

## Resources

- PortSwigger - [Cross-site Scripting (XSS)](https://portswigger.net/web-security/cross-site-scripting)
- Intigriti -  [Cross-site Scripting (XSS)](https://www.intigriti.com/hackademy/cross-site-scripting-xss)
- OWASP - [XSS (Cross Site Scripting)](https://owasp.org/www-community/attacks/xss/)
- Tryhackme - [Cross-site Scripting (XSS)](https://tryhackme.com/r/room/axss)
- XSS Cheat Sheet by PortSwigger - [Cross-site Scripting (XSS) Cheat Sheet](https://portswigger.net/web-security/cross-site-scripting/cheat-sheet)
- OWASP WebGoat
- XSS Game by Google
- Bug Bounty Bootcamp book

## Conclusion

XSS vulnerabilities, although common, can have severe consequences for applications and users alike. Understanding how XSS works and the different ways it can be exploited is essential for both developers and security testers.

With the combination of proper input validation, output encoding, and a robust Content Security Policy, XSS vulnerabilities can be effectively mitigated. Keep practicing with real-world challenges to improve your understanding and detection of XSS vulnerabilities!

Thank for reading.