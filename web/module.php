<?php
session_start();
if(!isset($_SESSION['RPirrigate_UserID']) && trim($_SERVER['REMOTE_ADDR'])!='127.0.0.1'){
  header('location: index.php?login');die();
}
if(!isset($_GET['id'])) die();

include 'config/config.php';
$db = new DB_CONN();
$location = $db->select1_setting('Location');
$userID = $_SESSION['RPirrigate_UserID'];
$lang = $db->select1_setting('Language');
include 'languages/'.$lang.'/'.$lang.'.php';

$currModuleID = $_GET['id'];

//If module doesn't exist, go to Home
if(!$db->select1_module_exists($currModuleID)){header('location: home.php'); die();}

$bannerMessage="";

if(isset($_POST['Description'])){
  $db->query_module_description_update($currModuleID,nl2br($_POST['Description']));
  $bannerMessage = LANG_module_BANNER_DESCRIPTION;
}

if(isset($_POST['ManualSave']) && $_POST['ManualSave']=='true'){
  $val  = isset($_POST['ManualVAL']);
  $act  = isset($_POST['ManualACT']);
  $db->query_module_manual_update($currModuleID, $act, $val);
  $bannerMessage = LANG_module_BANNER_MANUAL;
  //*** ALSO SEND SIGUSR2 TO THE DAEMON TO MAKE IT RELOAD MANUALS
  $pid = $db->select1_daemon_pid();
  //Newer php version use SIG_NAME, newer SIGNAME
  if(defined('SIG_USR2'))
    posix_kill($pid , SIG_USR2);
  else
    posix_kill($pid , SIGUSR2);
  sleep(1); //let daemon reload and log
}

if (isset($_POST['Settings_Throughtput']) && isset($_POST['Settings_Name']) && isset($_POST['Settings_GPIO'])){
  $db->query_module_settings_update($currModuleID, $_POST['Settings_Name'], $_POST['Settings_GPIO'], $_POST['Settings_Throughtput']);
  $bannerMessage = LANG_module_BANNER_SETTINGS;
  //*** ALSO SEND SIGUSR1 TO THE DAEMON TO MAKE IT RELOAD SETTINGS!!
  $pid = $db->select1_daemon_pid();
  //Newer php version use SIG_NAME, newer SIGNAME
  if(defined('SIG_USR1'))
    posix_kill($pid , SIG_USR1);
  else
    posix_kill($pid , SIGUSR1);
  sleep(1); //let daemon reload and log
}

if(isset($_POST['NewEvent_startdate']) && isset($_POST['NewEvent_starttime']) && isset($_POST['NewEvent_weeks']) && isset($_POST['NewEvent_days']) && isset($_POST['NewEvent_liters'])){
  $weeks = $_POST['NewEvent_weeks']+0;
  $days = $_POST['NewEvent_days']+0;

  $minutes = ($_POST['NewEvent_weeks']+0)*10080 +
             ($_POST['NewEvent_days']+0)*1440;
  $db->query_module_event_add($currModuleID, $minutes, $_POST['NewEvent_starttime'], $_POST['NewEvent_startdate'], $_POST['NewEvent_liters']);
  $bannerMessage = LANG_module_BANNER_NEWEVENT;
  //ALSO SEND SIGUSR1 TO THE DAEMON TO MAKE IT RELOAD SETTINGS
  $pid = $db->select1_daemon_pid();
  //Newer php version use SIG_NAME, newer SIGNAME
  if(defined('SIG_USR1'))
    posix_kill($pid , SIG_USR1);
  else
    posix_kill($pid , SIGUSR1);
  sleep(1); //let daemon reload and log
}

if(isset($_POST['DeleteEvent'])){
  $db->query_event_delete($_POST['DeleteEvent']);
  $bannerMessage = LANG_module_BANNER_DELETEEVENT;
  //ALSO SEND SIGUSR1 TO THE DAEMON
  $pid = $db->select1_daemon_pid();
  //Newer php version use SIG_NAME, newer SIGNAME
  if(defined('SIG_USR1'))
    posix_kill($pid , SIG_USR1);
  else
    posix_kill($pid , SIGUSR1);
  sleep(1); //let daemon reload and log
}

$currModule = $db->select_modules($currModuleID)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RPirrigate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="misc/bootstrap.css" media="screen">
    <link rel="stylesheet" href="misc/bootswatch.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script type="text/javascript">
      function HideShow(what){
        if($('#div'+what+'1').css("display")!="none"){
          $('#div'+what+'1').css("display","none");
          $('#div'+what+'2').css("display","block");
        } else {
          $('#div'+what+'1').css("display","block");
          $('#div'+what+'2').css("display","none");
        }
      }
      function validate_Settings(){
        alert("Vedere come validare GPIO... Nome Solo caratteri,spazi e numeri, Portata solo numeri...")

        //creo un array di tutti i GPIO del RPI(PROBLEMA: GESTIRE B+,2 hanno piu GPIO)
        //poi creare un array di tutti quelli usati (vedere nel DB)
        //e vedere che non sia nel primo ma non nel secondo
      }
      function Manual_Change(){
        var ManActDB = <?php echo $currModule['ManualACT']?>==1;
        var ManValDB = <?php echo $currModule['ManualVAL']?>==1;
        var ManActCHK = $('#chkManAct').prop('checked');
        var ManValCHK = $('#chkManVal').prop('checked');

        $('#trManualVal').css('display',ManActCHK? 'table-row' : 'none');

        if(ManActCHK)
          $('#aManual').css('display',(ManActDB!=ManActCHK || ManValDB!=ManValCHK)? 'block' : 'none');
        else
          $('#aManual').css('display',(ManActDB!=ManActCHK)? 'block' : 'none');

        frmManual.ManualACT.value = ManActCHK;
        frmManual.ManualVAL.value = ManValCHK;
      }
      function Events_HideShow(from, to){
        $('#divEvents'+from).css("display","none");
          $('#divEvents'+to).css("display","block");
      }

      function Events_Validate1(){
        if($('#txtNewStartDate').val().length==0){
          alert("<?php echo LANG_module_ERR2;?>");
          return;
        }  
        if($('#txtNewStartHour').val().length==0){
          alert("<?php echo LANG_module_ERR3;?>");
          return;
        }  
        Events_HideShow(2,3);
      }

      function Events_Validate2(){
        if($('#txtNewLiters').val().length==0){
          alert("<?php echo LANG_module_ERR4;?>");
          return false;
        }  
        return confirm('<?php echo LANG_settings_RUSURE?>');
      }

      function Logs_ShowHide(){
        if($('#divRowLogs').css('display')!= "block" ){
          $('#divRow1').css('display','none');
          $('#divRow2').css('display','none');
          $('#divRowLogs').css('display','block');
        } else {
          $('#divRow1').css('display','block');
          $('#divRow2').css('display','block');
          $('#divRowLogs').css('display','none');

        }
      }
    </script>
    <style type="text/css">
      input[type="checkbox"] { 
        opacity: 0;
      }

      /* Normal Track */
      input[type="checkbox"].ios-switch + div {
        vertical-align: middle;
        width: 40px;  height: 20px;
        border: 1px solid rgba(0,0,0,.4);
        border-radius: 999px;
        background-color: rgba(0, 0, 0, 0.1);
        -webkit-transition-duration: .4s;
        -webkit-transition-property: background-color, box-shadow;
        box-shadow: inset 0 0 0 0px rgba(0,0,0,0.4);
        display:inline-block;
      }

      /* Checked Track (Blue) */
      input[type="checkbox"].ios-switch:checked + div {
        width: 40px;
        background-position: 0 0;
        background-color: #3b89ec;
        border: 1px solid #0e62cd;
        box-shadow: inset 0 0 0 10px rgba(59,137,259,1);
      }

      /* Normal Knob */
      input[type="checkbox"].ios-switch + div > div {
        float: left;
        width: 18px; height: 18px;
        border-radius: inherit;
        background: #ffffff;
        -webkit-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
        -webkit-transition-duration: 0.4s;
        -webkit-transition-property: transform, background-color, box-shadow;
        -moz-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
        -moz-transition-duration: 0.4s;
        -moz-transition-property: transform, background-color;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px rgba(0, 0, 0, 0.4);
        pointer-events: none;
        margin-top: 1px;
        margin-left: 1px;
      }

      /* Checked Knob (Blue Style) */
      input[type="checkbox"].ios-switch:checked + div > div {
        -webkit-transform: translate3d(20px, 0, 0);
        -moz-transform: translate3d(20px, 0, 0);
        background-color: #ffffff;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px rgba(8, 80, 172,1);
      }
    </style>
  </head>
  <body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="#" class="navbar-brand"><?php echo $db->select1_username($userID);?>@RPirrigate</a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
            <li><a href="home.php">Dashboard</a></li>
            <?php
            $qry = $db->select_modules();
            while ($row = $qry->fetch(PDO::FETCH_BOTH)){
              if ($row['ModuleID']==$currModuleID)
                echo("<li class='active'><a href='#'>".$row['Name']."</a></li>\n");
              else
                echo("<li><a href='module.php?id=".$row['ModuleID']."'>".$row['Name']."</a></li>\n");
            }
            ?>
            <li><a href="module-new.php"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo LANG_menu_ADDMODULE; ?></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="settings.php"><i class="fa fa-cogs"></i>&nbsp;&nbsp;<?php echo LANG_menu_SETTINGS; ?></a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="bs-docs-section clearfix">
        <div class="row">
          <div class="col-lg-12">
            <div class="page-header">
              <?php if($bannerMessage!=""){ ?>
                <div class="bs-component">
                  <div class="alert alert-dismissible alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <h4>OK!</h4>
                    <p><?php echo $bannerMessage;?></p>
                  </div>
                </div>
              <?php } ?>
              <h1 id="dialogs"><?php echo $currModule['Name'];?></h1>
            </div>
          </div>
        </div>

        <div class="row" id="divRow1">
          <div class="col-lg-4">
            <div class="bs-component">
              
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_module_STATUS; ?></h3>
                </div>
                <div class="panel-body" style="font-size:90%">
                  <table><tr>
                  <form method="post" action="" name="frmManual">
                    <input type="hidden" name="ManualSave" value="true" />
                    <td>
                      <?php
                      $filename = glob("mod_images/".$currModuleID.".*");
                      if (count($filename)>0)
                        echo("<img src='".$filename[0]."' style='width:120px;max-height:135px;border-radius:10px'/>");
                      else
                        echo("<img src='misc/logo.png' style='width:120px;border-radius:10px'/>");
                      ?>
                    </td>
                    <td style="padding-left:10px;padding-top:3px;vertical-align:top">
                      <table>
                        <tr>
                          <td><b><?php echo LANG_module_MODE." ".LANG_module_MANUAL ?><b></td>
                          <td>
                            <div class="wrap">
                              <label>
                                <input name="ManualACT" type="checkbox" class="ios-switch" 
                                      <?php echo $currModule['ManualACT']? "checked value='true'" : "value='false'"?> 
                                      id="chkManAct"
                                      onclick="Manual_Change()"
                                       />
                              <div><div></div></div></label>
                            </div>
                          </td>
                        </tr> 
                        <tr id="trManualVal"  <?php echo !$currModule['ManualACT']? "style='display:none'" : ""?> >
                          <td><b><?php echo LANG_module_FORCEDVALUE;?></b></td>
                          <td>
                            <div class="wrap">
                              <label>
                                <input name="ManualVAL" type="checkbox" class="ios-switch" 
                                      <?php echo $currModule['ManualVAL']? "checked value='true'" : "value='false'"?> 
                                      id="chkManVal"
                                      onclick="Manual_Change()" />
                              <div><div></div></div></label>
                            </div>
                          </td>
                        </tr>
                      </table>
                      
                      <a href="javascript:frmManual.submit();" id="aManual"
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;position:absolute;bottom:0;margin-left:45px;margin-bottom:20px;display:none">
                          <?php echo LANG_module_SAVE; ?></a>
                    </td>
                  </form>
                  </tr></table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_module_DESCRIPTION;?></h3>
                </div>
                <div class="panel-body" style="text-align:center;" id="divDescription1">
                  <?php echo $currModule['Description']?>
                  
                  <br/>
                  <a href="javascript:HideShow('Description');" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;position:absolute;bottom:0;margin-left:-20px;margin-bottom:20px">
                          <?php echo LANG_module_EDIT; ?></a>
                </div>
                <div class="panel-body" style="text-align:center;display:none" id="divDescription2">
                  <form name="frmDescription" action="" method="post">
                    <textarea name="Description" rows="4" cols="50"><?php echo str_replace("<br />","",$currModule['Description'])?></textarea>
                  </form><br/>
                  <a href="javascript:HideShow('Description');" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:-12px">
                          <?php echo LANG_settings_BACK; ?></a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="javascript:frmDescription.submit();" 
                        onclick="return confirm('<?php echo LANG_settings_RUSURE?>');"
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;margin-top:-12px">
                          <?php echo LANG_settings_CONFIRM; ?></a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_module_IRRIG_LAST;?></h3>
                </div>
                <div class="panel-body" style="text-align:center;">
                  <?php
                  $last = $db->select_module_lastLog($currModuleID)->fetch(PDO::FETCH_ASSOC);
                  $NoLogsYet = empty($last);
                  if(!$NoLogsYet){
                    if($last['isRain']){
                      echo("<img src='misc/img_rain.png' /><br/><br/>");
                    } else {
                      echo("<img src='misc/img_irrigate.png' /><br/><br/>");
                    }
                    echo $last['Time'] . "<br/><b>" . ($last['isRain']? LANG_module_RAIN : (($last['EventID']=='-1')?LANG_module_MANUALIRRIGATION : LANG_module_PLANNEDIRRIGATION)) . " - ";
                    echo ($last['Liters']>-1)
                          ? $last['Liters'] ." ".  ($last['isRain']? "mm" : LANG_module_LITERS)."</b>"
                          : LANG_module_INPROGRESS."</b>";
                  } else {
                    echo("<img src='misc/img_noirrig.png' /><br/><br/>");
                    echo LANG_module_NOIRRIGYET;
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="divRow2">
          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_module_IRRIG_PLAN;?></h3>
                </div>
                <div class="panel-body" id="divEvents1">
                  <table style="width:100%;margin-top:-5px;text-align:center;font-size:90%">
                    <tr>
                      <td><b><?php echo LANG_module_LITERS ?></b></td>
                      <td><b><?php echo LANG_module_INTERVAL ?></b></td>
                      <td><b><?php echo LANG_module_IRRIG_NEXT ?></b></td>
                      <td></td>
                    </td>
                    <?php
                    $qry = $db->select_events($currModuleID);
                    $i=0;
                    while($row = $qry->fetch(PDO::FETCH_ASSOC)){
                      echo("<form name='frmDeleteEvent_$i' method='post' action=''>");
                      echo("<tr><td>" . $row['Liters']. "</td><td>".minutesToString($row['TimeInterval']). "</td>");
                      
                      echo("<td>" . $db->select1_event_nexttime($row['EventID'],$row['TimeInterval']) . "</td>");
                      echo("<td><a href='javascript:frmDeleteEvent_$i.submit();' onclick='return confirm(\"".LANG_settings_RUSURE ."\");'><img src='misc/img_delete.png' width=20 /></a></td></tr>");
                      echo("<input type='hidden' name='DeleteEvent' value='".$row['EventID']."'  />");
                      echo("</form>");
                      $i++;
                    }
                    ?>
                  </table>
                  <?php 
                  if($i<5){ ?>
                      <a href="javascript:Events_HideShow(1,2);" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;position:absolute;bottom:0;margin-left:90px;margin-bottom:20px">
                          <?php echo LANG_module_ADDEVENT; ?></a>
                      </div>
                      <form id="frmNewEvent" action="" method="post">
                        <div class="panel-body" id="divEvents2" style="display:none;text-align:center;">
                          <table style="width:100%;margin-top:-8px"><tr>
                            <td><b><?php echo LANG_module_IRRIGATEEVERY; ?></b></td>
                            <td>
                              <label for="NewEvent_weeks"><?php echo LANG_timestring_WEEKS; ?></label><br/>
                              <input id="txtNewWeeks" style="width:60px;display:inline-block" type="number" name="NewEvent_weeks" class="form-control input-sm" />
                            </td><td>
                              &nbsp;<label for="NewEvent_days"><?php echo LANG_timestring_DAYS; ?></label><br/>
                              <input id="txtNewDays" style="width:60px;display:inline-block" type="number" name="NewEvent_days" class="form-control input-sm" />
                            </td>
                          </tr><tr>
                            <td colspan="4" style="padding-top:10px;padding-bottom:10px">
                              <label for="NewEvent_liters"><?php echo LANG_module_STARTINGFROM; ?>&nbsp;&nbsp;&nbsp;</label>
                              <input id="txtNewStartDate" style="width:124px;display:inline-block;padding-right:0px;" type="date" name="NewEvent_startdate" class="form-control input-sm" />
                              <input id="txtNewStartHour" style="width:95px;display:inline-block;padding-right:0px;" type="time" name="NewEvent_starttime" class="form-control input-sm" />
                            </td>
                          </tr></table>
                          
                          <a href="javascript:Events_HideShow(2,1);" 
                              class="btn btn-warning input-sm" 
                              style="padding-top:4px;margin-top:6px">
                                <?php echo LANG_settings_BACK; ?></a>
                          &nbsp;&nbsp;&nbsp;
                          <a href="javascript:Events_Validate1();" 
                              class="btn btn-primary input-sm" 
                              style="padding-top:4px;margin-top:6px">
                                <?php echo LANG_settings_NEXT; ?></a>
                        </div>
                        <div class="panel-body" id="divEvents3" style="display:none;text-align:center;">
                          <table style="width:100%;margin-top:-8px"><tr>
                            <td colspan="3" style="padding-top:10px;padding-bottom:10px">
                              <label for="NewEvent_liters"><?php echo LANG_module_QUANTITYTOGIVE; ?>&nbsp;&nbsp;&nbsp;</label>
                              <input id="txtNewLiters" style="width:60px;display:inline-block" type="number" name="NewEvent_liters" class="form-control input-sm" />
                            </td>
                          </tr></table>
                          
                          <a href="javascript:Events_HideShow(3,2);" 
                              class="btn btn-warning input-sm" 
                              style="padding-top:4px;margin-top:56px">
                                <?php echo LANG_settings_BACK; ?></a>
                          &nbsp;&nbsp;&nbsp;
                          <input type="submit" class="btn btn-primary input-sm" onclick="return Events_Validate2();"
                              style="padding-top:4px;margin-top:56px" value="<?php echo LANG_settings_CONFIRM; ?>" />
                        </div>
                      </form>
                  <?php 
                  } else { ?>
                    </div>
                  <?php } ?>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    <?php echo LANG_module_IRRIGS_LAST;?>
                    <?php if (!$NoLogsYet): ?>
                      <span style="position:absolute;right:0;width:100px">
                        <a href="javascript:Logs_ShowHide();">
                          <?php echo LANG_module_VIEWALL;?></a></span>
                    <?php endif ?>
                  </h3>
                </div>
                <div class="panel-body">
                  <?php
                  if (!$NoLogsYet){ ?>
                    <table style="width:100%;">
                      <tr>
                        <td><b><?php echo LANG_module_DATE;?></b></td>
                        <td><b><?php echo LANG_module_TYPE;?></b></td>
                        <td></td>
                      </tr>
                      <?php
                      $lasts = $db->select_module_lastLogs($currModuleID);
                      while($row = $lasts->fetch(PDO::FETCH_ASSOC)){
                        echo("<tr><td>".substr($row['Time'],0, 16)."</td>");
                        echo("<td>".($row['isRain']? LANG_module_RAIN : (($row['EventID']=='-1')?LANG_module_MANUALIRRIGATION : LANG_module_PLANNEDIRRIGATION)) ."</td>");
                        echo("<td>".$row['Liters']." " . ($row['isRain']? "mm" : LANG_module_LITERS_SHORT) ."</td></tr>");
                      }
                      ?>
                    </table>
                  <?php } else { 
                    echo("<div style='text-align:center'><img src='misc/img_noirrig.png' /><br/><br/>");
                    echo LANG_module_NOIRRIGYET."</div>";
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_module_SETTINGS;?></h3>
                </div>
                <div class="panel-body" style="text-align:center;" id="divSettings1">
                  <table style="width:70%;margin-left:40px;margin-top:6px;">
                    <tr style="height:25px">
                      <td><b><?php echo LANG_module_NAME ?></b></td>
                      <td><?php echo $currModule['Name']?></td>
                    </tr>
                    <tr style="height:25px">
                      <td><b>GPIO PIN</b></td>
                      <td><?php echo $currModule['GPIO']?></td>
                    </tr>
                    <tr style="height:25px">
                      <td><b><?php echo LANG_module_THROUGHTPUT ?></b></td>
                      <td><?php echo $currModule['Throughtput']?></td>
                    </tr>
                  </table>
                  <a href="javascript:HideShow('Settings');" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;position:absolute;bottom:0;margin-left:-20px;margin-bottom:20px">
                          <?php echo LANG_module_EDIT; ?></a>
                  
                </div>
                <div class="panel-body" style="text-align:center;display:none" id="divSettings2">
                <form name="frmSettings" action="" method="post">
                  <table style="width:70%;margin-left:40px;margin-top:6px;">
                    <tr style="height:25px">
                      <td style="padding-right:10px;width:70%"><b><?php echo LANG_module_NAME ?></b></td>
                      <td><input required type="text" class="form-control input-sm" name="Settings_Name" value="<?php echo $currModule['Name']?>" /></td>
                    </tr>
                    <tr style="height:25px">
                      <td style="padding-right:10px;width:70%"><b>GPIO PIN</b></td>
                      <td>
                       <select required id="txtGPIO" name="Settings_GPIO" class="form-control input-sm">
                          <?php
                          $rev = trim(exec("cat /proc/cpuinfo | grep Revision | cut -f 2 -d: "));
                          
                          $used = array();
                          $qry = $db->select_modules_GPIOs_used();
                          while($row = $qry->fetch(PDO::FETCH_NUM)) array_push($used, $row[0]);

                          foreach($RPirrigate_GPIOok[$RPirrigate_RPImodel[$rev]] as $GPIO){
                            if (!in_array($GPIO, $used))
                              echo("<option value='$GPIO'>$GPIO</option>");
                          }

                          ?>
                          <option selected value="<?php echo $currModule['GPIO']?>"><?php echo $currModule['GPIO']?></option>
                        </select>
                    </tr>
                    <tr style="height:25px">
                      <td style="padding-right:10px;width:70%"><b><?php echo LANG_module_THROUGHTPUT ?></b></td>
                      <td><input required type="text" class="form-control input-sm" name="Settings_Throughtput" value="<?php echo $currModule['Throughtput']?>" /></td>
                    </tr>
                  </table>
                  <a href="javascript:HideShow('Settings');" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:10px">
                          <?php echo LANG_settings_BACK; ?></a>
                    &nbsp;&nbsp;&nbsp;
                  <input type="submit" class="btn btn-primary input-sm" 
                      style="padding-top:4px;margin-top:10px"
                        value="<?php echo LANG_settings_CONFIRM; ?>" />
                </div>
              </form></div>
            </div>
          </div>
        </div>

      </div>

      <div class="row" id="divRowLogs" style="display:none">
          <div class="col-lg-3"></div>
          <div class="col-lg-6">
            <div class="bs-component">
              
              <div class="panel-info" style="border: 1px solid #dddddd;border-radius: 4px;-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    <?php echo LANG_module_IRRIGS;?>
                    <span style="position:absolute;right:0;width:100px">
                        <a href="javascript:Logs_ShowHide();">
                          <?php echo LANG_module_BACK;?></a></span>
                  </h3>
                </div>
                <div class="panel-body">
                  <table style="width:100%;">
                      <tr>
                        <td><b><?php echo LANG_module_DATE;?></b></td>
                        <td><b><?php echo LANG_module_TYPE;?></b></td>
                        <td></td>
                      </tr>
                      <?php
                      $lasts = $db->select_module_logs($currModuleID);
                      while($row = $lasts->fetch(PDO::FETCH_ASSOC)){
                        echo("<tr><td>".substr($row['Time'],0, 16)."</td>");
                        echo("<td>".($row['isRain']? LANG_module_RAIN : (($row['EventID']=='-1')?LANG_module_MANUALIRRIGATION : LANG_module_PLANNEDIRRIGATION)) ."</td>");
                        echo("<td>".$row['Liters']." " . ($row['isRain']? "mm" : LANG_module_LITERS_SHORT) ."</td></tr>");
                      }
                      ?>
                    </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      <footer><?php include 'misc/footer.php';?></footer>
    </div>


<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="misc/bootstrap.min.js"></script>
</body>
</html>