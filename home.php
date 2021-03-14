<?php
$user_id = ""; //Set the variable that will store member_id from session to zero.
session_start(); //Start session.
if(isset($_SESSION['Lecturer_ID'])) //Check if the user has logged in or not using this session.
{
    //If the user has logged in, then store the member id in a variable called user_id and let them continue on this page.
    $user_id = $_SESSION['Lecturer_ID']; 
}
else 
{
    //If the user has not logged in, redirect them back to the login page.
    header("Location: login.php");
    exit();
}

$details = false; //set variable to default that controls the edit details button on this page
$change = false; //set variable to default that controls the change password button on this page

include "DB_Connect.php"; //database connection

//determines whether the edit details button is pressed or the change password is pressed
if(isset($_GET["action"]))//gets the value from the url
{
    $value = $_GET["action"]; //stores the value from the url in a variable
    if($value == "editdetails") //if the value from the url equals "editdetails"
    {
        $details = true; //the details variable will change and the page will allow the user to update their details
    }
    elseif($value == "changepassword") //if the value from the url equal "change password"
    {
        $change = true; //the change variable will change and the page will allow the user to update their password
    }
}

$sql = "SELECT * FROM lecturer WHERE Lecturer_ID = $user_id"; //gets all the lecturer's details using the lecturer id 
$query = mysqli_query($mysqli, $sql); //executes the sql query
$row = mysqli_fetch_array($query); //retrieves data in an array

$imagequery =  "SELECT * FROM timetable WHERE Lecturer_ID = $user_id"; //gets the lecturer's timetable using the lecturer id
$image = mysqli_query($mysqli, $imagequery); //executes the sql query
$getimage = mysqli_fetch_array($image); //retrieves image in an array
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lecturer - Homepage</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="home.php" class="active">Home</a>
<a href="invigilation.php">Invigilation</a>
<a href="login.php?Logout">Logout</a>
</div>
<form action="home.php" method="post" name="postimage" enctype="multipart/form-data">
<center>
<h1>Welcome <?php echo $row['Username'] ?> </h1>
<div class='container'>
<button class="button3" type="submit" name="ed">Edit Details</button> 
<button class="button3" type="submit" name="cp">Change Password</button>
<br><br> <br>
<?php if ($details == true): //if details variable is true  ?> 
<div class='post'>
<div class='div'>
<center><h2> Edit Details </h2><hr> <br>
<input class="input1" type="text" name="fullname" value="<?php echo $row['Name'] ?>"/><br>
<input class="input1" type="text" name="department" value="<?php echo $row['Department'] ?>"/><br>    
<input class="input1" type="text" name="contact" value="<?php echo $row['Phone'] ?>"/></br>
<button class="button2" type="submit" name="update">Update</button></center><br></div>

<?php elseif ($change == true): //if change variable is true ?>
<div class='post'>
<div class='div'>
<center><h2> Change Password </h2><hr> <br>
<input class="input1" type="password" name="opassword" placeholder="Enter Old Password" min="8"/><br>
<input class="input1" type="password" name="npassword" placeholder="Enter New Password" min="8"/><br>
<button class="button2" type="submit" name="change">Change</button></center> <br></div>

<?php else: //if both variable are false ?>
<div class='post'>
<div class='div'>
<center><h2> Your Profile </h2></center><hr><hr>
<h3>Your Name: <?php echo $row['Name'] //Lecturer's name?><br>
Department: <?php echo $row['Department'] //Department name?><br>    
Email: <?php echo $row['Email'] //Email address?><br>
Phone Number: <?php echo $row['Phone'] //Phone number?></br></h3><hr>
<center><h3>Timetable </h3> </center>
<?php echo "<img style = 'width:100%; height:50%; margin-bottom: 5px;' src = 'images/".$getimage['Image_name']."' alt = 'No timetable has been uploaded by the Human Resource Department.'/>"; //shows image of the timetable?>

<?php endif ?>

<?php
if(isset($_POST["ed"])) //If the button named 'ed' and called 'Edit Details' is pressed then:
{
    header("Location: home.php?action=editdetails"); //change the header accordingly
} 

if(isset($_POST["cp"])) //If the button named 'cp' and called 'Change Password' is pressed then:
{
    header("Location: home.php?action=changepassword"); //change the header accordingly
} 

if(isset($_POST["update"])) //If the button named 'update' and called 'Update' is pressed then:
{
    //Retrieves all data from the input text boxes
    $fullname = $mysqli->escape_string($_POST["fullname"]);
    $department = $mysqli->escape_string($_POST["department"]);
    $contact = $mysqli->escape_string($_POST["contact"]);

    if(empty($fullname) || empty($department) || empty($contact) ) //checks if any of the fields are empty
    {
        echo "You cannot leave any field empty!"; //Inform user that they left a field empty.
        exit();
    }

    //Updates the lecturer's details in the lecturer's table
    $sql = "UPDATE lecturer SET Name='$fullname', Department='$department', Phone='$contact' WHERE Lecturer_ID = $user_id";   
    $query = mysqli_query($mysqli, $sql); //executes sql query

    header("Location: home.php?detailsupdated"); //redirect to home page
} 

if(isset($_POST["change"])) //If the button named 'change' and called 'Change' is pressed then:
{
    //Retrieves data from both old password and new password field
    $old = $mysqli->escape_string($_POST["opassword"]);
    $new = $mysqli->escape_string($_POST["npassword"]);

    $passwordcheck = password_verify($old, $row["Keyword"]); //verifies if the old password matches the one in the database
    
    if($passwordcheck == false) //if old password doesn't match
    {
        //Show this error message
        echo "Incorrect Old Password.";
        exit();
    }
    elseif($passwordcheck == true) //if old password matches
    {
        $encryptedpassword = password_hash($new, PASSWORD_BCRYPT); //encrypts the new password

        //Updates the password in the lecturer table for the lecturer using the lecturer id
        $sql = "UPDATE lecturer SET Keyword='$encryptedpassword' WHERE Lecturer_ID = $user_id";   
        $query = mysqli_query($mysqli, $sql); //executes sql query

        header("Location: home.php?Password_Successfully_Changed."); //redirect to home page
    }
}
?>

</div>
</form>
</body>
</html>