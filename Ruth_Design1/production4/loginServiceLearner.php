<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

if ($_SESSION["aceCurrentUse"] != "loginServiceLearner.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='loginHome.php'>here</a> to be redirected to the login page");
}

$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

echo("
<!DOCTYPE html>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Time System</title>
    <link rel='stylesheet' href='host/fullcalendar.css' />
    <link href='host/scheduler.css' rel='stylesheet' />
    <link rel='stylesheet' href='host/bootstrap.css' />
    <link href='host/metisMenu.css' rel='stylesheet'>
    <link href='host/sb-admin-2.css' rel='stylesheet'>
    <link href='host/font-awesome.css' rel='stylesheet' type='text/css'>
    <link href='host/dataTables.bootstrap.css' rel='stylesheet'>
    <link href='host/dataTables.responsive.css' rel='stylesheet'>
    <script src='host/jquery.js'></script>
    <script src='host/moment.js'></script>
    <script src='host/fullcalendar.js'></script>
    <script src='host/scheduler.js'></script>
    <script src='host/bootstrap.js'></script>
    <script src='host/metisMenu.js'></script>
    <script src='host/raphael.js'></script>
    <script src='host/morris.js'></script>
    <script src='host/sb-admin-2.js'></script>
    <script src='host/jquery.dataTables.js'></script>
    <script src='host/dataTables.bootstrap.js'></script>
    <script src='host/dataTables.responsive.js'></script>

</head>

<body>

  <div id='wrapper'>");
include "nav.php";
echo ("
  <div id='page-wrapper'>
         <div class='container'>
        <div class='row'>
        <img src="images/iu_trident.png"/>
            <div class='col-md-4 col-md-offset-2'>
                <div class='login-panel panel panel-default'>
                    <div class='panel-heading'>
                        <h1 class='panel-title'
                        style='text-align: center;'>Service Learner Login</h1>
                    </div>
                    <div style='text-align: center;'
                    class='panel-body'>
                        <form role='form'>
                            <fieldset>
                                
                                <a href='inOutClock.php' class='btn btn-danger' name='CAS Login'>CAS Login</a>

                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
</body>

</html>");
?>
