<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");

if (mysqli_connect_errno())
{echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$var_shiftID = (int)mysqli_real_escape_string($con, $_GET['shiftID']);

$sql = "SELECT sh.shiftID as shiftID, sh.serviceLearnerID as serviceLearnerID, sh.sectionID as sectionID
FROM SHIFT as sh
WHERE sh.shiftID = ". $var_shiftID .";";

/*
$var_serviceLearnerID = (int)mysqli_real_escape_string($con, $_POST['serviceLearnerID']);
$var_sectionID = (int)mysqli_real_escape_string($con, $_POST['sectionID']);
$var_clockIn = (int)mysqli_real_escape_string($con, $_POST['clockIn']);
$var_clockOut = (int)mysqli_real_escape_string($con, $_POST['clockOut']);
$var_missedIn = (int)mysqli_real_escape_string($con, $_POST['missedIn']);
$var_missedOut = (int)mysqli_real_escape_string($con, $_POST['missedOut']);
$var_shiftComment = (string)mysqli_real_escape_string($con, $_POST['shiftComment']);
$var_success = mysqli_real_escape_string($con, $_GET['success']);


$sql = "SELECT sl.serviceLearnerID as serviceLearnerID, sl.serviceLearnerFirstName as serviceLearnerFirstName, 
sl.serviceLearnerLastName as serviceLearnerLastName, s.shiftID as shiftID, se.sectionID as sectionID, 
s.clockIn as clockIn, s.clockOut as clockOut, s.missedIn as missedIn, s.missedOut as missedOut, 
s.shiftComment as shiftComment
FROM SHIFT AS s, SERVICE_LEARNER AS sl, SECTION AS se
WHERE s.serviceLearnerID = ". $var_serviceLearnerID ." AND s.sectionID = ". $var_sectionID . ";";
*/

$result = mysqli_query($con, $sql);

mysqli_close($con);

if (mysqli_num_rows($result) > 1) {exit("Error: Multiple Shifts with the same ID");};

$formData = mysqli_fetch_assoc($result);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Edit Shift</title>
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
     <h1 class='page-header'>Edit Shift</h1>
 </div>
 </div>
 
  ");
 
 
  if ($var_success == 'deleted') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Shift Deleted Successfully</strong></div>
	</div>
   ");
 };
 
  echo("
 
  <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                   <p class='text-right'><a href='listShift.php'> View Existing Shifts </a></p>
                   <form action='sqlShift.php' method='post'>
                         <div class='form-group'>
                             <label>First Name</label>
                             <input type ='text' name='serviceLearnerFirstName' class='form-control' value='". $formData["serviceLearnerFirstName"] ."'>
                         </div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Last Name</label>
                             <input type='text' name='serviceLearnerLastName' class='form-control' value='". $formData["serviceLearnerLastName"] ."'>
                         </div>
                   </div>
                 </div>
             </div>
  <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Clock In</label>
                             <input type='datetime-local' name='clockIn' class='form-control' value='". $formData["clockIn"] ."'>
                         </div>
                   </div>
                 </div>
             </div>
   <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Clock Out</label>
                             <input type='datetime-local' name='clockOut' class='form-control' value='". $formData["clockOut"] ."'>
                         </div>
                   </div>
                 </div>
             </div>
   <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Missed In</label>
                             <input type='datetime-local' name='missedIn' class='form-control' value='". $formData["missedIn"] ."'>
                         </div>
                   </div>
                 </div>
             </div>
   <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Missed Out</label>
                             <input type='datetime-local' name='missed Out' class='form-control' value='". $formData["missedOut"] ."'>
                         </div>
                   </div>
                 </div>
             </div>
   <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Shift Comment</label>
                             <input type='text' name='shiftComment' class='form-control' value='". $formData["shiftComment"] ."'>
                         </div>
                   </div>
                 </div>
             </div>                                                                     
 <div class='panel-body'>
			 <div class='row'>
				 <div class='col-lg-6'>
						<input type='hidden' name='serviceLearnerID' value='" . $formData['shiftID'] . "'>
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
