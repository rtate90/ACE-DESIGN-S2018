<?php
session_start();
// determines if a semester filter is needed for the navbar
if ($_SESSION["aceAccessLevel"] == 3) {
	$semesterNeeded = True;
} elseif ($_SESSION["aceAccessLevel"] == 1) {
	$semesterNeeded = False;
} else { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='loginHome.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

// Grabs semester info
if (isset($_POST["semester"])) {
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

if ($_SESSION["aceAccessLevel"] == 3) {
	// if statement is for if user selects "All Semesters"
	if ($currentSemesterID == 0) {
		$sectionSql = "SELECT s.sectionID, s.sectionNumber, c.courseName, c.courseDepartment, c.courseCode
		FROM SECTION as s, COURSE as c
		WHERE s.courseID = c.courseID;";
	} else {
		$sectionSql = "SELECT s.sectionID, s.sectionNumber, c.courseName, c.courseDepartment, c.courseCode
		FROM SECTION as s, COURSE as c
		WHERE s.courseID = c.courseID AND 
		s.semesterID = ". $currentSemesterID .";";
	};
} elseif ($_SESSION["aceAccessLevel"] == 1) {
		$sectionSql = "SELECT s.sectionID, s.sectionNumber, c.courseName, c.courseDepartment, c.courseCode
		FROM SECTION as s, COURSE as c
		WHERE s.courseID = c.courseID AND 
		s.semesterID = ". $currentSemesterID ." AND 
		s.agencyID = ". $_SESSION["aceAgencyID"] .";";
};
	
$result = mysqli_query($con, $sectionSql);
$sqlError = mysqli_error($con);

mysqli_close($con);

echo ("
    <!DOCTYPE html>
    <html lang='en'>

    <head>

        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>ACE System Section List</title>
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
                <div class='row'>
                    <div class='col-lg-12'>
                        <h1 class='page-header'>Section List</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class='row'>
                    <div class='col-lg-12'>
                    <p class='text-right'><a role=\"button\" class=\"btn btn-lg btn-success\" href='addSection.php'> Add New Section </a></p>
                        <div class='panel panel-default'>
                            <!-- /.panel-heading -->
                            <div class='panel-body'>
                                <table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-student'>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
        <script>

        var dataSet = [
          ");

          if (mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                  echo "['" . $row["sectionNumber"] . "',
                  '" . $row["courseDepartment"] . "',
                  '" . $row["courseCode"] . "',
                  '" . $row["courseName"] . "',
    			  '<form action=\"editSection.php\" method=\"get\" style=\"display:inline\"> <button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"sectionID\" value=\"" . $row["sectionID"] .
                  "\">Edit</button> </form>'],";
              };
          } else {
              echo "['0 results']";
          };

    echo ("
        ]

        $(document).ready(function() {
            $('#dataTables-student').DataTable({
              data: dataSet,
              columns: [
              { title: 'Section Number' },
              { title: 'Course Department' },
              { title: 'Course Code'},
              { title: 'Course Name' },
              { title: 'Manage' },
                       ],
                responsive: true
            });
        });
        </script>

    </body>
    </html>");






?>
