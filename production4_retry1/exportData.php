<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = True;

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

// Grabs semester info
if ($_POST["semester"]) {
	list($currentSemesterID, $currentSemesterName) = explode(", ",$_POST["semester"]);
} else { 
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
};

mysqli_close($con);
echo("
<!DOCTYPE html>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Time System</title>
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
         <div class='container'>
        <div class='row'>
            <div class='col-md-4 col-md-offset-2'>
                <div class='login-panel panel panel-default'>
                    <div class='panel-heading'>
                        <h1 class='panel-title'
                        style='text-align: center;'>Export Data</h1>
                    </div>
                    <div style='text-align: center;'
                    class='panel-body'>
                        <form action='sqlData.php' method='post'>
                            <fieldset>
                                <button type='submit' class='btn btn-primary' name='semesterID' value='". $currentSemesterID ."'>Download CSV</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
</body>

</html>");
?>
