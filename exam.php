<?php
$user_id = ""; //Set the variable that will store EDID from session to zero.
session_start(); //Start session.
if(isset($_SESSION['EDID'])) //Check if the user has logged in or not using this session.
{
    //If the user has logged in, then store the EDID in a variable called user_id and let them continue on this page.
    $user_id = $_SESSION['EDID']; 
}
else 
{
    //If the user has not logged in, redirect them back to the login page.
    header("Location: login.php");
    exit();
}

$id = "null"; //set variable to null
$update = false; //set variable to false, this variable determines whether the user wants to edit details or not

//variable declaration, empty string
$p = "";
$c = "";
$t = "";
$n = "";
$d = "";

$s = "Select Session"; //variable for exam session, default value = Select Session

//Database Connection.
include_once "DB_Connect.php";

if (isset($_GET['dietid'])) //if 'diet' is mentioned in the url
{
    $id = $_GET['dietid']; //get the value from the url and store in a variable
    
    //retrieve all records from the diet table using this diet id
    $sql1 = "SELECT * FROM diet WHERE Diet_ID=$id";
    $query1 = mysqli_query($mysqli, $sql1);//execute sql query
    $row1 = mysqli_fetch_array($query1); //fetches records in an array
}

if (isset($_GET['del'])) //if 'del' is mentioned in the url
{
	$del = $_GET['del']; //get the value from the url and store in a variable
    
    //deletes the exam details from the exam table using exam id
    mysqli_query($mysqli, "DELETE FROM exam WHERE Exam_ID=$del"); 
    
    header("Location: exam.php?dietid=$id"); //refresh the page
}

if (isset($_GET['edit'])) //if 'del' is mentioned in the url
{
    $edit = $_GET['edit']; //get the value from the url and store in a variable
    $update = true; //set variable as true, this means the records are to be edited now
    $record = mysqli_query($mysqli, "SELECT * FROM exam JOIN datesession WHERE Exam_ID=$edit"); //loads all data of a diet using diet it

    if (count($record) == 1 ) //makes sure to get only one record
    {
        $r = mysqli_fetch_array($record); //uses array to fetch all the columns
        $p = $r['Programme']; //stores programme in a variable to display in the text box
        $c = $r['CourseName']; //stores course name in a variable to display in the text box
        $t = $r['TaughtBy']; //stores taught by in a variable to display in the text box
        $n = $r['NStudents']; //stores number of students in a variable to display in the text box
        $d = $r['ExamDate']; //stores exam date in a variable to display in the text box
        $s = $r['ExamSession']; //stores exam session in a variable to display in the text box
        $i = $r['Session_ID']; //stores session id in a variable
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exams</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="examdiet.php">Exam Diets</a>
<a href="invigilator.php">Invigilators</a>
<a href="timetables.php">Lecturer's Timetables</a>
<a href="login.php?Logout">Logout</a>
</div>
<center>
<form action="" method="post">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<h2>Exam Diet: <?php echo $row1["ExamDiet"]; //display name of the exam diet?></h2>
<div>
<input class="input2" type="text" name="prog" placeholder="Programme Name" value="<?php echo $p; //display programme name ?>"/>  
<input class="input2" type="text" name="course" placeholder="Course Name" value="<?php echo $c; //display course name ?>"/>   
<input class="input2" type="text" name="tby" placeholder="Taught By" value="<?php echo $t; //display taught by ?>"/>
<input class="input2" type="number" name="nstud" placeholder="No. Of Students" min="1" value="<?php echo $n; //display number of students ?>"/>
<input class="input2" type="date" name="date" min="<?php echo $row1['StartDate']; //minimum is set by exam diet start date?>" max="<?php echo $row1['EndDate']; //maximum is also set by exam diet start date?>" value="<?php echo $d; //display exam date ?>"/>
<select name="session" class="input2" required>
<option value="<?php echo $s; //display session?>"><?php echo $s; ?></option>
<option value="09:00 - 12:00">9 am - 12 pm</option>
<option value="14:00 - 17:00">2 pm - 5 pm</option>
<option value="18:00 - 21:00">6 pm - 9 pm</option>
</select>
<br>
<?php if ($update == true): //if update is set to true show update button  ?>
<button class="button2" type="submit" name="update">Update</button><br> <br>
<?php else: //otherwise show add button ?>
<button class="button2" type="submit" name="add">Add</button><br> <br>
<?php endif ?>
</div>

<center>
<h2>Existing Exams</h2>
<table class="data-table">
<thead>
<tr>
<th>Programme</th>
<th>Course</th>
<th>Lecturer</th>
<th>No. of Students</th>
<th>Date</th>
<th>Session</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php
//retrieves all data from the exam table based on diet id
$sql = "SELECT * FROM exam WHERE Diet_ID = $id";
$query = mysqli_query($mysqli, $sql); //executes sql query

while ($row = mysqli_fetch_array($query)) //loop through all records one by one
{   
    $s_id = $row["Session_ID"]; //store session id in a variable
    $sqls = "SELECT * FROM datesession WHERE Session_ID = $s_id"; //retrieves everything from datesession table using session id
    $querys = mysqli_query($mysqli, $sqls); //executes sql query
    $row2 = mysqli_fetch_array($querys); //fetches records in an array

    $Exam_ID = $row["Exam_ID"]; //store exam id in a variable

    //display all existing exams in a data table and dynamically create Edit and Delete buttons
    echo "<tr>
    <td>".$row["Programme"]."</td>
    <td>".$row["CourseName"]."</td>
    <td>".$row["TaughtBy"]."</td>
    <td>".$row["NStudents"]."</td>
    <td>".$row2["ExamDate"]."</td>
    <td>".$row2["ExamSession"]."</td>
    <td><a href='exam.php?dietid=$id&edit=$Exam_ID' class='edit'>Edit</a> 
    <a href='exam.php?dietid=$id&del=$Exam_ID' class='del'>Delete</a></td>";
}

if(isset($_POST["add"])) //If the button named 'add' and called 'Add' is pressed then:
{   
    //Store the values from each text box on the page into different variables.
    $prog = $mysqli->escape_string($_POST["prog"]);
    $course = $mysqli->escape_string($_POST["course"]);
    $tby = $mysqli->escape_string($_POST["tby"]);
    $nstud = $mysqli->escape_string($_POST["nstud"]);
    $date = $mysqli->escape_string($_POST["date"]);
    $session = $mysqli->escape_string($_POST["session"]);
    
    //Check if variables are empty - meaning whether the user has entered anything in the input boxes or not.
    if(empty($prog) || empty($course) || empty($tby) || empty($nstud) || empty($date) || empty($session)) 
    {
        //Inform user that they have left a field empty.
        echo "You cannot leave any field empty. Try again.";
        exit();
    }

    //checks if the exam date and exam session entered by the user already exist in the same row in the datesession table
    $datesessioncheck = $mysqli->query("SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '$session'") or die(mysqli_error($mysqli)); 
    
    if($datesessioncheck->num_rows <= 0) //if the same date and session does not exist in the date session table
    {
        //creates a new record in the datesession table using the date and session by the user
        $dssql = "INSERT INTO datesession (ExamDate, ExamSession) VALUES ('$date', '$session')";
        mysqli_query($mysqli, $dssql); //executes sql query
       
        $ds_id = $mysqli->insert_id; //gets the id of the newly created field in the datesession table 

        //creates a new record in the exam table using the diet id, programme name, course name, taught by, nstudents, and session id entered by the user
        $sql = "INSERT INTO exam (Diet_ID, Programme, CourseName, TaughtBy, NStudents, Session_ID)
        VALUES ('$id', '$prog', '$course', '$tby', '$nstud', '$ds_id')";
        mysqli_query($mysqli, $sql); //executes sql query

        //sums the number of students for each exam belonging to the same session in the exam table
        $result = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $ds_id"); 
        $array = mysqli_fetch_array($result); //fetches data in an array
        $sum = $array['value']; //stores the sum value in a variable

        //for each 30 students there will be 1 invigilator required. Right now this sum can go until 360, more can be added
        if($sum<=30){$invreq=1;}elseif($sum<=60){$invreq=2;}elseif($sum<=90){$invreq=3;}
        elseif($sum<=120){$invreq=4;}elseif($sum<=150){$invreq=5;}elseif($sum<=180){$invreq=6;}
        elseif($sum<=210){$invreq=7;}elseif($sum<=240){$invreq=8;}elseif($sum<=270){$invreq=9;}
        elseif($sum<=300){$invreq=10;}elseif($sum<=330){$invreq=11;}elseif($sum<=360){$invreq=12;}

        //updates the invigilators required field in the date session table using the newly created session id
        $sqlupdate = "UPDATE datesession SET InvReq = $invreq WHERE Session_ID = $ds_id";
        mysqli_query($mysqli, $sqlupdate); //executes sql query
    }
    else
    {
        //If the session exists in the database then:
        if($row = $datesessioncheck->fetch_assoc())
        { 
            $ds_id = $row["Session_ID"]; //gets the id of the session from the datesession table 

            //creates a new record in the exam table using the diet id, programme name, course name, taught by, nstudents, and session id entered by the user
            $sql = "INSERT INTO exam (Diet_ID, Programme, CourseName, TaughtBy, NStudents, Session_ID)
            VALUES ('$id', '$prog', '$course', '$tby', '$nstud', '$ds_id')";
            mysqli_query($mysqli, $sql); //executes sql query
            
            //sums the number of students for each exam belonging to the same session in the exam table
            $result = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $ds_id"); 
            $array = mysqli_fetch_array($result); //fetches data in an array
            $sum = $array['value']; //stores the sum value in a variable
        
            //for each 30 students there will be 1 invigilator required. Right now this sum can go until 360, more can be added
            if($sum<=30){$invreq=1;}elseif($sum<=60){$invreq=2;}elseif($sum<=90){$invreq=3;}
            elseif($sum<=120){$invreq=4;}elseif($sum<=150){$invreq=5;}elseif($sum<=180){$invreq=6;}
            elseif($sum<=210){$invreq=7;}elseif($sum<=240){$invreq=8;}elseif($sum<=270){$invreq=9;}
            elseif($sum<=300){$invreq=10;}elseif($sum<=330){$invreq=11;}elseif($sum<=360){$invreq=12;}
        
            //updates the invigilators required field in the date session table using the session id
            $sqlupdate = "UPDATE datesession SET InvReq = $invreq WHERE Session_ID = $ds_id";
            mysqli_query($mysqli, $sqlupdate); //executes sql query
        }
    }
    header("Location: exam.php?dietid=$id"); //refreshes the page
    exit();
}

if(isset($_POST["update"])) //If the button named 'update' and called 'Update' is pressed then:
{   
    echo "errorrr";
    //Store the values from each text box on the page into different variables.
    $prog = $mysqli->escape_string($_POST["prog"]);
    $course = $mysqli->escape_string($_POST["course"]);
    $tby = $mysqli->escape_string($_POST["tby"]);
    $nstud = $mysqli->escape_string($_POST["nstud"]);
    $date = $mysqli->escape_string($_POST["date"]);
    $session = $mysqli->escape_string($_POST["session"]);
    
    //Check if variables are empty - meaning whether the user has entered anything in the input boxes or not.
    if(empty($prog) || empty($course) || empty($tby) || empty($nstud) || empty($date) || empty($session)) 
    {
        //Inform user that they have left a field empty.
        echo "You cannot leave any field empty. Try again.";
        exit();
    }

    //checks if the exam date and exam session entered by the user already exist in the same row in the datesession table
    $datesessioncheck = $mysqli->query("SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '$session'") or die(mysqli_error($mysqli)); 
    
    if($datesessioncheck->num_rows <= 0)//if the same date and session does not exist in the date session table
    {
        //creates a new record in the datesession table using the date and session by the user
        $dssql = "INSERT INTO datesession (ExamDate, ExamSession) VALUES ('$date', '$session')";
        mysqli_query($mysqli, $dssql); //executes sql query
       
        $ds_id = $mysqli->insert_id; //gets the id of the newly created field in the datesession table       

        //updates the chosen record using the edit variable in the exam table using the diet id, programme name, course name, taught by, nstudents, and session id entered by the user
        $sql = "UPDATE exam SET Programme='$prog', CourseName='$course', TaughtBy='$tby', NStudents='$nstud', Session_ID='$ds_id' 
        WHERE Exam_ID=$edit";
        mysqli_query($mysqli, $sql); //executes sql query

        //sums the number of students for each exam belonging to the same session in the exam table
        $result = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $ds_id"); 
        $array = mysqli_fetch_array($result); //fetches data in an array
        $sum = $array['value']; //stores the sum value in a variable

        //for each 30 students there will be 1 invigilator required. Right now this sum can go until 360, more can be added
        if($sum<=30){$invreq=1;}elseif($sum<=60){$invreq=2;}elseif($sum<=90){$invreq=3;}
        elseif($sum<=120){$invreq=4;}elseif($sum<=150){$invreq=5;}elseif($sum<=180){$invreq=6;}
        elseif($sum<=210){$invreq=7;}elseif($sum<=240){$invreq=8;}elseif($sum<=270){$invreq=9;}
        elseif($sum<=300){$invreq=10;}elseif($sum<=330){$invreq=11;}elseif($sum<=360){$invreq=12;}

        //updates the invigilators required field in the date session table using the newly created session id
        $sqlupdate = "UPDATE datesession SET InvReq = $invreq WHERE Session_ID = $ds_id";
        mysqli_query($mysqli, $sqlupdate); //executes sql query
    }
    else
    {
        //If the session exists in the database then:
        if($row = $datesessioncheck->fetch_assoc())
        { 
            $ds_id = $row["Session_ID"]; //gets the id of the session from the datesession table 

            //updates the chosen record using the edit variable in the exam table using the diet id, programme name, course name, taught by, nstudents, and session id entered by the user
            $sql = "UPDATE exam SET Programme='$prog', CourseName='$course', TaughtBy='$tby', NStudents='$nstud', Session_ID='$ds_id' 
            WHERE Exam_ID=$edit";
            mysqli_query($mysqli, $sql); //executes sql query

            //sums the number of students for each exam belonging to the same session in the exam table
            $result = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $i"); 
            $array = mysqli_fetch_array($result); //fetches data in an array
            $sum = $array['value']; //stores the sum value in a variable

            //for each 30 students there will be 1 invigilator required. Right now this sum can go until 360, more can be added
            if($sum<=30){$invreq=1;}elseif($sum<=60){$invreq=2;}elseif($sum<=90){$invreq=3;}
            elseif($sum<=120){$invreq=4;}elseif($sum<=150){$invreq=5;}elseif($sum<=180){$invreq=6;}
            elseif($sum<=210){$invreq=7;}elseif($sum<=240){$invreq=8;}elseif($sum<=270){$invreq=9;}
            elseif($sum<=300){$invreq=10;}elseif($sum<=330){$invreq=11;}elseif($sum<=360){$invreq=12;}

            //updates the invigilators required field in the date session table using the newly created session id
            $sqlupdate = "UPDATE datesession SET InvReq = $invreq WHERE Session_ID = $i";
            mysqli_query($mysqli, $sqlupdate); //executes sql query
        }
    }
    header("Location: exam.php?dietid=$id"); //refreshes the page
    exit();
}   
?>

</tbody>
</table>
</form>
</body>
</form>
</html>

