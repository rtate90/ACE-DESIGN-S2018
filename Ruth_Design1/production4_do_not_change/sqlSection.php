<?php
session_start();

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_sectionID = (int)mysqli_real_escape_string($con, $_POST['sectionID']);
$var_courseID = (int)mysqli_real_escape_string($con, $_POST['courseID']);
$var_facultyID = (int)mysqli_real_escape_string($con, $_POST['facultyID']);
$var_agencyID = (int)mysqli_real_escape_string($con, $_POST['agencyID']);
$var_semesterID = (int)mysqli_real_escape_string($con, $_POST['semesterID']);
$var_sectionNumber = (int)mysqli_real_escape_string($con, $_POST['sectionNumber']);
$var_sectionHoursNeeded = (int)mysqli_real_escape_string($con, $_POST['sectionHoursNeeded']);

/*
echo("2. ". $var_courseID ."<br>");
echo("3. ". $var_facultyID ."<br>");
echo("4. ". $var_agencyID ."<br>");
echo("5. ". $var_semesterID ."<br>");
echo("6. ". $var_sectionNumber ."<br>");
echo("7. ". $var_sectionHoursNeeded ."<br>");
echo("8. ". $action ."<br>");
die();
*/

if ($action == "add") {
	
  $sql = "INSERT INTO SECTION (courseID, facultyID, agencyID, semesterID, sectionNumber, sectionHoursNeeded)
  VALUES (" . $var_courseID . ", " . $var_facultyID . ", " . $var_agencyID . ", " . $var_semesterID . ", " . $var_sectionNumber . ", " . $var_sectionHoursNeeded .");";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addSection.php?". "&success=added");
  
  die();

} elseif ($action == "edit") {
	
  $sql = "UPDATE SECTION
  SET courseID = " . $var_courseID . ", facultyID = " . $var_facultyID . ", agencyID = " . $var_agencyID . ", semesterID = " . $var_semesterID . ", sectionNumber = " . $var_sectionNumber . ", sectionHoursNeeded = " . $var_sectionHoursNeeded . "
  WHERE sectionID = " . $var_sectionID . ";";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listSection.php?&success=updated");
  die();

} elseif ($action == "delete") {
	
  $sqlFaculty = "DELETE FROM SECTION WHERE sectionID = " . $var_sectionID;
  $sqlManageSection = "DELETE FROM MANAGE_SECTION WHERE sectionID = " . $var_sectionID;
  $sqlShift = "DELETE FROM SHIFT WHERE sectionID = " . $var_sectionID;
  
  mysqli_query($con, $sqlFaculty);
  mysqli_query($con, $sqlManageSection);
  mysqli_query($con, $sqlShift);

  mysqli_close($con);

  header("Location: listSection.php?&success=delete");
  die();
}
?>