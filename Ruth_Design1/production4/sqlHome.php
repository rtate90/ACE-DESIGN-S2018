<?php
ini_set('session.cookie_lifetime', 28800);
ini_set('session.gc_maxlifetime', 28800);

session_start();

// The session will expire at 28800 seconds (8 hours)
$_SESSION["aceDiscardAfterTime"] = 28800;
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

if (isset($_POST['ToDo'])) {
	if ($_POST['ToDo'] === "logOut") {
		session_unset();
		session_destroy();
		header("Location: loginHome.php?logout=success");
		exit();
	};
	//SQL Connection
	$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
	if (mysqli_connect_errno()) {
		echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };
	
	$uid = mysqli_real_escape_string($con, $_POST['uid']);
	$pwd = mysqli_real_escape_string($con, $_POST['pwd']);
	
	//Error handlers
	//Check if inputs are empty
	if (empty($uid) || empty($pwd)) {
		header("Location: loginHome.php?login=empty");
		exit();
	} else {
		$sql = "SELECT * FROM USER WHERE userUsername='". $uid ."'";
		
		$result = mysqli_query($con, $sql);
		$resultCheck = mysqli_num_rows($result);
		if ($resultCheck < 1) {
			header("Location: loginHome.php?login=error_bad_username");
			exit();
		} else {
			if ($row = mysqli_fetch_assoc($result)) {
				if ($pwd === $row['userPassword']) {
					//Log in the user here
					$_SESSION["aceUserID"] = $row["userID"];
					$_SESSION["aceFirstName"] = $row["userFirstName"];
					$_SESSION["aceLastName"] = $row["userLastName"];
					$_SESSION["aceUsername"] = $row["userUsername"];
					$_SESSION["aceRole"] = $row["userRole"];
					$_SESSION["aceAccessLevel"] = (int)$row["userAccessLevel"];
					$_SESSION["aceAgencyID"] = (int)$row["agencyID"];
					$_SESSION["aceCurrentUse"] = $_POST["ToDo"];
					
					header("Location: ". $_SESSION["aceCurrentUse"] ."");
				} 
				elseif ($pwd != mysqli_fetch_assoc($result)){
					//Sends to error page
					header("Location: loginHome.php?login=error_bad_pass");
					exit();
				}
			}
		}
	}
}
else{
	header("Location: loginHome.php?login=error");
	exit();
}