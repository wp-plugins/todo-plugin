<?php
/*
Plugin Name: todo-plugin
Plugin URI: http://www.DustyAnt.com/
Description: Lets you create and manage a todo list. To show your todo list, just put <code>&lt;?php pravin_todo(); ?&gt;</code> in your template.
Version: 0.2
Author: Pravin Paratey
Author URI: http://www.DustyAnt.com
*/
/*  Copyright 2007 Pravin Paratey (pravinp@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
*/

class pravin {
	
	// --------------------------------------------------------------------
	// Responsible for installing the plugin
	// --------------------------------------------------------------------
	function todo_install() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'pravin_todo';
		
		// Check if the table is already present
		if($wpdb->get_var("SHOW TABLES LIKE '" . $table_name ."'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
				task_id 	bigint(20) 	not null auto_increment,
				task_desc	text		not null default '',
				task_owner 	bigint(20) 	not null,
				assigned_by bigint(30) 	not null,
				date_due 	bigint(11) 	not null default '0',
				date_created bigint(11)	not null default '0',
				priority	smallint(3)	not null default '1',
				notes		text		not null default '',
				status		tinyint(1)	not null default '0',
				unique key id(task_id)
				);";
				
			$results = $wpdb->query($sql);
			
			$table_name = $wpdb->prefix . 'pravin_todo_options';
			$sql = "CREATE TABLE $table_name (
				option_id		smallint(3)	not null,
				
				show_limit		smallint(3)	default '1',
				show_completed	tinyint(1)	default '0',
				show_duetoday	tinyint(1)	default '0',
				
				show_spectrum	tinyint(1)	default '1',
				hot_color		tinytext,
				cold_color		tinytext,
				
				format_date_due	tinytext,
				format_date_creat tinytext,
				
				sort_field1		tinytext,
				sort_order1 	tinytext,
				sort_field2		tinytext,
				sort_order2 	tinytext,
				
				show_task_id	tinyint(1)	default '0',
				show_task_owner	tinyint(1)	default '1',
				show_assigned_by tinyint(1) default '0',
				show_date_due	tinyint(1)	default '1',
				show_date_creat	tinyint(1)	default '0',
				show_priority	tinyint(1)	default '1',
				
				show_notes		tinyint(0)	default '0',
				column_order	text		default '',
				timezone_offset	tinytext,
				unique key id(option_id)
				);";
			
			$results = $wpdb->query($sql);

			// Add initial data
			$sql = "INSERT INTO `$table_name` (show_limit, sort_field1, sort_order1, sort_field2, sort_order2) " . 
				" VALUES ('5', 'status', 'DESC', 'date_due', 'DESC')";
			$results = $wpdb->query($sql);
		}
	}
	
	// --------------------------------------------------------------------
	// Adds the ToDo page under Manage
	// --------------------------------------------------------------------
	function todo_addpages() {
		add_management_page('Manage your ToDo list', 'ToDo', 8, 'todo', array('pravin', 'todo_addoption'));
	}
	
	// --------------------------------------------------------------------
	// Responsible for rendering the ToDo page under Manage
	// --------------------------------------------------------------------
	function todo_addoption()
	{		
		global $wpdb;
		
		$output_html = '<div class="wrap">	
	<h2>ToDo List</h2>
	<table cellpadding="5" cellspacing="2" width="100%">
	<tbody>
		<$ToDoList$>
	</tbody>
	</table>
	<p>&nbsp;</p>
	<h2>Add a ToDo</h2>
	<form name="addtodo" id="addtodo" action="edit.php?page=todo" method="post">
	<table cellpadding="5" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td align="right" width="20%"><label for="task">Task: </label></td>
		<td><input name="task" size="80"/></td>
	</tr>
	<tr>
		<td align="right"><label for="month">Due: </label></td>
		<td>
			<$ToDoAdd$>
		</td>
		</tr>
		<tr>
			<td align="right"><label for="taskowner">Assign to:</label></td>
			<td>
			<select name="taskowner">
				<$UserList$>
			</select>
			</td>
		</tr>
		<tr>
			<td align="right"><label for="priority">Priority:</label></td>
			<td>
				<select name="priority">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"><label for="notes">Notes:</label></td>
			<td>
				<textarea name="notes" rows="4" cols="60"></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
			<input type="hidden" name="operation" value="add" />
			<input type="submit" name="submit" value="Add ToDo" />
			</td>
		</tr>
	</tbody>
	</table>
	</form>
	<p>&nbsp;</p>
	<h2>Display Options</h2>
	<form action="edit.php?page=todo" method="POST">
	<table cellpadding="5" cellspacing="2" width="100%">
	<tbody>
		<tr>
			<td width="20%" align="right"><label for="limit">Number of tasks to show:</label></td>
			<td>
				<input type="text" name="limit" value="<$ShowLimit$>" size="4" maxsize="4" />
			</td>
		</tr>
		<tr>
			<td width="20%" align="right"><label for="showcompleted">Show Completed Tasks:</label></td>
			<td>
				<select name="showcompleted">
				<$ShowCompleted$>
				</select>
			</td>
		</tr>
		<tr>
			<td width="20%" align="right"><label for="showduetoday">Only show tasks due today:</label></td>
			<td>
				<select name="showduetoday">
				<$ShowDueToday$>
				</select>
			</td>
		</tr>
		<tr>
			<td width="20%" align="right"><label for="sortby1">Sort By (Field 1):</label></td>
			<td>
				<select name="sortby1">
				<$SortBy1$>
				</select>
				<select name="sortorder1">
				<$SortOrder1$>
				</select>
			</td>
		</tr>
		<tr>
			<td width="20%" align="right"><label for="sortby2">Sort By (Field 2):</label></td>
			<td>
				<select name="sortby2">
				<$SortBy2$>
				</select>
				<select name="sortorder2">
				<$SortOrder2$>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type="hidden" name="operation" value="options" /></td>
			<td>
				<input type="submit" value="Save Options" />
			</td>
		</tr>
	</tbody>
	</table>
	</form>
	<p>&nbsp;</p>
	<h2>Advanced</h2>
	<p>If you are upgrading from v0.1 to v0.2, use this to DROP your table. <b>You will lost all ToDo data and settings!</b> After you press the button, you will get an error. It is expected. Deactivate the plugin and activate it again to recreate your tables.</p>
	<form action="edit.php?page=todo" method="POST"><input type="hidden" name="operation" value="drop" /><input type="submit" value="Drop Table" /></form>
</div>';
		
		$table_name = $wpdb->prefix . 'pravin_todo';
		$sql = "select * from $table_name order by status ASC, date_due ASC ";
		$results = $wpdb->get_results($sql);
		
		$alt = 0;
		
		$todolist = '<tr style="background-color:#69c; color:#fff"><td>Date Due</td><td>Description</td><td>Task Owner</td><td>Priority</td><td>Assigned By</td><td>Action</td></tr>';
		if(count($results) > 0)
		{
			foreach($results as $result)
			{
				if(1 == $result->status)
				{
					$todolist .= '<tr bgcolor="#ccffcc">';
				}
				else if($result->date_due < time())
				{
					$todolist .= '<tr bgcolor="#ffcccc">';
				}
				else
				{
					$todolist .= '<tr>';
				}
				
				$assigned_by = $wpdb->get_var("SELECT user_nicename FROM `$wpdb->users` WHERE ID = $result->assigned_by LIMIT 1");
				$assigned_to = $wpdb->get_var("SELECT user_nicename FROM `$wpdb->users` WHERE ID = $result->task_owner LIMIT 1");
				
				// 'F jS, Y @ H:i'
				$todolist .= '<td><div title="' . gmdate('F jS, Y @ H:i', $result->date_due) . '">' . gmdate('m/d/y H:i', $result->date_due) . '</div></td>'. 
					'<td><div title="' . $result->notes . '">' .$result->task_desc . '</td><td>' .
					$assigned_to . '</td><td>' .
					$result->priority . '</td>' . 
					'<td><div title="Assigned on ' . gmdate('F jS, Y @ H:i', $result->date_created) . '">' . $assigned_by . '</td><td>' .
					'<form action="edit.php?page=todo" method="post">
						<select name="dowhat">
							<option value="';
						if($result->status == 0) {
							$todolist .= 'done">Mark Done';
						}
						else {
							$todolist .= 'undone">Mark Undone';
						}
				$todolist .= '</option>
							<option value="delete">Delete</option>
						</select>
						<input type="hidden" name="operation" value="update" />
						<input type="hidden" name="id" value="' . $result->task_id . '" />
						<input type="submit" value="Go!" />
					</form></td></tr>';
			}
		}
		
		$output_html = str_replace('<$ToDoList$>', $todolist, $output_html);

		$month_array = array('January', 'February', 'March', 'April', 
			'May', 'June', 'July', 'August', 
			'September', 'October', 'November', 'December');	
		$cur_month = date('n');
		$month_html = '';
		for($i=1; $i < 13; $i++)
		{
			$month_html .= '<option value="' . $i;
			if($cur_month == $i)
			{
				$month_html .= '" selected="selected';
			}
			$month_html .= '">' . $month_array[$i-1] . '</option>';
		}
			
		$month_html = '<select name="month">' . $month_html . '</select>' .
			' <input name="day" size="2" maxlength="2" value="' . date('d') . '" />, ' .
			'<input name="year" size="4" maxlength="4" value="' . date('Y') . '" /> @ ' .
			'<input name="hour" size="2" maxlength="2" value="' . date('H') . '" /> : ' .
			'<input name="minute" size="2" maxlength="2" value="' . date('i') . '" /> hrs';
			
		$output_html = str_replace('<$ToDoAdd$>', $month_html, $output_html);
			
		$users = $wpdb->get_results("SELECT * FROM `$wpdb->users`");
		$userlist = '';
		foreach($users as $user)
		{
			$userlist .= '<option value="' . $user->ID . '">' . $user->user_nicename . '</option>';
		}
		$output_html = str_replace('<$UserList$>', $userlist, $output_html);

		
		// Get Show Completed 
		$table_name = $wpdb->prefix . 'pravin_todo_options';
		
		$op = $wpdb->get_var("SELECT show_limit FROM $table_name LIMIT 1");
		$output_html = str_replace('<$ShowLimit$>', $op, $output_html);
		
		$op = $wpdb->get_var("SELECT show_completed FROM $table_name LIMIT 1");
		$option_html = '';
		if(1 == $op)
		{
			$option_html = '<option value="1" selected="selected">Yes</option><option value="0">No</option>';
		}
		else
		{
			$option_html = '<option value="1">Yes</option><option value="0" selected="selected">No</option>';
		}
		$output_html = str_replace('<$ShowCompleted$>', $option_html, $output_html);
		
		// Get Due Today
		$op = $wpdb->get_var("SELECT show_duetoday FROM $table_name LIMIT 1");
		$option_html = '';
		if(1 == $op)
		{
			$option_html = '<option value="1" selected="selected">Yes</option><option value="0">No</option>';
		}
		else
		{
			$option_html = '<option value="1">Yes</option><option value="0" selected="selected">No</option>';
		}
		$output_html = str_replace('<$ShowDueToday$>', $option_html, $output_html);

		// Get sort field 1
		$op = $wpdb->get_var("SELECT sort_field1 FROM $table_name LIMIT 1");
		$option_html = '';
		if('date_due' == $op)
		{
			$option_html = '<option value="date_due" selected="selected">Date due</option><option value="priority">Priority</option><option vale="status">Status</option>';
		}
		else if('priority' == $op)
		{
			$option_html = '<option value="date_due">Date due</option><option value="priority" selected="selected">Priority</option><option vale="status">Status</option>';
		}
		else
		{
			$option_html = '<option value="date_due">Date due</option><option value="priority">Priority</option><option vale="status" selected="selected">Status</option>';
		}
		$output_html = str_replace('<$SortBy1$>', $option_html, $output_html);
		
		// Get sort field 2
		$op = $wpdb->get_var("SELECT sort_field2 FROM $table_name LIMIT 1");
		$option_html = '';
		if('date_due' == $op)
		{
			$option_html = '<option value="date_due" selected="selected">Date due</option><option value="priority">Priority</option><option vale="status">Status</option>';
		}
		else if('priority' == $op)
		{
			$option_html = '<option value="date_due">Date due</option><option value="priority" selected="selected">Priority</option><option vale="status">Status</option>';
		}
		else
		{
			$option_html = '<option value="date_due">Date due</option><option value="priority">Priority</option><option vale="status" selected="selected">Status</option>';
		}
		$output_html = str_replace('<$SortBy2$>', $option_html, $output_html);				
				
		// Get Sort Order 1
		$op = $wpdb->get_var("SELECT sort_order1 FROM $table_name LIMIT 1");
		$option_html = '';
		if('asc' == $op)
		{
			$option_html = '<option value="asc" selected="selected">Ascending</option><option value="desc">Descending</option>';
		}
		else
		{
			$option_html = '<option value="asc">Ascending</option><option value="desc" selected="selected">Descending</option>';
		}
		$output_html = str_replace('<$SortOrder1$>', $option_html, $output_html);
		
		// Get Sort Order 2
		$op = $wpdb->get_var("SELECT sort_order2 FROM $table_name LIMIT 1");
		$option_html = '';
		if('asc' == $op)
		{
			$option_html = '<option value="asc" selected="selected">Ascending</option><option value="desc">Descending</option>';
		}
		else
		{
			$option_html = '<option value="asc">Ascending</option><option value="desc" selected="selected">Descending</option>';
		}
		$output_html = str_replace('<$SortOrder2$>', $option_html, $output_html);
		
		echo $output_html;
	}
}

// --------------------------------------------------------------------
// Widgetize!
// --------------------------------------------------------------------
function widget_pravin_todo() {

	function widget_todo($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$title = $options['title'];

		echo $before_widget . $before_title . $title . $after_title;
		echo pravin_get_todo();
		echo $after_widget;
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('ToDo Plugin', 'widgets'), 'widget_todo');
}

// --------------------------------------------------------------------
// Called when user clicks activate in the plugin menu
// --------------------------------------------------------------------
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	if (defined('WPINC') && strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) > 0) {
		add_action('init', array('pravin', 'todo_install'));
	}
}
// --------------------------------------------------------------------
// Insert the mt_add_pages() sink into the plugin hook list for 'admin_menu'
// --------------------------------------------------------------------
add_action('admin_menu', array('pravin', 'todo_addpages'));

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_pravin_todo');


// --------------------------------------------------------------------
// Handle any add/delete/update requests
// --------------------------------------------------------------------
$name = $_POST["operation"];
if('add' == $name) {
	global $wpdb;
	
	// I don't know how to get current user :(
	// wp-includes/pluggable-functions line 30 looks promising
	$assigned_by = '1';
	
	$table_name = $wpdb->prefix . 'pravin_todo';
	$date_due = mktime($_POST["hour"], $_POST["minute"], 0, $_POST["month"], $_POST["day"], $_POST["year"]);
	$sql = "INSERT INTO `$table_name` (task_desc, task_owner, assigned_by, date_due, date_created, priority, notes)  VALUES( '" .
			$wpdb->escape($_POST["task"]) . "' , '" . 
			$_POST['taskowner'] . "' , '" . 
			$assigned_by . "', '" . 
			$date_due . "', '" . 
			time() . "', '" .
			$_POST['priority'] . "', '" .
			$wpdb->escape($_POST['notes']) . "')";
	$results = $wpdb->query($sql);
}
else if('update' == $name) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pravin_todo';
	$sql = '';
	
	$dowhat = $_POST["dowhat"];
	$id = $_POST["id"];
	if('delete' == $dowhat)
	{
		$sql = "DELETE FROM $table_name WHERE task_id=$id";
	}
	else if('done' == $dowhat)
	{
		$sql = "UPDATE $table_name SET status='1' WHERE task_id=$id";
	}
	else
	{
		$sql = "UPDATE $table_name SET status='0' WHERE task_id=$id";
	}
	
	$results = $wpdb->query($sql);
}
else if('drop' == $name) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pravin_todo';
	$table_options = $table_name . '_options';
	
	$wpdb->query("DROP TABLE `$table_name`, `$table_options`;");
}
else if('options' == $name) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pravin_todo_options';
	
	$wpdb->query("UPDATE $table_name SET show_limit='" . $_POST['limit'] . 
	"', sort_field1='" . $_POST['sortby1'] . 
	"', sort_order1='" . $_POST['sortorder1'] .
	"', sort_field2='" . $_POST['sortby2'] .
	"', sort_order2='" . $_POST['sortorder2'] .
	"', show_completed='" . $_POST['showcompleted'] . 
	"', show_duetoday='" . $_POST['showduetoday'] . "'");
}

function pravin_get_todo()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'pravin_todo';
	$table_name_options = $wpdb->prefix . 'pravin_todo_options';
	
	$sql = "SELECT * from `$table_name_options` LIMIT 1";
	$options = $wpdb->get_results($sql);
	$option = $options[0];
	
	$where_clause = '';
	if($option->show_completed == '0')
	{
		$where_clause = " WHERE status='0' ";
	}
	$sql = "SELECT * from `$table_name` $where_clause ORDER BY $option->sort_field1 $option->sort_order1, $option->sort_field2 $option->sort_order2 LIMIT $option->show_limit";
	
	$results = $wpdb->get_results($sql);
	$output_html = '<ul>';
	foreach($results as $result)
	{
		$class = 'due';
		
		if($result->status == '1')
		{
			$class = 'done';
		}
		else if($result->date_due < time())
		{
			$class = 'duetoday';
		}
		$output_html .= '<li class="todo-' . $class . '" title="' . date('F jS, Y', $result->date_due) . '">' . $result->task_desc . "</li>";
	}
	$output_html .= '</ul>';
	return $output_html;
}
?>