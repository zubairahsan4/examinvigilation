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

$dietid="null"; //set variable to null
$date="null"; //set variable to null
$set = false; //set variable to false, this variable determines whether to show the dates in the diet

if(isset($_GET['diet'])) //if 'diet' is mentioned in the url
{
    $dietid = $_GET['diet']; //stores the diet value from the url into the variable named 'dietid'
    $set = true; //changes the variable called 'set' to true, meaning that now dates in this diet will be shown 
}
if(isset($_GET['id'])) //if 'id' is mentioned in the url
{
    $id = $_GET['id']; //stores the id value from the url into the variable named 'id'
}
if(isset($_GET['date'])) //if 'date' is mentioned in the url
{
    $date = $_GET['date']; //stores the date value from the url into the variable named 'date'
}

//Database Connection.
include "DB_Connect.php";

if(isset($_GET['add'])) //if 'add' is mentioned in the url
{
    $sid = $_GET['add']; //stores the value in a variable

    //increases the value of invigilators required in the datesession table by 1
    $q = "UPDATE datesession SET InvReq=InvReq+1 WHERE Session_ID=$sid"; 
    mysqli_query($mysqli, $q); //executes sql query

    header("Location: invigilator.php?diet=$dietid&date=$date"); //refreshes the page
}
if(isset($_GET['sub'])) //if 'i' is mentioned in the url
{
    $sid = $_GET['sub']; //stores the value in a variable

    //decreases the value of invigilators required in the datesession table by 1
    $q = "UPDATE datesession SET InvReq=InvReq-1 WHERE Session_ID=$sid"; 
    mysqli_query($mysqli, $q); //executes sql query

    header("Location: invigilator.php?diet=$dietid&date=$date"); //refreshes the page
}

if (isset($_GET['i'])) //if 'i' is mentioned in the url
{
    $del = $_GET['i']; //stores the value in a variable

    //removes a lecturer from invigilation duty on the chosen session in the invigilator table
    mysqli_query($mysqli, "DELETE FROM invigilator WHERE Session_ID=$id AND Lecturer_ID=$del"); 

    //updates the datesession table and decreases the value of invigilators assigned field by 1 using the session id
    $q = "UPDATE datesession SET InvAss=InvAss-1 WHERE Session_ID=$id"; 
    
    if(mysqli_query($mysqli, $q)) //if the query runs smoothly
    {
        //selects all details of a particular lecturer using lecturer id
        $sql = "SELECT * FROM lecturer WHERE Lecturer_ID = $del";
        $query = mysqli_query($mysqli, $sql); //executes sql query

        $row = mysqli_fetch_array($query); //data is retrieved in an array

        $email = $row['Email']; //email of the lecturer is retrieved from the database and stored in a variable
        $name = $row['Name']; //name of the lecturer is retrieved from the database and stored in a variable

        //selects all details of a particular session using lecturer id
        $sql2 = "SELECT * FROM datesession WHERE Session_ID = $id";
        $query2 = mysqli_query($mysqli, $sql2); //executes sql query

        $row2 = mysqli_fetch_array($query2);//data is retrieved in an array
        
        $to = "$email"; //the receiver of the email
        $subject = "Removal from Invigilation Duty"; //subject of the email

        //text in the email
        $txt = "Mr/Ms $name, you have been removed from the Invigilation duty on the following exam session:
        Date: ".$row2['ExamDate']."
        Session: ".$row2['ExamSession']."";

        //header of the email
        $headers = "From: SEGI Exam Department";
        mail($to,$subject,$txt,$headers); //sends mail
    }
	header("Location: invigilator.php?diet=$dietid&date=$date"); //refreshes the page
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
<title>Assign Invigilator</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="examdiet.php">Exam Diets</a>
<a href="invigilator.php" class="active">Invigilators</a>
<a href="timetables.php">Lecturer's Timetables</a>
<a href="login.php?Logout">Logout</a>
</div>
<center>
<form action="" method="post">

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
    header("Location: invigilator.php?diet=$choice"); //the url is changed as diet is mentioned with a value
    exit();
}   
?>

<?php if ($set == true): //when set variable equals true than the following will be shown?>
<h3>Select Date: <input class="input2" type="date" name="date" value="<?php echo $date?>" min="<?php echo $row2['StartDate'] //minimum value is set to start date from the diet table?>" max="<?php echo $row2['EndDate'] //minimum value is set to start date from the diet table?>"/>
&nbsp <button class="button2" type="submit" name="submitdate">Submit</button><br></h3>
<?php endif ?>
</center>
<div class="container">

<?php
if(isset($_POST["submitdate"]))//if the button named 'submitdate' and called 'Submit Date' is pressed then:
{
    //the date is retrieved from the input box and stored in a variable
    $date = $mysqli->escape_string($_POST["date"]);

    //header changes and now carries the diet id as well as the date
    header("Location: invigilator.php?diet=$dietid&date=$date");
}

if(isset($_GET['date']))  //gets the date value from the url
{
    $date = $_GET['date']; //stores the date value from the url in a variable
    
                                                        //9 - 12

    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '09:00 - 12:00'
    $sql912 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '09:00 - 12:00'";
    $query912 = mysqli_query($mysqli, $sql912); //executes sql query
    $row912 = mysqli_fetch_array($query912); //loads data in an array

    if(count($row912))
    {
        $session = $row912['Session_ID']; //stores the session id from the datesession table in a variable

        //retrieves all exams with the same session i.e. '09:00 - 12:00'
        $sql1 = "SELECT * FROM exam WHERE Session_ID = $session";
        $query1 = mysqli_query($mysqli, $sql1); //executes sql query

        //while loop goes through each record
        while($row1 = mysqli_fetch_array($query1))
        {
            $lec = $row1['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }

        //sums the number of students from the exam table that belong to this session
        $result1 = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $session"); 
        $array1 = mysqli_fetch_array($result1); //array gets all the values
        $sum1 = $array1['value']; //all the values in the array are added and their sum is stored in a variable

        //checks if the lecturer name in the taughtby is same as anyone in the lecturer table
        $sql2 = "SELECT * FROM lecturer WHERE Name NOT LIKE '%$lec%'";
        $query2 = mysqli_query($mysqli, $sql2); //executes sql query

        //selects all from lecturer and invigilator table using lecturer id where the session id is same
        $sql3 = "SELECT * FROM lecturer l JOIN invigilator i ON l.Lecturer_ID = i.Lecturer_ID WHERE i.Session_ID = $session";
        $query3 = mysqli_query($mysqli, $sql3); //executes sql query

        //display retrieved data with the increase and decrease buttons created dynamically
        echo "<div class='post'>
        <div class='div'>
        <center><h2>Exams on $date</h2></center><hr>
        <h3>Morning Session 9 am - 12 pm </h3>
        <h4>Total Students: ".$sum1." <br><br>
        Invigilators Required: ".$row912['InvReq']." 
        <a href='invigilator.php?diet=$dietid&date=$date&add=$session' class='increase'>Increase</a>
        <a href='invigilator.php?diet=$dietid&date=$date&sub=$session' class='decrease'>Decrease</a><br><br>
        Invigilators Assigned:  ".$row912['InvAss']." -> ";
    
        while ($row3 = mysqli_fetch_array($query3)) //while loop gets each record one by one
        {  
            //stores lecturer id and their name in variables 
            $l = $row3['Lecturer_ID'];
            $n = $row3['Name']; 
        
            //each lecturer name is a button and once clicked will transfer the lecturer id so they can be removed from the invigilation duty
            echo "<a href='invigilator.php?diet=$dietid&date=$date&id=$session&i=$l' class='idel'>$n </a>";
        } 

        //note for the user
        echo"<h5>*Note: Clicking on invigilator's name will remove them from invigilation duty.</h5>";
        
        //if the Invigilators Required does not equal Invigilators Assigned in the database
        if ($row912['InvReq'] != $row912['InvAss'])
        {
            //a dropdown list will appear that will contain the name of lecturers not teaching a course in this session
            echo "<h4>Assign Lecturer: <select class='input3' name='invig1'>"; 
        
            while ($row2 = mysqli_fetch_array($query2)) //while loop will load all lecturers in the dropdown list
            {
                echo "<option value='" . $row2['Lecturer_ID']. "'>" . $row2['Name'] . " </option>";
            }
        
            //assign button for this session
            echo "</select> <button class='button4' type='submit' name='assign1'>Assign</button></h4>"; 
        }
    }
    else
    {
        echo "No results found in the Morning session";
    }
    echo "<hr>";

                                             //2 - 5

    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '14:00 - 17:00'
    $sql25 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '14:00 - 17:00'";
    $query25 = mysqli_query($mysqli, $sql25); //executes sql query
    $row25 = mysqli_fetch_array($query25); //loads data in an array

    if(count($row25))
    {
        $session2 = $row25['Session_ID']; //stores the session id from the datesession table in a variable

        //retrieves all exams with the same session i.e. '14:00 - 17:00'
        $sql4 = "SELECT * FROM exam WHERE Session_ID = $session2";
        $query4 = mysqli_query($mysqli, $sql4);  //executes sql query
        
        //while loop goes through each record
        while($row4 = mysqli_fetch_array($query4))
        {
            $lec2 = $row4['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }

        //sums the number of students from the exam table that belong to this session
        $result2 = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $session2"); 
        $array2 = mysqli_fetch_array($result2); //array gets all the values
        $sum2 = $array2['value']; //all the values in the array are added and their sum is stored in a variable
        
        //checks if the lecturer name in the taughtby is same as anyone in the lecturer table
        $sql5 = "SELECT * FROM lecturer WHERE Name NOT LIKE '%$lec2%'";
        $query5 = mysqli_query($mysqli, $sql5); //executes sql query

        //selects all from lecturer and invigilator table using lecturer id where the session id is same
        $sql6 = "SELECT * FROM lecturer l JOIN invigilator i ON l.Lecturer_ID = i.Lecturer_ID WHERE i.Session_ID = $session2";
        $query6 = mysqli_query($mysqli, $sql6); //executes sql query

        //display retrieved data with the increase and decrease buttons created dynamically
        echo "<h3>Afternoon Session 2 pm - 5 pm </h3>
        <h4>Total Students: ".$sum2." <br><br>
        Invigilators Required: ".$row25['InvReq']." 
        <a href='invigilator.php?diet=$dietid&date=$date&add=$session2' class='increase'>Increase</a>
        <a href='invigilator.php?diet=$dietid&date=$date&sub=$session2' class='decrease'>Decrease</a><br><br>
        Invigilators Assigned:  ".$row25['InvAss']." -> ";
    
        while ($row6 = mysqli_fetch_array($query6)) //while loop gets each record one by one
        {  
            //stores lecturer id and their name in variables 
            $l2 = $row6['Lecturer_ID'];
            $n2 = $row6['Name'];

            //each lecturer name is a button and once clicked will transfer the lecturer id so they can be removed from the invigilation duty
            echo "<a href='invigilator.php?diet=$dietid&date=$date&id=$session2&i=$l2' class='idel'>$n2 </a>";
        } 

        //note for the user
        echo"<h5>*Note: Clicking on invigilator's name will remove them from invigilation duty.</h5>";
        
        //if the Invigilators Required does not equal Invigilators Assigned in the database
        if ($row25['InvReq'] != $row25['InvAss'])
        {
            //a dropdown list will appear that will contain the name of lecturers not teaching a course in this session
            echo "<h4>Assign Lecturer: <select class='input3' name='invig2'>"; 
        
            while ($row5 = mysqli_fetch_array($query5)) //while loop will load all lecturers in the dropdown list
            {
                echo "<option value='" . $row5['Lecturer_ID']. "'>" . $row5['Name'] . " </option>";
            }

            //assign button for this session
            echo "</select> <button class='button4' type='submit' name='assign2'>Assign</button></h4>"; 
        }
    }
    else
    {
        echo "No results found for Afternoon Session";
    }
    echo "<hr>";

                                                //6 - 9
    
    //retrieves all from datesession table using the date taken from the url and where the ExamSession field is equal to '18:00 - 21:00'
    $sql69 = "SELECT * FROM datesession WHERE ExamDate = '$date' AND ExamSession = '18:00 - 21:00'";
    $query69 = mysqli_query($mysqli, $sql69); //executes sql query
    $row69 = mysqli_fetch_array($query69); //loads data in an array

    if(count($row69))
    {
        $session3 = $row69['Session_ID']; //stores the session id from the datesession table in a variable   

        //retrieves all exams with the same session i.e. '14:00 - 17:00'
        $sql7 = "SELECT * FROM exam WHERE Session_ID = $session3";
        $query7 = mysqli_query($mysqli, $sql7); //executes sql query

        //while loop goes through each record
        while($row7 = mysqli_fetch_array($query7))
        {
            $lec3 = $row7['TaughtBy']; //stores the name of the lecturer that teacher the course in a variable
        }

        //sums the number of students from the exam table that belong to this session
        $result3 = $mysqli->query("SELECT SUM(NStudents) AS value FROM exam WHERE Session_ID = $session3"); 
        $array3 = mysqli_fetch_array($result3); //array gets all the values
        $sum3 = $array3['value']; //all the values in the array are added and their sum is stored in a variable
        
        //checks if the lecturer name in the taughtby is same as anyone in the lecturer table
        $sql8 = "SELECT * FROM lecturer WHERE Name NOT LIKE '%$lec3%'";
        $query8 = mysqli_query($mysqli, $sql8); //executes sql query

        //selects all from lecturer and invigilator table using lecturer id where the session id is same
        $sql9 = "SELECT * FROM lecturer l JOIN invigilator i ON l.Lecturer_ID = i.Lecturer_ID WHERE i.Session_ID = $session3";
        $query9 = mysqli_query($mysqli, $sql9); //executes sql query

        //display retrieved data with the increase and decrease buttons created dynamically
        echo "<h3>Evening Session 6 pm - 9 pm </h3>
        <h4>Total Students: ".$sum3." <br><br>
        Invigilators Required: ".$row69['InvReq']." 
        <a href='invigilator.php?diet=$dietid&date=$date&add=$session3' class='increase'>Increase</a>
        <a href='invigilator.php?diet=$dietid&date=$date&sub=$session3' class='decrease'>Decrease</a><br><br>
        Invigilators Assigned:  ".$row69['InvAss']." -> ";
        
        while ($row9 = mysqli_fetch_array($query9)) //while loop gets each record one by one
        {  
            //stores lecturer id and their name in variables 
            $l3 = $row9['Lecturer_ID'];
            $n3 = $row9['Name'];

            //each lecturer name is a button and once clicked will transfer the lecturer id so they can be removed from the invigilation duty
            echo "<a href='invigilator.php?diet=$dietid&date=$date&id=$session3&i=$l3' class='idel'>$n3 </a>";
        } 

        //note for the user
        echo"<h5>*Note: Clicking on invigilator's name will remove them from invigilation duty.</h5>";
        
        //if the Invigilators Required does not equal Invigilators Assigned in the database
        if ($row69['InvReq'] != $row69['InvAss'])
        {
            //a dropdown list will appear that will contain the name of lecturers not teaching a course in this session
            echo "<h4>Assign Lecturer: <select class='input3' name='invig3'>"; 
            
            while ($row8 = mysqli_fetch_array($query8)) //while loop will load all lecturers in the dropdown list
            {
                echo "<option value='" . $row8['Lecturer_ID']. "'>" . $row8['Name'] . " </option>";
            }

            //assign button for this session
            echo "</select> <button class='button4' type='submit' name='assign3'>Assign</button></h4>"; 
        }
    }
    else
    {
        echo "No results found for Evening Session<br><br>";
    }
}

if(isset($_POST["assign1"])) //If the button named 'assign1' and called 'Assign' is pressed then:
{  
    $lecturer = $_POST['invig1']; //stores the lecture id from the  first drop down list in a variable
    
    //Checks invigilator table using the session in the database.
    $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session'") or die(mysqli_error($mysqli)); 
    $check = $e->fetch_assoc(); //fetches associated columns
        
    if ($lecturer != $check["Lecturer_ID"]) //if the lecturer has not been assigned to this session before then:
    {
        //retrieves all data for a particular lecturer using lecturer id
        $sql = "SELECT * FROM lecturer WHERE Lecturer_ID = $lecturer";
        $query = mysqli_query($mysqli, $sql); //executes sql query
        $row = mysqli_fetch_array($query); //fetches results in an array
    
        $email = $row['Email']; //stores the email of the lecturer in a variable
        $name = $row['Name']; //stores the name of the lecturer in a variable
    
        //creates a new record in the invigilator that assigns the lecturer to the session
        $sqlinsert = "INSERT INTO invigilator (Session_ID, Lecturer_ID) VALUES ('$session', '$lecturer')";
        if ($mysqli->query($sqlinsert)) //If query runs smoothly.
        { 
            //increases the invigilators assigned count by one in the datesession table
            $sql = "UPDATE datesession SET InvAss = InvAss+1 WHERE Session_ID = $session";
            $query = mysqli_query($mysqli, $sql); //executes sql query

            $to = "$email"; //recipient of the email
            $subject = "New Invigilation Duty"; //subject of the email

            //text in the email
            $txt = "Mr/Ms $name, you have been assigned to the following exam session:
            Date: ".$row912['ExamDate']."
            Session: ".$row912['ExamSession']."";
            $headers = "From: SEGI Exam Department"; //header of the email

            if (mail($to,$subject,$txt,$headers)) //if mail is successful
            {            
                header ("Location: invigilator.php?diet=$dietid&date=$date"); //refresh page
                exit();    
            }
            else //otherwise 
            {
                //show error message
                echo "<br> Unsuccessful. Try again later.";
            }        
        }
    }
    else //if the lecturer has been assigned to this session before then:
    {
        //show this message
        echo "You have already assigned this Lecturer to this Exam. Please choose another Lecturer. Thank you.";
    }
}

if(isset($_POST["assign2"])) //If the button named 'assign2' and called 'Assign' is pressed then:
{  
    $lecturer = $_POST['invig2']; //stores the lecture id from the second drop down list in a variable
        
    //Checks invigilator table using the session in the database.
    $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session2'") or die(mysqli_error($mysqli)); 
    $check = $e->fetch_assoc(); //fetches associated columns
            
    if ($lecturer != $check["Lecturer_ID"])//if the lecturer has not been assigned to this session before then:
    {
        //retrieves all data for a particular lecturer using lecturer id
        $sql = "SELECT * FROM lecturer WHERE Lecturer_ID = $lecturer";
        $query = mysqli_query($mysqli, $sql); //executes sql query
        $row = mysqli_fetch_array($query); //fetches results in an array
        
        $email = $row['Email']; //stores the email of the lecturer in a variable
        $name = $row['Name']; //stores the name of the lecturer in a variable
        
        //creates a new record in the invigilator that assigns the lecturer to the session
        $sqlinsert = "INSERT INTO invigilator (Session_ID, Lecturer_ID) VALUES ('$session2', '$lecturer')";
        if ($mysqli->query($sqlinsert)) //If query runs smoothly.
        { 
            //increases the invigilators assigned count by one in the datesession table
            $sql = "UPDATE datesession SET InvAss = InvAss+1 WHERE Session_ID = $session2";
            $query = mysqli_query($mysqli, $sql); //executes sql query

            $to = "$email"; //recipient of the email
            $subject = "New Invigilation Duty"; //subject of the email

            //text in the email
            $txt = "Mr/Ms $name, you have been assigned to the following exam session:
            Date: ".$row25['ExamDate']."
            Session: ".$row25['ExamSession']."";
            $headers = "From: SEGI Exam Department"; //header of the email

            if (mail($to,$subject,$txt,$headers)) //if mail is successful
            {            
                header ("Location: invigilator.php?diet=$dietid&date=$date"); //refresh page
                exit();    
            }
            else //otherwise 
            {
                //show error message
                echo "<br> Unsuccessful. Try again later.";
            }        
        }
    }
    else //if the lecturer has been assigned to this session before then:
    {
        //show this message
        echo "You have already assigned this Lecturer to this Exam. Please choose another Lecturer. Thank you.";
    }
}

if(isset($_POST["assign3"])) //If the button named 'assign2' and called 'Assign' is pressed then:
{  
    $lecturer = $_POST['invig3']; //stores the lecture id from the second drop down list in a variable
            
    //Checks invigilator table using the session in the database.
    $e = $mysqli->query("SELECT * FROM invigilator WHERE Session_ID='$session3'") or die(mysqli_error($mysqli)); 
    $check = $e->fetch_assoc(); //fetches associated columns
                
    if ($lecturer != $check["Lecturer_ID"]) //if the lecturer has not been assigned to this session before then:
    {
        //retrieves all data for a particular lecturer using lecturer id
        $sql = "SELECT * FROM lecturer WHERE Lecturer_ID = $lecturer";
        $query = mysqli_query($mysqli, $sql); //executes sql query
        $row = mysqli_fetch_array($query);//fetches results in an array
        
        $email = $row['Email']; //stores the email of the lecturer in a variable
        $name = $row['Name']; //stores the name of the lecturer in a variable
        
        //creates a new record in the invigilator that assigns the lecturer to the session
        $sqlinsert = "INSERT INTO invigilator (Session_ID, Lecturer_ID) VALUES ('$session3', '$lecturer')";
        if ($mysqli->query($sqlinsert)) //If query runs smoothly.
        { 
            //increases the invigilators assigned count by one in the datesession table
            $sql = "UPDATE datesession SET InvAss = InvAss+1 WHERE Session_ID = $session3";
            $query = mysqli_query($mysqli, $sql); //executes sql query

            $to = "$email"; //recipient of the email
            $subject = "New Invigilation Duty"; //subject of the email

            //text in the email
            $txt = "Mr/Ms $name, you have been assigned to the following exam session:
            Date: ".$row69['ExamDate']."
            Session: ".$row69['ExamSession']."";
            $headers = "From: SEGI Exam Department"; //header of the email
            
            if (mail($to,$subject,$txt,$headers)) //if mail is successful
            {            
                header ("Location: invigilator.php?diet=$dietid&date=$date"); //refresh page
                exit();    
            }
            else //otherwise 
            {
                //show error message
                echo "<br> Unsuccessful. Try again later.";
            }        
        }
    }
    else //if the lecturer has been assigned to this session before then:
    {
        //show this message
        echo "You have already assigned this Lecturer to this Exam. Please choose another Lecturer. Thank you.";
    }
}
?>
</div>
</form>
</body>
</html>
