---
title: "Mobile Hacking Lab - GuessMe Writeup"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/MHL_GuessMe/GussMe.png
ribbon: green
description: "Hello, in today's writeup, I will walk you through GuessMe lab from MobileHackingLab."
categories:
  - Tutorials
toc: false
---

![GussMe](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\GussMe.png)

Hello, in today's writeup, I will walk you through [Guess Me](https://www.mobilehackinglab.com/course/lab-guess-me) lab from [MobileHackingLab](https://www.mobilehackinglab.com/).

In this lab we will abuse `Deep Link` behavior in android application to gain Remote Code Execution (RCE).

To understand what a `Webview` and `Deep Link` are, you can see this [tutorial](https://emp3r0r10.github.io/tutorials/SQL-Injection/).

Let's open the application through android emulator.

We can see it contains a simple game to guess a number between 1 and 100, but if we look closely, we can see a info button in the bottom right.

![App_Overview](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\App_Overview.png)

If we click on the info button, we will navigate to the following page.

![App_Overview](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\App_Overview_2.png)

Still not familiar, so let's analyze the app source code using `JADX-GUI`.

We can see that it contains two activities: `MainActivity` and `WebviewActivity` exported to `true`. It also contains an `INTERNET` permission to access external links.

![Manifest](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Manifest.png)

I checked `MainActivity` and found it not interesting as it contains some UI and logic for the guessing game, so let's navigate to `WebviewActivity`.

![Webview_1](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Webview_1.png)

**`WebviewActivity` Analysis:**

1. Creates a `webView` and checks it is not null.
2. Defines `setJavaScriptEnabled()` which enables the execution of JavaScript code inside the WebView.
3. Creates `webView3`, checks that it is not null, then passes the `webView` to it.
4. Sets `addJavascriptInerface()` on `webView3`, which allows web JavaScript code to call Java objects (Android code).
5. Calls `LoadAssetIndex()`.

![Webview_3](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Webview_3.png)

**`LoadAssetIndex()` Analysis**

1. Checks `webView` is not null.

2. Loads the `index.html` file located in `assets/`:

   ![index.html](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\index.html.png)

Call `handleDeepLink()`

![Webview_2](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Webview_2.png)

**`handleDeepLink()` Analysis**

1. Sends the intent and handles the deep link inside it.

1. Checks that the URI is not null.
2. Passes the URI to `isValidDeepLink()`.
   1. Checks that the URI starts with the `mhl` or `https` scheme and the `mobilehackinglab` host.
   2. Extracts the `url` parameter and checks it is not null.
   3. Checks that the value of the `url` parameter ends with `mobilehackinglab.com`.
3. If the URI is valid, it is passed to the `loadDeepLink(uri)` method, which:
   1. Takes the value of the `url` parameter and loads it through `webView.loadUrl()`.

So, the app flow is the following:

![Exploit_Flow](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Exploit_Flow.png)

Let's start `WebviewActivity` and send a deep link that bypasses the filter using `adb`:

`adb shell am start -n com.mobilehackinglab.guessme/.WebviewActivity -d "https://mobilehackinglab/?url=https://google.com/?test=mobilehackinglab.com"`

![google](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\google.png)

In the above command, we bypassed the `url` parameter check by sending a Google link with `mobilehackinglab.com` at the end, as it technically still redirects to `google.com`.

We want to get RCE from this vulnerability, but how?

If you remember, there is `addJavascriptInerface`, which allows us to call functions through JavaScript code, as we saw above in the `index.html` file.

![Webview_4](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\Webview_4.png)

> The `JavascriptInterface` is a bridge between your Android app (Java/Kotlin code) and JavaScript code that runs inside a WebView. It allows JavaScript code inside a web page (loaded in a WebView) to call methods from your Android app — for example, to access device features like the camera, GPS, or local storage.

**`MyJavascriptInterface()` Analysis:**

1. The `@JavascriptInterface` annotation allows JavaScript to call this method.
2. `loadWebsite()` Analysis:
   1.  Takes a URL and loads it.

3. `getTime()` Analysis:

   ![MyJavaScriptInterface](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\MyJavaScriptInterface.png)

   1. Takes a `Time` parameter.
   2. Checks that the parameter is not null.
   3. Passes the parameter to `exec()` and executes it on a shell, which indicates RCE.
   4. Initializes a `BufferedReader` to read the output of the executed command and a `StringBuilder` to accumulate the output.
   5. Reads each line of the command’s output, appends each line to **`output`**.
   6. After reading all lines, it closes the reader, trims the output to remove whitespaces, and returns it.

So, we can create a malicious HTML file like `index.html` and control the `Time` parameter to get RCE. We can host the following script on a webhook:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<h2>Getting RCE</h2>
<p id="result"></p>

<script>

    // Fetch and display the time when the page loads
    var result = AndroidBridge.getTime("id");
    var fullMessage = "I triggered RCE: \n" + result;
    document.getElementById('result').innerText = fullMessage;

</script>

</body>
</html>
```

If we send the deep link again, we get RCE successfully.

![lab_solved](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\lab_solved.png)

![GuessMe_Solved](/assets/images/tutorials/Android_Tutorial/MHL_GuessMe\GuessMe_Solved.png)