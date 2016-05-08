<?php

include '../config/config.php';

apiBaseChecks();

$db = new DB_CONN();

//aux data
$rev = trim(exec("cat /proc/cpuinfo | grep Revision | cut -f 2 -d: "));

$response = [];

$response['system']=[];
$response['system']['os'] = php_uname('s');
$response['system']['hostname'] = php_uname('n');;
$response['system']['release'] = php_uname('r');;
$response['system']['version'] = php_uname('v');
$response['system']['machine'] = php_uname('m');
$response['system']['rpimodel'] = $RPirrigate_RPImodel[$rev];


$response['info']=[];
$response['info']['datetime'] = date('d/m/Y H:i:s', time());
$response['info']['daemon'] = isDaemonRunning() ? 'OK' : 'KO';


$response['modules']=[];

$qry = $db->select_modules();
while ($row = $qry->fetch(PDO::FETCH_BOTH)){
    $module = [];

    $module['id']=$row['ModuleID'];
    $module['name']=$row['Name'];
    $module['description']=$row['Description'];

    $response['modules'][]=$module;
}
echo(json_encode($response));