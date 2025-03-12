---
title: "Android Components"
classes: wide
header:
  teaser: /assets/images/tutorials/Android_Tutorial/Android_Components/Android_Components_Cover.png
ribbon: red
description: "Android Application Components are essential building blocks of an android application. They are defined in `AndroidManifest.xml` file. They work all together to build a functional application. Each component has lifecycle and role."
categories:
  - Tutorials
toc: false
---

<img src="/assets/images/tutorials/Android_Tutorial/Android_Components/Android_Components.png" alt="Android_Architecture" style="zoom: 100%;" />

## Table of Contents
- [Android Application Components](#android-application-components)
  - [Activities](#activities)
    - [Types of Activity](#types-of-activity)
    - [Life Cycle of Activity](#life-cycle-of-activity)
    - [Implementation of Activity](#implementation-of-activity)
  - [Services](#services)
    - [Types of Services](#types-of-services)
    - [Life Cycle of Service](#life-cycle-of-service)
    - [Implementation of Service](#implementation-of-service)
  - [Broadcast Receivers](#broadcast-receivers)
    - [Types of Broadcast](#types-of-broadcast)
    - [Types of Broadcast Receiver](#types-of-broadcast-receiver)
    - [Use Cases for Broadcast Receivers](#use-cases-for-broadcast-receivers)
    - [Implementation of Static Broadcast Receiver](#implementation-of-static-broadcast-receiver)
  - [Content Provider](#content-provider)
    - [Key Concepts](#key-concepts)
    - [Life Cycle of Content Provider](#life-cycle-of-content-provider)
    - [Implementation of Content Provider](#implementation-of-content-provider)
  - [Intents](#intents)
    - [Types of Intents](#types-of-intents)
    - [Use Cases of Intents](#use-cases-of-intents)
  - [Intent Filters](#intent-filters)
    - [Intent Filter Attributes](#intent-filter-attributes)
    - [How Intent Filters Work](#how-intent-filters-work)
    - [Example of Intent Filters](#example-of-intent-filters)
      - [Handling a Web URL](#handling-a-web-url)
      - [Sharing Text](#sharing-text)
  - [Whole Example of Intents and Intent Filters](#whole-example-of-intents-and-intent-filters)
    - [Scenario](#scenario)
    - [How It Works](#how-it-works)

Android Application Components are essential building blocks of an android application. They are defined in `AndroidManifest.xml` file. They work all together to build a functional application. Each component has lifecycle and role.

There are four types of app components:

- Activities
- Services
- Broadcast receivers
- Content providers
- Intents
- Intent Filters

## Activities

Activities are responsible for **User Interface** of an application. It’s a focused screen that users interact with. It represents a single screen with a user interface.

### Types of Activity

1. **Main Activity**

   Main Activity is the first screen when the application lunches. It’s usually specified in the `AndroidManifest.xml` file.

2. **Sub Activity**

   Sub Activities are the rest of screens that user can see in an application expect the first screen.

### Life Cycle of Activity

- `onCreate()`: Called when the activity is first created.
- `onStart()`: Called when the activity becomes visible to the user.
- `onResume()`: Called when the activity starts interacting with the user.
- `onPause()`: Called when the activity is partially obscured (e.g., by a dialog).
- `onStop()`: Called when the activity is no longer visible.
- `onDestroy()`: Called when the activity is destroyed.

### Implementation of Activity

```
package com.example.application_2

import android.os.Bundle
import android.view.Menu
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_main)
        
        val btn = findViewById<Button>(R.id.button)
        btn.setOnClickListener {
            Toast.makeText(this, "My First App", Toast.LENGTH_SHORT).show()
        }
    }
}
```

![Android_Activity](/assets/images/tutorials/Android_Tutorial/Android_Components/Android_Activity.png)

> **Toast** provides simple feedback about an operation in a small popup. It only fills the amount of space required for the message and the current activity remains visible and interactive. Toasts automatically disappear after a timeout.

## Services

Services is running in the background to perform long-running operations without needing of User Interface. It can be running if the activity is running or stopped. An example of service that running on background is the running music in some games while the Main Activity of game is running.

### Types of Services

1. Started Service
   - A service is **started** when an application component (like an activity) starts it using `startService()`.
   - Once started, it runs indefinitely in the background until it is stopped by calling `stopService()` or `stopSelf()`.
   - **Example**: Playing music or downloading a file.
2. Bound Service
   - A service is **bound** when an application component binds to it using `bindService()`.
   - The service runs only as long as there are active clients bound to it. Once all clients unbind, the service stops.
   - **Example**: A service providing an API for other components (such as providing data to an activity).

### Life Cycle of Service

- `onCreate()`: Called when the service is first created. You can use this method to initialize resources or set up connections.
- `onStartCommand()`: Called when the service is started using `startService()`. It defines what action the service will perform.
- `onBind()`: Called when the service is bound to a client (using `bindService()`).
- `onUnbind()`: Called when the service is unbound from all clients.
- `onDestroy()`: Called when the service is destroyed or stopped.

### Implementation of Service

`Service.kt`

```Kotlin
package com.example.application_2

import android.app.Service
import android.content.Intent
import android.os.IBinder
import android.util.Log

class Service : Service() {

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        Log.d("BackgroundService", "Service is running...")
        // Do background tasks here

        // If the service gets killed, restart it
        return START_STICKY
    }

    override fun onBind(intent: Intent?): IBinder? {
        return null // No binding needed for this example
    }

    override fun onDestroy() {
        super.onDestroy()
        Log.d("BackgroundService", "Service stopped.")
    }
}
```

`AndroidManifest.xml`

```Kotlin
<service android:name=".Service" />
```

`MainActivity.kt`

```Kotlin
package com.example.application_2

import android.content.Intent
import android.content.IntentFilter
import android.net.wifi.WifiManager
import android.os.Bundle
import android.view.Menu
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class MainActivity : AppCompatActivity() {

    private lateinit var wifiStateReceiver: WifiStateReceiver

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val btn1 = findViewById<Button>(R.id.button1)

        val serviceIntent = Intent(this, Service::class.java)
        btn1.setOnClickListener {
            startService(serviceIntent)
        }

        val btn2 = findViewById<Button>(R.id.button2)
        btn2.setOnClickListener {
            stopService(serviceIntent)
        }
    }
}
```





![Service_1](/assets/images/tutorials/Android_Tutorial/Android_Components/Service_1.png)

![Service_2](/assets/images/tutorials/Android_Tutorial/Android_Components/Service_2.png)

## Broadcast receivers

**Broadcast** is an action send by the system or app to notify other components about an event.

**Broadcast Receiver** is a component that listens for a specific broadcast to perform actions when they occur.

Example of **Broadcast Receiver**: A receiver that listens for `ACTION_POWER_CONNECTED` to notify the user when the device is plugged in.

### Types of Broadcast

1. **System Broadcast**

   Sent by an Android System  (e.g., battery low, screen on/off).

2. **Custom Broadcast**

   Sent by applications for specific events.

### Types of Broadcast Receiver

1. **Static Broadcast Receiver**
   1. Declared in `AndroidManifest.xml`
   2. Always active as long as the app is installed.
2. **Dynamic Broadcast Receiver**
   1. Registered programmatically at runtime using `registerReceiver()`.
   2. Active only while the app or activity is running.

### Use Cases for Broadcast Receivers

1. **Listening for System Events:** Battery status, network changes, charging status, etc.
2. **Application-Specific Notifications:** Notify other components about specific events in the app.
3. **Custom Communication:** Custom events triggered between activities, services, or other apps.

### Implementation of static broadcast receiver

`WifiStateReceiver.kt`

```kotlin
package com.example.application_2

import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.net.wifi.WifiManager
import android.widget.Toast

class WifiStateReceiver : BroadcastReceiver() {
    override fun onReceive(context: Context, intent: Intent) {
        if (intent.action == WifiManager.WIFI_STATE_CHANGED_ACTION) {
            val wifiState = intent.getIntExtra(WifiManager.EXTRA_WIFI_STATE, WifiManager.WIFI_STATE_UNKNOWN)

            when (wifiState) {
                WifiManager.WIFI_STATE_ENABLED ->
                    Toast.makeText(context, "WiFi is Enabled", Toast.LENGTH_LONG).show()

                WifiManager.WIFI_STATE_DISABLED ->
                    Toast.makeText(context, "WiFi is Disabled", Toast.LENGTH_LONG).show()
            }
        }
    }
}
```

`MainActivity.kt`

```kotlin
package com.example.application_2

import android.content.IntentFilter
import android.net.wifi.WifiManager
import android.os.Bundle
import android.view.Menu
import android.view.MenuItem
import android.view.View
import android.widget.Button
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.widget.Toolbar
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class MainActivity : AppCompatActivity() {

    private lateinit var wifiStateReceiver: WifiStateReceiver

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        wifiStateReceiver = WifiStateReceiver()
        val filter = IntentFilter(WifiManager.WIFI_STATE_CHANGED_ACTION)
        registerReceiver(wifiStateReceiver, filter)
    }

    override fun onDestroy() {
        super.onDestroy()
        unregisterReceiver(wifiStateReceiver)
    }
}
```

`AndroidManifest.xml`

```kotlin
<receiver android:name=".WifiStateReceiver"
    android:exported="true">
    <intent-filter>
        <action android:name="android.net.wifi.WIFI_STATE_CHANGED" />
    </intent-filter>
</receiver>
```

![Broadcast_1](/assets/images/tutorials/Android_Tutorial/Android_Components/Broadcast_1.png)

![Broadcast_2](/assets/images/tutorials/Android_Tutorial/Android_Components/Broadcast_2.png)

## Content Provider

Content Provider is a component in Android that manages access to a structured set of data. It acts as an abstraction layer between the data source (e.g., a database, file, or network) and the components that need to access the data (e.g., activities, services, or other apps). Content providers are primarily used to share data between applications securely.

### Key Concepts

1. Data Sharing:

   - Content providers allow apps to share data with other apps while maintaining control over access permissions.

2. Structured Data:

   - Data is typically organized in a tabular format, similar to a database, with rows and columns.

3. URI (Uniform Resource Identifier):

   - Each content provider is identified by a unique URI, which is used to query or modify the data.

   - Example: 

     ```
     content://com.example.provider/table_name
     ```
   
     - `content://<authority>/<path>/<id>`
     - **`content://`** → Scheme used by content providers.
     - **`authority`** → The unique name of the content provider (usually the package name).
     - **`path`** → The data type (e.g., "contacts", "images").
     - **`id`** → The specific data item (optional).
   
4. CRUD Operations:

   - Content providers support the four basic database operations:
     - **Create**: Insert new data.
     - **Read**: Query existing data.
     - **Update**: Modify existing data.
     - **Delete**: Remove data.

### Life Cycle of Content Provider

- `query()`: A method that accepts arguments and fetches the data from the desired table. Data is retired as a cursor object.
- `insert()`: To insert a new row in the database of the content provider. It returns the content URI of the inserted row.
- `update()`: This method is used to update the fields of an existing row. It returns the number of rows updated.
- `delete()`: This method is used to delete the existing rows. It returns the number of rows deleted.
- `getType()`: This method returns the Multipurpose Internet Mail Extension(MIME) type of data to the given Content URI.
- `onCreate()`: As the content provider is created, the android system calls this method immediately to initialise the provider.

### Implementation of Content Provider

## Intents

Intents and Intent Filters are fundamental components in Android that enable communication between different components (e.g., activities, services, broadcast receivers) and even between different applications.

Intent is a messaging object used to request an action from another component or application. It acts as a bridge that helps in passing data or instructions between components (e.g., Activity, Service, BroadcastReceiver).

### Types of Intents

1. **Implicit Intents**

   Doesn’t specify the component. but instead the system determines which component can handle the request based on the **Intent Filters** declared in the manifest.

   Code Example:

   ```kotlin
   Intent intent = new Intent(Intent.ACTION_VIEW);
   intent.setData(Uri.parse("https://www.google.com"));
   startActivity(intent);
   ```

2. **Explicit Intents**

   Specify the component by name (Activity or Service). Used within the same application for direct communication.
   
   Code Example:
   
   ```Kotlin
   Intent intent = new Intent(MainActivity.this, SecondActivity.class);
   intent.putExtra("message", "Hello from MainActivity");
   startActivity(intent);
   ```

### Use Cases of Intents

- Starting an Activity or Service.
- Delivering a broadcast message.
- Passing data between components.
- Opening system features (e.g., camera, browser).

## Intent Filters

An Intent Filter is a declaration in the `AndroidManifest.xml` file that specifies the types of intents a component (e.g., activity, service, broadcast receiver) can handle. It allows a component to receive implicit intents. Components register Intent Filters in the manifest to indicate the actions they can handle.

### Intent Filter Attributes

1. **Action:** Describes the operation to be performed (`android.intent.action.VIEW`).

   **common action:**

   - `ACTION_VIEW`: Use this action in intent with startActivity() when you have some information that activity can show to the user like showing an image in a gallery app or  an address to view in a map app
   - `ACTION_SEND`: You should use this in intent with startActivity() when you have some data that the user can share through another app, such as an email app or social sharing app.
2. **Data:** Specifies the type of data the component can handle (`http://`, `content://`).
3. **Category:** Adds additional information about the component that can handle the intent (`android.intent.category.DEFAULT`).

   **common categories:**

   - `CATEGORY_BROWSABLE`: The target activity allows itself to be started by a web browser to display data referenced by a link.

### How Intent Filters Work

1. Declare Intent Filters:

   - Add `<intent-filter>` tags to the component in the manifest file.

2. Match Intents:

   - The system matches incoming implicit intents with the declared intent filters.

3. Resolve Components:

   - If a match is found, the component is activated.

### Example of Intent Filters

#### 1. Handling a Web URL

- Declare an intent filter to handle web URLs in an activity.

  ```xml
  <activity android:name=".WebActivity">
      <intent-filter>
          <action android:name="android.intent.action.VIEW" />
          <category android:name="android.intent.category.DEFAULT" />
          <category android:name="android.intent.category.BROWSABLE" />
          <data android:scheme="https" android:host="www.example.com" />
      </intent-filter>
  </activity>
  ```

- When another app sends an implicit intent to view `https://www.example.com`, this activity will be launched.

#### 2. Sharing Text

- Declare an intent filter to handle text sharing.

  ```xml
  <activity android:name=".ShareActivity">
      <intent-filter>
          <action android:name="android.intent.action.SEND" />
          <category android:name="android.intent.category.DEFAULT" />
          <data android:mimeType="text/plain" />
      </intent-filter>
  </activity>
  ```

- When another app sends an implicit intent to share text, this activity will be listed as an option.

## Example of Intents and Intent Filters

![Android_Intent_filters](/assets/images/tutorials/Android_Tutorial/Android_Components/Android_Intent_filters.png)

### **Scenario**

1. A user clicks a button in the app to open a website.
2. The system sends an **implicit intent** to open the URL.
3. The user is presented with a list of browsers (e.g., Chrome, Firefox, Edge).
4. The user selects **Google Chrome** to open the website.
5. The URL is opened in Chrome.

### **How It Works**

1. **Implicit Intent (Triggering the URL Opening Action)**

   - When the user clicks the button, the app creates an **implicit intent** to open a webpage.

   - The intent includes:
     - **Action**: `Intent.ACTION_VIEW` (to view the URL).
     - **Data**: The URL of the website (e.g., `https://www.google.com`).

   - Example Code (Sending the Intent from MainActivity.java)

     ```java
     package com.example.intentsdemo;
     
     import android.content.Intent;
     import android.net.Uri;
     import android.os.Bundle;
     import android.view.View;
     import android.widget.Button;
     import androidx.appcompat.app.AppCompatActivity;
     
     public class MainActivity extends AppCompatActivity {
         @Override
         protected void onCreate(Bundle savedInstanceState) {
             super.onCreate(savedInstanceState);
             setContentView(R.layout.activity_main);
     
             Button openBrowserButton = findViewById(R.id.openBrowserButton);
             openBrowserButton.setOnClickListener(new View.OnClickListener() {
                 @Override
                 public void onClick(View v) {
                     // Create an implicit intent to open a URL
                     Intent intent = new Intent(Intent.ACTION_VIEW);
                     
                     // Set the website URL
                     intent.setData(Uri.parse("https://www.google.com"));
     
                     // Allow the user to choose a browser
                     startActivity(Intent.createChooser(intent, "Open with"));
                 }
             });
         }
     }   
     ```

2. **Intent Filter (Declaring Browser App Compatibility)**

   - The browser apps (e.g., Chrome, Firefox) declare an **intent filter** in their `AndroidManifest.xml` file.

   - This lets the system know that they can handle web URLs.

   - The intent filter specifies:
     - **Action**: `Intent.ACTION_VIEW`
     - **Category**: `Intent.CATEGORY_BROWSABLE` (so it appears in the browser selection list).
     - **Data**: Scheme `http` and `https` (to handle web links).

   - Example (Intent Filter in Browser Apps like Chrome, Firefox, etc.)

     ```XML
     xmlCopyEdit<activity android:name=".BrowserActivity">
         <intent-filter>
             <action android:name="android.intent.action.VIEW" />
             <category android:name="android.intent.category.BROWSABLE" />
             <data android:scheme="http" />
             <data android:scheme="https" />
         </intent-filter>
     </activity>

3. **Chooser Dialog (Allowing the User to Select a Browser)**

   - When the intent is sent, Android checks for apps that match the intent filter.

   - If multiple apps (e.g., Chrome, Firefox, Edge) can handle the request, a **chooser dialog** appears.

   - The user selects **Google Chrome**.

4. **Opening the URL in Chrome**

   - Once the user selects Chrome, it receives the **intent** with the URL data.

   - Chrome opens the website using its internal web rendering engine.