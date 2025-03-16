# Android File & APK Structure

## APK Structure

APK file (`Android Package Kit`) is an archive file with `.apk` format that contains all files and components required to build an android application. You can extract files and codes from APK file by unzipping the file.

![APK_Structure](D:\Android_Tutorials\APK_Structure.png)

### `AndroidManifest.xml`

`AndroidManifest.xml` Defines and describes all application components such as activities, resources, services, permissions, .etc.

**Key Sections in the `AndroidManifest.xml`**

- **`<manifest>`**:

  This is the root element of the manifest file. It contains the package name and specifies the permissions and the components included in the app.

  ```xml
  <manifest xmlns:android="<http://schemas.android.com/apk/res/android>"
            package="com.example.app">
  </manifest>
  ```

- **`<application>`**:

  This section defines the application’s settings, such as the app icon, theme, and components (activities, services, etc.).

  ```xml
  <application
      android:icon="@drawable/ic_launcher"
      android:label="@string/app_name">
  </application>
  ```

  - **`android:icon`**: Specifies the icon to display on the home screen.
  - **`android:label`**: Sets the name of the app that will be shown to users.

- **`<activity>`**:

  Each activity in the app is declared within the **`<activity>`** tag. This includes important details like the activity's name, theme, and intent filters.

  ```xml
  <activity android:name=".MainActivity"
            android:label="@string/app_name">
      <intent-filter>
          <action android:name="android.intent.action.MAIN" />
          <category android:name="android.intent.category.LAUNCHER" />
      </intent-filter>
  </activity>
  ```

  - **`android:name`**: Specifies the class name of the activity.
  - **`intent-filter`**: Defines what intents the activity can handle. In this case, it’s the main launcher activity.

- **`<service>`**:

  Services are declared in this tag, which allows the app to run background tasks like downloads, data sync, etc.

  ```xml
  <service android:name=".MyService" />
  ```

- **`<receiver>`**:

  Broadcast receivers are declared here, specifying which system events or custom broadcasts they should respond to.

  ```xml
  <receiver android:name=".MyReceiver">
      <intent-filter>
          <action android:name="com.example.myapp.MY_CUSTOM_BROADCAST" />
      </intent-filter>
  </receiver>
  ```

- **`<provider>`**:

  Content providers, which handle shared data access, are declared here.

  ```xml
  <provider android:name=".MyContentProvider"
            android:authorities="com.example.myapp.provider" />
  ```

- **`<uses-permission>`**:

  This tag declares the permissions that the app needs to function. For example, requesting permission to use the internet or access the device’s camera.

### `/Lib/`

`/Lib/` Contains all native libraries (typically written in low-level languages like C or C++) with complied code (stored in `.so` files) that required for an application.

### `/META-INF/`

`/META-INF/` Contains metadata about the application. It plays a critical role in ensuring the integrity and authenticity of the APK. It includes:

1. `CERT.RSA`
   - Contains the public key for verifying the APK's signature.
   - Ensures the APK has not been tampered with since it was signed by the developer.
   - RSA stands for *Rivest-Shamir-Adleman*, a widely used encryption algorithm.
   - This file works with `CERT.SF` to validate the APK.
2. `CERT.SF`
   - Lists all the files in the APK and their cryptographic hashes (SHA-1 or SHA-256).
   - Ensures file integrity by verifying that no files have been modified.
   - `.SF` stands for *Signature File*.
   - It acts as a map linking each file in the APK to its corresponding hash.
3. `MANIFEST.MF`
   - A manifest file containing the SHA-1 or SHA-256 digests of every file in the APK.
   - Provides a foundation for verifying the APK's integrity.
   - Every file in the APK (except `META-INF/` files) is listed here with its cryptographic hash.
   - Serves as a bridge between the APK contents and the signature files (`CERT.SF` and `CERT.RSA`).

### `res/`

`res/` contains application resources such as images, colors, fonts, user interface layouts, .etc. Resources are used for user experiences.

#### Resource Types:

- `res/drawable/`

  It contains image resources like PNGs, GIFs, and other graphical assets used in the app’s UI. The images here can be used for icons, buttons, backgrounds, etc.

- `res/layout/`

  It contains XML files that define the layout of each screen in the app. These layouts specify the arrangement of UI elements such as buttons, text fields, and images. Each activity or fragment usually corresponds to a layout file.

- `res/values/`

  This folder contains XML files for storing value resources such as strings, dimensions, colors, and styles.

  - **`strings.xml`**: Stores text strings, including UI text, labels, messages, etc.
  - **`colors.xml`**: Defines color values that can be referenced throughout the app.
  - **`styles.xml`**: Specifies UI themes and styles for the app, including text size, font, and colors.
  - **`dimens.xml`**: Defines dimensions like margin, padding, or layout size, which can be used for consistent UI design.

- `res/anim/`

  It contains XML files that define animations, like fade effects, scaling, or movement. These animations can be used to animate UI components.

- `res/menu/`

  It holds XML files defining the menus for the app (e.g., options menus, context menus). These files specify the menu items that appear when users interact with the app’s UI.

- `res/raw/`

  It stores raw files such as audio or video files, which can be accessed programmatically using a URI. These files are stored as-is, without any processing or modification.

- `res/mipmap/`

  It is specifically used for storing app icons in various resolutions for different screen densities (`mdpi, hdpi, xhdpi, xxhdpi, xxxhdpi`).

### `assets/`

`assets/` Includes data such as photos, videos, documents, and databases. The files in `assets/` directory accessed without modification  

### `classes.dex`

`classes.dex` Contains all the java classes in a `.dex` (Dalvik Executable) file format, to be executed by the Android Runtime.

> Android Runtime (ART) is created specifically for the Android project. It executes the Dalvik executable (`.dex`) format and DEX bytecode specification.

> **what is DEX file?**
>
> When you write Android applications in Java or Kotlin, the source code is first compiled into Java bytecode (.class files) using a Java compiler. These bytecode files are then converted into the DEX format by the Android build system during the compilation process.

### `resources.arsc`

`resources.arsc` Contains precompiled resources. It contains a compiled version of all the resources in an Android app, such as strings, layouts, images, and other configuration data. This file allows the app to access and efficiently retrieve these resources during runtime.

**How `resources.arsc` Works?**

When an Android app is built, resources like strings, layouts, and drawables are compiled into the **`resources.arsc`** file. During the app's execution, the Android system can quickly reference and load these resources from this file rather than parsing individual XML files.

- Example: When an app requests a string resource using an identifier, such as `R.string.app_name`, Android looks it up in the `resources.arsc` file, retrieves the corresponding string, and displays it in the UI.

## File Structure

1. `/system/`

   - Contains the core operating system files, pre-installed apps, libraries, and system configurations.
   - `/system/app/` contains system (pre-installed) applications.
   - `/system/priv-app/` contains system (pre-installed) applications that need privileges.

2. `/data`

   - Stores user-installed apps.
   - Needs **root** privileges to view or modify on it.
   - `/data/app/` contains Third-Party (user-installed) applications APK files.
   - `/data/data` contains data for each app including database, shared preferences, and caches.

3. `/sdcard` or `/storage/emulated/0`

   - User-accessible storage for media, downloads, and app data (non-sensitive files).
   - Shared by all apps unless restricted by Android's scoped storage policies.
   - Example: Photos, videos, music, documents and downloads.

4. `/dev`

   1. Contains device files for hardware components.
   2. Example: Input devices, USB connections, and block storage.

5. `/etc`

   1. Contains configuration files for the Android operating system.

   - Examples: Network configurations, permissions, and system-wide settings.

6. `/lib`

   1. Contains shared libraries required by the Android runtime and system processes.

7. `/bin`

   - Contains essential binary files and executables for system-level commands.

8. `/proc`

   - A virtual directory providing information about system processes and kernel status.
   - Frequently used for debugging and system monitoring.
   - Example: `/proc/cpuinfo` (CPU details) or `/proc/meminfo` (memory details).

9. `/mnt`

   - Mount point for external storage, such as SD cards and USB drives.
   - Example: `/mnt/media_rw/` for SD card storage.

10. `/boot`

    - Stores the kernel and bootloader required to start the device.
    - Modifications here are critical and can brick the device.

11. `/cache`

    - Temporary storage for frequently accessed data or system updates.

12. `/data/data` 

    - This directory contains all user-installed applications.

13. `/data/user/0`

    - This directory contains data that is private to a specific app and cannot be accessed by other apps.

14. `/data/app`

    - This directory contains APK files of user-installed applications.

15. `/system/app`

    - This directory contains pre-installed system applications.

16. `/system/bin`

    - This directory contains binary files.

17. `/data/local/tmp`

    - This directory is world-writable, which can be a potential security issue.

18. `/data/system`

    - This directory contains system configuration files.

19. `/etc/apns-conf.xml`

    - This file contains default Access Point Name (APN) configurations for the device to connect with the current carrier’s network.

20. `/data/misc/wifi`

    - This directory contains WiFi configuration files.

21. `/data/misc/user/0/cacerts-added`

    - This directory contains user-added certificates.

22. `/etc/security/cacerts/`

    - This directory contains the system certificate store, which can only be accessed by root users.

23. `/data/data/<package-name>/shared_prefs/`

    - Stores small app settings or user preferences in key-value pairs.
