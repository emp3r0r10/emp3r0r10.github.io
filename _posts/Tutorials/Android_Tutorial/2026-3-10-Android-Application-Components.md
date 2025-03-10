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

![Android_Components](D:\Android_Tutorials\Android_Components.png)

Android Application Components are essential building blocks of an android application. They are defined in `AndroidManifest.xml` file. They work all together to build a functional application. Each component has lifecycle and role.

There are four types of app components:

- Activities
- Services
- Broadcast receivers
- Content providers
- Intents
- Intent Filters

## Activities

**Activities** are responsible for **User Interface** of an application. It’s a focused screen that users interact with. It represents a single screen with a user interface.

> **Events** are actions or occurrences (e.g., button clicks, swipes) that happen within an **Activity**.
>
> **Activities** are screens that provide the UI and handle events.
>
> ------
>
> ### **1. The Button Click is the Event**
>
> - When the user clicks the "Submit" button, the **button click** is the **event**.
> - This event is triggered by the user's action (clicking the button).
>
> ------
>
> ### **2. The Activity Handles the Event**
>
> - The **Activity** is responsible for **handling the event** (e.g., validating input, saving data, or navigating to another screen).
> - The Activity does not "do" the event; it **responds** to the event.
>
> ------
>
> ### **3. The Action is Not the Event**
>
> - The **action** (e.g., validating input, saving data, or navigating) is the **result** of handling the event.
> - The event (button click) triggers the Activity to perform the action.
>
> ------
>
> ### Scenario:
>
> - A user clicks a "Submit" button on a login screen.
>
> ### What Happens:
>
> 1. **Event**: The button click is the **event**.
> 2. **Event Handling**: The Activity's `OnClickListener` detects the event.
> 3. **Action**: The Activity performs actions like validating input and navigating to the next screen.

### Types of Activity

1. **Main Activity**

   Main Activity is the first screen when the application lunches. It’s usually specified in the `AndroidManifest.xml` file.

2. **Sub Activity**

   Sub Activities are the rest of screens that user can see in an application expect the first screen.

### Life Cycle

- `onCreate()`: Called when the activity is first created.
- `onStart()`: Called when the activity becomes visible to the user.
- `onResume()`: Called when the activity starts interacting with the user.
- `onPause()`: Called when the activity is partially obscured (e.g., by a dialog).
- `onStop()`: Called when the activity is no longer visible.
- `onDestroy()`: Called when the activity is destroyed.

## Services

Services is running in the background to perform long-running operations without needing of User Interface. It can be running if the activity is running or stopped. An example of service that running on background is the running music in some games while the Main Activity of game is running.

### **Types of Services**

1. Started Service
   - A service is **started** when an application component (like an activity) starts it using `startService()`.
   - Once started, it runs indefinitely in the background until it is stopped by calling `stopService()` or `stopSelf()`.
   - **Example**: Playing music or downloading a file.
2. Bound Service
   - A service is **bound** when an application component binds to it using `bindService()`.
   - The service runs only as long as there are active clients bound to it. Once all clients unbind, the service stops.
   - **Example**: A service providing an API for other components (such as providing data to an activity).

### Life Cycle

## Broadcast receivers

**Broadcast** is an action send by the system or app to notify other components about an event.

**Broadcast Receiver** is a component that listens for a specific broadcast to perform actions when they occur.

Example of **Broadcast Receiver**: A receiver that listens for `ACTION_POWER_CONNECTED` to notify the user when the device is plugged in.

### **Types of Broadcast**

1. **System Broadcast**

   Sent by an Android System  (e.g., battery low, screen on/off).

2. **Custom Broadcast**

   Sent by applications for specific events.

### **Types of Broadcast Receiver**

1. Static Broadcast Receiver
   1. Declared in `AndroidManifest.xml`
   2. Always active as long as the app is installed.
2. Dynamic Broadcast Receiver
   1. Registered programmatically at runtime using `registerReceiver()`.
   2. Active only while the app or activity is running.

### **Use Cases for Broadcast Receivers**

1. Listening for System Events:
   1. Battery status, network changes, charging status, etc.
2. Application-Specific Notifications:
   1. Notify other components about specific events in the app.
3. Custom Communication:
   1. Custom events triggered between activities, services, or other apps.

## Content Provider

Content Provider is a component in Android that manages access to a structured set of data. It acts as an abstraction layer between the data source (e.g., a database, file, or network) and the components that need to access the data (e.g., activities, services, or other apps). Content providers are primarily used to share data between applications securely.

### **Key Concepts**

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

## Intents

**Intents and Intent Filters are** fundamental components in Android that enable communication between different components (e.g., activities, services, broadcast receivers) and even between different applications.

**Intent** is a messaging object used to request an action from another component or application. It acts as a bridge that helps in passing data or instructions between components (e.g., Activity, Service, BroadcastReceiver).

### **Types of Intents**

1. **Implicit Intents**

   Doesn’t specify the component. The system determines which component can handle the request based on the **Intent Filters** declared in the manifest.

2. **Explicit Intents**

   Specify the component by name (Activity or Service). Used within the same application for direct communication.

### **Common Use Cases of Intents**

- Starting an Activity or Service.
- Delivering a broadcast message.
- Passing data between components.
- Opening system features (e.g., camera, browser).

## **Intent Filters**

An **Intent Filter** is a declaration in the `AndroidManifest.xml` file that specifies the types of intents a component (e.g., activity, service, broadcast receiver) can handle. It allows a component to receive implicit intents.

------

### **How Intent Filters Work**

1. Declare Intent Filters

   :

   - Add `<intent-filter>` tags to the component in the manifest.

2. Match Intents

   :

   - The system matches incoming implicit intents with the declared intent filters.

3. Resolve Components

   :

   - If a match is found, the component is activated.

------

### **Example of Intent Filters**

### 1. **Handling a Web URL**

- Declare an intent filter to handle web URLs in an activity.

  ```xml
  <activity android:name=".WebActivity"><intent-filter><action android:name="android.intent.action.VIEW" /><category android:name="android.intent.category.DEFAULT" /><category android:name="android.intent.category.BROWSABLE" /><data android:scheme="https" android:host="www.example.com" /></intent-filter></activity>
  ```

- When another app sends an implicit intent to view `https://www.example.com`, this activity will be launched.

### 2. **Sharing Text**

- Declare an intent filter to handle text sharing.

  ```xml
  <activity android:name=".ShareActivity"><intent-filter><action android:name="android.intent.action.SEND" /><category android:name="android.intent.category.DEFAULT" /><data android:mimeType="text/plain" /></intent-filter></activity>
  ```

- When another app sends an implicit intent to share text, this activity will be listed as an optio

## Whole Example of Intents and Intent Filters

### **Scenario**

1. A user clicks on a PDF file (e.g., in a file manager or email app).
2. The system sends an **implicit intent** to open the PDF.
3. The user is presented with a list of apps that can handle PDF files (e.g., PDF viewers).
4. The user selects a specific PDF viewer app to open the file.

------

### **How It Works**

### 1. **Implicit Intent**

- When the user clicks on the PDF file, the app (e.g., file manager or email app) creates an **implicit intent** to open the file.

- The intent includes:

  - **Action**: `Intent.ACTION_VIEW` (to view the file).
  - **Data**: The URI of the PDF file (e.g., `content://path/to/file.pdf`).
  - **Type**: The MIME type of the file (e.g., `application/pdf`).

  **Example Code (Sending the Intent):**

  ```java
  Intent intent = new Intent(Intent.ACTION_VIEW);
  intent.setDataAndType(Uri.parse("content://path/to/file.pdf"), "application/pdf");
  startActivity(intent);
  ```

### 2. **Intent Filter**

- The PDF viewer app declares an **intent filter** in its `AndroidManifest.xml` to indicate that it can handle PDF files.

- The intent filter specifies:Run HTML

  - **Action**: `Intent.ACTION_VIEW`
  - **Category**: `Intent.CATEGORY_DEFAULT`
  - **Data**: MIME type `application/pdf`

  **Example Code (Intent Filter in PDF Viewer App):**

  ```xml
  <activity android:name=".PdfViewerActivity"><intent-filter><action android:name="android.intent.action.VIEW" /><category android:name="android.intent.category.DEFAULT" /><data android:mimeType="application/pdf" /></intent-filter></activity>
  ```

### 3. **Chooser Dialog**

- When the implicit intent is sent, the Android system checks all installed apps for components that match the intent filter.
- If multiple apps can handle the intent (e.g., multiple PDF viewers), the system displays a **chooser dialog** with the available options.
- The user selects one of the apps (e.g., a PDF viewer) to open the file.

### 4. **Opening the PDF**

- The selected PDF viewer app receives the intent and opens the PDF file in its `PdfViewerActivity`.

- The app retrieves the PDF file's URI from the intent and displays it.

  **Example Code (Handling the Intent in PDF Viewer App):**

  ```java
  public class PdfViewerActivity extends AppCompatActivity {
      @Override
      protected void onCreate(Bundle savedInstanceState) {
          super.onCreate(savedInstanceState);
          setContentView(R.layout.activity_pdf_viewer);
  
          // Get the intent that started this activity
          Intent intent = getIntent();
  
          // Check if the intent action is ACTION_VIEW and the type is PDF
          if (Intent.ACTION_VIEW.equals(intent.getAction()) && intent.getType() != null) {
              if ("application/pdf".equals(intent.getType())) {
                  // Get the PDF file URI from the intent
                  Uri pdfUri = intent.getData();
  
                  // Open and display the PDF file
                  openPdf(pdfUri);
              }
          }
      }
  
      private void openPdf(Uri pdfUri) {
          // Code to open and display the PDF file
          // (e.g., using a PDF rendering library like PDFView)
      }
  }
  ```