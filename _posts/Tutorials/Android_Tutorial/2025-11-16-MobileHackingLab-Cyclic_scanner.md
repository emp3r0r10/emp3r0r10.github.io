---
title: "Mobile Hacking Lab - Cyclic Scanner Writeup"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Cyclic_Scanner.png
ribbon: green
description: "Hello, in today's writeup, I will walk you through Cyclic Scanner lab from MobileHackingLab."
categories:
  - Tutorials
toc: false
---

![Cyclic_Scanner](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Cyclic_Scanner.png)

Hello, in today's writeup, I will walk you through [Cyclic Scanner](https://www.mobilehackinglab.com/course/lab-cyclic-scanner) lab from [MobileHackingLab](https://www.mobilehackinglab.com/).

In this lab we will explore how to analyze Android Services to find vulnerabilities. We will find a misconfiguration which will allow us to achieve `Remote Code Execution (RCE)` on an Android device.

To understand what services are in more details, you can explore it in [Android Components](https://emp3r0r10.github.io/tutorials/Android_Application_Components/) Tutorial.

So, let's start by downloading the `.apk` and running it in an Android emulator to get an overview of how the application works at runtime.

At first glance, it asks for permission to access all files on the system and then shows a switch button to enable/disable a scanner.

![Premisson_Required (1)](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Premisson_Required (1).png)

![APK_runtime](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/APK_runtime.png)

So, let's move to static analysis and open the APK in `JADX-GUI` to analyze the source code.

![Manifest](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Manifest.png)

We can see above that the application contains:

The following Permissions:

1. `MANAGE_EXTERNAL_STORAGE` permission manages the access to files on the device's external storage.
2. `INTERNT` permission gives your app permission to access the internet both Wi-Fi and mobile data.
3. `FOREGROUND_SERVICE` permission allows the app to run a service in the foreground.

And The Following Activities:

1. `MainActivity`, which is the first screen that appears in the app and is exported to `true`.
2. `ScanService`, a service which is exported as `false`.

Let's move to `MainActivity` and analyze it.

The activity contains many UI implementations and the permission setup process, as we see at runtime.

I scrolled down until I found functions related to the switch button:

![MainActivity](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/MainActivity.png)

**`setupSwitch()` Analysis:**

It is used to check whether the button is checked or not. If checked, it starts the `ScanService` service.

**`setupSwitch$lambda$3()` Analysis:**

- It is used to perform an action based on the state change.
- If `isChecked` is true:
  1. The scanning service is started using `startForegroundService()`.
  2. A Toast message containing the text `Scan service started, your device will be scanned regularly.` is displayed.
- If `isChecked` is false:
  1. A Toast message is displayed indicating that the scanning service cannot be stopped: `Scan service cannot be stopped, this is for your own safety!`.

**`StartService()` Analysis:**

- A Toast message is displayed notifying the user that the scanning process has started: `Scan service started.`
- A foreground service named `ScanService` is started.
- The scanning process is started and runs continuously in the background without interruption.

Let's navigate to `ScanService` service and check it:

![ScanService](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/ScanService.png)

First, we will check `onStartCommand`:

1. Defines a message
2. Check that the intent is not null
3. Define notification to notify user that scan is started

We can navigate to `handleMessage` in the same file and see the following: 

![ScanService_2](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/ScanService_2.png)

**`handleMessage` Analysis:**

1. It checks that the message is not null.
2. It prints `starting file scan...`.
3. It gets the external storage directory `/sdcard/` and checks that it is not null.
4. It checks whether it is a file and can be read.
5. It prints the file path, then starts to scan it using `scanFile()`.

Move to `ScanFile`

![ScanFile](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/ScanFile.png)

**`ScanFile` Analysis**

1. It checks that the file object is not null.
2. It takes the file path and inserts it into the command `toybox sha1sum <File_Path>`.
3. It passes the command variable to the `command()` function directly without validation, which means the command will be executed as a system command. This indicates command injection and can lead to remote code execution.
4. It starts to read it and checks it is not null.
5. The SHA1 hash value is read and compared with the list of known malware.
6. If the hash is not in the list, the file is marked as `SAFE`.

Our approach now is to achieve RCE by injecting a malicious filename; when the scanner reads the path, it will inject it into the command variable and execute it as a system command.

So, we need to create a file with a name that contains a system command: `touch "; $(id) > rce.txt"`

![Exploit_1](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Exploit_1.png)

The final command will be:

`toybox sha1sum; $(id) > rce"`

And while the app scans the system files in `/sdcard/` (external storage), it will see the file path and execute `$(id)` as a system command.

![Exploit_2](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Exploit_2.png)

![Cyclick_Scanner_Solved](/assets/images/tutorials/Android_Tutorial/MHL_Cyclic_Scanner/Cyclick_Scanner_Solved.png)