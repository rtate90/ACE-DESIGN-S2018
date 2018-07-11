<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

// Checks to make sure that the user has access to create a service learner
if ($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='phpLoginPage.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$var_success = mysqli_real_escape_string($con, $_GET['success']);
// if a service learner needs to create his/her own account
if ($var_serviceLearnerUsername == $_GET['user']) {
	$var_serviceLearnerUsername = $_GET['serviceLearnerUsername'];
} 

// if a user is trying to create a service learner then the user is not allowed to be an agency
if ($var_serviceLearnerUsername) {
	passthru;
} elseif ($_SESSION["userAccessLevel"] == 2) { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page"); };

mysqli_close($con);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Add Service Learner</title>
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
			<div class='row'>
				 <div class='col-lg-12'>
					 <h1 class='page-header'>Add Service Learner ". $var_serviceLearnerUsername ."</h1>
				 </div>
			</div>

 ");

 if ($var_success == 'added') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Service Learner Added Successfully</strong></div>
	</div>
   ");
 };

 echo("
  <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>");
				 if ($var_serviceLearnerUsername) {
					 echo("<p><i>You need to create an account before you can use the ACE Timesheet System</i></p>");
				 }
                 echo("<p class='text-right'><a href='listServiceLearner.php'> View Existing Service Learners </a></p>
                   <form action='sqlServiceLearner.php' method='post'>
                         <div class='form-group'>
                             <label>First Name</label>
                             <input type ='text' name='serviceLearnerFirstName' class='form-control' required>
                         </div>
                     </div>
                 </div>
             </div>       
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Last Name</label>
                             <input type='text' name='serviceLearnerLastName' class='form-control' required>
                         </div>
                   </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Username</label>");
							 if ($var_serviceLearnerUsername) {
								echo("<input type='text' name='serviceLearnerUsername' value='". $var_serviceLearnerUsername ."' class='form-control' readonly='readonly' required>");
							 } else {
								echo("<input type='text' name='serviceLearnerUsername' class='form-control' required>");
							 }
                         echo("</div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Email</label>
                             <input type='text' name='serviceLearnerEmail' class='form-control' required>
                         </div>
                     </div>
                 </div>
             </div>
           
 <div class='panel-body'>
            <div class='row'>
                <div class='col-lg-6'>
                        <div class='form-group'>");
						if ($var_serviceLearnerUsername) {
							echo("<button type='submit' name='ToDo' value= 'addClock' class='btn btn-primary'>Save Entry</button>");
						} else {
							echo("<button type='submit' name='ToDo' value= 'add' class='btn btn-primary'>Save Entry</button>");
						}
                        echo("</div>
                    </div>
                </div>
            </div>
</form>
</div>
</body>
</html>");

?>
