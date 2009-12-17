<?php
    if(!current_user_can('manage_options')) {
	    die('Access Denied');
    }

    if($_GET["page"]=='greetings/greetings-edit.php'){
        if($_POST["action"]=="approve"){
            $selected_greetings = $_POST["greetings"];

            if(empty($selected_greetings)){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                foreach($selected_greetings as $selected_greeting){
                    approve_greeting($selected_greeting);
                }

                echo('<div id="message" class="updated fade"><p><strong>Approved.</strong></p></div>');
            }
        }elseif($_POST["action"]=="unapprove"){
            $selected_greetings = $_POST["greetings"];

            if(empty($selected_greetings)){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                foreach($selected_greetings as $selected_greeting){
                    unapprove_greeting($selected_greeting);
                }

                echo('<div id="message" class="updated fade"><p><strong>Unapproved.</strong></p></div>');
            }
        }elseif($_POST["action"]=="delete"){
            $selected_greetings = $_POST["greetings"];

            if(empty($selected_greetings)){
                echo('<div id="message" class="updated fade"><p><strong>Error.</strong></p></div>');
            }else{
                foreach($selected_greetings as $selected_greeting){
                    delete_greeting($selected_greeting);
                }

                echo('<div id="message" class="updated fade"><p><strong>Deleted.</strong></p></div>');
            }
        }
    }

    $greetings = get_greetings();

    ?>
    <div class="wrap">
      <h2>Edit <?php echo $greetings_plugin_title; ?></h2>
      <script type="text/javascript">
      function submit_edit_greetings_form(action){
         document.getElementById("action").value = action;
         document.getElementById("edit_greetings_form").submit();
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
      <form name="edit_greetings_form" id="edit_greetings_form" method="post">
        <table class="widefat fixed" cellspacing="0">
        	<thead>
        	<tr>
        	<th scope="col" id="cb" class="check-column" style=""><input type="checkbox" /></th>
        	<th scope="col" style="width: 15%">Name</th>
            <th scope="col" style="width: 15%">Email</th>
        	<th scope="col" style="width: 50%">Greeting</th>
            <th scope="col" style="width: 13%">Date</th>
            <th scope="col" style="width: 7%">Approved</th>
        	</tr>
        	</thead>

        	<tfoot>
        	<tr>
        	<th scope="col" class="check-column" style=""><input type="checkbox" /></th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
        	<th scope="col">Greeting</th>
            <th scope="col">Date</th>
            <th scope="col">Approved</th>
        	</tr>
        	</tfoot>

        	<tbody>
            <?php for($i=0; $i<count($greetings); $i++){ ?>
        	<tr class='alternate' valign="top">
        		<th scope="row" class="check-column"><input type="checkbox" name="greetings[]" value="<?php echo($greetings[$i]['id']); ?>" /></th>
        		<td>
                    <?php echo(esc_attr($greetings[$i]['name'])); ?>
        		</td>
                <td>
                    <?php echo(esc_attr($greetings[$i]['email'])); ?>
        		</td>
                <td>
                    <?php echo(convert_smilies($greetings[$i]['greeting'])); ?>
        		</td>
                <td>
                    <?php echo(date(get_option("date_format").", ".get_option("time_format"),intval($greetings[$i]['date']))); ?>
        		</td>
                <td>
                    <?php echo($greetings[$i]['approved']); ?>
        		</td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <p class="submit">
          <input name="approve" type="submit" value="Approve" onclick="submit_edit_greetings_form(this.name);" />
          <input name="unapprove" type="button" value="Unapprove" onclick="submit_edit_greetings_form(this.name);" />
          <input name="delete" type="submit" value="Delete" onclick="submit_edit_greetings_form(this.name);" />
          <input type="hidden" id="action" name="action" value="" />
        </p>
      </form>
</div>