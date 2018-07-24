<?php
session_start();

// Checks to make sure that the user has access to create a service learner
if ($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_ID = (int)mysqli_real_escape_string($con, $_POST['facultyID']);
$var_fName = (string)mysqli_real_escape_string($con, $_POST['fName']);
$var_lName = (string)mysqli_real_escape_string($con, $_POST['lName']);

//Further sanitize data
$var_fName = str_replace("'", "", $var_fName);
$var_fName = str_replace('"', '', $var_fName);
$var_fName = str_replace('\\', '', $var_fName);

$var_lName = str_replace("'", "", $var_lName);
$var_lName = str_replace('"', '', $var_lName);
$var_lName = str_replace('\\', '', $var_lName);

if ($action == "add") {
	
  $sql = "INSERT INTO FACULTY (facultyFirstName, facultyLastName)
  VALUES ('". $var_fName . "', '" . $var_lName . "');";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addFaculty.php?". "&success=added");
  
  die();

} elseif ($action == "edit") {
	
  $sql = "UPDATE FACULTY
  SET facultyFirstName = '" . $var_fName . "', facultyLastName = '" . $var_lName . "'
  WHERE facultyID = " . $var_ID . ";";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listFaculty.php?&success=updated");
  die();

} elseif ($action == "delete") {
	
  $sqlFaculty = "DELETE FROM FACULTY WHERE facultyID = " . $var_ID;
  $sqlSection = "UPDATE SECTION
  SET facultyID = NULL
  WHERE facultyID = " . $var_ID . ";";

  mysqli_query($con, $sqlFaculty);
  mysqli_query($con, $sqlSection);

  mysqli_close($con);

  header("Location: listFaculty.php?&success=deleted");
  die();
}
?>