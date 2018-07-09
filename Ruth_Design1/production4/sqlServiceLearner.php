<?php
session_start();

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };
// Checks to make sure that the user is not an agency
if ($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {

} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_ID = (int)mysqli_real_escape_string($con, $_POST['serviceLearnerID']);
$var_fName = (string)mysqli_real_escape_string($con, $_POST['serviceLearnerFirstName']);
$var_lName = (string)mysqli_real_escape_string($con, $_POST['serviceLearnerLastName']);
$var_username = (string)mysqli_real_escape_string($con, $_POST['serviceLearnerUsername']);
$var_email = (string)mysqli_real_escape_string($con, $_POST['serviceLearnerEmail']);

//Further sanitize data
$var_fName = str_replace("'", "", $var_fName);
$var_fName = str_replace('"', '', $var_fName);
$var_fName = str_replace('\\', '', $var_fName);

$var_lName = str_replace("'", "", $var_lName);
$var_lName = str_replace('"', '', $var_lName);
$var_lName = str_replace('\\', '', $var_lName);

$var_username = str_replace("'", "", $var_username);
$var_username = str_replace('"', '', $var_username);
$var_username = str_replace('\\', '', $var_username);

$var_email = str_replace("'", "", $var_email);
$var_email = str_replace('"', '', $var_email);
$var_email = str_replace('\\', '', $var_email);

// addClock is if a serviceLearner is signed in, and therefore needs to be redirected to inOutClock.php
if ($action == "add" or $action == "addClock") {
	
  $sql = "INSERT INTO SERVICE_LEARNER (serviceLearnerFirstName, serviceLearnerLastName, serviceLearnerUsername, serviceLearnerEmail)
  VALUES ('". $var_fName . "', '" . $var_lName . "', '" . $var_username . "', '" . $var_email . "');";

  mysqli_query($con, $sql);

  mysqli_close($con);

  if ($action == "add") {
	  header("Location: addServiceLearner.php?". "&success=added");
  } else {
	  header("Location: inOutClock.php");
  }
  
  die();

} elseif ($action == "edit") {

	// Checks access level to make sure that user has access to edit this service learner
	if ($_SESSION["aceAccessLevel"] == 3) {

	} elseif ($_SESSION["aceAccessLevel"] == 1) {
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
				if ($row["serviceLearnerID"] == $var_ID) {
					$hasAccess = True;
					break;
				};
			};
		};
		if ($hasAccess == False) {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};
	};

  $sql = "UPDATE SERVICE_LEARNER
  SET serviceLearnerFirstName = '" . $var_fName . "', serviceLearnerLastName = '" . $var_lName . "', serviceLearnerUsername = '" . $var_username . "', serviceLearnerEmail = '" . $var_email . "'
  WHERE serviceLearnerID = " . $var_ID . ";";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listServiceLearner.php?&success=updated");
  die();

} elseif ($action == "delete") {
	// Checks access level to make sure that user has access to edit this service learner
	if ($_SESSION["aceAccessLevel"] == 3) {

	} elseif ($_SESSION["aceAccessLevel"] == 1) {
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
				if ($row["serviceLearnerID"] == $var_ID) {
					$hasAccess = True;
					break;
				};
			};
		};
		if ($hasAccess == False) {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};
	};
	
  $sqlServiceLearner = "DELETE FROM SERVICE_LEARNER WHERE serviceLearnerID = " . $var_ID;
  $sqlShift = "DELETE FROM SHIFT WHERE serviceLearnerID = " . $var_ID;

  mysqli_query($con, $sqlServiceLearner);
  mysqli_query($con, $sqlShift);

  mysqli_close($con);

  header("Location: listServiceLearner.php?&success=delete");
  die();
}
?>