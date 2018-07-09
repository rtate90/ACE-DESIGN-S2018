<?php
include ("sqlCas.php");
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

$serviceLearnerUsername = $_SESSION["user"];
$agencyID = $_SESSION["aceAgencyID"];

if (isset($serviceLearnerUsername) and isset($agencyID)) {
} else { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

// Grabs the information for the current semester
$sqlCurrentSemester = "SELECT semesterID, semesterName, semesterStart, semesterEnd
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

// Takes the signed in user's agencyID to find the agencyName
$sqlAgency = "SELECT agencyID, agencyName
FROM AGENCY
WHERE agencyID = ". $agencyID .";";
$resultAgency = mysqli_query($con, $sqlAgency);
if (mysqli_num_rows($resultAgency) > 0) {
	while($row = mysqli_fetch_assoc($resultAgency)) {
		$agencyName = $row["agencyName"];
	};
};

// Takes username from CAS and grabs serviceLearner information
$sqlServiceLearner = "SELECT serviceLearnerID, serviceLearnerFirstName, serviceLearnerLastName
FROM SERVICE_LEARNER
WHERE serviceLearnerUsername = '". $serviceLearnerUsername ."';";
$resultServiceLearner = mysqli_query($con, $sqlServiceLearner);
if (mysqli_num_rows($resultServiceLearner) > 0) {
	while($row = mysqli_fetch_assoc($resultServiceLearner)) {
		$serviceLearnerID = $row["serviceLearnerID"];
		$serviceLearnerName = $row["serviceLearnerFirstName"]. " " .$row["serviceLearnerLastName"];
	};
};

// Finds last clock in/out
$sqlShift2 = "SELECT shiftID, clockIn, clockOut
FROM SHIFT
WHERE serviceLearnerID = ". $serviceLearnerID ."
ORDER BY clockIn DESC
LIMIT 1;";
$resultShift2 = mysqli_query($con, $sqlShift2);
if (mysqli_num_rows($resultShift2) > 0) {
	while($row = mysqli_fetch_assoc($resultShift2)) {
		$shiftID = (string)$row["shiftID"];
		$clockIn = (string)$row["clockIn"];
		$clockOut = (string)$row["clockOut"];
	};
};

// Grabs information for the section list in the clock in/out form
if ($clockOut != "0000-00-00 00:00:00") {
	$sqlSection = "SELECT CONCAT(sc.sectionNumber, ' - ', c.courseDepartment, ' ', c.courseCode, ' - ', c.courseName, ' - ', sc.sectionHoursNeeded, 'hours') as sectionInfo, sc.sectionID as sectionID
	FROM SECTION as sc, COURSE as c
	WHERE sc.courseID = c.courseID AND
	sc.semesterID = ". $currentSemesterID ." AND
	sc.agencyID = ". $agencyID .";";
} else {
	$sqlSection = "SELECT CONCAT(sc.sectionNumber, ' - ', c.courseDepartment, ' ', c.courseCode, ' - ', c.courseName, ' - ', sc.sectionHoursNeeded, 'hours') as sectionInfo, sc.sectionID as sectionID
	FROM SHIFT as s, SECTION as sc, COURSE as c
	WHERE sc.courseID = c.courseID AND
		s.sectionID = sc.sectionID AND
		sc.semesterID = ". $currentSemesterID ." AND
		s.shiftID = ". $shiftID ."
	GROUP BY sectionID
	LIMIT 1;";};
$resultSection = mysqli_query($con, $sqlSection);

// Grabs section information for the section list at the bottom of the page
$sqlShift = "SELECT ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted, sc.sectionHoursNeeded as hoursNeeded, CONCAT(c.courseDepartment, ' ', c.courseCode) as course, c.courseName as courseName, sc.sectionNumber as sectionNum
FROM SHIFT as s, SECTION as sc, COURSE as c
WHERE s.serviceLearnerID = ". $serviceLearnerID ." AND
	sc.semesterID = ". $currentSemesterID ." AND
	s.sectionID = sc.sectionID AND
	sc.courseID = c.courseID
GROUP BY sc.sectionID;";
$resultShift = mysqli_query($con, $sqlShift);

mysqli_close($con);

echo("
<!DOCTYPE html>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Missed Clock</title>
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
		echo("<div id='page-wrapper'>
		<div class='row'>
			<div class='col-lg-12'>
				<h1 class='page-header'>Missed Punch ". $serviceLearnerUsername ."</h1>
			</div>
		</div>
        <div class='row'>
            <div class='col-md-4 col-md-offset-4'>
                <div class='login-panel panel panel-default'>
                    <div class='panel-heading'>
                        <h1 class='panel-title'>ACE Missed Punch</h1>
                    </div>
                    <div style='text-align: center;'
                    class='panel-body'>
                        <form action='sqlMissed.php' method='post'>
                            <fieldset>
                                <div class='form-group'>

									<p class='form-control-static'><strong>Name: </strong>". $serviceLearnerName ."</p>
                  <p class='form-control-static'><strong>Agency: </strong>". $agencyName ."</p>
								</div>
								<div class='form-group'>
								<label style='text-align: center;'> Section: </label>
								<select class='form-control' name='sectionID'>");
								if (mysqli_num_rows($resultSection) > 0) {
									while($row = mysqli_fetch_assoc($resultSection)) {
										echo("<option value='". $row["sectionID"] ."'>". $row["sectionInfo"] ."</option>");}
								};
									echo("</select>
								</div>

                                 <div class='form-group'>
                                    <input class='form-control' placeholder='Date' name='Date' type='Date' value=''>
                                </div>
                                 <div class='form-group'>
                                    <input class='form-control' placeholder='Time' name='Time' type='Time' value=''>
                                </div>
								<div class='form-group'>
									<textarea class='form-control' placeholder='Note' name='Note' type='Note' value='' rows='3'></textarea>
								</div>");
									
								if ($clockOut != "0000-00-00 00:00:00") {
									echo("<p>You've been clocked out since ". $clockOut ."</p>
									<button type='clock_in' name='clockIn' value='". $serviceLearnerID ."' class='btn btn-primary'>Clock In</button>
									<button type='clock_out' name='clockOut' value='". $serviceLearnerID ."'class='btn btn-info' disabled>Clock Out</button>");
								} else { 
									echo("<p>You've been clocked in since ". $clockIn ."</p>
									<button type='clock_in' name='clockIn' value='". $serviceLearnerID ."' class='btn btn-info' disabled>Clock In</button>
									<button type='clock_out' name='clockOut' value='". $serviceLearnerID ."'class='btn btn-primary'>Clock Out</button>");}

							echo("</fieldset>
                        </form>
						<a href='inOutClock.php'><button class='btn btn-warning'>Clock In/Out</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>");
?>
