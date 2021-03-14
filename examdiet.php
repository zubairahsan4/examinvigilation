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

$update = false; //set variable to false, this variable determines whether the user wants to edit details or not

//variable declaration, empty strings
$d = ""; 
$s = "";
$e = "";

//Database Connection.
include_once "DB_Connect.php";

if (isset($_GET['del'])) //if 'del' is mentioned in the url
{
    $id = $_GET['del']; //get the value from the url and store in a variable

	mysqli_query($mysqli, "DELETE FROM diet WHERE Diet_ID=$id"); //deletes diet from the diet table using diet id
	header('Location: examdiet.php'); //refreshes the page
}

if (isset($_GET['edit'])) //if 'edit' is mentioned in the url
{
    $id = $_GET['edit']; //get the value from the url and store in a variable
    $update = true; //set variable as true, this means the records are to be edited now

    $record = mysqli_query($mysqli, "SELECT * FROM diet WHERE Diet_ID=$id"); //loads all data of a diet using diet it

    if (count($record) == 1 ) //makes sure to get only one record
    {
        $n = mysqli_fetch_array($record); //uses array to fetch all the columns
        $d = $n['ExamDiet']; //stores examdiet in a variable to display in the text box
        $s = $n['StartDate']; //stores startdate in a variable to display in the text box
        $e = $n['EndDate']; //stores enddate in a variable to display in the text box
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exam Diets</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="examdiet.php" class="active">Exam Diets</a>
<a href="invigilator.php">Invigilators</a>
<a href="timetables.php">Lecturer's Timetables</a>
<a href="login.php?Logout">Logout</a>
</div>
<form action="" method="post">
<center>
<h2>Add New Exam Diet</h2>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<div>
<h3><input class="input2" type="text" name="diet" placeholder="Exam Diet" value="<?php echo $d; //display retrieved diet name ?>"/>
&nbspFrom:<input class="input2" type="date" name="sdate" placeholder="Start Date" value="<?php echo $s; //display retrieved start date of the diet ?>"/>    
&nbsp To:<input class="input2" type="date" name="edate" placeholder="End Date" value="<?php echo $e; //display retrieved end date of the diet ?>"/> <br>
<?php if ($update == true): //if update is set to true show update button ?>
<button class="button2" type="submit" name="update">Update</button><br> <br>
<?php else: //otherwise show add button ?>
<button class="button2" type="submit" name="add">Add</button><br> <br>
<?php endif ?>
</div>

<?php
if(isset($_POST["add"])) //If the button named 'add' and called 'Add' is pressed then:
{   
    //Store the values from each text box on the page into different variables.
    $diet = $mysqli->escape_string($_POST["diet"]);
    $sdate = $mysqli->escape_string($_POST["sdate"]);
    $edate = $mysqli->escape_string($_POST["edate"]);

    //Check if variables are empty - meaning whether the user has entered anything in the input boxes or not.
    if(empty($diet) || empty($sdate) || empty($edate)) 
    {
        //Inform user that they have left a field empty.
        echo "You cannot leave any field empty. Try again.";
        exit();
    }

    //Insert the values entered by the user in all the fields on the page to table called diet in the database using the variables above.
    $sql = "INSERT INTO diet (ExamDiet, StartDate, EndDate, EDID) 
    VALUES ('$diet', '$sdate', '$edate', '$user_id')";
    mysqli_query($mysqli, $sql); //executes sql query

    header('Location: examdiet.php'); //refresh the page
    exit();
}
if(isset($_POST["update"])) //If the button named 'update' and called 'Update' is pressed then:
{   
    //Store the values from each text box on the page into different variables.
    $diet = $mysqli->escape_string($_POST["diet"]);
    $sdate = $mysqli->escape_string($_POST["sdate"]);
    $edate = $mysqli->escape_string($_POST["edate"]);
    
    //Check if variables are empty - meaning whether the user has entered anything in the input boxes or not.
    if(empty($diet) || empty($sdate) || empty($edate)) 
    {
        //Inform user that they have left a field empty.
        echo "You cannot leave any field empty. Try again.";
        exit();
    }
    
    //Update the values entered by the user in all the fields on the page to table called diet in the database using the variables above.
    $sql = "UPDATE diet SET ExamDiet='$diet', StartDate='$sdate', EndDate='$edate', EDID='$user_id' WHERE Diet_ID = '$id'";
    mysqli_query($mysqli, $sql); //execute sql query
    
    header('Location: examdiet.php'); //refresh the page
    exit();
}
?>
<center>
<h2>Existing Exams Diets</h2>
<table class="data-table">
<thead>
<tr>
<th>Exam Diet</th>
<th>Starting Date</th>
<th>Ending Date</th>
<th>Courses</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<?php
$sql = 'SELECT * FROM diet'; //retrieves all data from the diet table
$query = mysqli_query($mysqli, $sql); //executes sql query

while ($row = mysqli_fetch_array($query)) //loop through all records one by one
{    
    $Diet_ID = $row["Diet_ID"]; //store diet id in a variable
    $Diet = $row["ExamDiet"]; //store diet name in a variable

    //display all existing diets in a data table and dynamically create View courses button as well as Edit and Delete buttons
    echo "<tr>
        <td>".$row["ExamDiet"]."</td>
        <td>".$row["StartDate"]."</td>
        <td>".$row["EndDate"]."</td>
        <td><a href='exam.php?dietid=$Diet_ID' class='view'>View</td>
        <td><a href='examdiet.php?edit=$Diet_ID' class='edit'>Edit</a> 
        <a href='examdiet.php?del=$Diet_ID' class='del'>Delete</a></td>";
}
?>
</tbody>
</table>
</form>
</body>
</html>