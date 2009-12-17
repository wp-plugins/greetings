<?php   
$path  = '';

if(!defined('WP_LOAD_PATH')){
	$root = dirname(dirname(dirname(dirname(__FILE__)))).'/';

	if(file_exists($root.'wp-load.php')){
        define('WP_LOAD_PATH',$root);
	}else{
        if(file_exists($path.'wp-load.php')){
            define('WP_LOAD_PATH',$path);
        }else{
            exit("Cannot find wp-load.php");
        }
	}
}

require_once(WP_LOAD_PATH.'wp-load.php');

session_start();

global $wpdb;
$greetings_table_name = $wpdb->prefix."greetings";
$greetings_plugin_prefix = "greetings_";

$greetings_options = get_option($greetings_plugin_prefix."options");
$greetings_only_logged = $greetings_options["only_logged"];

if($greetings_only_logged=="Yes" && !is_user_logged_in()){
    exit("You must be logged in to send a greeting.");
}

$greeting_sender_name = $_POST["greeting_sender_name"];
$greeting_sender_email = $_POST["greeting_sender_email"];
$greeting = $_POST["greeting"];
$greeting_captcha = $_POST["captcha"];
$greeting_added_date = time();

function is_valid_email($email) {
  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
    return false;
  }
  return true;
}

function add_greeting($name,$email,$greeting,$captcha,$date){
    global $wpdb;
    global $greetings_table_name;

    $name = trim(strip_tags($name));
    $email = trim(strip_tags($email));
    $greeting = nl2br(trim(strip_tags($greeting)));
    $date = intval($date);

    if($name=="" || $name=="Name"){
        return "Name Error";
    }

    if($email=="" || $email=="Email (will not be published)" || !is_valid_email($email)){
        return "Email Error";
    }

    if($greeting=="" || $greeting=="Text"){
        return "Greeting Error";
    }

    if(md5($captcha)!=$_SESSION["captcha"]){
        return "CAPTCHA Error";
    }

    $add_greeting_query = "INSERT INTO ".$greetings_table_name." (name, email, greeting, date, approved) VALUES ('".$name."','".$email."','".$greeting."','".$date."','No')";

    $wpdb->query($add_greeting_query);

    return true;
}
    $add_greeting_status = add_greeting($greeting_sender_name,$greeting_sender_email,$greeting,$greeting_captcha,$greeting_added_date);

    if($add_greeting_status===true){
        echo("Your greeting has added. It will be shown after approval. Thank You.");
    }elseif($add_greeting_status=="Name Error"){
        echo("Please enter your name.");
    }elseif($add_greeting_status=="Email Error"){
        echo("Please enter a valid email.");
    }elseif($add_greeting_status=="Greeting Error"){
        echo("Please enter your greeting.");
    }elseif($add_greeting_status=="CAPTCHA Error"){
        echo("The security code is wrong.");
    }else{
        echo("Cannot add the greeting.\n".mysql_error());
    }
?>