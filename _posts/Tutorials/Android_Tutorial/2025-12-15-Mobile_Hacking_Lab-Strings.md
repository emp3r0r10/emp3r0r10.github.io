---
title: "Mobile Hacking Lab - Secure Notes Writeup"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/MHL_Strings/Strings.png
ribbon: green
description: "Hello, in today's writeup, I will walk you through Strings lab from MobileHackingLab."
categories:
  - Tutorials
toc: false
---


![Strings](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Strings.png)

Hello, in today's writeup, I will walk you through [Strings](https://www.mobilehackinglab.com/course/lab-strings) lab [MobileHackingLab](https://www.mobilehackinglab.com/).

The lab provided us with APK file and our goal is to get the flag. So, let's get started.

First, let's run the app in an Android emulator to examine its runtime behavior and get an overview.

We can see it is a blank page with `Hello from C++` at center.

![App_Overview](/assets/images/tutorials/Android_Tutorial/MHL_Strings/App_Overview.png)

## Static Analysis

As usual, let's start static analysis and examine the source code using `JADX-GUI`.

We can see in `AndroidManifest.xml` there are two exported activities: `MainActivity` and `Activity2` with `intent-filter`.

![Manifest](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Manifest.png)

Let's start with `MainActivity`.

![MainActivity_1](/assets/images/tutorials/Android_Tutorial/MHL_Strings/MainActivity_1.png)

![MainActivity_2](/assets/images/tutorials/Android_Tutorial/MHL_Strings/MainActivity_2.png)

**`MainActivity` Analysis:**

1. Allows Java to call native functions through JNI. As it's `native`, so the code can be found in a shared library (`.so`) not in java code.

   ```java
   public final native String stringFromJNI();
   ```

2. Loads the native library named **libchallenge.so** into memory.

   ```java
   static {
   System.loadLibrary("challenge");
   }
   ```

3. `KLOW()` Analysis:

   1. Defines a function called `KLOW()` that creates a shared preferences file called `DAD4`.
   2. Creates an editor object to edit shared preferences and checks it is not null.
   3. Formats the date and retreive the current data in `dd/MM/yyyy` format and save it in `cu_d` object.
   4. Saves the date and `UUU0133` key into shared preferences.
   5. Applies the changes.

   ```java
   public final void KLOW() {
       SharedPreferences sharedPreferences = getSharedPreferences("DAD4", 0);
       SharedPreferences.Editor editor = sharedPreferences.edit();
       Intrinsics.checkNotNullExpressionValue(editor, "edit(...)");
       SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
       String cu_d = sdf.format(new Date());
       editor.putString("UUU0133", cu_d);
       editor.apply();
   }
   ```

Let's navigate to `Activity2`.

![Activity2_1](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Activity2_1.png)

![Activity2_2](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Activity2_2.png)

**`Activity2` Analysis:**

1. Retrieves `DAD4` shared preferences.

   ```java
   SharedPreferences sharedPreferences = getSharedPreferences("DAD4", 0);
   ```

2. Fetches the `UUU0133` key's value. If no value found, returns `null`

   ```java
   String u_1 = sharedPreferences.getString("UUU0133", null);
   ```

3. Checks if the action of the incoming intent that starts `Activity2` is `android.intent.action.VIEW`.

   ```java
   boolean isActionView = Intrinsics.areEqual(getIntent().getAction(), "android.intent.action.VIEW");
   ```

4. Checks if the value `u_1` object (the returned value from shared preferences) is equal to the returned value from `cd()` method.

   ```java
   boolean isU1Matching = Intrinsics.areEqual(u_1, cd());
   ```

5. If the `isActionView` and `isU1Matching` are valid, it starts to check the URI of the intent.

6. Checks that the URI is not null and the schema is `mhl` and the host is `labs`.

7. Define a `base64Value` object that contains a last path segment of the URI and decode it.

8.  If the `decodedValue` is not null, it uses `decrypt()` method to decrypt `bqGrDKdQ8zo26HflRsGvVA==` using AES/CBC/PKCS5Padding with a specified `IV` and secret key.

9.  If the decrypted value equals `ds` (object contains the bas64 decoded value), it loads the `flag` native library and calls a native method **`getflag()`** to retrieve a flag.

   ```java
   if (isActionView && isU1Matching) {
   Uri uri = getIntent().getData();
   if (uri != null && Intrinsics.areEqual(uri.getScheme(), "mhl") && Intrinsics.areEqual(uri.getHost(), "labs")) {
       String base64Value = uri.getLastPathSegment();
       byte[] decodedValue = Base64.decode(base64Value, 0);
       if (decodedValue != null) {
           String ds = new String(decodedValue, Charsets.UTF_8);
           byte[] bytes = "your_secret_key_1234567890123456".getBytes(Charsets.UTF_8);
           Intrinsics.checkNotNullExpressionValue(bytes, "this as java.lang.String).getBytes(charset)");
           String str = decrypt("AES/CBC/PKCS5Padding", "bqGrDKdQ8zo26HflRsGvVA==", new SecretKeySpec(bytes, "AES"));
           if (str.equals(ds)) {
               System.loadLibrary("flag");
               String s = getflag();
               Toast.makeText(getApplicationContext(), s, 1).show();
               return;
           }
           finishAffinity();
           finish();
           System.exit(0);
           return;
       }
       finishAffinity();
       finish();
       System.exit(0);
       return;
   }
   finishAffinity();
   finish();
   System.exit(0);
   return;
   }
   finishAffinity();
   finish();
   System.exit(0);
   ```

10. Validates the `decrypt()` method parameters and check they are not null.

11. Gets the `IV` value from `fixedIV` in `Activity2Kt` and stores it in `bytes` object.

12. Decodes the encrypted string from base64, then decodes the result from AES encryption using `key` (secret key) and `IV` .

    ```java
    public final String decrypt(String algorithm, String cipherText, SecretKeySpec key) {
        Intrinsics.checkNotNullParameter(algorithm, "algorithm");
        Intrinsics.checkNotNullParameter(cipherText, "cipherText");
        Intrinsics.checkNotNullParameter(key, "key");
        Cipher cipher = Cipher.getInstance(algorithm);
        try {
            byte[] bytes = Activity2Kt.fixedIV.getBytes(Charsets.UTF_8);
            Intrinsics.checkNotNullExpressionValue(bytes, "this as java.lang.String).getBytes(charset)");
            IvParameterSpec ivSpec = new IvParameterSpec(bytes);
            cipher.init(2, key, ivSpec);
            byte[] decodedCipherText = Base64.decode(cipherText, 0);
            byte[] decrypted = cipher.doFinal(decodedCipherText);
            Intrinsics.checkNotNull(decrypted);
            return new String(decrypted, Charsets.UTF_8);
        } catch (Exception e) {
            throw new RuntimeException("Decryption failed", e);
        }
    }
    ```

    ![IV_Fixed](/assets/images/tutorials/Android_Tutorial/MHL_Strings/IV_Fixed.png)

13. Gets the current data using `Date()` method and convert its format to `dd/MM/yyyy` n stores it into a `cu_d` object in `Activity2Kt` then into a string called `str`.

14. If the string is null, it will throws `UninitializedPropertyAccessException` Exception. otherwise, returns the `str` values.  

    ```java
    private final String cd() {
        String str;
        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        String format = sdf.format(new Date());
        Intrinsics.checkNotNullExpressionValue(format, "format(...)");
        Activity2Kt.cu_d = format;
        str = Activity2Kt.cu_d;
        if (str == null) {
            Intrinsics.throwUninitializedPropertyAccessException("cu_d");
            return null;
        }
        return str;
    }
    ```

## Exploitation

Here is the approach to get flag:

![App_Flow](/assets/images/tutorials/Android_Tutorial/MHL_Strings/App_Flow.png)

So, we need to invoke `KLOW()` method using Frida to generate the required `SharedPreferences` file (`DAD4`) and when the condition happens, it will match the value of `UUU0133` key and the result of `cd()` method. Then it will start to validate the URI.

![Frida_Script](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Frida_Script.png)

Let's start `frida-server` and run the script.

![Frida_Server](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Frida_Server.png)

![Frida_Client](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Frida_Client.png)

Now, we need to send a valid URI. Let's do this.

We can obtain the base64 encoded value via decrypting `bqGrDKdQ8zo26HflRsGvVA==` and then base64 encode the result again.

![Decrypt_Key](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Decrypt_Key.png)

![Base64_Encode](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Base64_Encode.png)

We can start an activity using the following `adb` command now:

`adb shell am start  -n com.mobilehackinglab.challenge/.Activity2 -a android.intent.action.VIEW -d "mhl://labs/bWhsX3NlY3JldF8xMzM"`

![adb_activity2](/assets/images/tutorials/Android_Tutorial/MHL_Strings/adb_activity2.png)

We got `Success` message, but no flag returned.

![Sucess_message](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Sucess_message.png)

If we back to the lab page, we can see a small hint which tells us that the flag is in memory. 

![Flag_Hint](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Flag_Hint.png)

So, we can scan the memory using three ways:

**Way #1: Using Objection**

1. Detect the Process ID of lab package name.

   ![frida-ps](/assets/images/tutorials/Android_Tutorial/MHL_Strings/frida-ps.png)

2. Scan the memory using objection memory module and search for `MHL{` (the flag format).

   ![Flag](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Flag.png)

**Way #2: Using Fridump**

> [Fridump](https://github.com/Nightbringer21/fridump.git) is an open source memory dumper tool, used to retrieve data stored in RAM from all different devices and operating systems. It is using as base [Frida](http://www.frida.re/) (excellent framework, if you don’t know it you should give it a look!) to scan the memory from the access level of a specific application and dump the accessible sectors to separate files.
>
> From: https://pentestcorner.com/introduction-to-fridump/

1. Detect the Process ID of lab package name (it is same as way #1).

2. Run `Fridump` using: `python3 .\fridump.py -U -s Strings`.

   ![fridump](/assets/images/tutorials/Android_Tutorial/MHL_Strings/fridump.png)

3. Looking through the `strings.txt` file, we can find the flag.

   ![fridump_Result](/assets/images/tutorials/Android_Tutorial/MHL_Strings/fridump_Result.png)

​	![Flag_2](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Flag_2.png)

**Way #3: Using Frida**

I searched for how to scan memory using frida and the [docs](https://frida.re/docs/javascript-api/#memory) helped me to do this.

So, after we trigger `Activity2` using the valid base64 value, the native library called `libflag.so` is loaded and the flag is initialized in memory. So, we need to scan `libflag.so` for the `"MHL"` pattern and read the flag using the following Frida script:

<!-- ![Extract_Flag](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Extract_Flag.png) -->
```Javascript
Java.perform(function() {
    setTimeout(function () {
        var moduleName = "libflag.so";
        var pattern = "4D 48 4C 7B"; // MHL {
        var module = Process.getModuleByName(moduleName);
        if (module === null) {
            console.log("[-] Module not found: " + moduleName);
        } else {
            console.log("[*] Scanning module:", moduleName, module.base, "size:", module.size);
            Memory.scan(module.base, module.size, pattern, {
                onMatch: function (address, size) {
                    console.log("[+] match at", address, "size", size);

                    console.log(hexdump(address, { length: 64 }));

                    var flagString = Memory.readCString(address);

                    console.log("Flag: ", flagString);
                },
                onComplete: function() {
                    console.log("[*] scan complete");   
                },
                onError: function (reason) {
                    console.log("[-] scan error:", reason);
                }
            });
        }
    }, 2000);
});
```

`readCSString(address)` to extract a C string from the address.

Let's run the script and get the flag.

![Frida_Flag](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Frida_Flag.png)

> **Flag:** MHL{IN_THE_MEMORY}

![Lab_Solved](/assets/images/tutorials/Android_Tutorial/MHL_Strings/Lab_Solved.png)