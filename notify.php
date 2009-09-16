<?php
//====================================================
//Email the task
//====================================================


$task = $_POST["task"];
$taskowner = $_POST["taskowner"];
$notes = stripslashes($_POST["notes"]);
$deadline = date("D M j Y", $date_due);
$assigner = $current_user->display_name;
$blogtitle = get_option('blogname');
$adminemail = get_settings('admin_email');
$siteurl = get_bloginfo('siteurl');
global $wpdb;
//			$task_owner = $wpdb->get_results("SELECT display_name FROM $wpdb->users WHERE display_name = $taskowner");
			$task_owner_email = $wpdb->get_var("SELECT user_email FROM `$wpdb->users` WHERE ID = $taskowner LIMIT 1");
			$task_owner_firstname = get_usermeta($taskowner,'first_name');
//			$task_owner_firstname = $wpdb->get_var("SELECT first_name FROM `$wpdb->user_meta` WHERE ID = $taskowner LIMIT 1");
function mail_todo(){
global $task_owner_email, $blogtitle, $adminemail, $task_owner_firstname, $deadline, $assigner, $task, $notes, $siteurl;

$messagebody = "Dear $task_owner_firstname:\n\nYou have been given the following assignment by $assigner: \n\n$task \n\n $notes \n\nYour assignment is due on $deadline\n\nPlease acknowledge receipt of this email and contact the editor if you have any questions.\n\nBest regards\n\n$assigner\n\n$siteurl";
$to      = $task_owner_email;
$subject = "New Assignment from $blogtitle";
$message = $messagebody;
$headers = "MIME-Version: 1.0\n" .
      "From:  Abeng News Editor <$adminemail> \n" .
      "Reply-To: $adminemail\n" .
      "Return-Path: $adminemail\n" .
      "Cc: " . $adminemail . "\n";
      "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
 wp_mail($to, $subject, $message, $headers);
 // ==================================================
 // End Function email the task
 // ==================================================
}
?>
