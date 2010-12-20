<?php
header("content-type: application/x-javascript");
$greetings_plugin_url = $_GET["greetings_plugin_url"];
?>

function show_add_greetings_panel(){
    document.getElementById("add_greetings_panel").style.display = "";
}

function hide_add_greetings_panel(){
    document.getElementById("add_greetings_panel").style.display = "none";
}

function add_greetings(){
  sender_name = document.getElementById("greeting_sender_name").value;
  sender_email = document.getElementById("greeting_sender_email").value;
  greeting = document.getElementById("greeting_text").value;
  captcha = document.getElementById("greeting_captcha").value;

  greeting_parameters = "greeting_sender_name="+sender_name+"&greeting_sender_email="+sender_email+"&greeting="+greeting+"&captcha="+captcha;

  if(window.XMLHttpRequest){
        request = new XMLHttpRequest();
        request.onreadystatechange = processRequestChange;
        request.open("POST", "<?php echo($greetings_plugin_url); ?>greetings-add.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.setRequestHeader("Content-length", greeting_parameters.length);
        request.setRequestHeader("Connection", "close");
        request.send(greeting_parameters);
  }else{
    if(window.ActiveXObject){
        request = new ActiveXObject("Microsoft.XMLHTTP");
            if(request){
                request.onreadystatechange = processRequestChange;
                request.open("POST", "<?php echo($greetings_plugin_url); ?>greetings-add.php", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.setRequestHeader("Content-length", greeting_parameters.length);
                request.setRequestHeader("Connection", "close");
                request.send(greeting_parameters);
            }
        }
    }
}

function processRequestChange() {
   if(request.readyState==4){
    if(request.status==200){
        alert(request.responseText);

        document.getElementById("greetings_add_loading").style.display = "none";
        document.getElementById("add_greetings_button").style.display = "";

        if(request.responseText=="Your greeting has added. Thank You."){
          hide_add_greetings_panel();
          document.getElementById("greeting_sender_name").value = "Name";
          document.getElementById("greeting_sender_email").value = "Email (will not be published)";
          document.getElementById("greeting_text").value = "Greeting";
          document.getElementById("greeting_captcha_image").src = "<?php echo($greetings_plugin_url); ?>captcha/captcha_image.php?"+Math.random();
          document.getElementById("greeting_captcha").value = "Security Code";
        }

    }else{
      alert("Cannot add the greeting\n" + request.statusText);
    }
  }else{
    document.getElementById("add_greetings_button").style.display = "none";
    document.getElementById("greetings_add_loading").style.display = "";
  }
}