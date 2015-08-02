<?php
include 'config/config.php';
$db = new DB_CONN();
$lang = $db->select1_setting('Language');
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
  </head>
  <body style="padding-top:0">

    <div class="container">


      <!-- Forms
      ================================================== -->
        <div class="row">
          <div class="col-lg-12">
            <div class="page-header"  style="text-align:center">
              <img src="misc/logo_200.png" />
            </div>
          </div>
        </div>

        
        <div class="row" style="">
          <div class="col-lg-4" style="margin:auto;float:none;">
            <div class="well bs-component">
              <form class="form-horizontal" method="post" action="login.php">
                <fieldset>
                  <legend  style="text-align:center">Log In</legend>
                  <?php if (isset($_GET['wrong'])){ ?>
                    <div class="form-group" style="text-align:center; font-weight:bold; color:#b94a48">
                      <?php echo LANG_index_WRONGMSG; ?>
                    </div>
                  <?php } ?>
                  <?php if (!isset($_GET['wrong'])){ ?>
                    <div class="form-group">
                  <?php } else{ ?>
                    <div class="form-group has-error">
                  <?php } ?>
                    <p style="font-weight:bold;text-align:center;">Username</p>
                    <div class="col-lg-10" style="width:100%">
                      <input name="username" type="text" class="form-control" placeholder="Username">
                    </div>
                  </div>
                  <?php if (!isset($_GET['wrong'])){ ?>
                    <div class="form-group">
                  <?php } else{ ?>
                    <div class="form-group has-error">
                  <?php } ?>
                    <p style="font-weight:bold;text-align:center;">Password</p>
                    <div class="col-lg-10" style="width:100%">
                      <input name="password" type="password" class="form-control" placeholder="Password" />
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-10" style="text-align:center;padding-top:30px;width:100%">
                      <button type="submit" class="btn btn-primary">Login</button>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
  <script type="text/javascript">
/* <![CDATA[ */
(function(){try{var s,a,i,j,r,c,l=document.getElementsByTagName("a"),t=document.createElement("textarea");for(i=0;l.length-i;i++){try{a=l[i].getAttribute("href");if(a&&a.indexOf("/cdn-cgi/l/email-protection") > -1  && (a.length > 28)){s='';j=27+ 1 + a.indexOf("/cdn-cgi/l/email-protection");if (a.length > j) {r=parseInt(a.substr(j,2),16);for(j+=2;a.length>j&&a.substr(j,1)!='X';j+=2){c=parseInt(a.substr(j,2),16)^r;s+=String.fromCharCode(c);}j+=1;s+=a.substr(j,a.length-j);}t.innerHTML=s.replace(/</g,"&lt;").replace(/>/g,"&gt;");l[i].setAttribute("href","mailto:"+t.value);}}catch(e){}}}catch(e){}})();
/* ]]> */
</script>
</body>
</html>
