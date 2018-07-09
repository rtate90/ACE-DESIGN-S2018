<?php
session_start();

if ($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_courseID = (int)mysqli_real_escape_string($con, $_POST['courseID']);
$var_courseName = (string)mysqli_real_escape_string($con, $_POST['courseName']);
$var_courseDepartment = (string)mysqli_real_escape_string($con, $_POST['courseDepartment']);
$var_courseCode = (int)mysqli_real_escape_string($con, $_POST['courseCode']);

//Further sanitize data
$var_courseName = str_replace("'", "", $var_courseName);
$var_courseName = str_replace('"', '', $var_courseName);
$var_courseName = str_replace('\\', '', $var_courseName);

// addCourse
if ($action == "add") {
	
  $sql = "INSERT INTO COURSE (courseName, courseDepartment, courseCode)  VALUES ('$var_courseName', '$var_courseDepartment','$var_courseCode')";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addCourse.php?". "&success=added");
  die();

} elseif ($action == "edit") {
	
  $sql = "UPDATE COURSE
  SET courseName = '$var_courseName', courseDepartment = '$var_courseDepartment', courseCode = '$var_courseCode' WHERE courseID = '$var_courseID'";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listCourse.php?&success=updated");
  die();

} elseif ($action == "delete") {
	
  $sqlCourse = "DELETE FROM COURSE WHERE courseID = '$var_courseID' ";

  mysqli_query($con, $sqlCourse);
 
  mysqli_close($con);

  header("Location: listCourse.php?&success=deleted");
  die();
}
?>