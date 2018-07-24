<?php
session_start();

$connect = mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018"); 
header('Content-Type: text/csv; charset=utf-8');  
header('Content-Disposition: attachment; filename=data.csv'); 
$output = fopen("php://output", "w");

if ($_POST["semesterID"] == 0) {
	if ($_SESSION["aceAccessLevel"] == 3) {
		
		//user 
		$sqlUser = "SELECT * FROM USER ORDER BY userID ASC";

		//agency
		$sqlAgency = "SELECT * FROM AGENCY ORDER BY agencyID ASC";

		//semester
		$sqlSemester = "SELECT * FROM SEMESTER ORDER BY semesterID ASCS"; 
		
		//section 
		$sqlSection = "SELECT * FROM SECTION ORDER BY sectionID ASC";

		//course 
		$sqlCourse = "SELECT * FROM COURSE ORDER BY courseID ASC";
		 
		//faculty
		$sqlFaculty = "SELECT * FROM FACULTY ORDER BY facultyID ASC";

		//service learner  
		$sqlServiceLearner = "SELECT * FROM SERVICE_LEARNER ORDER BY serviceLearnerID ASC"; 

		//shift
		$sqlShift = "SELECT * FROM SHIFT ORDER BY shiftID ASC;";
		
		// AGGREGATE DATA //
		
		// Aggregate data for faculty by service learners and their sections
		$sqlAggregateSection = "SELECT CONCAT(sl.serviceLearnerFirstName, ' ', sl.serviceLearnerLastName) as serviceLearnerName, 
			sl.serviceLearnerUsername as serviceLearnerUsername,
			sc.sectionNumber as sectionNumber,
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			c.courseName as courseName,
			CONCAT(f.facultyFirstName, ' ',f.facultyLastName) as facultyName,
			a.agencyName as agencyName,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted, 
			sc.sectionHoursNeeded as hoursNeeded
		FROM SHIFT as s, SECTION as sc, COURSE as c, SERVICE_LEARNER as sl, FACULTY as f, SEMESTER as sm, AGENCY as a
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND
			s.sectionID = sc.sectionID AND
			sc.courseID = c.courseID AND
			sc.semesterID = sm.semesterID AND
			sc.facultyID = f.facultyID AND 
			sc.agencyID = a.agencyID
		GROUP BY sc.sectionID, sl.serviceLearnerID;";
		
		// Aggregate data for faculty
		$sqlAggregateFaculty = "SELECT CONCAT(f.facultyFirstName, ' ', f.facultyLastName) as facultyName, 
			sc.sectionNumber as sectionNumber, 
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded,
			COUNT(sl.serviceLearnerID) as totalNumStudents
		FROM SECTION as sc, FACULTY as f, COURSE as c, SHIFT as sh, SERVICE_LEARNER as sl
		WHERE sc.facultyID = f.facultyID AND
			sc.courseID = c.courseID AND 
			sc.sectionID = sh.sectionID AND 
			sh.serviceLearnerID = sl.serviceLearnerID
		GROUP BY sc.sectionID;";
		
		// Aggregate data for agencies
		$sqlAggregateAgency = "SELECT a.agencyName as agencyName,
			COUNT(sc.sectionID) as totalNumberSections,
			COUNT(sl.serviceLearnerID) as totalNumberServiceLearners,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded
		FROM SERVICE_LEARNER as sl, SHIFT as sh, SECTION as sc, AGENCY as a
		WHERE sl.serviceLearnerID = sh.serviceLearnerID AND
			sh.sectionID = sc.sectionID AND
			sc.agencyID = a.agencyID
		GROUP BY a.agencyID;";

	} elseif ($_SESSION["aceAccessLevel"] == 2 or $_SESSION["aceAccessLevel"] == 1) {
		  
		//agency
		$sqlAgency = "SELECT * 
		FROM AGENCY 
		WHERE agencyID = ". $_SESSION["aceAgencyID"] ." 
		ORDER BY agencyID ASC";
		
		//semester
		$sqlSemester = "SELECT * 
		FROM SEMESTER 
		ORDER BY semesterID ASC";  

		//section
		$sqlSection = "SELECT * 
		FROM SECTION 
		WHERE agencyID = ". $_SESSION["aceAgencyID"] ." 
		ORDER BY sectionID ASC";  

		//course 
		$sqlCourse = "SELECT c.courseID , c.courseName, c.courseDepartment, c.courseCode
		FROM COURSE AS c, SECTION AS s 
		WHERE c.courseID = s.courseID AND 
			s.agencyID = ". $_SESSION["aceAgencyID"] ." 
		GROUP BY c.courseID
		ORDER BY c.courseID ASC"; 
		
		//faculty
		$sqlFaculty = "SELECT f.facultyID, f.facultyFirstName, f.facultyLastName
		FROM FACULTY AS f, SECTION AS s 
		WHERE f.facultyID=s.facultyID AND 
			s.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY f.facultyID
		ORDER BY f.facultyID ASC";  

		//service learner
		$sqlServiceLearner = "SELECT sl.serviceLearnerID, sl.serviceLearnerFirstName, sl.serviceLearnerLastName, sl.serviceLearnerUsername, sl.serviceLearnerEmail
		FROM SERVICE_LEARNER AS sl, SHIFT AS sh, SECTION AS se 
		WHERE sl.serviceLearnerID=sh.serviceLearnerID AND 
			sh.sectionID=se.sectionID AND 
			se.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY sl.serviceLearnerID
		ORDER BY sl.serviceLearnerID ASC";
		
		//shift
		$sqlShift = "SELECT sh.shiftID, sh.serviceLearnerID, sh.sectionID, sh.clockIn, sh.clockOut, sh.missedIn, sh.missedOut, sh.shiftComment
		FROM SHIFT AS sh, SECTION AS se 
		WHERE sh.sectionID=se.sectionID AND 
			se.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY sh.shiftID
		ORDER BY sh.shiftID ASC"; 
		
		// AGGREGATE DATA //
		
		// Aggregate data for faculty by service learners and their sections
		$sqlAggregateSection = "SELECT CONCAT(sl.serviceLearnerFirstName, ' ', sl.serviceLearnerLastName) as serviceLearnerName, 
			sl.serviceLearnerUsername as serviceLearnerUsername,
			sc.sectionNumber as sectionNumber,
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			c.courseName as courseName,
			CONCAT(f.facultyFirstName, ' ',f.facultyLastName) as facultyName,
			a.agencyName as agencyName,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted, 
			sc.sectionHoursNeeded as hoursNeeded
		FROM SHIFT as s, SECTION as sc, COURSE as c, SERVICE_LEARNER as sl, FACULTY as f, SEMESTER as sm, AGENCY as a
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND
			s.sectionID = sc.sectionID AND
			sc.courseID = c.courseID AND
			sc.semesterID = sm.semesterID AND
			sc.facultyID = f.facultyID AND 
			sc.agencyID = a.agencyID AND
			a.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY sc.sectionID, sl.serviceLearnerID;";
		
		// Aggregate data for faculty
		$sqlAggregateFaculty = "SELECT CONCAT(f.facultyFirstName, ' ', f.facultyLastName) as facultyName, 
			sc.sectionNumber as sectionNumber, 
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded,
			COUNT(sl.serviceLearnerID) as totalNumStudents
		FROM SECTION as sc, FACULTY as f, COURSE as c, SHIFT as sh, SERVICE_LEARNER as sl
		WHERE sc.facultyID = f.facultyID AND
			sc.courseID = c.courseID AND 
			sc.sectionID = sh.sectionID AND 
			sh.serviceLearnerID = sl.serviceLearnerID AND
			sc.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY sc.sectionID;";
		
		// Aggregate data for agencies
		$sqlAggregateAgency = "SELECT a.agencyName as agencyName,
			COUNT(sc.sectionID) as totalNumberSections,
			COUNT(sl.serviceLearnerID) as totalNumberServiceLearners,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded
		FROM SERVICE_LEARNER as sl, SHIFT as sh, SECTION as sc, AGENCY as a
		WHERE sl.serviceLearnerID = sh.serviceLearnerID AND
			sh.sectionID = sc.sectionID AND
			sc.agencyID = a.agencyID AND
			a.agencyID = ". $_SESSION["aceAgencyID"] ."
		GROUP BY a.agencyID;";

	} else {die("There is not a user signed in. Please sign in <a href='loginHome.php'>here</a>");};	
} else {
	if ($_SESSION["aceAccessLevel"] == 3) {
		
		//user 
		$sqlUser = "SELECT * FROM USER ORDER BY userID ASC";

		//agency
		$sqlAgency = "SELECT * FROM AGENCY ORDER BY agencyID ASC";

		//semester
		$sqlSemester = "SELECT * 
		FROM SEMESTER
		WHERE semesterID = ". $_POST["semesterID"] ."
		ORDER BY semesterID ASC"; 
		
		//section 
		$sqlSection = "SELECT * 
		FROM SECTION
		WHERE semesterID = ".$_POST["semesterID"] ."
		ORDER BY sectionID ASC";

		//course 
		$sqlCourse = "SELECT c.courseID , c.courseName, c.courseDepartment, c.courseCode
		FROM COURSE as c, SECTION as sc
		WHERE c.courseID = sc.courseID AND 
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY c.courseID
		ORDER BY c.courseID ASC";
		 
		//faculty
		$sqlFaculty = "SELECT f.facultyID, f.facultyFirstName, f.facultyLastName
		FROM FACULTY as f, SECTION as sc
		WHERE f.facultyID = sc.facultyID
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY f.facultyID
		ORDER BY f.facultyID ASC";

		//service learner  
		$sqlServiceLearner = "SELECT sl.serviceLearnerID, sl.serviceLearnerFirstName, sl.serviceLearnerLastName, sl.serviceLearnerUsername, sl.serviceLearnerEmail
		FROM SERVICE_LEARNER as sl, SHIFT as sh, SECTION as sc
		WHERE sl.serviceLearnerID=sh.serviceLearnerID AND 
			sh.sectionID=sc.sectionID AND 
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sl.serviceLearnerID
		ORDER BY sl.serviceLearnerID ASC"; 

		//shift
		$sqlShift = "SELECT sh.shiftID, sh.serviceLearnerID, sh.sectionID, sh.clockIn, sh.clockOut, sh.missedIn, sh.missedOut, sh.shiftComment 
		FROM SHIFT as sh, SECTION as sc
		WHERE sh.sectionID = sc.sectionID AND
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sh.shiftID
		ORDER BY sh.shiftID ASC;";
		
		// AGGREGATE DATA //
		
		// Aggregate data for faculty by service learners and their sections
		$sqlAggregateSection = "SELECT CONCAT(sl.serviceLearnerFirstName, ' ', sl.serviceLearnerLastName) as serviceLearnerName, 
			sl.serviceLearnerUsername as serviceLearnerUsername,
			sc.sectionNumber as sectionNumber,
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			c.courseName as courseName,
			CONCAT(f.facultyFirstName, ' ',f.facultyLastName) as facultyName,
			a.agencyName as agencyName,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted, 
			sc.sectionHoursNeeded as hoursNeeded
		FROM SHIFT as s, SECTION as sc, COURSE as c, SERVICE_LEARNER as sl, FACULTY as f, SEMESTER as sm, AGENCY as a
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND
			s.sectionID = sc.sectionID AND
			sc.courseID = c.courseID AND
			sc.semesterID = sm.semesterID AND
			sc.facultyID = f.facultyID AND 
			sc.agencyID = a.agencyID AND
			sm.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sc.sectionID, sl.serviceLearnerID;";
		
		// Aggregate data for faculty
		$sqlAggregateFaculty = "SELECT CONCAT(f.facultyFirstName, ' ', f.facultyLastName) as facultyName, 
			sc.sectionNumber as sectionNumber, 
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded,
			COUNT(sl.serviceLearnerID) as totalNumStudents
		FROM SECTION as sc, FACULTY as f, COURSE as c, SHIFT as sh, SERVICE_LEARNER as sl
		WHERE sc.facultyID = f.facultyID AND
			sc.courseID = c.courseID AND 
			sc.sectionID = sh.sectionID AND 
			sh.serviceLearnerID = sl.serviceLearnerID AND
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sc.sectionID;";
		
		// Aggregate data for agencies
		$sqlAggregateAgency = "SELECT a.agencyName as agencyName,
			COUNT(sc.sectionID) as totalNumberSections,
			COUNT(sl.serviceLearnerID) as totalNumberServiceLearners,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded
		FROM SERVICE_LEARNER as sl, SHIFT as sh, SECTION as sc, AGENCY as a
		WHERE sl.serviceLearnerID = sh.serviceLearnerID AND
			sh.sectionID = sc.sectionID AND
			sc.agencyID = a.agencyID
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY a.agencyID;";

	} elseif ($_SESSION["aceAccessLevel"] == 2 or $_SESSION["aceAccessLevel"] == 1) {
		  
		//agency
		$sqlAgency = "SELECT * 
		FROM AGENCY 
		WHERE agencyID = ". $_SESSION["aceAgencyID"] ." 
		ORDER BY agencyID ASC";
		
		//semester
		$sqlSemester = "SELECT * 
		FROM SEMESTER 
		WHERE semesterID = ". $_POST["semesterID"] ."
		ORDER BY semesterID ASC";  

		//section
		$sqlSection = "SELECT * 
		FROM SECTION 
		WHERE agencyID = ". $_SESSION["aceAgencyID"] ." AND 
			semesterID = ". $_POST["semesterID"] ."
		ORDER BY sectionID ASC";  

		//course 
		$sqlCourse = "SELECT c.courseID , c.courseName, c.courseDepartment, c.courseCode
		FROM COURSE AS c, SECTION AS s 
		WHERE c.courseID = s.courseID AND 
			s.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			s.semesterID = ". $_POST["semesterID"] ."
		GROUP BY c.courseID
		ORDER BY c.courseID ASC"; 
		
		//faculty
		$sqlFaculty = "SELECT f.facultyID, f.facultyFirstName, f.facultyLastName
		FROM FACULTY AS f, SECTION AS s 
		WHERE f.facultyID=s.facultyID AND 
			s.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			s.semesterID = ". $_POST["semesterID"] ."
		GROUP BY f.facultyID
		ORDER BY f.facultyID ASC";  

		//service learner
		$sqlServiceLearner = "SELECT sl.serviceLearnerID, sl.serviceLearnerFirstName, sl.serviceLearnerLastName, sl.serviceLearnerUsername, sl.serviceLearnerEmail
		FROM SERVICE_LEARNER AS sl, SHIFT AS sh, SECTION AS se 
		WHERE sl.serviceLearnerID=sh.serviceLearnerID AND 
			sh.sectionID=se.sectionID AND 
			se.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			se.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sl.serviceLearnerID
		ORDER BY sl.serviceLearnerID ASC";
		
		//shift
		$sqlShift = "SELECT sh.shiftID, sh.serviceLearnerID, sh.sectionID, sh.clockIn, sh.clockOut, sh.missedIn, sh.missedOut, sh.shiftComment
		FROM SHIFT AS sh, SECTION AS se 
		WHERE sh.sectionID=se.sectionID AND 
			se.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			se.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sh.shiftID
		ORDER BY sh.shiftID ASC"; 
		
		// AGGREGATE DATA //
		
		// Aggregate data for faculty by service learners and their sections
		$sqlAggregateSection = "SELECT CONCAT(sl.serviceLearnerFirstName, ' ', sl.serviceLearnerLastName) as serviceLearnerName, 
			sl.serviceLearnerUsername as serviceLearnerUsername,
			sc.sectionNumber as sectionNumber,
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			c.courseName as courseName,
			CONCAT(f.facultyFirstName, ' ',f.facultyLastName) as facultyName,
			a.agencyName as agencyName,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, s.clockIn, s.clockOut)/3600), 2) as hoursCompleted, 
			sc.sectionHoursNeeded as hoursNeeded
		FROM SHIFT as s, SECTION as sc, COURSE as c, SERVICE_LEARNER as sl, FACULTY as f, SEMESTER as sm, AGENCY as a
		WHERE sl.serviceLearnerID = s.serviceLearnerID AND
			s.sectionID = sc.sectionID AND
			sc.courseID = c.courseID AND
			sc.semesterID = sm.semesterID AND
			sc.facultyID = f.facultyID AND 
			sc.agencyID = a.agencyID AND
			a.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sc.sectionID, sl.serviceLearnerID;";
		
		// Aggregate data for faculty
		$sqlAggregateFaculty = "SELECT CONCAT(f.facultyFirstName, ' ', f.facultyLastName) as facultyName, 
			sc.sectionNumber as sectionNumber, 
			CONCAT(c.courseDepartment, ' ', c.courseCode) as courseInfo,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded,
			COUNT(sl.serviceLearnerID) as totalNumStudents
		FROM SECTION as sc, FACULTY as f, COURSE as c, SHIFT as sh, SERVICE_LEARNER as sl
		WHERE sc.facultyID = f.facultyID AND
			sc.courseID = c.courseID AND 
			sc.sectionID = sh.sectionID AND 
			sh.serviceLearnerID = sl.serviceLearnerID AND
			sc.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY sc.sectionID;";
		
		// Aggregate data for agencies
		$sqlAggregateAgency = "SELECT a.agencyName as agencyName,
			COUNT(sc.sectionID) as totalNumberSections,
			COUNT(sl.serviceLearnerID) as totalNumberServiceLearners,
			ROUND(SUM(TIMESTAMPDIFF(SECOND, sh.clockIn, sh.clockOut)/3600), 2) as hoursCompleted, 
			SUM(sc.sectionHoursNeeded) as hoursNeeded
		FROM SERVICE_LEARNER as sl, SHIFT as sh, SECTION as sc, AGENCY as a
		WHERE sl.serviceLearnerID = sh.serviceLearnerID AND
			sh.sectionID = sc.sectionID AND
			sc.agencyID = a.agencyID AND
			a.agencyID = ". $_SESSION["aceAgencyID"] ." AND
			sc.semesterID = ". $_POST["semesterID"] ."
		GROUP BY a.agencyID;";

	} else {die("There is not a user signed in. Please sign in <a href='loginHome.php'>here</a>");};
};

//user
if (isset($sqlUser)) {
	fputcsv($output, array('userID','agencyID' , 'userFirstName', 'userLastName', 'userUserName', 'userEmail', 'userRole', 'userAccessLevel')); 
	$resultUser = mysqli_query($connect, $sqlUser);  
	while($row = mysqli_fetch_assoc($resultUser)) {  
		fputcsv($output, $row);  
	};
}

//agency
if (isset($sqlAgency)) {
	fputcsv($output, array('agencyID', 'agencyName'));
	$resultAgency = mysqli_query($connect, $sqlAgency);  
	while($row = mysqli_fetch_assoc($resultAgency)) {  
		fputcsv($output, $row);  
	};
}	

//semester
if (isset($sqlSemester)) {
	fputcsv($output, array('semesterID','semesterName'));  
	$resultSemester = mysqli_query($connect, $sqlSemester);  
	while($row = mysqli_fetch_assoc($resultSemester)) {  
		fputcsv($output, $row);  
	};
}

//section
if (isset($sqlSection)) {
	fputcsv($output, array('sectionID', 'courseID', 'facultyID', 'agencyID', 'semesterID', 'sectionNumber', 'sectionHoursNeeded')); 
	$resultSection = mysqli_query($connect, $sqlSection);  
	while($row = mysqli_fetch_assoc($resultSection)) {  
		fputcsv($output, $row);  
	}; 
}

//course
if (isset($sqlCourse)) {
	fputcsv($output, array('courseID','courseName', 'courseDepartment' , 'courseCode')); 
	$resultCourse = mysqli_query($connect, $sqlCourse);  
	while($row = mysqli_fetch_assoc($resultCourse)) {  
		fputcsv($output, $row);  
	}; 
}

//faculty
if (isset($sqlFaculty)) {
	fputcsv($output, array('facultyID','facultyFirstName', 'facultyLastName'));  
	$resultFaculty = mysqli_query($connect, $sqlFaculty);  
	while($row = mysqli_fetch_assoc($resultFaculty)) {  
		fputcsv($output, $row);  
	};
}

//service learner
if (isset($sqlServiceLearner)) {
	fputcsv($output, array('serviceLearnerID','serviceLearnerFirstName' , 'serviceLernerLastName', 'serviceLearnerUsername', 'serviceLearnerEmail'));
	$resultServiceLearner = mysqli_query($connect, $sqlServiceLearner);  
	while($row = mysqli_fetch_assoc($resultServiceLearner)) {  
		fputcsv($output, $row);  
	};	
}

//shift
if (isset($sqlShift)) {
	fputcsv($output, array('shiftID', 'serviceLearnerID', 'sectionID', 'clockIn', 'clockOut', 'missedIn', 'missedOut', 'comment'));  
	$resultShift = mysqli_query($connect, $sqlShift);  
	while($row = mysqli_fetch_assoc($resultShift)) {  
		fputcsv($output, $row);  
	};	
}

//sqlAggregateSection
if (isset($sqlAggregateSection)) {
	fputcsv($output, array('serviceLearnerName', 'serviceLearnerUsername', 'sectionNumber', 'courseInfo', 'courseName', 'facultyName', 'agencyName', 'hoursComplete', 'hoursNeeded'));  
	$resultAggregateSection = mysqli_query($connect, $sqlAggregateSection);  
	while($row = mysqli_fetch_assoc($resultAggregateSection)) {  
		fputcsv($output, $row);  
	};	
}

//sqlAggregateFaculty
if (isset($sqlAggregateFaculty)) {
	fputcsv($output, array('facultyName', 'sectionNumber', 'courseInfo', 'totalHoursCompleted', 'totalHoursNeeded', 'totalNumberStudents'));  
	$resultAggregateFaculty = mysqli_query($connect, $sqlAggregateFaculty);  
	while($row = mysqli_fetch_assoc($resultAggregateFaculty)) {  
		fputcsv($output, $row);  
	};	
}

//sqlAggregateAgency
if (isset($sqlAggregateAgency)) {
	fputcsv($output, array('agencyName', 'totalNumberSections', 'totalNumberServiceLearners', 'totalHoursCompleted', 'totalHoursNeeded'));  
	$resultAggregateAgency = mysqli_query($connect, $sqlAggregateAgency);  
	while($row = mysqli_fetch_assoc($resultAggregateAgency)) {  
		fputcsv($output, $row);  
	};	
}

fclose($output); 
mysqli_close($con);
?>  