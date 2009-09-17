<?php
// --------------------------------------------------------------------
// Dashboard widget
// --------------------------------------------------------------------
/* ------ Function Definitions: START ------ */
function todo_add_dashboard_widgets() {
	wp_add_dashboard_widget('todo', 'Assignment Board', 'todo_dashboard_widget_function');	
} 

/* ------ Function Definitions: END ------ */
//add_action('plugins_loaded', 'todo_install');
// Hoook into the dashboard
add_action('wp_dashboard_setup', 'todo_add_dashboard_widgets');
function todo_dashboard_widget_function(){
		global $wpdb;
				$table_name = $wpdb->prefix . 'pravin_todo';
		$sql = "select * from $table_name order by status ASC, date_due ASC ";
		$results = $wpdb->get_results($sql);
		
		$alt = 0;
				$todolist = '<tr style="background-color:#69c; color:#fff"><td>Date Due</td><td>Description</td><td>Task Owner</td><td>Assigned By</td><td>Status</td></tr>';
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
				
				$assigned_by = $wpdb->get_var("SELECT display_name FROM `$wpdb->users` WHERE ID = $result->assigned_by LIMIT 1");
				$assigned_to = $wpdb->get_var("SELECT display_name FROM `$wpdb->users` WHERE ID = $result->task_owner LIMIT 1");
				
				// 'F jS, Y @ H:i'
				$todolist .= '<td><div title="' . gmdate('F jS, Y @ H:i', $result->date_due) . '">' . gmdate('M j, y', $result->date_due) . '</div></td>'. 
					'<td><div title="' . stripslashes($result->notes) . '">' .$result->task_desc . '</td><td>' .
					$assigned_to . '</td>' .
					 '' . 
					'<td><div title="Assigned on ' . gmdate('F jS, Y @ H:i', $result->date_created) . '">' . $assigned_by . '</td><td>' .
					'
						
							<option value="';
						if($result->status == 0) {
							$todolist .= 'done">Pending';
						}
						else {
							$todolist .= 'undone">Done';
						}
				$todolist .= '

						<input type="hidden" name="id" value="' . $result->task_id . '" />
						
					</td></tr>';
			}
		}
			$output_html = str_replace('<$ToDoList$>', $todolist, $output_html);
echo '<div class="wrap">	
	<h3>ToDo List</h3>
	<table cellpadding="5" cellspacing="2" width="100%">
	<tbody>';
echo	$todolist;
echo	'</tbody>
	</table>

	</div>' ;
}
// --------------------------------------------------------------------
// End dashboard widget
// --------------------------------------------------------------------		
?>