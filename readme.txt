=== ToDo Plugin  === 
Contributors: pravin, Pizdin Dim, starapple
Tags: todo, list,  tasks, assignments, roles
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 0.2.3

This plugin lets you create and manage a todo list. Writers are sent assignments and a list kept on the Dashboard on the status of the assignments.

== Description == 

== Installation ==
1. Place the todo-plugin folder in your plugins directory`/wp-content/plugins/`.
2. Activate from the Plugins page in WordPress.

== Frequently Asked Questions ==

== Screenshots ==

 == Upgrading ==
If you are upgrading from v0.1 to v0.2.2, go to the ToDo -Add page and use this to DROP your table.You will lose all ToDo data and settings! After you press the button, you may get an error. It is expected. Deactivate the plugin and activate it again to recreate your tables.

== Uninstalling ==
1. Go to the ToDo -Add page and use the button in the Advanced section to DROP your table.You will lose all ToDo data and settings! After you press the button, you may get an error. It is expected. 
2.  Deactivate the plugin. If you activate it again, it will recreate your tables.

== Usage ==
1. You enter your to-do using the form on the Add a ToDo page (ToDo - Add link in the WordPress Tools menu).
2. Do a brief summary in the "Task" field
3. Select a deadline date and make sure to 
4. Select the correct user. 
5. Information in the "Notes" text area will be appended to the "Task" in the body of the email message, so you may try to make the idea flow by giving details about the task.
6. The information saved will be displayed in the list above the form and an email will be sent to the person given the task.

== Settings ==
In the default settings at installation, any registered user of your site may be chosen from the entire membership and assigned a task. This means scrolling through the list of all users to find the person to receive the assignment. This may be tedious if you have many users. Because WordPress groups users by roles, it may be easier to select a recipient from a smaller group of say "Authors" or "Editors". The ToDo List Settings page allows you to keep the default total list or you may select a role (group) from which to choose who will get an assignment.

This plugin is compatible with the Role Manager plugin so you will also be able to select users from groups created by Role Manager.

Roles:
By default only users in the administrator and editor roles can access the Add a ToDo page. However, if you have installed the Role Manager plugin, you may assign persons with other roles to have access to assign tasks.

To do this you must use Role Manager to create a capability called Add Assignment. You can then activate it for Administrator, Editor and other roles you wish to give the privilege of adding to dos (assignments). 

Assignments Board:
An Assignment Board (ToDo List) widget displays in the Dashboard to remind administrators and editors of the pending tasks. The Widget is set to be viewed only by administrators and editors.

Template Usage:
A ToDo list can be shown in your template by using a widget. To show your todo list, just put <code><?php pravin_get_todo(); ?></code> in your template.

== Change log ==
April 2009 - added Dashboard widget, email notification and role capability via options setting.

2007 - Original version