<?php
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

    <div class='container'>
        <div class='row'>");
		
 if ($_GET['login'] == 'empty') {
   echo("
	<div class='row'>
		<div class='alert alert-danger'><strong>Entry Fields Are Empty</strong></div>
	</div>
   ");
 };
 
 if ($_GET['login'] == 'error_bad_username') {
   echo("
	<div class='row'>
		<div class='alert alert-danger'><strong>Incorrect Username</strong></div>
	</div>
   ");
 };
 
 if ($_GET['login'] == 'error_bad_pass') {
   echo("
	<div class='row'>
		<div class='alert alert-danger'><strong>Incorrect Password</strong></div>
	</div>
   ");
 };
 
 if ($_GET['logout'] == 'success') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>You are logged out!</strong></div>
	</div>
   ");
 };
            echo("<div class='col-md-4 col-md-offset-4'>
                <div class='login-panel panel panel-default'>
                    <div class='panel-heading'>
                        <img src='images/iulogin.png' alt='IU logo' width='100%'>
                    </div>
                    <div style='text-align: center;'
                    class='panel-body'>
                    	<h1 class='panel-title'
                        style='text-align: center; margin-bottom: 1em;'>ACE Time System
						</h1>
                        <form action='sqlHome.php' method='post'>
                            <fieldset>
                                <div class='form-group'>
                                    <input class='form-control' placeholder='Username' name='uid' autofocus>
                                </div>
                                <div class='form-group'>
                                    <input class='form-control' placeholder='Password' name='pwd'>
                                </div>
								<button type='submit' name='ToDo' value= 'loginServiceLearner.php' class='btn btn-success' style='background-color: #a90000; border: none; margin-right: 1em;'>Clock In/Out</button>
								<button type='submit' name='ToDo' value= 'exportData.php' class='btn btn-primary' style='background-color: #eeedeb; border-color: #ddd; color: black;'>Export Data</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>");
?>
