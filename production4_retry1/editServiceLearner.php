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

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$var_serviceLearnerID = (int)mysqli_real_escape_string($con, $_GET['serviceLearnerID']);
$var_success = mysqli_real_escape_string($con, $_GET['success']);

// Grabs the information for the current semester for access level checks
$sqlCurrentSemester = "SELECT semesterID, semesterName
	FROM SEMESTER
	ORDER BY semesterStart DESC
	LIMIT 1;";
$resultCurrentSemester = mysqli_query($con, $sqlCurrentSemester);
if (mysqli_num_rows($resultCurrentSemester) > 0) {
	while ($row = mysqli_fetch_assoc($resultCurrentSemester)) {
		$currentSemesterID = $row["semesterID"];
		$currentSemesterName = $row["semesterName"];
	};
};

// Checks access level to make sure that user has access to edit this service learner
if ($_SESSION["aceAccessLevel"] == 3) {

} elseif ($_SESSION["aceAccessLevel"] == 2) {
	die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
} elseif ($_SESSION["aceAccessLevel"] == 1) {
	$sqlServiceLearner = "SELECT sl.serviceLearnerID as serviceLearnerID
	FROM SERVICE_LEARNER as sl, SHIFT as s, SECTION as sc
	WHERE sl.serviceLearnerID = s.serviceLearnerID AND 
	sc.sectionID = s.sectionID AND 
	sc.semesterID = ". $currentSemesterID ." AND 
	sc.agencyID = ". $_SESSION["aceAgencyID"] ."
	GROUP BY sl.serviceLearnerID;";
	$resultServiceLearner = mysqli_query($con, $sqlServiceLearner);
	
	$hasAccess = False;
	if (mysqli_num_rows($resultServiceLearner) > 0) {
		while($row = mysqli_fetch_assoc($resultServiceLearner)) {
			if ($row["serviceLearnerID"] == $var_serviceLearnerID) {
				$hasAccess = True;
				break;
			};
		};
	};
	if ($hasAccess == False) {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};
	
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$sql = "SELECT sl.serviceLearnerID, sl.serviceLearnerFirstName, sl.serviceLearnerLastName, sl.serviceLearnerUsername, sl.serviceLearnerEmail
	FROM SERVICE_LEARNER as sl
	WHERE sl.serviceLearnerID = " . $var_serviceLearnerID . ";";
$result = mysqli_query($con, $sql);

mysqli_close($con);

if (mysqli_num_rows($result) > 1) {exit("Error: Multiple Service Learners with same Service LearnerID");};

$formData = mysqli_fetch_assoc($result);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Edit Service Learner</title>
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
    <script src='host/dataTables.bootstrap.js'></script>
    <script src='host/dataTables.responsive.js'></script>
    <script src='host/jquery.dataTables.js'></script>

</head>

<body>

    <div id='wrapper'>");

include "nav.php";

echo ("
	<div id='page-wrapper'>
		<div class='row'>
			 <div class='col-lg-12'>
				 <h1 class='page-header'>Edit Service Learner</h1>
			 </div>
		</div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                 <p class='text-right'><a href='listServiceLearner.php'> View Existing Service Learners </a></p>
                   <form action='sqlServiceLearner.php' method='post'>
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
                             <label>UserName</label>
                             <input type='text' name='serviceLearnerUsername' class='form-control' value='". $formData["serviceLearnerUsername"] ."'>

                         </div>
                   </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>

                         <div class='form-group'>
                             <label>Email</label>
                             <input type='text' name='serviceLearnerEmail' class='form-control' value='". $formData["serviceLearnerEmail"] ."'>

                         </div>
                     </div>
                 </div>
             </div>

<input type='hidden' name='serviceLearnerID' value='" . $var_serviceLearnerID . "'>
<button type='submit' class='btn btn-default pull-left' name= 'ToDo' value='edit'>Update Entry</button>
<button type='submit' class='btn btn-danger pull-left' name= 'ToDo' value='delete'>Delete Entry</button>
</form>
            </div>

</body>
</html>");

?>
