function show_add_greetings_panel(){
    document.getElementById("add_greetings_panel").style.display = "";
}

function hide_add_greetings_panel(){
    document.getElementById("add_greetings_panel").style.display = "none";
}

function add_greetings(){
  alert(document.getElementById("name").value);
}