<?php
$user_id = ""; //Set the variable that will store Lecturer_ID from session to zero.
session_start(); //Start session.
if(isset($_SESSION['Lecturer_ID'])) //Check if the user has logged in or not using this session.
{
    //If the user has logged in, then store the Lecturer_ID in a variable called user_id and let them continue on this page.
    $user_id = $_SESSION['Lecturer_ID']; 
}
else 
{
    //If the user has not logged in, redirect them back to the login page.
    header("Location: login.php");
    exit();
}

//Database Connection.
include_once "DB_Connect.php";

$dietid="null"; //set variable to null
$date="null"; //set variable to null
$set = false; //set variable to false, this variable determines whether to show the dates in the diet

if(isset($_GET['diet'])) //if 'diet' is mentioned in the url
{
    $dietid = $_GET['diet']; //stores the diet value from the url into the variable named 'dietid'
    $set = true; //changes the variable called 'set' to true, meaning that now dates in this diet will be shown
}
if(isset($_GET['date'])) //if 'date' is mentioned in the url
{
    $date = $_GET['date']; //stores the date value from the url into the variable named 'date'
}
if(isset($_GET['volid'])) //if 'volid' is mentioned in the url
{
    $volid = $_GET['volid']; //stores the volid value (session chosen by the lecturer) from the url into the variable name 'volid'
    
    //creates a new field in the invigilator table and assigns a lecturer to that particular session
    $sql = "INSERT INTO invigilator (Session_ID, Lecturer_ID) VALUES ('$volid', '$user_id')"; 
    $query = mysqli_query($mysqli, $sql); //executes sql query

    //updates the datesession table and increases the count of Invigilators Assigned based on the session id taken from the url
    $sql = "UPDATE datesession SET InvAss = InvAss+1 WHERE Session_ID = $volid";
    $query = mysqli_query($mysqli, $sql); //executes sql query

    header("Location: invigilation.php?diet=$dietid&date=$date"); //refreshes the page
}
if(isset($_GET['wdrawid'])) //if 'wdrawid' is mentioned in the url
{
    $wdrawid = $_GET['wdrawid']; //stores the wdrawid value (session chosen by the lecturer) from the url into the variable name 'wdrawid'

    //removes the field in the invigilator table using the session id in the 'wdrawid' variable and the lecturer id
    $sql = "DELETE FROM invigilator WHERE Session_ID=$wdrawid AND Lecturer_ID=$user_id";
    $query = mysqli_query($mysqli, $sql); //executes sql query

    //updates the datesession table and decreases the count of Invigilators Assigned based on the session id taken from the url
    $sql = "UPDATE datesession SET InvAss = InvAss-1 WHERE Session_ID = $wdrawid";
    $query = mysqli_query($mysqli, $sql); //executes sql query

    header("Location: invigilation.php?diet=$dietid&date=$date"); //refreshes the page
}

$sql1 = "SELECT * FROM diet"; //retrieves everything from the diet table
$query1 = mysqli_query($mysqli, $sql1); //executes sql query

$sql2 = "SELECT * FROM diet WHERE Diet_ID = $dietid"; //retrieves a particular record from the diet table using dietid
$query2 = mysqli_query($mysqli, $sql2); //executes sql query
$row2 = mysqli_fetch_array($query2); //retrieves everything in an array
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invigilation</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="home.php">Home</a>
<a href="invigilation.php" class="active">Invigilation</a>
<a href="login.php?Logout">Logout</a>
</div>
<div class='container'>
<form action="" method="post">
<center>
<?php
echo "<h3>Select Exam Diet: <select class='input2' name='diet'>"; 
while ($row1 = mysqli_fetch_array($query1)) //loads all exam diets one by one using array in a drop down list
{
    echo "<option value='" . $row1['Diet_ID']. "'>" . $row1['ExamDiet'] . " </option>";
}
echo "</select> &nbsp <button class='button2' type='submit' name='submitdiet'>Submit Diet</button></h3>"; 

if(isset($_POST["submitdiet"])) //If the button named 'submitdiet' and called 'Submit Diet' is pressed then:
{
    $choice = $_POST['diet']; //variable stores the diet id from the form
    header("Location: invigilation.php?diet=$choice"); //the url is changed as diet is mentioned with a value
    exit();
}   
?>

<?php if ($set == true): //when set variable equals true than the following will be shown?>
<h3>Select Date: <input class="input2" type="date" name="date" value="<?php echo $date?>" min="<?php echo $row2['StartDate'] //minimum value is set to start date from the diet table?>" max="<?php echo $row2['EndDate'] //minimum value is set to start date from the diet table?>"/>
&nbsp <button class="button2" type="submit" name="submitdate">Submit Date</button><br></h3>
<?php endif ?>

<?php
if(isset($_POST["submitdate"])) //if the button named 'submitdate' and called 'Submit Date' is pressed then:
{
    //the date is retrieved from the input box and stored in a variable
    $date = $mysqli->escape_string($_POST["date"]);

    //header changes and now carries the diet id as well as the date
    header("Location: invigilation.php?diet=$dietid&date=$date");
}

if(isset($_GET['date'])) //gets the date value from the url
{
    $date = $_GET['date']; //stores the date value from the url in a variable

    //9 - 12

    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '09:00 - 12:00'
    $sql3 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '09:00 - 12:00'";
    $query3 = mysqli_query($mysqli, $sql3); //executes sql query
    
    //displays the details for the morning session on this date
    echo "<div class='post'>
    <div class='div'>
    <center><h2>Exam Sessions on $date</h2></center><hr>
    <h3>Morning Session 9am - 12pm </h3>";

    while($row3 = mysqli_fetch_array($query3)) //while loop retrieves data
    {
        $session = $row3['Session_ID']; //the id of the session is stored in a variable  
        echo "<h4> Invigilators Required: ".$row3['InvReq']."<br><br>
        Invigilators Assigned: ".$row3['InvAss']."<br><br>";

        //retrieves all exams with the same session i.e. '09:00 - 12:00'
        $sqll = "SELECT * FROM exam WHERE Session_ID = '$session'";
        $queryl = mysqli_query($mysqli, $sqll); //executes sql query

        while($rowl = mysqli_fetch_array($queryl)) //while loop goes through each record
        {
            $lec1 = $rowl['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }
        
        //searches the lecturer table for name and lecturer id matches to check whether this lecturer teacher a course in this exam session or not
        $namesql1 = $mysqli->query("SELECT * FROM lecturer WHERE Name LIKE '%$lec1%' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli));
        
        //if there is no such record
        if ($namesql1->num_rows <= 0)
        {
            //and if the Invigilators Required does not equal Invigilators Assigned in the database
            if ($row3['InvReq'] != $row3['InvAss'])
            {
                //searches in the invigilator table to see if this lecturer has already been assigned to this session
                $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                    
                if ($e->num_rows <= 0) //if there is no such record
                {
                    //a button named 'Volunteer' will appear so that the lecturer can volunteer for this session as an invigilator
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&volid=$session' class='vol'>Volunteer</a><br><br>"; 
                }
                else //if the lecturer is assigned to the session
                {
                    //a button named 'Withdraw' will appear so that the lecturer can withdraw from the invigilation duty for this session
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session' class='wdraw'>Withdraw</a><br><br>";
                }
            }
            elseif ($row3['InvReq'] == $row3['InvAss']) //if the Invigilators Required equals Invigilators Assigned in the database
            {
                //searches the invigilator table for this session id
                $e2 = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                
                if ($e2->num_rows > 0) //if there is such record
                {
                    //withdraw button will appear allowing the logged in lecturer to withdraw from the invigilation duty
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session' class='wdraw'>Withdraw</a><br><br>";
                }              
            }
        }

        //if the userid that is logged in matches the lecturer's id of the lecturer who is teaching a course in this exam session
        else
        {
            echo "No Action<br><br>"; //this message is shown
        }
    }
    echo "<hr>";

    //2 - 5

    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '14:00 - 17:00'
    $sql4 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '14:00 - 17:00'";
    $query4 = mysqli_query($mysqli, $sql4); //executes sql query
    
    //displays the details for the morning session on this date
    echo "<h3>Afternoon Session 2pm - 5pm </h3>";

    while($row4 = mysqli_fetch_array($query4)) //while loop retrieves data
    {
        $session2 = $row4['Session_ID']; //the id of the session is stored in a variable  
        echo "<h4> Invigilators Required: ".$row4['InvReq']."<br><br>
        Invigilators Assigned: ".$row4['InvAss']."<br><br>";

        //retrieves all exams with the same session i.e. '14:00 - 17:00'
        $sqll2 = "SELECT * FROM exam WHERE Session_ID = '$session2'";
        $queryl2 = mysqli_query($mysqli, $sqll2); //executes sql query
        
        while($rowl2 = mysqli_fetch_array($queryl2)) //while loop goes through each record
        {
            $lec2 = $rowl2['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }
        
        //searches the lecturer table for name and lecturerid matches to check whether this lecturer teaches a course in this exam session or not
        $namesql2 = $mysqli->query("SELECT * FROM lecturer WHERE Name LIKE '%$lec2%' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli));
        
        //if there is no such record 
        if ($namesql2->num_rows <= 0)
        {      
            //and if the Invigilators Required does not equal Invigilators Assigned in the database
            if ($row4['InvReq'] != $row4['InvAss'])
            {
                //searches in the invigilator table to see if this lecturer has already been assigned to this session
                $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session2' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                
                if ($e->num_rows <= 0) //if there is no such record
                {
                    //a button named 'Volunteer' will appear so that the lecturer can volunteer for this session as an invigilator
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&volid=$session2' class='vol'>Volunteer</a><br><br>"; 
                }
                else //if the lecturer is assigned to the session
                {
                    //a button named 'Withdraw' will appear so that the lecturer can withdraw from the invigilation duty for this session
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session2' class='wdraw'>Withdraw</a><br><br>";
                }
            }
            elseif ($row4['InvReq'] == $row4['InvAss'])//if the Invigilators Required equals Invigilators Assigned in the database
            {
                //searches the invigilator table for this session id and lecturer
                $e2 = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session2' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                
                if ($e2->num_rows > 0) //if there is such record
                {
                    //withdraw button will appear allowing the logged in lecturer to withdraw from the invigilation duty
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session2' class='wdraw'>Withdraw</a><br><br>";
                }
            }
        }

        //if the userid that is logged in matches the lecturer's id of the lecturer who is teaching a course in this exam session
        else
        {
            echo "No Action<br><br>"; //this message is shown
        }
    }
    echo "<hr>";

    //6 - 9

    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '14:00 - 17:00'
    $sql5 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '18:00 - 21:00'";
    $query5 = mysqli_query($mysqli, $sql5); //executes sql query
    
    //displays the details for the morning session on this date
    echo "<h3>Evening Session 6pm - 9pm </h3>";

    while($row5 = mysqli_fetch_array($query5)) //while loop retrieves data
    {
        $session3 = $row5['Session_ID']; //the id of the session is stored in a variable
        echo "<h4> Invigilators Required: ".$row5['InvReq']."<br><br>
        Invigilators Assigned: ".$row5['InvAss']."<br><br>";

        //retrieves all exams with the same session i.e. '14:00 - 17:00'
        $sqll3 = "SELECT * FROM exam WHERE Session_ID = '$session3'";
        $queryl3 = mysqli_query($mysqli, $sqll3);//executes sql query

        while($rowl3 = mysqli_fetch_array($queryl3)) //while loop goes through each record
        {
            $lec3 = $rowl3['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }
        
        //searches the lecturer table for name and lecturer id matches to check whether this lecturer teacher a course in this exam session or not
        $namesql3 = $mysqli->query("SELECT * FROM lecturer WHERE Name LIKE '%$lec3%' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli));
    
        //if there is no such record
        if ($namesql3->num_rows <= 0)
        {
            //and if the Invigilators Required does not equal Invigilators Assigned in the database
            if ($row5['InvReq'] != $row5['InvAss'])
            {
                //searches in the invigilator table to see if this lecturer has already been assigned to this session
                $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session3' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                
                if ($e->num_rows <= 0) //if there is no such record
                {
                    //a button named 'Volunteer' will appear so that the lecturer can volunteer for this session as an invigilator
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&volid=$session3' class='vol'>Volunteer</a><br>"; 
                }
                else //if the lecturer is assigned to the session
                {
                    //a button named 'Withdraw' will appear so that the lecturer can withdraw from the invigilation duty for this session
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session3' class='wdraw'>Withdraw</a><br>";
                }
            }
            elseif ($row5['InvReq'] == $row5['InvAss'])//if the Invigilators Required equals Invigilators Assigned in the database
            {
                //searches the invigilator table for this session id
                $e2 = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session3' AND Lecturer_ID = $user_id") or die(mysqli_error($mysqli)); 
                
                if ($e2->num_rows > 0) //if there is such record
                {
                    //withdraw button will appear allowing the logged in lecturer to withdraw from the invigilation duty
                    echo "Action: <a href='invigilation.php?diet=$dietid&date=$date&wdrawid=$session3' class='wdraw'>Withdraw</a><br>";
                }
            }
        }
            
        //if the userid that is logged in matches the lecturer's id of the lecturer who is teaching a course in this exam session
        else
        {
            echo "No Action<br><br>"; //this message is shown
        }
    }
}
?> 

</div>
</center>
</form>
</body>
</html>
