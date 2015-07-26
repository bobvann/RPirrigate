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
include 'languages/'.$lang.'/'.$lang.'.php';
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
    <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <?php if($location!=""): ?>
      <script type="text/javascript">
        var loc = "<?php echo $location;?>";
        var lang = '<?php echo strtolower($lang);?>';

        $.getJSON("//api.worldweatheronline.com/free/v2/weather.ashx?q="+loc+"&key=dadb7eba889f53e8a61dd447cac39&format=json&fx=no&lang="+lang, function( x ) {
          $('#imgWeather').attr("src",x.data.current_condition[0].weatherIconUrl[0].value.substr(5));
          $('#spanWeather').html("<br/><b>"+loc+"</b><br/>"+x.data.current_condition[0]['lang_'+lang][0].value+"<br/>"+Math.round(x.data.current_condition[0].temp_C)+" C");
          $('#pWeather').remove();
        });
      </script>
    <?php endif ?>
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
            <li class="active">
              <a href="">Dashboard</a>
            </li>
            <?php
            $qry = $db->select_modules();
            while ($row = $qry->fetch(PDO::FETCH_BOTH))
              echo("<li><a href='module.php?id=".$row['ModuleID']."'>".$row['Name']."</a></li>\n");
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


      <!-- Navbar
      ================================================== -->
      <div class="bs-docs-section clearfix">


        <div class="row">
          <div class="col-lg-12">
            <div class="page-header">
              <h1 id="dialogs">Dashboard</h1>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-4">
            <div class="bs-component">
              
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_home_SYSTEM; ?></h3>
                </div>
                <div class="panel-body">
                 <b>O/S:</b>&nbsp;&nbsp;<?php echo php_uname('s');?><br/>
                 <b>Hostname:</b>&nbsp;&nbsp;<?php echo php_uname('n');?><br/>
                 <b>Release:</b>&nbsp;&nbsp;<?php echo php_uname('r');?><br/>
                 <b>Version:</b>&nbsp;&nbsp;<?php echo php_uname('v');?><br/>
                 <b>Machine:</b>&nbsp;&nbsp;<?php echo php_uname('m');?><br/>
                 <b>Raspberry Pi Model:</b>&nbsp;&nbsp;
                    <?php
                    $rev = trim(exec("cat /proc/cpuinfo | grep Revision | cut -f 2 -d: "));
                    echo $RPirrigate_RPImodel[$rev] . " ($rev)";
                    ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"><?php echo LANG_home_WEATHER; ?></h3>
                </div>
                <?php if($location!=""): ?>
                  <div class="panel-body" style="text-align:center;">
                    <img width='70' src='' id='imgWeather' />
                    <span id='spanWeather'></span>
                    <p id='pWeather'><?php echo LANG_home_LOADING;?></p>
                  </div>
                <?php else: ?>
                  <div class="panel-body" style="text-align:center;">
                    Please select your location in settings.
                  </div>
                <?php endif ?>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="bs-component">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title">Info</h3>
                </div>
                <div class="panel-body" style="text-align:center;">
                  <p><b>DateTime: </b><?php echo date('m/d/Y H:i:s', time()); ?></p>
                  <p><b>Daemon Status: </b> Running</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <?php
            $qry = $db->select_modules();
            $i=0;
            while ($row = $qry->fetch(PDO::FETCH_BOTH)){ 
              $i++;

              //every three panels, go to new row
              if ($i>3 &&  $i%3==1)
                echo("</div><div class='row'>");
              ?>
              <div class="col-lg-4">
                <div class="bs-component">
                  
                  <div class="panel panel-primary">
                    <div class="panel-heading">
                      <h3 class="panel-title"><?php echo "<a href='module.php?id=".$row['ModuleID']."' align='left'>".$row['Name']."</a>" ?></h3>
                    </div>
                    <div class="panel-body">
                      <table>
                        <tr>
                          <td>
                            <?php
                            $filename = glob("mod_images/".$row['ModuleID'].".*");
                            if (count($filename)>0)
                              echo("<img src='".$filename[0]."' height='100' style='border-radius:10px'/>");
                            else
                              echo("<img src='misc/logo.png' height='100' style='border-radius:10px'/>");
                            ?>

                          <td style="padding-left:20px">
                            <b><?php echo $row['Name'] ?></b><br/>
                            <?php echo $row['Description'] ?>
                          </td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
      </div>
      <footer><?php include 'misc/footer.php';?></footer>
    </div>


<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="misc/bootstrap.min.js"></script>
</body>
</html>
