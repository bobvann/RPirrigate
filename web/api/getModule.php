<?php

include '../config/config.php';

apiBaseChecks();

$db = new DB_CONN();

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

$currModule = $db->select_modules($currModuleID)->fetch(PDO::FETCH_ASSOC);
$last = $db->select_module_lastLog($currModuleID)->fetch(PDO::FETCH_ASSOC);
$events = $db->select_events($currModuleID);
$lasts = $db->select_module_logs($currModuleID);

$response = [];

$response['id'] = $currModule['ModuleID'];
$response['manualMode'] = $currModule['ManualACT']?true:false;
$response['manualValue'] = $currModule['ManualVAL']?true:false;
$response['name'] = $currModule['Name'];
$response['description'] = $currModule['Description'];
$response['gpio'] = $currModule['GPIO'];
$response['throughtput'] = $currModule['Throughtput'];

$response['lastIrrigation'] = [];
$response['lastIrrigation']['isRain'] = $last['isRain'];
$response['lastIrrigation']['time'] = $last['Time'];
$response['lastIrrigation']['liters'] = $last['Liters'];

$response['events'] = [];

while($row = $events->fetch(PDO::FETCH_ASSOC)){
    $event = [];
    $event['liters'] = $row['Liters'];
    $event['timeInterval'] = $row['TimeInterval'];
    $event['nextIrrigation'] = $db->select1_event_nexttime($row['EventID'],$row['TimeInterval']);

    $response['events'][] = $event;
}

$response['logs'] = [];

while($row = $lasts->fetch(PDO::FETCH_ASSOC)){
    $log = [];
    $log['time'] = $row['Time'];
    $log['isRain'] = $row['isRain']? true : false;
    $log['liters'] = $row['Liters'];

    $response['logs'][] = $log;
}

echo(json_encode($response));