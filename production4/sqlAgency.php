<?php
session_start();

// if you are not an admin, you do not have access to this page
if ($_SESSION["aceAccessLevel"] == 3) {
} else { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_agencyID = (int)mysqli_real_escape_string($con, $_POST['agencyID']);
$var_agencyName = (string)mysqli_real_escape_string($con, $_POST['agencyName']);

//Further sanitize data
$var_agencyName = str_replace("'", "", $var_agencyName);
$var_agencyName = str_replace('"', '', $var_agencyName);
$var_agencyName = str_replace('\\', '', $var_agencyName);

// addAgency
if ($action == "add") {
	
  $sql = "INSERT INTO AGENCY (agencyName)
  VALUES ('". $var_agencyName ."');";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addAgency.php?". "&success=added");
  die();

} elseif ($action == "edit") {
	
  $sql = "UPDATE AGENCY
  SET agencyName = '" . $var_agencyName . "'
  WHERE agencyID = " . $var_agencyID . ";";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listAgency.php?&success=updated");
  die();

} elseif ($action == "delete") {
	
  $sqlAgency = "DELETE FROM AGENCY WHERE agencyID = " . $var_agencyID;
  $sqlSection = "DELETE FROM SECTION WHERE agencyID = " . $var_agencyID;
  $sqlUser = "DELETE FROM USER WHERE agencyID = " . $var_agencyID;

  mysqli_query($con, $sqlAgency);
  mysqli_query($con, $sqlSection);
  mysqli_query($con, $sqlUser);

  mysqli_close($con);

  header("Location: listAgency.php?&success=deleted");
  die();
}
?>