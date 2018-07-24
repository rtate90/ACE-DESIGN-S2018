<?php
session_start();
// determines if a semester filter is needed for the navbar
if ($_SESSION["aceAccessLevel"] == 3) {
	$semesterNeeded = True;
} elseif ($_SESSION["aceAccessLevel"] == 1) {
	$semesterNeeded = False;
} else { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
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

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

// Grabs semester info
if (isset($_POST["semester"])) {
	list($currentSemesterID, $currentSemesterName) = explode(", ",$_POST["semester"]);
} else { 
	// Grabs the information for the current semester if no semester is selected
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
};

if ($_SESSION["aceAccessLevel"] == 3) {
	// if statement is for if user selects "All Semesters"
	if ($currentSemesterID == 0) {
		$sql = "SELECT sl.serviceLearnerID as serviceLearnerID,
		sl.serviceLearnerFirstName as serviceLearnerFirstName,
		sl.serviceLearnerLastName as serviceLearnerLastName,
		sl.serviceLearnerUsername as serviceLearnerUsername,
		sl.serviceLearnerEmail as serviceLearnerEmail
		FROM SERVICE_LEARNER as sl;";
	} else {
		$sql = "SELECT sl.serviceLearnerID as serviceLearnerID,
		sl.serviceLearnerFirstName as serviceLearnerFirstName,
		sl.serviceLearnerLastName as serviceLearnerLastName,
		sl.serviceLearnerUsername as serviceLearnerUsername,
		sl.serviceLearnerEmail as serviceLearnerEmail
		FROM SERVICE_LEARNER as sl, SHIFT as s, SECTION as sc
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND 
		sc.sectionID = s.sectionID AND 
		sc.semesterID = ". $currentSemesterID ."
		GROUP BY sl.serviceLearnerID;";
	}
} elseif ($_SESSION["aceAccessLevel"] == 1) {
		$sql = "SELECT sl.serviceLearnerID as serviceLearnerID,
		sl.serviceLearnerFirstName as serviceLearnerFirstName,
		sl.serviceLearnerLastName as serviceLearnerLastName,
		sl.serviceLearnerUsername as serviceLearnerUsername,
		sl.serviceLearnerEmail as serviceLearnerEmail
		FROM SERVICE_LEARNER as sl, SHIFT as s, SECTION as sc
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND 
		sc.sectionID = s.sectionID AND 
		sc.semesterID = ". $currentSemesterID ." AND 
		sc.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY sl.serviceLearnerID;";
};

$result = mysqli_query($con, $sql);
$var_success = mysqli_real_escape_string($con, $_GET['success']);

mysqli_close($con);
echo("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content=''>
    <meta name='author' content=''>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Service Learner List</title>
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

echo("
		<!-- Page Content -->
        <div id='page-wrapper'>
            <div class='container-fluid'>
                <div class='row'>
                    <div class='col-lg-12'>
                        <h1 class='page-header'>Service Learner List</h1>						
                    </div>
                </div>");

 if ($var_success == 'updated') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Entry Updated Successfully</strong></div>
	</div>
   ");
 };
  if ($var_success == 'delete') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Entry Deleted Successfully</strong></div>
	</div>
   ");
 };

				echo("<div class='row'>
					<div class='col-lg-12'>
					<p class='text-right'><a role=\"button\" class=\"btn btn-lg btn-success\" href='addServiceLearner.php'>Add Service Learner</a></p>
						<div class='panel panel-default'>
							<div class='panel-body'>
								<table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-serviceLearner'>
								</table>
							</div>
						</div>
					</div>
				</div>
				<script>
					var dataSet = [");
//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
					
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
		
		// Calculates hours needed and hours completed for every service learner
		$sql2 = "SELECT SUM(sn.sectionHoursNeeded) as hoursNeeded, ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted
		FROM SHIFT as s, SECTION as sn
		WHERE s.serviceLearnerID = ". $row["serviceLearnerID"]. " AND
		s.sectionID = sn.sectionID;";
		$result2 = mysqli_query($con, $sql2);
		if (mysqli_num_rows($result2) > 0) {
			while($row2 = mysqli_fetch_assoc($result2)) {
				$hoursNeeded = (string)$row2["hoursNeeded"];
				$hoursCompleted = (string)$row2["hoursCompleted"];
			};
		};
		
		echo("['" . $row["serviceLearnerFirstName"] . "',
		'" . str_replace("'", "", $row["serviceLearnerLastName"]) . "',
		'" . $row["serviceLearnerUsername"] . "',
		'" . $row["serviceLearnerEmail"] . "',
		'" . $hoursCompleted . "',
		'" . $hoursNeeded . "',
		'<form action=\"editServiceLearner.php\" method=\"get\" style=\"display:inline\"> <button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"serviceLearnerID\" value=\"". $row["serviceLearnerID"] ."\">Edit</button></form>'],");
	};
};

mysqli_close($con);
echo("]

					$(document).ready(function() {
						$('#dataTables-serviceLearner').DataTable({
						  data: dataSet,
						  columns: [
						  { title: 'First Name' },
						  { title: 'Last Name' },
						  { title: 'Username' },
						  { title: 'Email' },
						  { title: 'Hours Completed' },
						  { title: 'Hours Needed' },
						  { title: 'Manage' }
						],
							responsive: true,
							order: [1, 'asc']
						});
					});			
				</script>
            </div>
            <!-- /.container-fluid -->
        </div>
    </div>
    <!-- /#wrapper -->


</body>

</html>");
?>
