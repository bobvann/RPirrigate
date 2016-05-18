<?php
/**
 * Created by IntelliJ IDEA.
 * User: bob
 * Date: 18/05/16
 * Time: 22:30
 */

include '../config/config.php';

apiBaseChecks();

$db = new DB_CONN();
/*
 * CREATE TABLE "tbModules" (
  "ModuleID" INTEGER NOT NULL ,
  "Name" varchar(50) NOT NULL,
  "Description" text NOT NULL,
  "GPIO" int(11) NOT NULL,
  "Throughtput" decimal(10,2) NOT NULL,
  "ManualACT" tinyint(1) DEFAULT '0',
  "ManualVAL" tinyint(1) DEFAULT '0',
  PRIMARY KEY ("ModuleID")
);
 */
$db = new DB_CONN();

$updateData = [];

if(!isset($_POST['moduleID'])){
    http_response_code(400);
    die();
}

$currModuleID = $_POST['moduleID'];

//If module doesn't exist, not found
if(!$db->select1_module_exists($currModuleID)){
    http_response_code(404); //not found
    die();
}

$reloadDaemon = false;
$reloadManual = false;

if(isset($_POST['U_Description'])){
    $updateData['Description'] = substr($_POST['U_Description'],0,1000);
}
if(isset($_POST['U_Name'])){
    $updateData['Name'] = substr($_POST['U_Name'],0,50);
}
if(isset($_POST['U_GPIO'])){
    $updateData['GPIO'] = intval($_POST['U_GPIO']);

    //validating GPIO
    $rev = trim(exec("cat /proc/cpuinfo | grep Revision | cut -f 2 -d: "));
    $used = array();
    $allowed = [];
    $qry = $db->select_modules_GPIOs_used();
    while($row = $qry->fetch(PDO::FETCH_NUM)) array_push($used, $row[0]);
    foreach($RPirrigate_GPIOok[$RPirrigate_RPImodel[$rev]] as $GPIO){
        if (!in_array($GPIO, $used))
            $allowed[] = $GPIO;
    }

    if(!in_array($updateData['GPIO'],$allowed)){
        http_response_code(400);die(); //BAD REQUEST, GPIO INVALID
    }

    $reloadDaemon = true;
}
if(isset($_POST['U_Throughtput'])){
    $updateData['Throughtput'] = floatval($_POST['U_Throughtput']);

    // validating
    if($updateData['Throughtput']<=0){
        http_response_code(400);die(); //BAD REQUEST, THROUGHTPUT INVALID
    }

    $reloadDaemon = true;
}
if(isset($_POST['U_ManualACT'])){
    $updateData['ManualACT'] = boolval($_POST['U_ManualACT']);

    $reloadManual = true;
}
if(isset($_POST['U_ManualVAL'])){
    $updateData['ManualVAL'] = boolval($_POST['U_ManualVAL']);


    $reloadManual = true;
}


$db->query_module_update_api($currModuleID,$updateData);

if($reloadDaemon){
    $pid = $db->select1_daemon_pid();
    if(defined('SIG_USR1'))
        posix_kill($pid , SIG_USR1);
    else
        posix_kill($pid , SIGUSR1);
}

if($reloadManual){
    $pid = $db->select1_daemon_pid();
    if(defined('SIG_USR2'))
        posix_kill($pid , SIG_USR2);
    else
        posix_kill($pid , SIGUSR2);
}






