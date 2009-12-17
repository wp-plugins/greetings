<?php
    if(!current_user_can('manage_options')) {
	    die('Access Denied');
    }

    if($_GET["page"]=='greetings/greetings-images.php'){
        if($_POST["action"]=="delete"){
            $selected_greetings_images = $_POST["greetings_images"];

            if(empty($selected_greetings_images)){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                foreach($selected_greetings_images as $selected_greeting_image){
                    delete_greeting_image($selected_greeting_image);
                }

                echo('<div id="message" class="updated fade"><p><strong>Deleted.</strong></p></div>');
            }
        }

        if($_POST["action"]=="upload"){
            $greeting_image_name = $_FILES["greeting_image"]["name"];
            $greeting_image_tmp_name = $_FILES["greeting_image"]["tmp_name"];

            $extensions = array("jpg", "png", "jpeg", "gif");

            $image_extension = substr(strrchr($greeting_image_name, "."), 1);

            $is_greeting_image_uploaded = false;

            if(array_search($image_extension,$extensions)!==false){
                if(move_uploaded_file($greeting_image_tmp_name, dirname(__FILE__)."/images/".$greeting_image_name)){
                    $is_greeting_image_uploaded = true;
                }
            }

            if(!$is_greeting_image_uploaded){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                echo('<div id="message" class="updated fade"><p><strong>Uploaded.</strong></p></div>');
            }
        }
    }

    $greetings_images = get_greetings_images();

    ?>
    <div class="wrap">
      <h2>Edit <?php echo $greetings_plugin_title; ?> Images</h2>
      <script type="text/javascript">
      function submit_manage_greetings_form(action){
         document.getElementById("action").value = action;
         document.getElementById("manage_greetings_images_form").submit();
      }
      </script>
      <style>
        .widefat td {
        	padding: 3px 7px;
        	vertical-align: top;
        }

        .widefat tbody th.check-column {
        	padding: 7px 0;
            vertical-align: top;
        }
      </style>
      <form name="manage_greetings_images_form" id="manage_greetings_images_form" method="post">
        <table class="widefat fixed" cellspacing="0">
        	<thead>
        	<tr>
        	<th scope="col" id="cb" class="check-column" style=""><input type="checkbox" /></th>
        	<th scope="col" style="width: 65%">Image</th>
            <th scope="col" style="width: 15%">Dimensions</th>
            <th scope="col" style="width: 15%">Size</th>
            <th scope="col" style="width: 15%">Type</th>
        	</tr>
        	</thead>

        	<tfoot>
        	<tr>
        	<th scope="col" class="check-column" style=""><input type="checkbox" /></th>
            <th scope="col">Image</th>
            <th scope="col">Dimensions</th>
            <th scope="col">Size</th>
            <th scope="col">Type</th>
        	</tr>
        	</tfoot>

        	<tbody>
            <?php for($i=0; $i<count($greetings_images); $i++){ ?>
        	<tr class='alternate' valign="top">
        		<th scope="row" class="check-column"><input type="checkbox" name="greetings_images[]" value="<?php echo($greetings_images[$i]); ?>" /></th>
        		<td>
                    <img src="<?php echo($greetings_plugin_url); ?>images/<?php echo($greetings_images[$i]); ?>" alt="<?php echo(greetings_image_alt($greetings_images[$i])); ?>">
        		</td>
                <td>
                    <?php
                     list($file_width,$file_height) = getimagesize(dirname(__FILE__)."/images/".$greetings_images[$i]);
                     echo($file_width." x ".$file_height." pixels");
                    ?>
        		</td>
                <td>
                    <?php echo(number_format(filesize(dirname(__FILE__)."/images/".$greetings_images[$i])/1024, 2, '.', '')." KB"); ?>
        		</td>
                <td>
                    <?php echo(substr(strrchr(dirname(__FILE__)."/images/".$greetings_images[$i], "."), 1)); ?>
        		</td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <p class="submit">
          <input name="delete" type="submit" value="Delete" onclick="submit_manage_greetings_form(this.name);" />
          <input type="hidden" id="action" name="action" value="" />
        </p>
      </form>

      <h2>Upload new image</h2>
      <form enctype="multipart/form-data" method="post">
        <input type="file" name="greeting_image">
        <p class="submit">
          <input name="upload" type="submit" value="Upload" />
          <input type="hidden" id="action" name="action" value="upload" />
        </p>
      </form>
    </div>