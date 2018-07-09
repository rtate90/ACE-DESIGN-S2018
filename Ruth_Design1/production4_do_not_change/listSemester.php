<?php
session_start();
// determines if a semester filter is needed for the navbar
$semesterNeeded = False;

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

if (mysqli_connect_errno())
{echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$sql = "SELECT s.semesterID as sID, s.semesterName as sName 
FROM SEMESTER as s;";

$result = mysqli_query($con, $sql);

$var_success = mysqli_real_escape_string($con, $_GET['success']);

mysqli_close($con);
	
echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ACE Semesters</title>
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


echo("
			<div id='page-wrapper'>
            <div class='row'>
                <div class='col-lg-12'>
                    <h1 class='page-header'>Semester List</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
");

 if ($var_success == 'updated') {
   echo("
  <div class='row'>
    <div class='alert alert-success'><strong>Semester Updated Successfully</strong></div>
  </div>
   ");
 };

 if ($var_success == 'deleted') {
   echo("
  <div class='row'>
    <div class='alert alert-success'><strong>Semester Deleted Successfully</strong></div>
  </div>
   ");
 };

 echo("  
            <div class='row'>
                <div class='col-lg-12'>
                <p class='text-right'><a role=\"button\" class=\"btn btn-lg btn-success\" href='addSemester.php'> Add New Semester </a></p>
                    <div class='panel panel-default'>
                        <!-- /.panel-heading -->
                        <div class='panel-body'>
                            <table width='100%' class='table table-striped table-bordered table-hover' id='dataTables-semester'>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    <script>

    var dataSet = [
	");

      if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
              echo "['" . $row["sName"] . "',
              '<form action=\"editSemester.php\" method=\"get\" style=\"display:inline\"> <button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"semesterID\" value=\"" . $row["sID"] .
              "\">Edit</button> </form>'],";
		  };
      } else {
          echo "['0 results']";
      };

echo ("
    ]

    $(document).ready(function() {
        $('#dataTables-semester').DataTable({
          data: dataSet,
          columns: [
		  { title: 'Semester' },
          { title: 'Manage' }
        ],
            responsive: true,
            order: [1, 'asc']
        });
    });
    </script>

	</body>
</html>");
/*
Used before database was updated
{ title: 'Address' },
          { title: 'City' },
          { title: 'State' },
          { title: 'Zip Code' },
          { title: 'Phone' },
*/

?>
