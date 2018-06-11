<?php
session_start();
// determines if a semester filter is needed for the navbar
// false means that it will only show the most recent semester
$semesterNeeded = False;

if ($_SESSION["aceCurrentUse"] != "exportData.php"){
	die("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");
};

if (time() > $_SESSION['aceDiscardAfter']) {
    // This session has worn out its welcome, kill it and start a brand new one
    session_unset();
    session_destroy();
	session_start();
	die("Your session has expired. Please log in once again. Click <a href='phpLoginPage.php'>here</a> to be redirected to the login page");
}
$_SESSION["aceDiscardAfter"] = time() + $_SESSION["aceDiscardAfterTime"];

if ($_SESSION["aceAccessLevel"] == 3) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");

if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$var_userID = (int)mysqli_real_escape_string($con, $_GET['userID']);
$var_success = mysqli_real_escape_string($con, $_GET['success']);

$sql = "SELECT u.userID, u.userFirstName, u.userLastName, u.userUsername, u.userEmail, u.userPassword, u.userAccessLevel, u.userRole, u.agencyID
FROM USER as u
WHERE u.userID = " . $var_userID . ";";
$result = mysqli_query($con, $sql);

$agencySQL = "SELECT * FROM AGENCY;";
$agencyResult = mysqli_query($con, $agencySQL);

mysqli_close($con);

if (mysqli_num_rows($result) > 1) {exit("Error: Multiple Users with same UserID");};

$formData = mysqli_fetch_assoc($result);

echo ("
<!DOCTYPE html>
<html lang='en'>

<head>

    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>MCCSC Demo Calendar</title>
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
     <h1 class='page-header'>Edit User</h1>
 </div>
 </div>
  ");

 if ($var_success == 'updated') {
   echo("
<div class='row'>
<div class='alert alert-success'><strong>Entry Updated Successfully</strong></div>
</div>
   ");
 };

 echo("

  <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                 <p class='text-right'><a href='listUser.php'> View Existing Users </a></p>
                   <form action='sqlUser.php' method='post'>
                         <div class='form-group'>
                             <label>First Name</label>
                             <input type ='text' name='userFirstName' class='form-control' value='" . $formData["userFirstName"] . "'>
                         </div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Last Name</label>
                             <input type='text' name='userLastName' class='form-control' value='" . $formData["userLastName"] . "'>
                         </div>
                     </div>
                 </div>
             </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Username</label>
                             <input type='text' name='userUsername' class='form-control' value='" . $formData["userUsername"] . "'>
                         </div>
                   </div>
                 </div>
             </div>
<div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>
                         <div class='form-group'>
                             <label>Email</label>
                             <input type='text' name='userEmail' class='form-control' value='" . $formData["userEmail"] . "'>
                         </div>
                     </div>
                 </div>
             </div>
<div class='panel-body'>
           <div class='row'>
               <div class='col-lg-6'>
                       <div class='form-group'>
                           <label>Password</label>
                           <input type='text' name='userPassword' class='form-control' value='" . $formData["userPassword"] . "'>
                       </div>
                   </div>
               </div>
           </div>
<div class='panel-body'>
           <div class='row'>
               <div class='col-lg-6'>
                       <div class='form-group'>
                            <label>Role</label>
                            <select name='userAccessLevel' class='form-control' value=". $formData["userAccessLevel"] .">");
							    if ($formData["userAccessLevel"] == 1) {
									echo("<option value=1 selected>ACE</option>
										<option value=2>Agency</option>
										<option value=3>Admin</option>");
								} elseif ($formData["userAccessLevel"] == 2) {
									echo("<option value=2 selected>Agency</option>
										<option value=1>ACE</option>
										<option value=3>Admin</option>");
								} else {
									echo("<option value=3 selected>Admin</option>
										<option value=1>ACE</option>
										<option value=2>Agency</option>");
								};
                            echo("
                            </select>
                       </div>
                   </div>
               </div>
           </div>
 <div class='panel-body'>
             <div class='row'>
                 <div class='col-lg-6'>

                         <div class='form-group'>
                             <label>Agency</label>
                             <select name='agencyID' class='form-control'>
                             ");

                             if (mysqli_num_rows($agencyResult) > 0) {
                                 while($row = mysqli_fetch_assoc($agencyResult)) {
                                   if ($row["agencyID"] == $formData["agencyID"]) {
                                     echo ("<option selected value='" . $row["agencyID"] . "'>" . $row["agencyName"] . "</option>");
                                   } else {
                                     echo ("<option value='" . $row["agencyID"] . "'>" . $row["agencyName"] . "</option>");
                                   };
                                 };
                             } else {
                                 echo "['0 results']";
                             };

                             echo("
                             </select

                             </select>
                         </div>
                     </div>
                 </div>
             </div>
		<input type='hidden' name='userID' value='" . $var_userID . "'>
		<button type='submit' class='btn btn-default pull-left' name= 'ToDo' value='edit'>Update Entry</button>
		<button type='submit' class='btn btn-danger pull-left' name= 'ToDo' value='delete'>Delete Entry</button>
</form>                      
</body>
</html>");

?>
