<?php
/*
Plugin Name: ToDo
Plugin URI: http://www.DustyAnt.com/
Description: Lets you create and manage a todo list
Version: 0.1
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

/* Notes:
 * Fields - date_opened, due_date, author, text, status
*/

class pravin {

	function todo_install() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'pravin_todo';
		
		// Check if the table is already present
		if($wpdb->get_var("show tables like '" . $table_name ."'") != $table_name) {
			
			$sql = 'create table ' . $table_name . " (
				id mediumint(9) not null auto_increment,
				opened_date bigint(11) default '0' not null,
				due_date bigint(11) not null,
				item text not null,
				status tinyint default '0' not null,
				unique key id(id)
				);";
				
			$results = $wpdb->query($sql);
		}
	}
	
	function todo_addpages() {
		add_management_page('Manage your ToDo list', 'ToDo', 8, 'todo', array('pravin', 'todo_addoption'));
	}
	
	function todo_addoption() {
		
		$month_array = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		
		echo <<<OPTIONFORM0
	<div class="wrap">	
		<h2>ToDo List</h2>
		<table cellpadding="5" cellspacing="2" width="100%">
		<tbody>
OPTIONFORM0;

		global $wpdb;
		$table_name = $wpdb->prefix . 'pravin_todo';
		$sql = "select id, due_date, item, status from " . $table_name . " order by status ASC, due_date ASC ";
		$results = $wpdb->get_results($sql);
		
		$alt = 0;
		
		$output_html = '';
		if(count($results) > 0)
		{
			foreach($results as $result)
			{
				if(1 == $result->status)
				{
					$output_html .= '<tr bgcolor="#ccffcc">';
				}
				else if($result->due_date < time())
				{
					$output_html .= '<tr bgcolor="#ffcccc">';
				}
				else
				{
					$output_html .= '<tr>';
				}
				
				$output_html .= '<td>' . date('M-d-Y', $result->due_date) . '</td><td>'. $result->item . '</td><td>' .
					'<form name="addtodo" id="addtofo" action="edit.php?page=todo" method="post">
						<select name="dowhat">
							<option value="';
						if($result->status == 0) {
							$output_html .= 'done">Mark Done';
						}
						else {
							$output_html .= 'undone">Mark Undone';
						}
				$output_html .= '</option>
							<option value="delete">Delete</option>
						</select>
						<input type="hidden" name="operation" value="update" />
						<input type="hidden" name="id" value="' . $result->id . '" />
						<input type="submit" value="Go!" />
					</form></td></tr>';
			}
		}
		
		echo $output_html;

echo <<<OPTIONFORM0
		</tbody>
		</table>
		
		<h2>Add a ToDo</h2>
		<form name="addtodo" id="addtofo" action="edit.php?page=todo" method="post">
		<table cellpadding="5" cellspacing="2" width="100%">
		<tbody>
		<tr>
			<td><label for="task"><b>Task: </b></label><input name="task" size="80"/></td>
			<td><label for="month"><b>Due: </b></label>
			<select name="month">
OPTIONFORM0;
			
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
			
			echo $month_html . '</select>' ;
			echo '<input name="day" size="2" maxlength="2" value="' . date('d') . '" />';
			echo '<input name="year" size="4" maxlength="4" value="' . date('Y') . '" />';
			echo <<<OPTIONFORM2
			</td>
			<td>
			<input type="hidden" name="operation" value="add" />
			<input type="submit" name="submit" value="Add" />
			</td>
		</tbody>
		</table>
		</form>
	</div>
OPTIONFORM2;
	}
}

// This function called when user clicks activate in the plugin menu
add_action('activate_pravin/todo.php', array('pravin', 'todo_install'));

// Insert the mt_add_pages() sink into the plugin hook list for 'admin_menu'
add_action('admin_menu', array('pravin', 'todo_addpages'));


// Handle any add/delete/update requests
$name = $_POST["operation"];
if('add' == $name) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'pravin_todo';
	$sql = "insert into " . $table_name . " (opened_date, due_date, item) " .
			"values ('" . time() . "', '" . mktime(0, 0, 0, $_POST["month"], $_POST["day"], $_POST["year"]) . 
			"', '" . $wpdb->escape($_POST["task"]) . "');";
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
		$sql = 'delete from ' . $table_name . ' where id=' . $id;
	}
	else if('done' == $dowhat)
	{
		$sql = 'update ' . $table_name . " set status='1' where id=" .$id;
	}
	else
	{
		$sql = 'update ' . $table_name . " set status='0' where id=" .$id;
	}
	
	$results = $wpdb->query($sql);
}


// This function should be called from within your template
// $type = {all, due, duetoday} all => get all, due => get only those due, duetoday => get those due today
// $limit = how many todo's do you want to display
// $before = what you want preceding each item
// $after = what you want following each item
//
// Ex. get_todo('all', 4, '<li>', '</li>');
//
// Note: 
// 1. Each item will be enclosed in <div> tags with the classes 'todo-done', 'todo-due', 'todo-duetoday'
// 2. The due date will be shown on hover
// 3. Due today also means all those tasks whose entries have expired
function pravin_get_todo($type = 'due', $limit = 1000, $before = '' , $after = '')
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'pravin_todo';
	$sql = '';
	if('all' == $type)
	{
		$sql = "select due_date, item, status from " . $table_name . " order by status ASC, due_date ASC";
	}
	else if('due' == $type)
	{
		$sql = "select due_date, item, status from " . $table_name . " where status = '0' order by due_date ASC ";
	}
	
	$results = $wpdb->get_results($sql);
	$output_html = '';
	$count = 1;
	foreach($results as $result)
	{
		if($count > $limit)
			break;
		$count++;
		
		$class = 'due';
		
		if($result->status == '1')
		{
			$class = 'done';
		}
		else if($result->due_date < time())
		{
			$class = 'duetoday';
		}
		$output_html .= $before . '<div class="todo-' . $class . '" title="' . date('F jS, Y', $result->due_date) . '">' . $result->item . "</div>" . $after ;
	}
	
	return $output_html;
}

// This function runs basic diagnostics
function pravin_todo_diag()
{
	global $wpdb;
	$output_html = '';
	
	// Check for table create perms
	$sql = 'show grants';
	$output_html .= '<h3>Checking table create perms</h3>';
	$results = $wpdb->get_var($sql);
	if(!$results)
	{
		$output_html .= '<p><b>Unable to run query:</b> ' . mysql_error() . '</p>';
	}
	else
	{
		$output_html .= '<p>' . $results . '</p>';
	}
	
	
	// Print the wp prefix string
	$output_html .= '<h3>Prefix string for tables</h3><p>' . $wpdb->prefix . '</p>';
	
	
	// Print the pravin_todo_table
	$sql = "show tables like '" . $wpdb->prefix . "pravin_todo'";
	$results = $wpdb->get_var($sql);
	$output_html .= '<h3>Listing todo table</h3>';
	if(!$results)
	{
		$output_html .= '<p><b>Unable to list todo table:</b> ' . mysql_error() . '</p>';
	}
	else
	{
		$output_html .= '<p>' . $results . '</p>';
	}
	
	echo $output_html;
}
	
?>