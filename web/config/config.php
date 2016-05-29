<?php
// This file includes other config files
define ('VERSION_RPirrigate', '0.1.0');

$RPirrigate_supported_languages= array(
	'EN'=>'English',
	'IT'=>'Italiano'
	);

$RPirrigate_RPImodel = array(
	'0002'=>'B',
	'0003'=>'B',
	'0004'=>'B',
	'0005'=>'B',
	'0006'=>'B',
  	'0007'=>'A',
  	'0008'=>'A',
  	'0009'=>'A',
  	'000d'=>'B',
  	'000e'=>'B',
        '000f'=>'B',
        '0010'=>'B+',
        '0012'=>'A+',
        '0013'=>'B+',
        '0015'=>'A+',
        'a01041'=>'2B',
        'a101041'=>'2B',
        'a21041'=>'2B',
        '900092'=>'0',
        '900093'=>'0',
        'a22082'=>'3B',
        'a02082'=>'3B');

$RPirrigate_GPIOok = array(
	'A'=>array(2,3,4,7,8,9,10,11,14,15,17,18,22,23,24,25,27),
	'B'=>array(2,3,4,7,8,9,10,11,14,15,17,18,22,23,24,25,27),
	'A+'=>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27),
	'B+'=>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27),
	'2A'=>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27),
	'2B'=>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27),
	'0'=>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27),
	'3B' =>array(2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27)
	);

function apiBaseChecks(){
    if($_SERVER['REQUEST_METHOD']!="POST"){
        http_response_code(405);die(); //not allowed
    }

	if($_SERVER['REMOTE_ADDR']=="127.0.0.1"){
		//always allow from localhost
		return;
	}

    if(!isset($_POST['username'])){
        http_response_code(400);die();
    }
    if(!isset($_POST['password'])){
        http_response_code(400);die();
    }

    $db = new DB_CONN();

    $user = $_POST['username'];
    $pass = $_POST['password'];
    $userID = 0;
    $logged = $db->login($user, $pass);

    if (!($logged>0)){
    	http_response_code(401);die();
    }
}

function isDaemonRunning(){
	exec("ps aux | grep RPirrigate-maind | grep -v grep", $output);
	return (count($output)>0 );
}

function minutesToString($minutes){
	$weeks = intval($minutes / 10080);
	$minutes = $minutes % 10080;

	$days = intval($minutes / 1440);
	$minutes = $minutes % 1440;

	$hours = intval($minutes / 60);
	$minutes = $minutes % 60;

	$str =  ($weeks>0 ? "$weeks " .($weeks>1? LANG_timestring_WEEKS : LANG_timestring_WEEK) . ", " : ""). 
			($days>0 ?"$days " .($days>1? LANG_timestring_DAYS : LANG_timestring_DAY) . ", " : "").
			($hours>0 ?"$hours " .($hours>1? LANG_timestring_HOURS : LANG_timestring_HOUR) . ", " : ""). 
			($minutes>0 ?"$minutes " .($minutes>1? LANG_timestring_MINUTES : LANG_timestring_MINUTE) . "  ": "");  //leave here 2 spaces

	return substr($str, 0, strlen($str)-2);
}

class DB_CONN {
	private $db_conn;

	public function __construct(){

		$this->db_conn = new PDO("sqlite:/srv/rpirrigate/data/database.sqlite","","", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	}

	public function __destruct(){
		$this->db_conn = NULL;
	}

	//Return strings about last happened error  -- NOT WORKING ALWAYS GIVES SOMETHING LIKE "00000"
	public function err_info(){
		$err = $this->db_conn->errorInfo();
		$str = '';
		foreach ($err as $one){
			$str.="<br/>&nbsp;&nbsp;" . $one; 
		}

		return $str;
	}

	//Execute Query and returns statuss
	public function ex_query($sql, $params){
		$sql = $this->db_conn->prepare($sql);

		return  $sql->execute($params);
	}

	//Execute Select and returns PDO object to be fetched
	public function ex_select($sql, $params){

		$sql = $this->db_conn->prepare($sql);

		$sql->execute($params);

		return $sql;
	}

	//Executes  select and returns first row of first column
	//(for queries which get only one result)
	public function ex_select_getFirst($sql, $params){

		$sql =$this->db_conn->prepare($sql);

		$sql->execute($params);

		$sql = $sql->fetch(PDO::FETCH_NUM);

		return $sql[0];
	}

	//return user associative array
	public function login($username, $password){
		$password = md5($password);
		$sql = "SELECT * FROM tbLogin WHERE Username = :username AND Password = :password;";
		$par = array(':username' => $username, ':password' => $password);
		return $this->ex_select($sql, $par)->fetch(PDO::FETCH_BOTH);
	}

	//adds a new user to the database
	public function add_user($username, $password){
		$password = md5($password);
		$sql = "INSERT INTO tbLogin (Username, Password) VALUES (:user, :pass)";
		return $this->ex_query($sql,array(':user'=>$username,':pass'=>$password));
	}

	//deletes an user
	public function delete_user($UserID){
		$sql = "DELETE FROM tbLogin WHERE UserID = :id";
		$this->ex_query($sql,array(':id'=>$UserID));
	}
	//returns PDO to fetch of existent modules
	public function select_modules($id = NULL){
		if ($id==NULL){
			 $sql = "SELECT * FROM tbModules ORDER BY Name ASC;";
			 $params = array();
		} else {
			$sql = "SELECT * FROM tbModules WHERE ModuleID = :id ORDER BY Name ASC;";
			$params = array(':id'=>$id);
		}
		
		return $this->ex_select($sql,$params);
	}

	public function select_users(){
		$sql = "SELECT UserID, Username FROM tbLogin";
		return $this->ex_select($sql,array());
	}

	//return the hash of user password (sha of md5)
	public function select1_hash_password($userID){
		$sql = "SELECT Password FROM tbLogin WHERE UserID = :id";
		return sha1($this->ex_select_getFirst($sql,array(':id'=>$userID)));
	}

	//changes the password if old is correct, else returns false
	public function change_password($user, $old, $new){
		$old=md5($old);
		$logged = $this->ex_select("SELECT * FROM tbLogin WHERE UserID = :id AND Password = :pass; ",
					array(':id'=>$user,':pass'=>$old))->fetch(PDO::FETCH_NUM);
		if (!empty($logged)){
			$new = md5($new);
			$sql = "UPDATE tbLogin SET Password = :pass WHERE UserID = :user";
			return $this->ex_query($sql, array(':pass'=>$new, ':user'=>$user));
		} else { 
			return false;}
	}
	//return the setting 
	public function select1_setting($setting){
		$sql = "SELECT Value FROM tbSettings WHERE Name = :name";
		return $this->ex_select_getFirst($sql,array(':name'=>$setting));
	}
	//set new setting
	public function set_setting($setting, $value){
		$sql = "UPDATE tbSettings SET Value = :val WHERE Name = :name";
		return $this->ex_query($sql, array(':val'=>$value, ':name'=>$setting));
	}

	public function select1_username($UserID){
		$sql = "SELECT Username FROM tbLogin WHERE UserID = :id";
		return $this->ex_select_getFirst($sql,array(':id'=>$UserID));
	}

	public function select1_module_name($id){
		$sql = "SELECT Name FROM tbModules WHERE ModuleID = :id";
		return $this->ex_select_getFirst($sql,array(':id'=>$id));
	}

	public function query_module_description_update($id, $descr){
		$sql = "UPDATE tbModules SET Description = :descr WHERE ModuleID = :id";
		return $this->ex_query($sql, array(':id'=>$id, ':descr'=>$descr));
	}

	public function select_module_lastLog($module){
		$sql = "SELECT * ";
		$sql.= "FROM tbLogs ";
		$sql.= "WHERE ModuleID = :module ";
		$sql.= "UNION SELECT * FROM tbLogs WHERE isRain = 1 ";
		$sql.= "ORDER BY Time DESC LIMIT 1";

		return $this->ex_select($sql,array(':module'=>$module));
	}

	public function select_module_lastLogs($module){
		$sql = "SELECT * ";
		$sql.= "FROM tbLogs ";
		$sql.= "WHERE Liters > -1 AND ModuleID = :module ";
		$sql.= "UNION SELECT * FROM tbLogs WHERE isRain = 1 ";
		$sql.= "ORDER BY Time DESC LIMIT 6";

		return $this->ex_select($sql,array(':module'=>$module));
	}

	public function select_module_logs($module){
		$sql = "SELECT * ";
		$sql.= "FROM tbLogs ";
		$sql.= "WHERE Liters > -1 AND ModuleID = :module ";
		$sql.= "UNION SELECT * FROM tbLogs WHERE isRain = 1 ";
		$sql.= "ORDER BY Time DESC";

		return $this->ex_select($sql,array(':module'=>$module));
	}

	public function query_module_manual_update($id, $act, $val){
		if ($act!=1 && $act!="1")
			$act = "0";
		if($val!=1 && $val!="1")
			$val = "0";

		$sql = "UPDATE tbModules ";
		$sql.= "SET ManualACT = :act, ManualVAL = :val ";
		$sql.= "WHERE ModuleID = :id";

		return $this->ex_query($sql, array(':act'=>$act, ':val'=>$val, ':id'=>$id));
	}

	public function query_module_settings_update($id, $name, $gpio, $thr){
		$sql = "UPDATE tbModules ";
		$sql.= "SET Name = :name, ";
		$sql.= "GPIO = :gpio, ";
		$sql.= "Throughtput = :thr ";
		$sql.= "WHERE ModuleID = :id";
		$params = array(':id'=>$id,
						':name'=>$name,
						':gpio'=>$gpio,
						':thr'=>$thr
						);
		return $this->ex_query($sql, $params);

	}

	public function query_module_update_api($id, $updateData){
		if(count($updateData)>0){
			$params = [];
			$sql = "UPDATE tbModules SET ";

			if(isset($updateData['Name'])){
				$sql .= "Name = :Name, ";
				$params[':Name'] = $updateData['Name'];
			}
			if(isset($updateData['Description'])){
				$sql .= "Description = :description, ";
				$params[':description'] = $updateData['Description'];
			}
			if(isset($updateData['GPIO'])){
				$sql .= "GPIO = :GPIO, ";
				$params[':GPIO'] = $updateData['GPIO'];
			}
			if(isset($updateData['Throughtput'])){
				$sql .= "Throughtput = :Throughtput, ";
				$params[':Throughtput'] = $updateData['Throughtput'];
			}
			if(isset($updateData['ManualACT'])){
				$sql .= "ManualACT = :ManualACT, ";
				$params[':ManualACT'] = $updateData['ManualACT']?1:0;
			}
			if(isset($updateData['ManualVAL'])){
				$sql .= "ManualVAL = :ManualVAL, ";
				$params[':ManualVAL'] = $updateData['ManualVAL']?1:0;
			}


			$sql = substr($sql,0,-2) . " WHERE ModuleID = :ModuleID";

			$params[':ModuleID'] = $id;


			return $this->ex_query($sql, $params);
		}
	}
	public function select_events($module){
		return $this->ex_select("SELECT * FROM tbEvents WHERE ModuleID = :module ;", 
								array(':module'=>$module));
	}

	public function query_module_event_add($module, $interval, $hour, $startingFrom, $liters){
		$sql = "INSERT INTO tbEvents (TimeInterval, Liters, Hour, ModuleID, FirstExecution) ";
		$sql.= "VALUES (:int, :lit, :hour, :mod, :start)";
		return $this->ex_query($sql, array(':int'=>$interval,
										   ':lit'=>$liters,
										   ':hour'=>$hour,
										   ':mod'=>$module,
										   ':start'=>$startingFrom));
	}

	public function select1_event_nexttime($eventID, $interval){
		$sql = "SELECT strftime('%Y-%m-%d' , datetime(Time, '+" . $interval . " minutes')) FROM tbLogs WHERE EventID = :event ORDER BY Time DESC LIMIT 1;";

		$executed = $this->ex_select_getFirst($sql, array(':event'=>$eventID ));

		if($executed){
			$hour = $this->ex_select_getFirst("SELECT Hour FROM tbEvents WHERE EventID = :event ;",array(':event'=>$eventID));
			return $executed . " " . $hour;
		} else{
			$sql = "SELECT (FirstExecution || ' ' || Hour) FROM tbEvents WHERE EventID = :event ;";
			return $this->ex_select_getFirst($sql, array(':event'=>$eventID));
		}
	}

	public function query_event_delete($event){
		$sql = "DELETE FROM tbEvents WHERE EventID = :id";
		return $this->ex_query($sql, array(':id'=>$event));
	}

	public function query_module_delete($module){
		$sql = "DELETE FROM tbModules WHERE ModuleId = :id";
		return $this->ex_query($sql, array(':id'=>$module));
	}

	public function query_module_add($name, $description, $gpio, $throughtput, $image){
		$sql = "INSERT INTO tbModules (Name, Description, GPIO, Throughtput) ";
		$sql.= "VALUES (:name, :description, :gpio, :throughtput)";
		$params = array(':name'=>$name,
						':description'=>$description,
						':gpio'=>$gpio,
						':throughtput'=>$throughtput);
		$this->ex_query($sql, $params);

		$check = getimagesize($image["tmp_name"]);

    	if($check !== false) {
    		$ID = $this->db_conn->lastInsertId();
			$ext = pathinfo($image['name'], PATHINFO_EXTENSION);

    		$target = "mod_images/".$ID .".".$ext ;
    		move_uploaded_file($image["tmp_name"], $target);
    	}
	}

	public function select1_module_exists($id){
		$sql = "SELECT * FROM tbModules WHERE ModuleID = :id";
		return ($this->ex_select($sql,array(':id'=>$id))->fetch(PDO::FETCH_NUM));
	}

	public function select_modules_GPIOs_used(){
		return $this->ex_select("SELECT GPIO FROM tbModules",array());
	}

	public function select1_daemon_pid(){
		return $this->ex_select_getFirst("SELECT Value FROM tbSettings WHERE Name = 'LastPID'",array());
	}

	public function select_nextrainforecasts(){
		$sql = "SELECT ForecastID, Time, Liters FROM tbRainForecasts WHERE Time > strftime('%Y-%m-%d %H:%M:%S','now','localtime')";
		return $this->ex_select($sql,array());
	}
}
?>
