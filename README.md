# RPirrigate


#### UPDATE 2016/05/08

Web API for reading data

(see documentation BELOW)


#### UPDATE 2015/08/02

Updated to SQLite insted of MySQL

See install folder for more details


## ***

RPirrigate is licensed under the Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International Public License


Raspberry Pi irrigation system software

RPirrigate provide to the users an easy-to-use, nice-to-see software for managing an irrigation system using a Raspberry Pi and few relayes and valves connected to the GPIO ports.

A guide for the hardware part of the projects still needs to be written yet.

It also has full multilanguage support, with language files easy to build and add to the software.

Currently supported languages are English and Italian.

#### FILE STRUCTURE

/web      -> web interface

/daemon   -> daemon always running

/data -> database

/install -> Install guide and files


MISCELLANEOUS NOTES:

*** WEB ***

Languages constants:

LANG_<page_name>_<element_name>

e.g. LANG_settings_NEW


#### INSTALLATION GUIDE

see install folder

Login credentials: admin/admin


#### WEB API

Currently supporting only read-only API. working on data update APIs

###### Notes:

ALL Web API calls require the following *POST* parameters (so the 'session' state MUST be kept client-side):
  - username
  - password

ALL Web API calls must be using the POST method.

ALL Web API calls return JSON.

##### Common Behaviour:
   -  request method != POST      ->   403 (no response)
   -  username or password parameter not POSTed    ->  400 (no response)
   -  wrong username/password    ->   401 (no response)
   -
#### APIs list:

##### getIndex
  - URL : http://<ip:port>/api/getIndex.php
  - returning the information available on the dashboard
  - parameters: just username and password (as described above)

##### getModule
  - URL : http://<ip:port>/api/getModule.php
  - returning the information available on the module page
  - parameters: username, password and POST parameter moduleID
  - returning status code 400 + blank response if no "moduleID" POST parameter is available
  
##### updateModule
  - URL : http://<ip:port>/api/updateModule.php
  - updating the information for a module
  - parameters: username, password and POST parameter moduleID
  - returning status code 400 + blank response if no "moduleID" POST parameter is available
  - other parameters as many as you want to update: for each parameter of the following list it will update:
    - U_Description
    - U_Name
    - U_GPIO         (MUST BE VALID FOR THE RPI MODEL AND NOT ALREADY USED - OTHERWISE WILL RETURN status code 400)
    - U_Throughtput  (MUST BE VALID FLOAT NUMBER > 0 - OTHERWISE WILL RETURN status code 400)
    - U_ManualACT ( 1/0 ) other values (including 'false') will be evalued to TRUE
    - U_ManualVAL (same)
  - ManualACT and ManualVAL work this way: if ManualACT is 1 (manual activated), the valve will be put in the state said by ManualVAL
  So if ManualVAL is 1, will open water, if ManualVAL is 0, will close water.
  - PLEASE NOTE that this will make the daemon reload settings and/or manual state. DO NOT MISUSE this web service.
