<?php
session_start();

$newComment = $_POST["note"];
$sectionID = $_POST["sectionID"];
$clockInID = $_POST["clockIn"];
$clockOutID = $_POST["clockOut"];
$missedDate = $_POST["Date"];
$missedTime = $_POST["Time"];
$currentTime = date('Y-m-d H:i:s', strtotime("$missedDate $missedTime"));
$timeStamp = date("Y-m-d H:i:s");

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno())
{echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

if ($clockInID > 0) {
	$sql = "INSERT INTO SHIFT (clockIn, sectionID, serviceLearnerID, shiftComment, missedIn)
	VALUES ('". $currentTime ."', ". $sectionID .", ". $clockInID .", '". $newComment ."', '". $timeStamp ."');";
} elseif ($clockOutID > 0) {
	$sqlShift = "SELECT shiftID, shiftComment
	FROM SHIFT
	WHERE serviceLearnerID = ". $clockOutID ."
	ORDER BY clockIn DESC
	LIMIT 1;";
	$resultShift = mysqli_query($con, $sqlShift);

	$shiftID = 0;
	$oldComment = "";
	if (mysqli_num_rows($resultShift) > 0) {
		while($row = mysqli_fetch_assoc($resultShift)) {
			$shiftID = $row["shiftID"];
			$oldComment = $row["shiftComment"];
		};
	};
	$comment = $newComment. " - " . $oldComment;

	$sql = "UPDATE SHIFT
	SET clockOut = '". $currentTime ."', shiftComment = '". $comment ."', missedOut = '". $timeStamp ."'
	WHERE shiftID = ". $shiftID .";";
} else {die("You need to either clock in or out, sucka.<a href='inOutClock.php'>here</a>");};
$result = mysqli_query($con, $sql);

if($result){
} else {die("SQL Error: <a href='inOutClock.php'>here</a>");}

mysqli_close($con);

header("Location: inOutClock.php");
die();
?>
