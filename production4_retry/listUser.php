<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

// if you are not an admin, you do not have access to this page
if ($_SESSION["aceAccessLevel"] == 3) {
} else { die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='loginHome.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

//SQL Query
$sql = "SELECT u.userID AS userID,
	u.userFirstName AS userFirstName,  
	u.userLastName AS userLastName,
	u.userUsername AS userUsername,
	u.userEmail AS userEmail,
	u.userRole AS userRole,
	u.userAccessLevel AS userAccessLevel,
	a.agencyName AS agencyName
FROM USER AS u, AGENCY AS a
WHERE u.agencyID = a.agencyID;";

$result = mysqli_query($con, $sql);
$var_success = mysqli_real_escape_string($con, $_GET['success']);

mysqli_close($con);
echo("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content=''>
    <meta name='author' content=''>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE User List</title>
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
                    <h1 class='page-header'>User List</h1>
                </div>
            </div>");

 if ($var_success == 'updated') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Entry Updated Successfully</strong></div>
	</div>
   ");
 };
  if ($var_success == 'delete') {
   echo("
	<div class='row'>
		<div class='alert alert-success'><strong>Entry Deleted Successfully</strong></div>
	</div>
   ");
 };

			echo("<div class='row'>
                <div class='col-lg-12'>
                <p class='text-right'><a role=\"button\" class=\"btn btn-lg btn-success\" href='addUser.php'> Add New User </a></p>
                    <div class='panel panel-default'>
                        <div class='panel-body'>
                            <table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-user'>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		<script>

			var dataSet = [");
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
		
		echo ("['" . $row["userFirstName"] . "',
		'" . $row["userLastName"] . "',
		'" . $row["userUsername"] . "',
		'" . $row["userEmail"] . "',
		'" . $row["userRole"] . "',
		'" . $row["agencyName"] . "',
		'<form action=\"editUser.php\" method=\"get\" style=\"display:inline\"> <button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"userID\" value=\"". $row["userID"] ."\">Edit</button> </form>'],");
	};
};

echo ("
    ]

    $(document).ready(function() {
        $('#dataTables-user').DataTable({
          data: dataSet,
          columns: [
          { title: 'First Name' },
          { title: 'Last Name' },
          { title: 'Username' },
          { title: 'Email' },
          { title: 'Role' },
          { title: 'Agency Name' },
          { title: 'Manage' }
        ],
            responsive: true,
            order: [1, 'asc']
        });
    });
    </script>

</body>
</html>");

?>