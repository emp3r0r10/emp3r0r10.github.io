---
title: "CyCTF 2025 Quals - Mobile Writeups"
classes: wide
header:
  teaser: /assets/images/ctf_writeups/CYCTF25/CYCTF_Logo.jpg
ribbon: blue
description: "Hello everyone, In this writeup, I will show you how I solved `Grand Theft Mobile` and `Vault Raider` mobile challenges from **CyCTF**, powered by [**CyShield**](https://www.linkedin.com/company/cyshield). Let's get started."
categories:
  - CTF Writeups
toc: true
---

Hello everyone, In this writeup, I will show you how I solved `Grand Theft Mobile` and `Vault Raider` mobile challenges from **CyCTF**, powered by [**CyShield**](https://www.linkedin.com/company/cyshield). Let's get started.

## Grand Theft Mobile

![GTM_Challenge](/assets/images/ctf_writeups/CYCTF25/GTM/GTM_Challenge.png)

Let's download the APK file and start the app on an android emulator to see how it works at runtime.

We can see that it provides us with a user-input to enter name and a button to submit.

![App_Overview](/assets/images/ctf_writeups/CYCTF25/GTM/App_Overview.png)

If we enter a random name, it shows us the following message: `you look like FIP agent i won't give you your share`.

![App_Test](/assets/images/ctf_writeups/CYCTF25/GTM/App_Test.png)



Nothing else, so let's start analyzing the source code using `JADX-GUI`.

**`AndroidManifest.xml` Analysis**

![Android_Manifest](/assets/images/ctf_writeups/CYCTF25/GTM/Android_Manifest.png)

1. Uses the SDK version `34` and the minimum version is `25`.
2. Declares `DYNAMIC_RECEIVER_NOT_EXPORTED_PERMISSION` that Android automatically adds to applications targeting API level 33 (Android 13) or higher. Its purpose is to enhance security by preventing other applications from connecting to dynamic broadcast receivers without explicit permission.
3. Defines an `MainAcvitiy` and export it to `true`.
4. Adds `intent-filter` to `MainAcvitiy` to be `LAUNCHER` with `android.intent.action.MAIN` (which means the first screen appears when you open the app as we see above).
5. `androidx.startup.InitializationProvider` is a part of `AndroidX` App Startup library. It's a `ContentProvider` used to initialize libraries at app start.
6. `android:exported="false"` — not available to other apps.
7. `android:authorities` — unique authority string required for provider identification.
8. `<meta-data>` entries tell the startup provider which initializers to run (EmojiCompat, Lifecycle, Profile Installer, etc.). These are normal library bootstrapping entries.
9. Declares a `BroadcastReceiver` from AndroidX Profile Installer library.
10. `android:permission="android.permission.DUMP"` is to tell senders must hold the `DUMP` permission to send broadcasts to this receiver. `DUMP` is a privileged/system-level permission (normally restricted), so typical third-party apps cannot send those broadcasts.
11. `android:enabled="true"` the receiver is active.
12. `android:exported="true"` the receiver **can receive broadcasts from other apps** (subject to permission). Combined with `DUMP` requirement usually prevents unprivileged apps from reaching it.
13. `android:directBootAware="false"` makes the component not run before device unlock, and it also prevents the component from accessing device-protected storage before the user has unlocked the device. This
14. The `intent-filter` entries list the actions this receiver listens for (install/save profiles, benchmark ops).

Let's navigate to `ManActivity`.

**`MainActivity.java` Analysis**

![MainActivity_1](/assets/images/ctf_writeups/CYCTF25/GTM/MainActivity_1.png)

![MainActivity_2](/assets/images/ctf_writeups/CYCTF25/GTM/MainActivity_2.png)

1. Declares `encrypted` string with a value: `1vhL9yh+Q/6sXJKHJ8mHB2p0K3HZgpBY9drRMAhDmCk=`.
2. Declares `TextView greetingOutput` and `EditText nameInput` and handle them on `onCreate()` function (related to UI).
3. Declares a **native (JNI)** method `sendFlag(Context)` implemented in a native library `libgtm.so`.
4. `System.loadLibrary("gtm")` loads that library at class load time.
5. `m68lambda$onCreate$0$comctfgtmMainActivity`  Analysis:
   1. Writes a log message to the Android system log (logcat).
   2. Declares `username` string and assign the user-input value to it.
   3. If the `username` is not empty:
      1. Declares a `secretKey` string and assign the returned value from `getDecryptionKey()` function to it.
      2. Declares ` decrypted` string and assign the returned value from `decrypt()` function to it.
      3. If username equals decrypted, it writes a log message (`flag sent:`) and calls native `sendFlag(this)`
   4. If not match, it shows an "imposter" toast.

6. `getDecryptionKey` Analysis:
   1. Declares an array of characters.
   2. Declares a `g` string with value: `Thi3f`.
   3. Returns the the combination of some strings to be: `wh@T_A_Thi3f!!!!` (which is the secret).
7. `decrypt` Analysis:
   1. Base64-decodes the input ciphertext.
   2. Uses AES in ECB mode with PKCS#5/7 padding.
   3. Initializes cipher for decrypt (`cipher.init(2, skeySpec)` — `2` is `Cipher.DECRYPT_MODE`).
   4. Decrypts and returns the plaintext string.

We need to submit a username which equals to decrypted value from `decrypt()` function to get the flag.

 We have the encrypted value and the secret, so we can decrypt it using the following script:

![Decrypt_Script](/assets/images/ctf_writeups/CYCTF25/GTM/Decrypt_Script.png)

![Decrypted_String](/assets/images/ctf_writeups/CYCTF25/GTM/Decrypted_String.png)

Now, if we submit the username as `Tr3V0R_not_Micheal`, we can see the `flag sent` message but the flag itself not appeared in logs or in UI. That means the flag is transferred internally. 

![App_Test_2](/assets/images/ctf_writeups/CYCTF25/GTM/App_Test_2.png)

So, what we can do is to dump the memory and check whether the flag is retrieved there.

Let's get the process id (PID) of the the app then run [fridump](http://github.com/Nightbringer21/fridump) to dump the memory.

![frida-ps](/assets/images/ctf_writeups/CYCTF25/GTM/frida-ps.png)

![fridump](/assets/images/ctf_writeups/CYCTF25/GTM/fridump.png)

If we read the `strings.txt` file, we can find the flag.

![Flag](/assets/images/ctf_writeups/CYCTF25/GTM/Flag.png)

> **Flag:** cyctf{aX9tG4LkZp72MvBQeC3AH8OGMJ}

## Vault Raider

![Vault-Raider_Challenge](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Vault-Raider_Challenge.png)

Like the previous challenge, let's the run the APK on an android emulator to examine it at runtime.

We can see that it shows us a blank screen with text in the center and the bottom there is a toast which tells us `Incorrect Master Key!`

![App_Overview](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/App_Overview.png)

Let's analyze the source code.

The android manifest is like the previous one with one activity (`MainActivity`), so without wasting time, let's move to `MainActivity`.

![Android_Manifest](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Android_Manifest.png)

**`MainActivity` Analysis**

![MainActivity_1](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/MainActivity_1.png)

1. Declares `Key_ALIASE` and `TAG` strings with some values.

2. Declares a native (JNI) method `getPartB(String str)` implemented in a native library `libvaultraider.so`.

3. `System.loadLibrary("vaultraider")` loads that library at class load time.

4. `onCreate()` Analysis:

   1. Reads device `IMEI` (requires `READ_PHONE_STATE` permission). If permission missing, `getIMEI()` returns `000000000000000`.

      ![GetIMEL](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/GetIMEL.png)

   2. Passes the value of `IMEI` to `HashUtils` and assign its value to `PartA`.

      <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/HashUtils.png" alt="HashUtils" style="zoom:50%;" />

   - `HashUtils` Analysis:
     - Creates a SHA-256 digest string.
     - Converts the input `String` to bytes using UTF-8 encoding
     - Computes the 32-byte SHA-256 hash.
     - Loop through hash and generate a SHA-256 then return SHA-256 digest lowercase hexadecimal string.

5. Extract device Android ID (stable per device/user) and assign it to `androidId` string.

6. Passes the value of `androidId` to `getPartB` native method and assign its value to `PartB`.

   <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Native_library_1.png" alt="Native_library_1" style="zoom:50%;" />

   <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Native_library_2.png" alt="Native_library_2" style="zoom:50%;" />

7. For more understanding of code, I used `ChatGPT` to make it more readable.

   <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Native_library_3.png" alt="Native_library_3" style="zoom: 67%;" />

   <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Native_library_4.png" alt="Native_library_4" style="zoom: 67%;" />

   - `libvaultraider.so` Analysis:
     - The function receives the Java `androidId` string from the Java side.
     - If `androidId` is `null`, it immediately returns a hardcoded message (some error text). Otherwise it turns the Java string into a regular C string so the native code can read it.
     - It runs a small sanitization/normalization step on the string (for example: trim whitespace, remove bad characters, or force lowercase — we don’t know exact details until we inspect that sanitizer function).
     - Then it computes the SHA-256 hash of that sanitized string.
     - It runs a small validation check on the hash output (some internal sanity test). If the check passes, it returns the SHA-256 result (likely as a 64-character lowercase hex string). If the check fails, it returns another hardcoded error string.

8. Gets the `app_name` from `res/values/strings.xml` file and assign its value to `disguisedBase64`.

9. Passes the value of `androidId` to `XorUtils` method and assign its value to `PartC`.

   <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/XORUtils.png" alt="XORUtils" style="zoom:50%;" />

10. Concatenates `PartA + PartC + partB` into `concatenatedParts` string.

11. Passes the value of `concatenatedParts` to `HashUtils` again and assign the value to `masterKey`.

12. Calles `getCorrectMasterKeyFromKeystore()` method.

    <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/MainActivity_3.png" alt="MainActivity_3" style="zoom:50%;" />

13. App reads Intent extra `masterKey`. If it matches computed `masterKey`, it calls `dF(masterKey)`, otherwise shows `Incorrect master key!`.

    <img src="/assets/images/ctf_writeups/CYCTF25/Vault_Raider/MainActivity_2.png" alt="MainActivity_2" style="zoom:50%;" />

    - `df()` Analysis:

      - Builds the flag by calling `gf(mk)` and wrapping result into `cyctf{...}`. It displays and logs the flag.

    - `gf()` Analysis:

      - Takes a string `k` (the `masterKey`) and produces a new string made of:
        1. A fixed long hex **prefix** that never changes (it’s produced from a fixed list of bytes in the code),
        2. An underscore `_`,
        3. the **first 8 characters** of `k` **reversed**,
        4. the suffix `_solved`.

      - The final returned value looks like: `<prefix_hex>_<reversed-first-8-of-k>_solved`

    - `bl()` Analysis:

      - Does nothing relevant.

Let's recap what we should do to get the flag:

![Exploit_Flow](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Exploit_Flow.png)

1. **Get  PartA:**

   For `PartA`, actually I tried `000000000000000` as  `IMEI` value and it worked.

   IMEI (SHA-256): `664e7c008e22933e2358f5b74864e1c7bef2331480e6be12427457ac483fce53`.

   I think any IMEI value will work as it validates the prefix in flag and don't validate the first reversed 8-bit. 

2. **Get Part C:**

   We can extract the `app_name` from `res/values/strings.xml`.

   ![App_Name](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/App_Name.png)

   Let's run the following script to get the `PartC`:

   ```python
   #!/usr/bin/env python3
   import sys
   import argparse
   import base64
   
   def xor_bytes(data: bytes, key: bytes) -> bytes:
       return bytes((b ^ key[i % len(key)]) for i, b in enumerate(data))
   
   def main():
       p = argparse.ArgumentParser(description="Decode Base64 and XOR with repeating key")
       p.add_argument("b64", help="Base64 string (e.g. resource value)")
       p.add_argument("--key", default="ctfkey", help="XOR key (default: ctfkey)")
       args = p.parse_args()
   
       try:
           raw = base64.b64decode(args.b64, validate=True)
       except Exception as e:
           print("Base64 decode error:", e, file=sys.stderr)
           sys.exit(1)
   
       out = xor_bytes(raw, args.key.encode('utf-8'))
       # Try to decode to UTF-8 for human-readable output; fallback to hex if not valid
       try:
           text = out.decode('utf-8')
       except UnicodeDecodeError:
           text = None
   
       print("Input (base64) :", args.b64)
       print("Decoded bytes  :", raw.hex())
       print("XOR key        :", args.key)
       if text is not None:
           print("Result (utf-8) :", text)
       else:
           print("Result (hex)   :", out.hex())
   
   if __name__ == "__main__":
       main()
   ```

   ![Secret](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Secret.png)

   `PartC`: `S3CR3T`.

3. **Get Part B:**

   When we run the app in an android emulator, we can capture the Android ID from `logcat`.

   ![Android_ID](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Android_ID.png)

   Android ID: `581582a98a0753f6`.

   Android ID (SHA-256): `f45cc6d5ec6b7515ba791ff341b473ec0249a322fb7ac037c21bccced92bf9d5`.

4. **Concerted String (MasterKey):**

   ````
   concatenated Parts = PartA + PartC + PartB 14bdcd6fd64180af5e7791df91b6af8e9a3e7bc844997eb8c29252706df97ca5S3CR3Tf45cc6d5ec6b7515ba791ff341b473ec0249a322fb7ac037c21bccced92bf9d5
   ````

5. **Generate `masterKey` Hash:**

   Let's SHA-256 encrypt the `masterKey` 

   ![Master_Key](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Master_Key.png)

   Master Key: `60816c4c63377f191a671f4025d7f2a09943d91dcf030a2bdd929910b79f7649`

6. **Get the flag:**

   Now let's get the flag using the following script:

   ```python
   #!/usr/bin/env python3
   # compute_flag_from_master.py
   # Re-implements gf(k) and dF(k) from the Java code you posted.
   
   master_key = "60816c4c63377f191a671f4025d7f2a09943d91dcf030a2bdd929910b79f7649"
   
   # z bytes from gf(); kotlin.io.encoding.Base64.padSymbol is '=' (ASCII 61)
   z = [52, 101, -5, 68, -98, 126, 74, -47, 99, 106, 101, 17, -96, -62, 57, 0,
        -66, 45, 61, -44, -84, 46, 106, 10, -43, -108, -95, -30, 59, -73, -50, -118, -100]
   
   def to_signed_byte(b):
       # in Java a byte is signed; formatting %02x prints two's-complement byte value
       return b & 0xff
   
   def compute_prefix_hex(z_bytes):
       return ''.join(f"{to_signed_byte(b):02x}" for b in z_bytes)
   
   def gf(k: str) -> str:
       prefix = compute_prefix_hex(z)
       # take first 8 chars of k and reverse their order
       first8 = k[:8]
       reversed_first8 = first8[::-1]
       return f"{prefix}_{reversed_first8}_solved"
   
   if __name__ == "__main__":
       gf_val = gf(master_key)
       flag = f"cyctf{{{gf_val}}}"
       print("master_key:", master_key)
       print("gf(master_key):", gf_val)
       print("flag:", flag
   ```

   ![Flag](/assets/images/ctf_writeups/CYCTF25/Vault_Raider/Flag.png)


> **Flag:** cyctf{3465fb449e7e4ad1636a6511a0c23900be2d3dd4ac2e6a0ad594a1e23bb7ce8a9c_c4c61806_solved}