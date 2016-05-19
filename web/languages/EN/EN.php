<?php
// file della lingua inglese
// ora ci definiamo tutte le costanti a caso, poi faremo uno skeleton cosi possano creare anche le altre 
define('LANG_FLAGFILE','languages/EN/flag.jpg');

define('LANG_index_WRONGMSG', 'Invalid username and/or password');

define('LANG_menu_SETTINGS', 'Settings');
define('LANG_menu_ADDMODULE', 'New Module');

define('LANG_home_SYSTEM', 'System');
define('LANG_home_WEATHER', 'Current Weather');
define('LANG_home_VERSION', 'Version');
define('LANG_home_LOADING', 'Loading...');
define('LANG_home_DATETIME','System date/time');
define('LANG_home_DAEMON', 'Daemon status');
define('LANG_home_DAEMON_OK', 'Running');
define('LANG_home_DAEMON_KO', 'Stopped');
define('LANG_home_NEXTRAIN', 'Next Rain Forecasts');
define('LANG_home_TIME','Time');

define('LANG_settings_LANGUAGE', 'Language');
define('LANG_settings_LOCATION', 'Location');
define('LANG_settings_CHANGE','Change');
define('LANG_settings_ALERTTTW',"<b>Caution: </b>this is an advanced stting. Modifying it may result in a malfunction of RPirrigate.");
define('LANG_settings_ALERTTTW2',"Change only if you know what you're doing!");
define('LANG_settings_CURRENT','Current');
define('LANG_settings_NEW','New');
define('LANG_settings_BACK','Back');
define('LANG_settings_NEXT','Next');
define('LANG_settings_LOCATION_MSG',"Please select your location. This information is needed by the weather");
define('LANG_settings_CONFIRM','Confirm');
define('LANG_settings_ERRORLOC','Error. The location you entered is invalid');
define('LANG_settings_ERRORTTW','Invalid TTW. Please read documentation');
define('LANG_settings_USERS','Users');
define('LANG_settings_GENERAL','General');
define('LANG_settings_CHANGEPASSWORD','Change Password');
define('LANG_settings_CHANGEPASSWORD_MSG',"Here you can change your password");
define('LANG_settings_CHANGEPASSWORD_OLD',"Old Password");
define('LANG_settings_CHANGEPASSWORD_NEW',"New Password");
define('LANG_settings_CHANGEPASSWORD_ERROR1',"Current and new password fields must be filled up");
define('LANG_settings_CHANGEPASSWORD_ERROR2',"The new passwords do not match");
define('LANG_settings_CHANGEPASSWORD_ERROR3',"The current password you wrote does not match your password");
define('LANG_settings_OTHERUSERS','Other Users');
define('LANG_settings_ADDUSER','Add New User');
define('LANG_settings_ADDUSER_MSG',"Here you can add new users to RPirrigate");
define('LANG_settings_ADDUSER_ERROR1', 'Username and passwords fields must be filled up');
define('LANG_settings_ADDUSER_ERROR2', 'Username must contain only numbers and letters');
define('LANG_settings_ADDUSER_ERROR3', 'Two passwords do not match');
define('LANG_settings_USERS_MSG',"Here you can other users who can access to this RPirrigate system and delete them");
define('LANG_settings_USERS_NOOTHERS',"There is no other user who can access this.");
define('LANG_settings_BANNER_CHANGEPASSWORD', "Passsword was succesfully changed");
define('LANG_settings_BANNER_ADDUSER', "New user succesfully added");
define('LANG_settings_BANNER_DELETEUSER', "User succesfully deleted");
define('LANG_settings_BANNER_LANGUAGE', "Language succesfully changed");
define('LANG_settings_BANNER_LOCATION', "Location succesfully changed");
define('LANG_settings_BANNER_WEATHER', "Weather preferences succesfully saved");
define('LANG_settings_RUSURE', "Are you sure?");
define('LANG_settings_WEATHER','Weather Forecasts');
define('LANG_settings_WEATHER_MSG','Choose if to enable the weather forecasts or not.');

define('LANG_module_STATUS','Status');
define('LANG_module_MODE', 'Mode');
define('LANG_module_AUTO', 'Automatic');
define('LANG_module_MANUAL', 'Manual');
define('LANG_module_DESCRIPTION', 'Module Description');
define('LANG_module_IRRIG_LAST', 'Last irrigation');
define('LANG_module_IRRIG_NEXT', 'Next irrigation');
define('LANG_module_IRRIG_PLAN', 'Irrigation Plan');
define('LANG_module_IRRIGS_LAST', 'Last irrigations');
define('LANG_module_IRRIGS', 'Irrigations');
define('LANG_module_VIEWALL', 'View All');
define('LANG_module_SETTINGS', 'Module settings');
define('LANG_module_NAME', 'Name');
define('LANG_module_EDIT', 'Edit');
define('LANG_module_BACK', 'Back');
define('LANG_module_IRRIGATION','Irrigation');
define('LANG_module_MANUALIRRIGATION','Manual Irrig.');
define('LANG_module_PLANNEDIRRIGATION','Planned Irrig.');
define('LANG_module_RAIN','Rain');
define('LANG_module_DATE','Date');
define('LANG_module_TYPE','Type');
define('LANG_module_NOIRRIGYET', 'This module has never been irrigated yet');
define('LANG_module_SAVE', 'Save');
define('LANG_module_FORCEDVALUE', 'Forced Value');
define('LANG_module_BANNER_MANUAL', 'Manual mode succesfully activated/deactivated');
define('LANG_module_BANNER_DESCRIPTION', 'Description succesfully updated');
define('LANG_module_BANNER_SETTINGS', 'Settings succesfully updated');
define('LANG_module_BANNER_NEWEVENT', 'New irrigation succesfully added');
define('LANG_module_BANNER_DELETEEVENT', 'Irrigation succesfully deleted');
define('LANG_module_THROUGHTPUT', 'Throughtput (lt/h)');
define('LANG_module_LITERS','Liters');
define('LANG_module_LITERS_SHORT','Lt.');
define('LANG_module_INTERVAL','Interval');
define('LANG_module_ADDEVENT','New Irrigation');
define('LANG_module_QUANTITYTOGIVE','Liters of water for every irrigation');
define('LANG_module_STARTINGFROM','Starting from');
define('LANG_module_IRRIGATEEVERY','Irrigate every');
define('LANG_module_INPROGRESS', "In progress");
define('LANG_module_ERR1', "Please fill up at least one of the Weeks/Days/Hours fields");
define('LANG_module_ERR2', "Please fill up the Start Data field");
define('LANG_module_ERR3', "Please fill up the Start Hour field");
define('LANG_module_ERR4', "Please fill up the Liters field");
define('LANG_module_DELETE_BTN', "Delete Module");
define('LANG_module_DELETE_ALERT', "Are you sure? This action cannot be undone!");

define('LANG_modulenew_IMAGEFILE', 'Image File');
define('LANG_modulenew_IMAGE_LOGODEFAULT', "If you don't upload any image, default will be used");
define('LANG_modulenew_BANNER', "New module succesfully added");
define('LANG_modulenew_ERR1', "Please fill up the Name field");
define('LANG_modulenew_ERR2', "Please fill up the Description field");
define('LANG_modulenew_ERR3', "Please select one GPIO PIN");
define('LANG_modulenew_ERR4', "Please fill up the Throughtput field");

define('LANG_timestring_WEEKS', 'Weeks');
define('LANG_timestring_DAYS', 'Days');
define('LANG_timestring_HOURS', 'Hours');
define('LANG_timestring_MINUTES', 'Minutes');
define('LANG_timestring_WEEK', 'Week');
define('LANG_timestring_DAY', 'Day');
define('LANG_timestring_HOUR', 'Hour');
define('LANG_timestring_MINUTE', 'Minute');

?>
