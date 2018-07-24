<?php
// Since every page besides the sql pages has a navbar in it, this security check is essentially carried over to every page
if (isset($_SESSION["aceUserID"])) {
	
} else {die ("There is not a user signed in. Please sign in <a href='loginHome.php'>here</a>");};

$aceCurrentUse = $_SESSION["aceCurrentUse"];

//SQL Connection
$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()) {
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n ");};

// Grabs the information from the current page to see if a semester filter is needed
if ($semesterNeeded) {
	$sqlSemester = "SELECT semesterID, semesterName
		FROM SEMESTER
		ORDER BY semesterStart DESC;";
	$resultSemester = mysqli_query($con, $sqlSemester);	
}

mysqli_close($con);
echo("
<!-- Navigation -->




<nav class='navbar navbar-default navbar-static-top' role='navigation' style='margin-bottom: 0'>

<div class='navbar-header'>


	
		<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-collapse'>
			<span class='sr-only'>Toggle navigation</span>
			<span class='icon-bar'></span>
			<span class='icon-bar'></span>
			<span class='icon-bar'></span>
		</button>


<img src='images/iu-trident.jpg' style='max-width:154px;max-height:70px '>

 <a class='navbar-brand'>ACE Time System</a>");




		if (isset($currentSemesterName)) {
			echo("<a class='navbar-brand'>". $currentSemesterName ."</a>");
		} else { echo("<a class='navbar-brand'>All Semesters</a>");};
		echo("
	</div>
	<!-- /.navbar-header -->

	<ul class='nav navbar-top-links navbar-right'>");
		if ($semesterNeeded) {
			echo("<li class='dropdown'>
				<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
					</i> Semester <i class='fa fa-caret-down'></i>
				</a>
				<ul class='dropdown-menu dropdown-user'>
					<form method='POST' action='". basename($_SERVER['PHP_SELF']) ."'>");
						if (mysqli_num_rows($resultSemester) > 0) {
							while ($row = mysqli_fetch_assoc($resultSemester)) {
								echo("<input name='semester' type='submit' value='". $row["semesterID"] .", ". $row["semesterName"] . "'>". $row["semesterName"] ."<br>");
							};
						};
						
					echo("<input name='semester' type='submit' value='0, All Semesters'>All Semesters<br>
					</form>
				</ul>
			</li>");
		};
		echo("<li class='dropdown'>
			<a class='dropdown-toggle' data-toggle='dropdown' href='#'>
				<i class='fa fa-user fa-fw'></i> <i class='fa fa-caret-down'></i>
			</a>
			<ul class='dropdown-menu dropdown-user'>
				<li><a href='#'><i class='fa fa-user fa-fw'></i> User Profile</a></li>
				<li><a href='#'><i class='fa fa-gear fa-fw'></i> Settings</a></li>
				<li class='divider'></li>
				<form action='sqlHome.php' method='post'>
					<li><button type='submit' name='ToDo' value= 'logOut'><i class='fa fa-sign-out fa-fw'></i> Logout</button></li>
				</form>
				</li>
			</ul>
		</li>
	</ul>

	<div class='navbar-default sidebar' role='navigation'>
		<div class='sidebar-nav navbar-collapse'>
			<ul class='nav' id='side-menu'>");
				if ($aceCurrentUse == "loginServiceLearner.php") {
					echo ("<li> <a href='loginServiceLearner.php'><i class='fa fa-clock-o fa-fw'></i> Clock In/Out</a></li>
					<li> <a href='loginHome.php'><i class='fa fa-floppy-o fa-fw'></i> Export Data</a></li>");
				} elseif ($aceCurrentUse == "exportData.php") {
					echo ("<li> <a href='loginHome.php'><i class='fa fa-clock-o fa-fw'></i> Clock In/Out</a></li>
					<li> <a href='exportData.php'><i class='fa fa-floppy-o fa-fw'></i> Export Data</a></li>");
					if ($_SESSION["aceAccessLevel"] == 3 or $_SESSION["aceAccessLevel"] == 1) {
						echo ("
						<li> <a href='#'><i class='fa fa-user-o fa-fw'></i> Manage Data<span class='fa arrow'></span></a>
							<ul class='nav nav-second-level'>
								<li> <a href='listServiceLearner.php'> Manage Service Learners</a> </li>
								<li> <a href='listShift.php'> Manage Shifts</a> </li>
								<li> <a href='listFaculty.php'> Manage Faculty</a> </li>
								<li> <a href='listSection.php'> Manage Sections</a> </li>
								<li> <a href='listCourse.php'> Manage Courses</a> </li>");
								if ($_SESSION["aceAccessLevel"] == 3) {
									echo("
									<li> <a href='listUser.php'> Manage Users</a> </li>
									<li> <a href='listAgency.php'> Manage Agency</a> </li>
									<li> <a href='listSemester.php'> Manage Semesters</a> </li>");
								};
							echo ("
							</ul>
						</li>");
					};
				};
			echo("</ul>
		</div>
	</div>
</nav>");
?>