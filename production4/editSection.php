<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

if($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {
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
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };
	
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

$var_success = mysqli_real_escape_string($con, $_GET['success']);
$var_sqlError = mysqli_real_escape_string($con, $_GET['errorMessage']);
$var_sectionID = (int)mysqli_real_escape_string($con, $_GET['sectionID']);

if ($_SESSION["aceAccessLevel"] == 1) {
	$sqlSection = "SELECT sectionID
	FROM SECTION
	WHERE semesterID = ". $currentSemesterID ." AND 
		agencyID = ". $_SESSION["aceAgencyID"] .";";
	$resultSection= mysqli_query($con, $sqlSection);
	
	$hasAccess = False;
	if (mysqli_num_rows($resultSection) > 0) {
		while($row = mysqli_fetch_assoc($resultSection)) {
			if ($row["sectionID"] == $var_sectionID) {
				$hasAccess = True;
				break;
			};
		};
	};
	if ($hasAccess == False) {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};
};

$sql = "SELECT s.sectionID as sectionID, s.courseID as courseID, s.facultyID as facultyID, s.agencyID as agencyID, s.semesterID as semesterID, s.sectionNumber as sectionNumber, s.sectionHoursNeeded as sectionHoursNeeded
FROM SECTION as s
WHERE s.sectionID = " . $var_sectionID . ";";
$result = mysqli_query($con, $sql);

$sqlCourse = "SELECT * FROM COURSE;";
$resultCourse = mysqli_query($con, $sqlCourse);

$sqlFaculty = "SELECT * FROM FACULTY;";
$resultFaculty = mysqli_query($con, $sqlFaculty);

$sqlAgency = "SELECT * FROM AGENCY;";
$resultAgency = mysqli_query($con, $sqlAgency);

$sqlSemester = "SELECT * FROM SEMESTER;";
$resultSemester = mysqli_query($con, $sqlSemester);

mysqli_close($con);

if (mysqli_num_rows($result) > 1) {exit("Error: Multiple Sections with same sectionID");};

$formData = mysqli_fetch_assoc($result);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>MCCSC Demo Calendar</title>
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
     <h1 class='page-header'>Edit Section</h1>
 </div>
 </div>
  ");

 if ($var_success == 'updated') {
   echo("
<div class='row'>
<div class='alert alert-success'><strong>Entry Updated Successfully</strong></div>
</div>
   ");
 };
	
 echo("
<form action='sqlSection.php' method='post'>
 <div class='panel-body'>
 <div class='row'>
     <div class='col-lg-8'>
     <label>Select Course</label>
         <div class='panel panel-default'>
             <div class='panel-body'>
                 <table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-section'></table>
             </div>
         </div>
     </div>
 </div>
 </div>
 <div class='panel-body'>
 <div class='row'>
     <div class='col-lg-8'>
     <label>Select Faculty</label>
         <div class='panel panel-default'>
             <div class='panel-body'>
                 <table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-faculty'></table>
             </div>
         </div>
     </div>
 </div>
 </div>
<div class='panel-body'>
           <div class='row'>
               <div class='col-lg-6'>
                       <div class='form-group'>
                           <label>Agency</label>
                           <select name='agencyID' class='form-control' required>
                           ");
                           if (mysqli_num_rows($resultAgency) > 0) {
                              while($row = mysqli_fetch_assoc($resultAgency)) {
								if ($row["agencyID"] == $formData["agencyID"]) {
                                     echo ("<option selected value='" . $row["agencyID"] . "'>" . $row["agencyName"] . "</option>");
                                   } else {
                                     echo ("<option value='" . $row["agencyID"] . "'>" . $row["agencyName"] . "</option>");
                                   };
                                 };
                           } else {echo("['0 results']");};
						   echo("
						   </select>
						</div>
                   </div>
               </div>
           </div>
<div class='panel-body'>
           <div class='row'>
               <div class='col-lg-6'>
                       <div class='form-group'>
                           <label>Semester</label>
                           <select name='semesterID' class='form-control' required>
                           ");
                           if (mysqli_num_rows($resultSemester) > 0) {
                              while($row = mysqli_fetch_assoc($resultSemester)) {
                                if ($row["semesterID"] == $formData["semesterID"]) {
                                     echo ("<option selected value='" . $row["semesterID"] . "'>" . $row["semesterName"] . "</option>");
                                   } else {
                                     echo ("<option value='" . $row["semesterID"] . "'>" . $row["semesterName"] . "</option>");
                                   };
                                 };
                           } else {echo("['0 results']");};
						   echo("
						   </select>
						</div>
                   </div>
               </div>
           </div>
 <div class='panel-body'>   
			 <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Section Number</label>
                             <input type='text' name='sectionNumber' class='form-control' value ='" . $formData["sectionNumber"] . "'>
                         </div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>   
			 <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Hours Needed</label>
                             <input type='text' name='sectionHoursNeeded' class='form-control' value='" . $formData["sectionHoursNeeded"] ."'>
                         </div>
                     </div>
                 </div>
             </div>
<input type='hidden' name='sectionID' value='" . $var_sectionID . "'>
<button type='submit' class='btn btn-default pull-left' name= 'ToDo' value='edit'>Update Entry</button>
<button type='submit' class='btn btn-danger pull-left' name= 'ToDo' value='delete'>Delete Entry</button>
</form>
</div>
</div>
<script>

var dataSetCourse = [
  ");

  if (mysqli_num_rows($resultCourse) > 0) {
      while($row = mysqli_fetch_assoc($resultCourse)) {
		  if ($row["courseID"] == $formData["courseID"]) {
              echo "['" . $row["courseDepartment"] . "',
              '" . $row["courseCode"] . "',
              '" . $row["courseName"] . "',
              '<div class=\"radio btn btn-success btn-xs\"><label><input checked id=\"checkMe\" required type=\"radio\" name=\"courseID\" value=\"" . $row["courseID"] . "\"> Current Selection</label></div>'],";
            } else {
				echo "['" . $row["courseDepartment"] . "',
				'" . $row["courseCode"] . "',
				'" . $row["courseName"] . "',
				'<div class=\"radio\"><label><input required type=\"radio\" name=\"courseID\" value=\"" . $row["courseID"] . "\"> Select</label></div>'],";
			};
	  };
	  
  } else { 
		echo "['0 results']";
		};

echo ("
]

var dataSetFaculty = [
  ");

  if (mysqli_num_rows($resultFaculty) > 0) {
      while($row = mysqli_fetch_assoc($resultFaculty)) {
		  if ($row["facultyID"] == $formData["facultyID"]) {
              echo "['" . $row["facultyFirstName"] . "',
              '" . str_replace("'", "", $row["facultyLastName"]) . "',
              '<div class=\"radio btn btn-success btn-xs\"><label><input checked id=\"checkMe\" required type=\"radio\" name=\"facultyID\" value=\"" . $row["facultyID"] . "\"> Current Selection</label></div>'],";
            } else{
          echo "['". $row["facultyFirstName"] ."',
          '". str_replace("'", "", $row["facultyLastName"]) ."',
          '<div class=\"radio\"><label><input required type=\"radio\" name=\"facultyID\" value=\"" . $row["facultyID"] . "\"> Select</label></div>'],";
		};
	  };
  } else { 
		echo "['0 results']";
		};

echo ("
]

$(document).ready(function() {
    $('#dataTables-section').DataTable({
      data: dataSetCourse,
      columns: [
      { title: 'Department' },
      { title: 'Code' },
      { title: 'Name' },
	  { title: 'Select' }
    ],
        responsive: true,
        order: [2, 'asc'],
        pageLength: 10
    });
	
    $('#dataTables-faculty').DataTable({
      data: dataSetFaculty,
      columns: [
      { title: 'First Name' },
      { title: 'Last Name' },
	  { title: 'Select' }
    ],
        responsive: true,
        order: [1, 'asc'],
        pageLength: 10
    });
});

</script>	
</body>
</html>");

?>