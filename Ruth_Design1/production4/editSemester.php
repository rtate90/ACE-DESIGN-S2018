<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='phpLoginPage.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

// Checks to make sure that the user has access to create a service learner
if ($_SESSION["aceAccessLevel"] == 3) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");

if (mysqli_connect_errno())
{echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$var_semesterID = (int)mysqli_real_escape_string($con, $_GET['semesterID']);
$var_success = mysqli_real_escape_string($con, $_GET['success']);

$sql = "SELECT s.semesterID as semesterID, s.semesterName as semesterName
FROM SEMESTER as s
WHERE s.semesterID = ". $var_semesterID .";";

$result = mysqli_query($con, $sql);

mysqli_close($con);

if (mysqli_num_rows($result) > 1) {exit("Error: Multiple Semesters with the same ID");};

$formData = mysqli_fetch_assoc($result);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Edit Semesters</title>
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
     <h1 class='page-header'>Edit Semester</h1>
 </div>
 </div>
 
  ");
 
 
  if ($var_success == 'deleted') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Semester Deleted Successfully</strong></div>
	</div>
   ");
 };
 
  echo("
 
  <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                   <p class='text-right'><a href='listSemester.php'> View Existing Semesters </a></p>
                   <form action='sqlSemester.php' method='post'>
                         <div class='form-group'>
                             <label>Semester Name</label>
                             <input type ='text' name='semesterName' class='form-control' value='". $formData["semesterName"] ."'>
                         </div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>
			 <div class='row'>
				 <div class='col-lg-6'>
						<input type='hidden' name='semesterID' value='" . $var_semesterID . "'>
						<button type='submit' name='ToDo' value= 'edit' class='btn btn-primary'>Update Entry</button>
						<button type='submit' name='ToDo' value= 'delete' class='btn btn-danger'>Delete Entry</button>
				 </div>
			 </div>
		 </div>
</form>
</div>

</body>
</html>");

?>
