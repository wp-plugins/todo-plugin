<?php

function todo_plugin_options() {
?>

<div class="wrap">
<h2>ToDo List Settings</h2>
<p>By default any registered user of your site may be chosen from the entire membership and assigned a task. This means scrolling through the list of all users to find the person to receive the assignment. This may be tedious if you have many users. Because WordPress groups users by roles, it may be easier to select a recipient from a smaller group of say "Authors" or "Editors". This page allows you to keep the default total list or you may select a group from which to choose who will get an assignment.</p> 
<p>This plugin is compatible with the Role Manager plugin so you will also be able to select users from groups created by Role Manager.</p>

<form method="post" action="options-general.php?page=todo.php">

<?php
 			global $wpdb;
			 $table_name = $wpdb->prefix . 'pravin_todo_options';
		    $user_group = $wpdb->get_var("SELECT show_role_option FROM $table_name LIMIT 1");
?>
<table class="form-table">
<tr valign="top">
<th scope="row">Current User Group</th>
<td><input type="text" name="current_role" value="<?php echo $user_group; ?>" /></td>
</tr>
 
<tr valign="top">
<th scope="row">Choose User Group</th>
<td><select name="usergroups">
<option value="users">users</option>
<?php
	if ( !isset($wp_roles) ) 
				$wp_roles = new WP_Roles();
			foreach ($wp_roles->get_names() as $role=>$roleName) {
			echo '<option value="'.$role.'">'.$role.'</option>';
			}
?>" /></select></td>
</tr>

</table>

<input type="hidden" name="operation" value="showroleoption" />
<input type="hidden" name="page_options" value="current_role" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<p>You may go to the <a href="tools.php?page=todo">Add ToDo page</a> to enter an assignment for a user from the group you have chosen.
</div>

<?php
}
?>