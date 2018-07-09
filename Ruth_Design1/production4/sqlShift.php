<?php
session_start();

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_shiftID = (int)mysqli_real_escape_string($con, $_POST['shiftID']);
$var_serviceLearnerID = (int)mysqli_real_escape_string($con, $_POST['serviceLearnerID']);
$var_sectionID = (int)mysqli_real_escape_string($con, $_POST['sectionID']);
$var_clockIn = mysqli_real_escape_string($con, $_POST['clockIn']);
$var_clockOut = mysqli_real_escape_string($con, $_POST['clockOut']);
$var_missedIn = mysqli_real_escape_string($con, $_POST['missedIn']);
$var_missedOut = mysqli_real_escape_string($con, $_POST['missedOut']);
$var_shiftComment = (string)mysqli_real_escape_string($con, $_POST['shiftComment']);

//Further sanitize data
$var_shiftComment = str_replace("'", "", $var_shiftComment);
$var_shiftComment = str_replace('"', '', $var_shiftComment);
$var_shiftComment = str_replace('\\', '', $var_shiftComment);

// echo("1. ". $action ."<br>");
// echo("2. ". $var_shiftID ."<br>");
// echo("3. ". $var_serviceLearnerID ."<br>");
// echo("4. ". $var_sectionID ."<br>");
// echo("5. ". $var_clockIn ."<br>");
// echo("6. ". $var_clockOut ."<br>");
// echo("7. ". $var_missedIn ."<br>");
// echo("8. ". $var_missedOut ."<br>");
// echo("9. ". $var_shiftComment ."<br>");


// addShift
if ($action == "add") {
	
  $sql = "INSERT INTO SHIFT (serviceLearnerID, sectionID, clockIn, clockOut, missedIn, missedOut, shiftComment)
  VALUES (". $var_serviceLearnerID . ", ". $var_sectionID . ", '". $var_clockIn . "', '". $var_clockOut . "', '" . $var_missedIn . "', '" . $var_missedOut . "', '" . $var_shiftComment . "');";
  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addShift.php?&success=added");
  die();

} elseif ($action == "edit") {

  $sql = "UPDATE SHIFT
  SET clockIn = '" . $var_clockIn . "', clockOut = '". $var_clockOut . "', missedIn = '" . $var_missedIn . "', missedOut = '". $var_missedOut . "', shiftComment = '". $var_shiftComment . "'
  WHERE shiftID = " . $var_shiftID . ";";
  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listShift.php?&success=updated");
  die();
  
} elseif ($action == "delete") {
	
  $sqlShift = "DELETE FROM SHIFT WHERE shiftID = ". $var_shiftID .";";
  mysqli_query($con, $sqlShift);

  mysqli_close($con);

  header("Location: listShift.php?&success=deleted");
  die();
}
?>