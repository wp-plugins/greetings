<?php
/*
Plugin Name: Greetings
Plugin URI: http://rubensargsyan.com/wordpress-plugin-greetings/
Description: Any occasion to receive greetings? So, this plugin is just for you! <a href="admin.php?page=greetings.php">Settings</a>
Version: 1.1
Author: s_ruben
Author URI: http://rubensargsyan.com/
*/

/*  Copyright 2009 Ruben Sargsyan (email: info@rubensargsyan.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

$greetings_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
$greetings_plugin_title = "Greetings";
$greetings_plugin_prefix = "greetings_";
$greetings_table_name = $wpdb->prefix."greetings";
$greetings_version = "1.1";

function install_greetings(){
	global $wpdb;
    $greetings_plugin_title = "Greetings";
    $greetings_table_name = $wpdb->prefix."greetings";
    $greetings_plugin_prefix = "greetings_";
    $greetings_version = "1.1";

	$charset_collate = '';
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}

    if($wpdb->get_var("SHOW TABLES LIKE '$greetings_table_name'")!=$greetings_table_name){
	    $create_greetings_table = "CREATE TABLE $greetings_table_name(".
			"id INT(11) NOT NULL auto_increment,".
			"name tinytext NOT NULL,".
            "email VARCHAR(100) NOT NULL,".
			"greeting TEXT NOT NULL,".
            "date INT(11) NOT NULL,".
            "approved VARCHAR(3) NOT NULL DEFAULT 'No',".
			"PRIMARY KEY (id)) $charset_collate;";

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($create_greetings_table);
    }

    set_greetings_default_options();

    $greetings_widget_options = array("title"=>$greetings_plugin_title);

    add_option($greetings_plugin_prefix."widget_options",$greetings_widget_options);

    if(get_option($greetings_plugin_prefix."version")===false){
        add_option($greetings_plugin_prefix."version",$greetings_version);
    }elseif(get_option($greetings_plugin_prefix."version")<$greetings_version){
        update_option($greetings_plugin_prefix."version",$greetings_version);
    }
}

function greetings_menu(){
    if(get_not_approved_greetings_count()>0){
        $not_approved_greetings_count = "<span class='update-plugins count-".get_not_approved_greetings_count()."'><span class='plugin-count'>".get_not_approved_greetings_count()."</span></span>";
    }else{
        $not_approved_greetings_count = '';
    }

    if(function_exists('add_menu_page')){
		add_menu_page(__('Options', 'greetings'), __('Greetings '.$not_approved_greetings_count, 'greetings'), 'manage_options', basename(__FILE__), 'greetings_options');
	}
	if(function_exists('add_submenu_page')){
        add_submenu_page(basename(__FILE__), __('Options', 'greetings'), __('Options', 'greetings'), 'manage_options', basename(__FILE__), 'greetings_options');
		add_submenu_page(basename(__FILE__), __('Edit', 'greetings'), __('Edit', 'greetings'),  'manage_options', 'greetings/greetings-edit.php');
        add_submenu_page(basename(__FILE__), __('Images', 'greetings'), __('Images', 'greetings'),  'manage_options', 'greetings/greetings-images.php');
		add_submenu_page(basename(__FILE__), __('Donate', 'greetings'), __('Donate', 'greetings'),  'manage_options', 'greetings/greetings-donate.php');
	}
}

function set_greetings_default_options(){
    global $greetings_plugin_prefix;

    $greetings_only_logged = "No";
    $greetings_show_avatar = "No";

    $greetings_options = array("only_logged"=>$greetings_only_logged,"show_avatar"=>$greetings_show_avatar);

    add_option($greetings_plugin_prefix."options",$greetings_options);
}

function update_greetings_options($greetings_options){
    global $greetings_plugin_prefix;

    $current_greetings_options = get_greetings_options();

    $greetings_options = array_merge($current_greetings_options,$greetings_options);

    update_option($greetings_plugin_prefix."options",$greetings_options);
}

function get_greetings_options(){
    global $greetings_plugin_prefix;

    $greetings_options = get_option($greetings_plugin_prefix."options");

    return $greetings_options;
}

function greetings_options(){
    global $greetings_plugin_url, $greetings_plugin_title, $greetings_plugin_prefix;

    if($_GET["page"]==basename(__FILE__)){
        if($_POST["action"]=="save"){
            if($_POST[$greetings_plugin_prefix."only_logged"]){
                $greetings_only_logged = "Yes";
            }else{
                $greetings_only_logged = "No";
            }

            if($_POST[$greetings_plugin_prefix."show_avatar"]){
                $greetings_show_avatar = "Yes";
            }else{
                $greetings_show_avatar = "No";
            }

            $greetings_options = array("only_logged"=>$greetings_only_logged,"show_avatar"=>$greetings_show_avatar);

            foreach($greetings_options as $greetings_option => $greetings_option_value){
                if(empty($greetings_option_value)){
                    unset($greetings_options[$greetings_option]);
                }
            }

            update_greetings_options($greetings_options);

            echo('<div id="message" class="updated fade"><p><strong>Saved.</strong></p></div>');
        }elseif($_POST["action"]=="reset"){
            delete_option($greetings_plugin_prefix."options");

            echo('<div id="message" class="updated fade"><p><strong>Reseted.</strong></p></div>');
        }
    }

    if(get_greetings_options()===false){
        set_greetings_default_options();
    }

    $greetings_options = get_greetings_options();
?>
    <div class="wrap">
      <h2><?php echo $greetings_plugin_title; ?> Options</h2>

      <form method="post">
        <table width="100%" border="0" id="greetings_options_table">
          <tr>
            <td width="15%" valign="middle"><strong>Set access options</strong></td>
            <td width="85%">
                <input name="<?php echo($greetings_plugin_prefix); ?>only_logged" id="<?php echo($greetings_plugin_prefix); ?>only_logged" type="checkbox" <?php if($greetings_options["only_logged"]=="Yes"){ echo('checked="checked"'); } ?> />
                <small>Users must be registered and logged in to send a greeting.</small></td>
          </tr>
          <tr>
            <td width="15%" valign="middle"><strong>Show Avatar</strong></td>
            <td width="85%">
                <input name="<?php echo($greetings_plugin_prefix); ?>show_avatar" id="<?php echo($greetings_plugin_prefix); ?>show_avatar" type="checkbox" <?php if($greetings_options["show_avatar"]=="Yes"){ echo('checked="checked"'); } ?> />
            </td>
          </tr>
        </table>
        <p class="submit">
          <input name="save" type="submit" value="Save" />
          <input type="hidden" name="action" value="save" />
        </p>
      </form>
      <form method="post">
        <p class="submit">
          <input name="reset" type="submit" value="Reset" />
          <input type="hidden" name="action" value="reset" />
        </p>
      </form>
    </div>
<?php
}

function greetings_widget_options(){
    if(isset($_POST["greetings_widget_title"])){
        $greetings_widget_options["title"] = strip_tags(stripslashes($_POST["greetings_widget_title"]));

        update_option("greetings_widget_options", $greetings_widget_options);
    }

    $greetings_widget_options = get_option("greetings_widget_options");
    ?>
    <p><label for="greetings_widget_title">Title:</label>
	<input class="widefat" id="greetings_widget_title" name="greetings_widget_title" type="text" value="<?php echo(esc_attr($greetings_widget_options["title"])); ?>" /></p>
    <?php
}

function greetings_widget(){
  global $greetings_plugin_url, $greetings_plugin_title, $greetings_plugin_prefix, $greetings_table_name;
  $greetings_widget_options = get_option($greetings_plugin_prefix."widget_options");
  if($greetings_widget_options["title"]!=""){
    $greetings_widget_title = $greetings_widget_options["title"];
  }else{
    $greetings_widget_title = $greetings_plugin_title;
  }

  $greetings_options = get_option($greetings_plugin_prefix."options");
  $greetings_only_logged = $greetings_options["only_logged"];

  $greetings_images = get_greetings_images();
  $greetings_rand_image = array_rand($greetings_images);
  $greetings_image = $greetings_images[$greetings_rand_image];
?>
  <style>
  #add_greetings_panel{
    border: 1px solid #DDDDDD;
    background: #FFFFFF;
    position: absolute;
    margin-top: -50px;
    margin-left: -50px;    
    padding: 5px;
    z-index: 100;
  }
  </style>

  <h2 class="widgettitle"><?php echo($greetings_widget_title); ?></h2>
  <div style="margin-top: 5px"><img src="<?php echo($greetings_plugin_url); ?>images/<?php echo($greetings_image); ?>" alt="<?php echo(greetings_image_alt($greetings_image)); ?>"></div>

  <?php
  if($greetings_only_logged=="No" || is_user_logged_in()){
  ?>
    <div style="margin-top: 5px"><button onclick="show_add_greetings_panel();">Add Greeting</button></div>
    <div id="add_greetings_panel" style="display: none">
      <div style="font-size: 16px; font-weight: bold; text-align: center">Add Greeting</div>
      <div style="margin-top: 5px">All fields are required.</div>
      <div>
          <div style="margin-top: 5px"><input type="text" name="greeting_sender_name" id="greeting_sender_name" style="width: 290px; background-color: #ffffff; color: #000000; border: 1px solid #000000" value="Name" onfocus="if(this.value=='Name'){ this.value = ''; }" onblur="if(this.value==''){ this.value = 'Name'; }"></div>
          <div style="margin-top: 5px"><input type="text" name="greeting_sender_email" id="greeting_sender_email" style="width: 290px; background-color: #ffffff; color: #000000; border: 1px solid #000000" value="Email (will not be published)" onfocus="if(this.value=='Email (will not be published)'){ this.value = ''; }" onblur="if(this.value==''){ this.value = 'Email (will not be published)'; }"></div>
          <div style="margin-top: 5px"><textarea name="greeting_text" id="greeting_text" style="width: 290px; height: 100px; overflow: auto; background-color: #ffffff; color: #000000; border: 1px solid #000000" onfocus="if(this.value=='Greeting'){ this.value = ''; }" onblur="if(this.value==''){ this.value = 'Greeting'; }">Greeting</textarea></div>
          <div style="margin-top: 5px"><img id="greeting_captcha_image" style="vertical-align: middle" src="<?php echo($greetings_plugin_url); ?>captcha/captcha_image.php" alt="Security Code"> <input type="text" name="greeting_captcha" id="greeting_captcha" maxlength="15" style="width: 100px; vertical-align: middle; background-color: #ffffff; color: #000000; border: 1px solid #000000" value="Security Code" onfocus="if(this.value=='Security Code'){ this.value = ''; }" onblur="if(this.value==''){ this.value = 'Security Code'; }"></div>
          <div style="margin-top: 5px"><button id="add_greetings_button" onclick="add_greetings();">Add</button><img src="<?php echo($greetings_plugin_url); ?>javascript/loading.gif" id="greetings_add_loading" style="display: none"></div>
      </div>
      <div style="text-align: center; margin-top: 5px"><button onclick="hide_add_greetings_panel();">Close</button></div>
    </div>
  <?php
  }else{
  ?>
    <div style="margin-top: 5px">You must be <a href="<?php echo wp_login_url(get_permalink()); ?>">logged in</a> to send a greeting.</div>
  <?php
  }
}

function get_greetings_images(){
  global $greetings_plugin_url;

  $dirname = dirname(__FILE__)."/images";

  $extensions = array("jpg", "png", "jpeg", "gif");
  $images = array();

  if($handle = opendir($dirname)){
      while(false!==($file=readdir($handle))){
          if(array_search(substr(strrchr($file, "."), 1),$extensions)!==false){
            $images[] = $file;
          }
      }

      closedir($handle);
  }

  return($images);
}

function greetings_image_alt($img){
  $image_alt = str_replace(substr(strrchr($img, "."), 0),"",$img);
  
  return ucfirst(str_replace("_"," ",$image_alt));
}

function greetings_init(){
  global $greetings_plugin_title;

  if(!isset($_SESSION)){
    session_start();
  }

  wp_register_sidebar_widget("greetings",$greetings_plugin_title, 'greetings_widget');
  register_widget_control($greetings_plugin_title, 'greetings_widget_options');
}

function get_greetings(){
    global $wpdb;
    global $greetings_table_name;

    $get = "SELECT * FROM ".$greetings_table_name." ORDER BY date DESC;";

    $results = $wpdb->get_results($get);

    $greetings = array();
    $i = 0;

    if($results){
		foreach($results as $result) {
		    $greetings[$i]["id"] = intval($result->id);
			$greetings[$i]["name"] = $result->name;
			$greetings[$i]["email"] = $result->email;
            $greetings[$i]["greeting"] = $result->greeting;
            $greetings[$i]["date"] = $result->date;
            $greetings[$i]["approved"] = $result->approved;

            $i++;
		}
	}

    return $greetings;
}

function get_not_approved_greetings_count(){
    global $wpdb;
    global $greetings_table_name;

    $get = "SELECT * FROM ".$greetings_table_name." WHERE approved='No' ORDER BY date DESC;";

    $results = $wpdb->get_results($get);

    return count($results);
}

function approve_greeting($selected_greeting){
    global $wpdb;
    global $greetings_table_name;

    $selected_greeting = intval($selected_greeting);

    if(empty($selected_greeting)){
        return false;
    }

    $approve_greeting_query = "UPDATE ".$greetings_table_name." SET approved='Yes' WHERE id='$selected_greeting'";

    $wpdb->query($approve_greeting_query);

    return true;
}

function unapprove_greeting($selected_greeting){
    global $wpdb;
    global $greetings_table_name;

    $selected_greeting = intval($selected_greeting);

    if(empty($selected_greeting)){
        return false;
    }

    $unapprove_greeting_query = "UPDATE ".$greetings_table_name." SET approved='No' WHERE id='$selected_greeting'";

    $wpdb->query($unapprove_greeting_query);

    return true;
}

function delete_greeting($selected_greeting){
    global $wpdb;
    global $greetings_table_name;

    $selected_greeting = intval($selected_greeting);

    if(empty($selected_greeting)){
        return false;
    }

    $delete_greeting_query = "DELETE FROM ".$greetings_table_name." WHERE id='$selected_greeting'";

    $wpdb->query($delete_greeting_query);

    return true;
}

function delete_greeting_image($selected_greeting_image){
    if(file_exists(dirname(__FILE__)."/images/".$selected_greeting_image)){
        unlink(dirname(__FILE__)."/images/".$selected_greeting_image);
        return true;
    }

    return false;
}

wp_enqueue_style('greetings', $greetings_plugin_url.'css/greetings-style.css');
wp_enqueue_script('greetings', $greetings_plugin_url.'javascript/greetings-javascripts.php?greetings_plugin_url='.$greetings_plugin_url);

add_filter('the_content', 'display_greetings');

function display_greetings($content){
  $greetings_options = get_greetings_options();
  $greetings = get_greetings();

  $greetings_content = '<div class="greetings">';

  for($i=0; $i<count($greetings); $i++){
    if($greetings[$i]["approved"]=="No"){
        continue;
    }

    if($i==0){
      $is_first = "last_greeting";
    }else{
      $is_first = "not_last_greeting";
    }

    $greetings_content .= '<div class="greeting '.$is_first.'"><div>';
    if($greetings_options["show_avatar"]=="Yes"){
        $greetings_content .= get_avatar($greetings[$i]["email"],32).' ';
    }
    $greetings_content .= '<span class="greeting_sender_name">'.$greetings[$i]["name"].'</span> says:</div><div class="greeting_date">'.date(get_option("date_format").", ".get_option("time_format"),$greetings[$i]['date']).'</div><div class="greeting_text">'.convert_smilies($greetings[$i]["greeting"]).'</div></div>';
  }

  $greetings_content .= "</div>";

  $content = str_replace("[greetings]",$greetings_content,$content);

  return $content;
}

register_activation_hook(__FILE__,'install_greetings');
add_action('admin_menu', 'greetings_menu');
add_action("plugins_loaded", "greetings_init");
?>