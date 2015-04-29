<?php
session_start();
if(!isset($_SESSION['RPirrigate_UserID']) && trim($_SERVER['REMOTE_ADDR'])!='127.0.0.1'){
  header('location: index.php?login');die();
}
include 'config/config.php';
$db = new DB_CONN();
$lang = $db->select1_setting('Language');
$location = $db->select1_setting('Location');
$userID = $_SESSION['RPirrigate_UserID'];
$hashPWD = $db->select1_hash_password($userID);

$bannerMessage="";
//check that new language!=current language also
if (isset($_POST['ChangeLanguage']) && $_POST['ChangeLanguage'] != $lang){
  $lang= $_POST['ChangeLanguage'];
  $db->set_setting('Language', $lang);
  $bannerMessage = "languagedone";
}

//include the file only afther eventual language change
include 'languages/'.$lang.'/'.$lang.'.php';

//workaround... because I didn't have the constants when changing language
if ($bannerMessage=="languagedone")
  $bannerMessage = LANG_settings_BANNER_LANGUAGE;

if (isset($_POST['ChangeLocation']) && $_POST['ChangeLocation'] != $location){
  $location= $_POST['ChangeLocation'];
  $db->set_setting('Location', $location);
  $bannerMessage = LANG_settings_BANNER_LOCATION;
}

if (isset($_POST['AddUser_user']) && isset($_POST['AddUser_pwd'])) {
  $db->add_user($_POST['AddUser_user'], $_POST['AddUser_pwd']) or die($db->err_info());
  $bannerMessage = LANG_settings_BANNER_ADDUSER;
}

if (isset($_POST['ChangePassword_old']) && isset($_POST['ChangePassword_new1'])){
  $db->change_password($userID, $_POST['ChangePassword_old'], $_POST['ChangePassword_new1'])
    or die('AUTHENTICATION ERROR. IMPOSSIBLE TO CHANGE THE PASSWORD');
  $bannerMessage = LANG_settings_BANNER_CHANGEPASSWORD;
}

if(isset($_POST['DeleteUser'])){
  $db->delete_user($_POST['DeleteUser']);
  $bannerMessage = LANG_settings_BANNER_DELETEUSER;
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
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
    <script src="//crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha1.js"></script>
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
      function location_validate(){

        $.getJSON("//api.worldweatheronline.com/free/v2/search.ashx?key=dadb7eba889f53e8a61dd447cac39&format=json&query="+$('#txtLocation').val(), function( x ) {
          if(x.data == undefined){
            $('#divLoc2').css("display","none");
            $('#divLoc3').css("display","block");

            $('#tbLocations').html("");
            var i=0;
            $.each( x.search_api.result, function( key, val ) {
              if(i<4){
                var locat=val.areaName[0].value;
                var country=val.country[0].value;

                $('#tbLocations').append("<tr><td><a href=\"javascript:location_Selected('"+locat+"','"+country+"');\"><img src='misc/img_select.png' /></a>&nbsp;&nbsp;</td>"+
                  "<td>"+locat+", "+val.region[0].value+", "+country+"</td></tr>");
              }
              i++;
            });

          } else{
            $('#bLocation').css("color","red");
            $('#bLocation').html("<?php echo LANG_settings_ERRORLOC;?>");
          }
        });
      }
      function location_Back(){
        $('#divLoc3').css("display","none");
        $('#divLoc2').css("display","block");
      }
      function location_Selected(loc,state){
        frmLocation.ChangeLocation.value = loc+","+state;
        frmLocation.submit();
      }
      function isCharOk(str) { 
        //accepted chars are numbers and letters
        return (str.length === 1 && ( str.match(/[a-z]/i) != null || str.match(/[0-9]/i) != null));
      }
      function AddUser_validate(){
        var user = $('#txtAddUser_user').val();
        var pwd1 = $('#txtAddUser_pwd1').val();
        var pwd2 = $('#txtAddUser_pwd2').val();
        if(user.length==0 || pwd1.length==0 || pwd2.length==0){
          alert("<?php echo LANG_settings_ADDUSER_ERROR1;?>");
          return;
        }
        for(var i=0;i<user.length;i++){
          if(!isCharOk(user.charAt(i))){
            alert("<?php echo LANG_settings_ADDUSER_ERROR2;?>");
            return;
          }
        }
        if(pwd1!=pwd2){
          alert("<?php echo LANG_settings_ADDUSER_ERROR3;?>");
          return;
        }
        frmAddUser.submit();
      }
      function ChangePassword_validate(){
        var oldHASH = '<?php echo $hashPWD;?>';

        var old  = $('#txtChangePassword_old').val();
        var new1 = $('#txtChangePassword_new1').val();
        var new2 = $('#txtChangePassword_new2').val();
        var oldPWD = CryptoJS.SHA1(CryptoJS.MD5(old).toString()).toString();
        if(old.length==0 || new1.length==0 || new2.length==0){
          alert("<?php echo LANG_settings_CHANGEPASSWORD_ERROR1;?>");
          return;
        }
        if(new1!=new2){
          alert("<?php echo LANG_settings_CHANGEPASSWORD_ERROR2;?>");
          return;
        }
        if(oldHASH!=oldPWD){
          alert("<?php echo LANG_settings_CHANGEPASSWORD_ERROR3;?>");
          return;
        }
        frmChangePassword.submit();
      }
      function DeleteUser_confirm(i){
        if (confirm("<?php echo LANG_settings_RUSURE;?>")){
          $('#frmDelete_'+i).submit();
        }
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
            <li><a href="module-new.php"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo LANG_menu_ADDMODULE; ?></a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="#"><i class="fa fa-cogs"></i>&nbsp;&nbsp;<?php echo LANG_menu_SETTINGS; ?></a></li>
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
              <h1 id="dialogs"><?php echo LANG_menu_SETTINGS; ?></h1>
            </div>
          </div>
        </div>
        <h2><?php echo LANG_settings_USERS;?></h2>
        <div class="row">
          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_settings_CHANGEPASSWORD; ?></h3>
                </div>
                <div class="panel-body" style="text-align:center;" id="divChangePassword1">
                  <?php echo LANG_settings_CHANGEPASSWORD_MSG?><br/><br/><br/><br/>
                  <a href="javascript:HideShow('ChangePassword');" class="btn btn-primary input-sm" style="padding-top:4px;margin-top:4px"><?php echo LANG_settings_CHANGEPASSWORD; ?></a>
                </div>
                <div class="panel-body" style="text-align:center;display:none" id="divChangePassword2">
                  <form method="post" action="" name="frmChangePassword">
                    <table style="width:100%">
                      <tr>
                        <td><?php echo LANG_settings_CHANGEPASSWORD_OLD?></td>
                        <td>
                          <input type="password" id="txtChangePassword_old" name="ChangePassword_old"
                          class="form-control input-sm" 
                          style="width:100%;margin-top:5px;margin:auto;display:inline" />
                        </td>
                      </tr>
                      <tr>
                        <td><?php echo LANG_settings_CHANGEPASSWORD_NEW?></td>
                        <td>
                          <input type="password" id="txtChangePassword_new1" name="ChangePassword_new1"
                          class="form-control input-sm" 
                          style="width:1'0%;margin-top:5px;margin:auto;display:inline" />
                        </td>
                      </tr>
                      <tr>
                        <td><?php echo LANG_settings_CHANGEPASSWORD_NEW?>(2)</td>
                        <td>
                          <input type="password" id="txtChangePassword_new2" name="ChangePassword_new2"
                          class="form-control input-sm" 
                          style="width:100%;margin-top:5px;margin:auto;display:inline" />

                        </td>
                    </table>
                    <a href="javascript:HideShow('ChangePassword');" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_BACK; ?></a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="javascript:ChangePassword_validate();" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_CONFIRM; ?></a>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_settings_OTHERUSERS; ?></h3>
                </div>
                <div class="panel-body" style="text-align:center;">
                  <table style="margin:auto;width:30%">
                    <?php 
                    echo LANG_settings_USERS_MSG . "<br/><br/>";
                    $qry = $db->select_users();
                    $i=0;
                    while($row = $qry->fetch(PDO::FETCH_ASSOC)){
                      if($row['UserID']!=$userID){
                         echo("<tr style='padding-top:10px'><form name='frmDelete_$i' id='frmDelete_$i' method='post' action=''>");
                         echo("<input type='hidden' name='DeleteUser' value='".$row['UserID']."' />");
                         echo("<td><b>" . $row['Username']."</b></td><td><a href='javascript:DeleteUser_confirm($i)'>");
                         echo("<img src='misc/img_delete.png' /></a>");
                         echo("</td></form></tr>");
                         $i++;
                      }
                    }
                    if($i==0)  echo LANG_settings_USERS_NOOTHERS;
                    ?>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_settings_ADDUSER; ?></h3>
                </div>
                <div class="panel-body" style="text-align:center;" id="divAddUser1">
                  <?php echo LANG_settings_ADDUSER_MSG?><br/><br/><br/>
                  <a href="javascript:HideShow('AddUser');" class="btn btn-primary input-sm" style="padding-top:4px;margin-top:4px"><?php echo LANG_settings_ADDUSER; ?></a>
                </div>
                <div class="panel-body" style="text-align:center;display:none" id="divAddUser2">
                  <form method="post" action="" name="frmAddUser">
                    <table style="width:100%">
                      <tr>
                        <td>Username</td>
                        <td>
                          <input type="text" id="txtAddUser_user" name="AddUser_user"
                          class="form-control input-sm" 
                          style="width:100%;margin-top:5px;margin:auto;display:inline" />
                        </td>
                      </tr>
                      <tr>
                        <td>Password</td>
                        <td>
                          <input type="password" id="txtAddUser_pwd1" name="AddUser_pwd"
                          class="form-control input-sm" 
                          style="width:100%;margin-top:5px;margin:auto;display:inline" />
                        </td>
                      </tr>
                      <tr>
                        <td>Password(2)</td>
                        <td>
                          <input type="password" id="txtAddUser_pwd2" name="AddUser_pwd2"
                          class="form-control input-sm" 
                          style="width:100%;margin-top:5px;margin:auto;display:inline" />
                        </td>
                      </tr>
                    </table>
                    <a href="javascript:HideShow('AddUser');" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_BACK; ?></a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="javascript:AddUser_validate();" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_CONFIRM; ?></a>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <h2><?php echo LANG_settings_GENERAL;?></h2>
        <div class="row">
          <div class="col-lg-4">
            <div class="bs-component">
              
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_settings_LANGUAGE; ?></h3>
                </div>
                <div class="panel-body">
                  <form method="post" action="" style="text-align:center">
                    <img src="<?php echo LANG_FLAGFILE;?>" height="55" style="border-radius:10px"/><br/>
                    <select class="form-control input-sm" name="ChangeLanguage" style="width:85px;margin:auto;margin-top:17px">
                      <?php
                      foreach($RPirrigate_supported_languages as $code=>$name){
                        if($code==$lang)
                          echo("<option selected value='$code'>$name</option>");
                        else 
                          echo("<option value='$code'>$name</option>");
                      }
                      ?>
                    </select>
                    <button type="submit" class="btn btn-primary input-sm" style="padding-top:4px;margin-top:7px"><?php echo LANG_settings_CHANGE; ?></button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_settings_LOCATION; ?></h3>
                </div>
                <div class="panel-body" style="text-align:center;" id="divLoc1">
                  <?php echo LANG_settings_LOCATION_MSG."<br/><br/><br/><b>".
                  LANG_settings_CURRENT .":</b> " . $location ?><br/>
                  <a href="javascript:HideShow('Loc');" class="btn btn-primary input-sm" 
                    style="padding-top:4px;margin-top:4px"><?php echo LANG_settings_CHANGE; ?></a>
                </div>

                <div class="panel-body" style="text-align:center;display:none" id="divLoc2">
                    <?php echo "<b>".LANG_settings_CURRENT .":</b> " . $location 
                    . "<br/><br/><b id='bLocation'>". LANG_settings_NEW . "</b><br/>"?>

                    <input type="text" id="txtLocation" name="ChangeLocation"
                        class="form-control input-sm" 
                        style="width:50%;margin-top:5px;margin:auto" />

                    <a href="javascript:HideShow('Loc');" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_BACK; ?></a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="javascript:location_validate();" 
                        class="btn btn-primary input-sm" 
                        style="padding-top:4px;margin-top:8px">
                          <?php echo LANG_settings_CONFIRM; ?></a>
                </div>
                <div class="panel-body" style="text-align:center;display:none" id="divLoc3">
                  <form method="post" action="" name="frmLocation">
                    <input type="hidden" name="ChangeLocation" />
                    <table id="tbLocations" style="text-align:left;"></table>


                    <a href="javascript:location_Back();" 
                        class="btn btn-warning input-sm" 
                        style="padding-top:4px;margin-top:8px;position:absolute;bottom:10px;left:150px">
                          <?php echo LANG_settings_BACK; ?></a>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>
      <footer><?php include 'misc/footer.php';?></footer>
    </div>


<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="misc/bootstrap.min.js"></script>
</body>
</html>
