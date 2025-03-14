---
title: "Android Architecture"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/Android_Architecture/Android_Architecture_Cover.jpeg
ribbon: red
description: "System Apps or Application Layer is a top layer of android architecture. It contains all apps on the device. These applications rely on the services and frameworks (**next layers**)."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Android_Tutorial/Android_Architecture/Android_Architecture.png" alt="Android_Architecture" style="zoom: 100%;" />

## System Apps (Application Layer)

System Apps or Application Layer is a top layer of android architecture. It contains all apps on the device. These applications rely on the services and frameworks (**next layers**)

### Types of Applications

1. **Pre-Installed Applications**
    1. Made by the manufacturer of the device.
    2. Essential for basic operations. (e.g. contacts, messages, camera, themes, gallery, .etc)
    3. Cannot be uninstalled by the user (without rooting).
    4. **Location:** `/system/app` or `/system/priv-app`
2. **Third Party (User-Installed) Applications**
    1. Installed by user from applications store (e.g. Google Play Store, APK files)
    2. (e.g. whatsapp, facebook, twitter, .etc)
    3. **Location: `/data/app`**

## Java API Framework

Application Framework is providing important and ready-to-use APIs and classes which used is essential to create an android applications. It includes different types of services activity manager, notification manager, view system, package manager etc. which are helpful for the development of our application according to the prerequisite. It acts like a **bridge between** application layer and android run time. It facilitates communication between hardware components and applications such as camera, GPS, Bluetooth.

### Components of the Application Framework

1. **Activity Manager**
   
    Manages the lifecycle of Android apps and provides APIs that allow developers to start, stop, and manage the different activities within an app.
    
2. **Window Manager**
   
    Manages the display of the app’s user interface and provides APIs that allow developers to create, update, and manipulate the app’s user interface.
    
3. **Package Manager**
   
    Manages the installation, upgrade, and removal of Android apps and provides APIs that allow developers to query information about installed apps.
    
4. **Content Providers**
   
    Provides a standardized interface for accessing data across different apps and allows developers to share data between apps.
    
5. **Location Manager**
   
    Manages location-based services which allows apps to access GPS or network-based location data.
    
6. **Notification Manager**
   
    Enables apps to create and display notifications in the status bar or as pop-ups.
    
7. **Resource Manager**
   
    Provides access to non-code resources such as strings, layouts, images, and themes.
    

> The Java API layer specifically refers to the set of APIs that are written in the Java programming language and are used to interact with the Android platform.
>

## Android Runtime

Android Runtime layer is when the application is executed. The runtime provides the environment where all the code runs and interacts with system resources.

### Execution Process

1. The developer writes code in Java/Kotlin, the source code is converted into **Java bytecode** (`.class` format) using the Java compiler (`javac`) or Kotlin compiler (`kotlinc`).
2. The Java Bytecode is then converted into Dalvik Executable (`.dex`) files by the `dx` tool (or `d8`/`r8` tools in modern Android builds). This step is necessary because Android does not use the standard Java Virtual Machine (JVM) but instead uses its own runtime environment (DVM or ART).
3. **Execution on DVM or ART**
   
    The Dalvik Executable file (`.dex`) then will be executed by **DVM** or **ART.**
    
    1. **Dalvik Virtual Machine (DVM)**:
       
        Dalvik Virtual Machine (DVM) is the custom program introduced for Android apps. It takes the Dalvik Executable file and run it
        
        > Dalvik Virtual Machine (DVM) were used in older versions of android (before **Android 5.0** **Lollipop**)
        >
        
        > JIT (Just-In-Time Compilation) is a compilation process in which code is translated from an intermediate representation or a higher-level language (e.g., JavaScript or Java bytecode) into machine code at runtime, rather than prior to execution.
        >
    2. **Android Runtime (ART)**:
       Android Runtime (ART) is created specifically for the Android project. It executes the Dalvik executable (`.dex`) format and DEX bytecode specification.
       
        > Android Runtime (ART) started from **Android 5.0** **Lollipop**).
        >
       
        > **ahead-of-time compilation** (**AOT compilation**) is the act of [compiling](https://en.wikipedia.org/wiki/Compiler) an (often) higher-level [programming language](https://en.wikipedia.org/wiki/Programming_language) into an (often) lower-level language before execution of a program, usually at build-time, to reduce the amount of work needed to be performed at [run time](https://en.wikipedia.org/wiki/Run_time_(program_lifecycle_phase)).
        >  At install time, ART compiles apps using the on-device **`dex2oat`** tool. 
        >  AOT compilation improves the app's startup time and runtime performance, reducing the need for runtime interpretation.
        >

## **Platform Libraries**

Platform Libraries are native `c/c++` libraries designed to provide low-level system functionalities. It includes everything needed to build an app, including source code, resource files, and an Android manifest. 

Platform Libraries include general-purpose libraries the provide core functionalities for various operations:

- **WebKit**: A **web rendering engine** used for displaying web content. WebKit is used by Android’s **WebView** component to display webpages within apps.
- **OpenSSL**: A widely-used **cryptographic library** that provides essential encryption and decryption algorithms. OpenSSL is used for implementing secure communications, such as SSL/TLS protocols, ensuring that data is securely transmitted over networks.
- **libc**: The standard C library, which provides basic functionality like memory management, file I/O, and string manipulation.
- **SQLite**: An embedded database engine used in Android apps to handle local data storage.
- **Zlib**: A library for data compression and decompression, often used in Android to handle file and data compression tasks.
- **libexif:** A JPEG EXIF processing library
- l**ibexpat:** The Expat XML parser
- **libaudioalsa/libtinyalsa:** The ALSA audio library
- **libbluetooth:** The BlueZ Linux Bluetooth library
- **libdbus:** The D-Bus IPC library

### Shared Libraries

Platform Libraries are compiled into shared libraries that may be dynamically loaded during Android runtime. The purpose of compiling is to provide reusable functionality across different applications or system processes.

Shared object files can be dynamically loaded into an application at runtime using mechanisms such as the **Java Native Interface (JNI)**, which allows Java code to interact with native code written in languages like C or C++.

**Location of Shared Libraries:** `/lib/` directory.

> You can create a shared library (`.so` file) using **C/C++ with the Android NDK (Native Development Kit)**.
>

## Linux Kernel

Android is based on Linux Operating System but it has several modifications to optimize mobile devices. It can take commands like any Linux device such as enhanced power management, security features, and support for specific hardware components. Linux Kernel is the main part of android architecture that exists at the root of android architecture. It’s responsible for memory management, security, process management, power, .etc. It manages all the drivers needed during the runtime of an Android device, such as camera drivers, display drivers, audio drivers, Bluetooth drivers, and memory drivers, among others.

 The Linux Kernel interacts directly with the hardware and provides an **abstraction layer** that allows higher-level software, such as Android applications, to access hardware components without needing to deal with low-level hardware details. This abstraction makes the hardware resources available through standardized interfaces, so developers don't need to write device-specific code.

### Responsibilities

1. **Device Drive**
   
    The kernel includes **device drivers** for various hardware components, such as:
    
    - **Bluetooth**: Allows applications to communicate with Bluetooth devices for tasks like file transfers, streaming audio, or pairing with other devices.
    - **Camera**: Handles the integration with the device's camera hardware, enabling applications to capture images and videos.
    - **Audio**: Manages audio input and output, allowing applications to play sound, record audio, or interact with other audio devices like Bluetooth speakers and headphones.
    - **Sensors**: Coordinates the data from various sensors such as accelerometers, gyroscopes, and proximity sensors.
    - **Touchscreen**: Controls the input from the touchscreen, processing gestures and touch events.
2. **Memory and Process Management:**
The Linux kernel ensures that memory and processing resources are allocated properly between different applications, services, and hardware tasks. It uses sophisticated techniques like virtual memory, process scheduling, and resource isolation to ensure system stability and responsiveness.
3. **Security and Access Control:** 
   
    Android’s Linux kernel plays a crucial role in system security. It implements mechanisms like:
    
    - **SELinux (Security-Enhanced Linux)**: Provides mandatory access control (MAC) to restrict the actions of processes and limit their access to system resources.
    - **App Sandbox**: Every Android application runs in its own isolated process, and the kernel ensures that these applications cannot interfere with each other or access sensitive data from other apps or system processes.
4. **Power Management:**
The kernel also manages the power consumption of the device, implementing policies that help extend battery life by controlling hardware components and optimizing system performance.