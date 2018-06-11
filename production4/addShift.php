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
	die("Your session has expired. Please log in once again. Click <a href='phpLoginPage.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); }; };

// Grabs semester info
if ($_POST["semester"]) {
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

// if statement is for if user selects "All Semesters"
if ($currentSemesterID == 0) {
	$sqlSection = "SELECT sc.sectionID as sectionID, CONCAT(sc.sectionNumber, ' - ', c.courseDepartment, ' ', c.courseCode, ' - ', c.courseName, ' - ', sm.semesterName, ' - ', sc.sectionHoursNeeded, 'hours') as sectionInfo
		FROM SECTION as sc, COURSE as c, SEMESTER as sm
		WHERE sc.courseID = c.courseID AND 
		sc.semesterID = sm.semesterID
		GROUP BY sc.sectionID
		ORDER BY sc.sectionNumber ASC;";
} else {
	$sqlSection = "SELECT sc.sectionID as sectionID, CONCAT(sc.sectionNumber, ' - ', c.courseDepartment, ' ', c.courseCode, ' - ', c.courseName, ' - ', sm.semesterName, ' - ', sc.sectionHoursNeeded, 'hours') as sectionInfo
		FROM SECTION as sc, COURSE as c, SEMESTER as sm
		WHERE sc.courseID = c.courseID AND
		sc.semesterID = sm.semesterID AND
		sm.semesterID = ". $currentSemesterID ."
		GROUP BY sc.sectionID
		ORDER BY sc.sectionNumber ASC;";
};
$resultSection = mysqli_query($con, $sqlSection);

$sqlServiceLearner = "SELECT concat (sl.serviceLearnerFirstName, ' ', sl.serviceLearnerLastName, ' - ', sl.serviceLearnerUsername) as userName 
	FROM SERVICE_LEARNER as sl;";
$resultServiceLearner = mysqli_query($con, $sqlServiceLearner);

//pulls the list of service learner names and puts them in a new list options
//options contains a list of names in HTML format
$options = "";

while($row = mysqli_fetch_array($resultServiceLearner)) {
	$options = $options."<option>$row[0]</option>";
};

$var_success = mysqli_real_escape_string($con, $_GET['success']);

mysqli_close($con);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Add an Agency</title>
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
     <h1 class='page-header'>Add Shift</h1>
 </div>
 </div>

 ");

 if ($var_success == 'added') {
   echo("
<div class='row'>
<div class='alert alert-success'><strong>Entry Added Successfully</strong></div>
</div>
   ");
 };

 echo("
		<div class='panel-body'>
			
			<div class='row'>
                <div class='col-lg-8'>
					<p class='text-right'><a href='listShifts.php'> View Shifts </a></p>
				</div>
			</div>
		");
		
		
//Drop down list
echo("
	<div class='row'>
		<div class='col-lg-2'>
			<label> Service Learner: </label>
		</div>
		<div class='col-lg-6'>
			<select class='form-control' name='name-list'>
");	
	echo $options;
echo("
			</select>
		</div>
	</div>

");	
//End drop down list

echo("
			<div class = 'row'>
				<div class = 'col-lg-2'>
					<label>Section: </label>
				</div>
				<div class = 'col-lg-6'>
					<select class='form-control' name='sectionID'>
");	

if (mysqli_num_rows($resultSection) > 0) {
	while($row = mysqli_fetch_assoc($resultSection)) {
		echo("<option value=". $row["sectionID"] .">". $row["sectionInfo"] ."</option>");
	};
};

echo("
					</select>
				</div>
			</div>
");
echo("
			<div class = 'row'>
				<div class = 'col-lg-2'>
					<label>Clock-In:</label>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='date' name='clockInDate' class='form-control'>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='time' name='clockInTime' class='form-control'>
				</div>
			</div>
			
			<div class = 'row'>
				<div class = 'col-lg-2'>
					<label>Clock-Out:</label>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='date' name='clockInDate' class='form-control'>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='time' name='clockInTime' class='form-control'>
				</div>
			</div>
			
			<div class = 'row'>
				<div class = 'col-lg-2'>
					<label>Missed-In:</label>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='date' name='clockInDate' class='form-control'>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='time' name='clockInTime' class='form-control'>
				</div>
			</div>
			
			<div class = 'row'>
				<div class = 'col-lg-2'>
					<label>Missed-Out:</label>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='date' name='clockInDate' class='form-control'>
				</div>
					
				<div class = 'col-lg-3'>
					<input type ='time' name='clockInTime' class='form-control'>
				</div>
			</div>
			
			<div class='row'>
                <div class='col-lg-8'>
                        <div class='form-group'>
                            <label>Comments</label>
                            <textarea class='form-control' rows='3' id='comment'></textarea>
							 
							
						</div>
				</div>
			</div>
			
			<button type='submit' name='ToDo' value= 'add' class='btn btn-primary'>Save Entry</button>
		</div>
						
             
</body>
</html>");

?>