Java.perform(function () {
    setTimeout(function () {
  
      Java.choose("com.mobilehackinglab.challenge.MainActivity" , {
        onMatch : function(instance){ 
          console.log("Instance: " + instance);
          console.log("call KLOW method: " + instance.KLOW());
        },
        onComplete:function(){}
      
      });
    }, 2000);
  
    setTimeout(function () {
      Java.choose("com.mobilehackinglab.challenge.Activity2" , {
          onMatch : function(instance){ 
            console.log("Instance: "+instance);
            console.log("cd method: " + instance.cd());
          },
          onComplete:function(){}
        
        });
      }, 2000);

});
