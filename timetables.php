<?php
$user_id = ""; //Set the variable that will store EDID from session to null.
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

//Store user's query in a variable by getting that value from the URL. 
if (!isset($_GET['page'])) //If the page value in URL is not set, then:
{
    $page = 1; //Value 1 will be stored as default in the variable that stores the page number. 
}
else //Otherwise:
{
    $page = $_GET['page']; //Get the value of page in the URL and store it in the variable.
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lecturer's Timetables</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="examdiet.php">Exam Diets</a>
<a href="invigilator.php">Invigilators</a>
<a href="timetables.php" class="active">Lecturer's Timetables</a>
<a href="login.php?Logout">Logout</a>
</div>
<center>
<h2>Lecturer's Details and Timetables</h2>

<div class='container'>

<?php
//Set the number of posts per page. Changing this number will change the number of posts per page for all conditions below.
$results_per_page = 10;

//Database Connection.
include "DB_Connect.php";

$sql = "SELECT * FROM lecturer"; //retrieves all data from the lecturer table
$query = mysqli_query($mysqli, $sql); //executes sql query
$number_of_results = mysqli_num_rows($query); //Count the number of records retrieved and save it in a variable.

//Divide the number of records retrived by the set (2) number of results per page.
$number_of_pages = ceil($number_of_results/$results_per_page); 
        
//Determine the first result on this page.
$this_page_first_result = ($page-1)*$results_per_page; 

$sql = "SELECT * FROM lecturer LIMIT ".$this_page_first_result.','.$results_per_page; //limits the data retrieved
$query = mysqli_query($mysqli, $sql); //executes sql query

while ($row = mysqli_fetch_array($query)) //Fetch all records one by one.
{
    $lecturerid = $row['Lecturer_ID']; //stores lecturerid in a variable
    $imagequery =  "SELECT * FROM timetable WHERE Lecturer_ID = $lecturerid"; //gets lecturer's timetable from the timetable table in the database
    $image = mysqli_query($mysqli, $imagequery); //executes sql query
    $getimage = mysqli_fetch_array($image); //retrieves images in an array
    echo "<div class='div'>
    <h4>Name: ".$row['Name']." <br>
    Department: ".$row['Department']."<br>    
    Email Address: ".$row['Email']."<br>
    Phone Number: ".$row['Phone']."</h4><hr>
    <center><h3>Current Timetable</h3> </center>
    <img style = 'width:100%; height:50%; margin-bottom: 5px;' src = 'images/".$getimage['Image_name']."' alt = 'No Image for this post.'/></div>";
}
   
for ($page=1;$page<=$number_of_pages;$page++) //Display the total number of pages at the bottom of the page in links so the user can browse through.
{
    echo "<a href=timetables.php?page=$page class='page'>$page</a>&nbsp;&nbsp;"; //page numbers
}
?>
<br> <br>
</form>
</body>
</html>