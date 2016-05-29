<?php
/**
 * Created by IntelliJ IDEA.
 * User: bob
 * Date: 29/05/16
 * Time: 15:37
 */
include '../config/config.php';

apiBaseChecks();

$db = new DB_CONN();

if(!isset($_POST['moduleIDs'])){
    http_response_code(400);
    die();
}

$a = json_decode($_POST['moduleIDs']);

if(!is_array($a)){
    http_response_code(400);
    die();
}

//$u = "http://127.0.0.1/rpirrigate";
$s = implode(" ", $a);

$uri = $_SERVER['REQUEST_URI'];

$uri = str_replace("waterNow","updateModule",$uri);

$u = "http://127.0.0.1:" . $_SERVER['SERVER_PORT'] . $uri;

shell_exec("/srv/rpirrigate/scripts/RPirrigate-waterNow.py $u $s  > /dev/null 2>/dev/null &");