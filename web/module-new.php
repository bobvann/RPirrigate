<?php
session_start();
if(!isset($_SESSION['RPirrigate_UserID']) && trim($_SERVER['REMOTE_ADDR'])!='127.0.0.1'){
  header('location: index.php?login');die();
}
include 'config/config.php';
$db = new DB_CONN();
$userID = $_SESSION['RPirrigate_UserID'];
$lang = $db->select1_setting('Language');
include 'languages/'.$lang.'/'.$lang.'.php';

$bannerMessage="";
if(isset($_POST['name'])&&isset($_POST['description'])&&isset($_POST['gpio'])&&isset($_POST['throughtput']) ){
  $db->query_module_add($_POST['name'],$_POST['description'],$_POST['gpio'],$_POST['throughtput'],$_FILES['image']);
  $bannerMessage = LANG_modulenew_BANNER;
  //*** ALSO SEND SIGUSR1 TO THE DAEMON TO MAKE IT RELOAD SETTINGS!!
  $pid = $db->select1_daemon_pid();
  posix_kill($pid , SIGUSR1);
  sleep(1); //let daemon reload and log
}
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
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
    <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js"></script>
    <script type="text/javascript">
    function Step1_2(){
      if($('#txtName').val().length==0 ){
        alert("<?php echo LANG_modulenew_ERR1; ?>");
        return;
      }
      if($('#txtDescription').val().length==0){
        alert("<?php echo LANG_modulenew_ERR2; ?>");
        return;
      }
      $('#fsStep1').css('display','none');
      $('#fsStep2').css('display','block');
    }
    function Step2_1(){
      $('#fsStep2').css('display','none');
      $('#fsStep1').css('display','block');
    }
    function Step2_end(){
      var gpio = $('#txtGPIO').val();
      var thro = $('#txtThroughtput').val();

      if(gpio.length==0 ){
        alert("<?php echo LANG_modulenew_ERR3; ?>");
        return;
      }

      if(thro.length==0){
        alert("<?php echo LANG_modulenew_ERR4; ?>");
        return;
      }

      frmNewModule.submit();
    }
    </script>
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
            <li>
              <a href="home.php">Dashboard</a>
            </li>
            <?php
            $qry = $db->select_modules();
            while ($row = $qry->fetch(PDO::FETCH_BOTH))
              echo("<li><a href='module.php?id=".$row['ModuleID']."'>".$row['Name']."</a></li>\n");
            ?>
            <li class="active"><a href="module-new.php"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo LANG_menu_ADDMODULE; ?></a></li>
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
              <h1 id="dialogs"><?php echo LANG_menu_ADDMODULE; ?></h1>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2"></div>
          <div class="col-lg-8">
            <div class="bs-component">
              
              <div class="panel panel-info" style="height:400px">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_menu_ADDMODULE; ?>
                  </h3>
                </div>
                <div class="panel-body">
                  <form name="frmNewModule" class="form-horizontal" method="post" action="" style="width:50%;margin:auto" 
                        enctype="multipart/form-data">
                    <fieldset id="fsStep1">
                      <div class="form-group">
                        <p style="font-weight:bold;text-align:center;"><?php echo LANG_module_NAME ?></p>
                        <div class="col-lg-10" style="width:100%">
                          <input id="txtName" name="name" type="text" class="form-control" >
                        </div>
                      </div>
                      <div class="form-group">
                        <p style="font-weight:bold;text-align:center;"><?php echo LANG_module_DESCRIPTION ?></p>
                        <div class="col-lg-10" style="width:100%" >
                          <textarea id="txtDescription" name="description" rows="7" class="form-control"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-lg-10" style="text-align:center;width:100%">
                          <a href="javascript:Step1_2();"
                            class="btn btn-primary input-sm" 
                            style="padding-top:4px;margin-top:15px;">
                              <?php echo LANG_settings_NEXT; ?></a>
                      </div>
                    </fieldset>
                    <fieldset id="fsStep2" style="display:none">
                      <div class="form-group">
                        <p style="font-weight:bold;text-align:center;">GPIO PIN (GPIO.BCM)</p>
                        <div class="col-lg-10" style="width:30%;float:none;margin:auto">
                          <select id="txtGPIO" name="gpio" class="form-control">
                            <option value="">...</option>
                            <?php
                            $rev = trim(exec("cat /proc/cpuinfo | grep Revision | cut -f 2 -d: "));
                            $rev = "0003"; //ONLY FOR TESTING ON NON RPI MACHINES
                            
                            $used = array();
                            $qry = $db->select_modules_GPIOs_used();
                            while($row = $qry->fetch(PDO::FETCH_NUM)) array_push($used, $row[0]);

                            foreach($RPirrigate_GPIOok[$RPirrigate_RPImodel[$rev]] as $GPIO){
                              if (!in_array($GPIO, $used))
                                echo("<option value='$GPIO'>$GPIO</option>");
                            }

                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <p style="font-weight:bold;text-align:center;"><?php echo LANG_module_THROUGHTPUT ?></p>
                        <div class="col-lg-10" style="width:30%;float:none;margin:auto" >
                          <input id="txtThroughtput" name="throughtput" type="text" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <p style="font-weight:bold;text-align:center;"><?php echo LANG_modulenew_IMAGEFILE ?></p>
                        <div class="col-lg-10" style="width:80%;float:none;margin:auto" >
                          <input type="file" name="image" class="form-control" accept="image/*">
                          <?php echo LANG_modulenew_IMAGE_LOGODEFAULT ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-lg-10" style="text-align:center;width:100%">
                          <a href="javascript:Step2_1();" 
                            class="btn btn-warning input-sm" 
                            style="padding-top:4px;margin-top:18px">
                              <?php echo LANG_settings_BACK; ?></a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:Step2_end();" 
                            class="btn btn-primary input-sm" 
                            style="padding-top:4px;margin-top:18px">
                              <?php echo LANG_settings_CONFIRM; ?></a>
                      </div>
                    </fieldset>
                  </form>
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
