Java.perform(function() {
    setTimeout(function () {
        var moduleName = "libflag.so";
        var pattern = "4D 48 4C 7B"; // MHL{

        var module = Process.getModuleByName(moduleName);

        if (module === null) {
            console.log("[-] Module not found: " + moduleName);
        } else {
            console.log("[*] Scanning module:", moduleName, module.base, "size:", module.size);
            Memory.scan(module.base, module.size, pattern, {
                onMatch: function(address, size) {
                    console.log("[+] match at", address, "size", size);

                    console.log(hexdump(address, { length: 64 }));

                    var flagString = Memory.readCString(address);

                    console.log("Flag:", flagString);
                },
                onComplete: function() {
                    console.log("[*] scan complete");
                },
                onError: function(reason) {
                    console.log("[-] scan error:", reason);
                }
            });
        }
    }, 2000);
});
