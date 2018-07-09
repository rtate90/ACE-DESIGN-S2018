<?php
session_start();

if ($_SESSION["aceAccessLevel"] == 3) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno())
{echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

// Grabs information from the form page
$var_userID = (int)mysqli_real_escape_string($con, $_POST['userID']);
$var_agencyID = (int)mysqli_real_escape_string($con, $_POST['agencyID']);
$var_userFirstName = (string)mysqli_real_escape_string($con, $_POST['userFirstName']);
$var_userLastName = (string)mysqli_real_escape_string($con, $_POST['userLastName']);
$var_userUsername = (string)mysqli_real_escape_string($con, $_POST['userUsername']);
$var_userEmail = (string)mysqli_real_escape_string($con, $_POST['userEmail']);
$var_userPassword = (string)mysqli_real_escape_string($con, $_POST['userPassword']);
$var_userAccessLevel = (int)mysqli_real_escape_string($con, $_POST['userAccessLevel']);
if ($var_userAccessLevel == 1) {
	$var_userRole = "ace";
} elseif ($var_userAccessLevel == 2) {
	$var_userRole = "agency";
} else {$var_userRole = "admin";};
$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);

/* For debugging purposes
echo("1. ". $var_userID ."<br>");
echo("2. ". $var_agencyID ."<br>");
echo("3. ". $var_userFirstName ."<br>");
echo("4. ". $var_userLastName ."<br>");
echo("5. ". $var_userUsername ."<br>");
echo("6. ". $var_userEmail ."<br>");
echo("7. ". $var_userPassword ."<br>");
echo("8. ". $var_userAccessLevel ."<br>");
echo("9. ". $var_userRole ."<br>");
echo("10. ". $action ."<br>");
die();
*/

// Further sanitize data
$var_userFirstName = str_replace("'", "", $var_userFirstName);
$var_userFirstName = str_replace('"', '', $var_userFirstName);
$var_userFirstName = str_replace('\\', '', $var_userFirstName);

$var_userLastName = str_replace("'", "", $var_userLastName);
$var_userLastName = str_replace('"', '', $var_userLastName);
$var_userLastName = str_replace('\\', '', $var_userLastName);

$var_userUsername = str_replace("'", "", $var_userUsername);
$var_userUsername = str_replace('"', '', $var_userUsername);
$var_userUsername = str_replace('\\', '', $var_userUsername);

$var_userEmail = str_replace("'", "", $var_userEmail);
$var_userEmail = str_replace('"', '', $var_userEmail);
$var_userEmail = str_replace('\\', '', $var_userEmail);

$var_userPassword = str_replace("'", "", $var_userPassword);
$var_userPassword = str_replace('"', '', $var_userPassword);
$var_userPassword = str_replace('\\', '', $var_userPassword);

if ($action == "add") {
	
	$sql = "INSERT INTO USER (agencyID, userFirstName, userLastName, userUsername, userEmail, userPassword, userRole, userAccessLevel)
	VALUES (". $var_agencyID . ", '" . $var_userFirstName . "', '" . $var_userLastName . "', '" . $var_userUsername . "', '" . $var_userEmail . "', '" . $var_userPassword . "', '" . $var_userRole . "', ". $var_userAccessLevel .");";

    mysqli_query($con, $sql);
	
    mysqli_close($con);
	
	header("Location: addUser.php?". "&success=added");
  
    die();

}elseif ($action == "edit") {
	// This determines if the user has access to the selected teacher's calendar

    $sql = "UPDATE USER
    SET agencyID = ". $var_agencyID .", userFirstName = '" . $var_userFirstName . "', userLastName = '" . $var_userLastName . "', userUsername = '" . $var_userUsername . "', userEmail = '" . $var_userEmail . "', userPassword = '" . $var_userPassword . "', userAccessLevel = ". $var_userAccessLevel .", userRole = '" . $var_userRole . "'
    WHERE userID = " . $var_userID . ";";

    mysqli_query($con, $sql);

    mysqli_close($con);

    header("Location: listUser.php?userID=" . $var_userID . "&success=updated");
	die();
	
} elseif ($action == "delete") {
	// This determines if the user has access to the selected teacher's calendar

  $sqlUser = "DELETE FROM USER WHERE userID = " . $var_userID;
  $sqlManageSection = "DELETE FROM MANAGE_SECTION WHERE userID = " . $var_userID;
 
  mysqli_query($con, $sqlUser);
  mysqli_query($con, $sqlManageSection);

  mysqli_close($con);

  header("Location: listUser.php?". "&success=delete");
  die();
}

?>
