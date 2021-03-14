<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verify</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>	
<body>
<div class="menu">
<a href="login.php">Login</a>
<a href="register.php">Register</a>
</div>
<center><br> <br>
<h1> Verify Your Account Here. </h1><br> <br> <br> 
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post"  onsubmit="return checkform(this);">
<div>
<input class="input1" type="text" name="code" placeholder="Verification Code" required/><br>
</div>
<button class="button2" type="submit" name="verify">Verify</button><br> <br>
</html>

<?php
session_start(); //starts a session

if(isset($_POST["verify"])) //when the verify button is clicked
{
    //Database Connection.
    include_once "DB_Connect.php";
    
    //Store the verification pin, entered by the user in the input box, into a variable.
    $pin = $_POST["code"];

    if(empty($pin)) //Inform the user if the verification field is empty.
    {
        echo "You cannot leave Verification Code field empty!"; //error message
        exit();
    }
    else
    {
        //Check the database if the pin exists or not.
		$pincheck = $mysqli->query("SELECT * FROM lecturer WHERE pin = '$pin'") or die(mysqli_error($mysqli)); 
        
        if($pincheck->num_rows <= 0) //If pin doesn't exist change the header to inform of the invalid pin.
        {
            $_SESSION["unsuccessful"] = "Invalid Pin";
            echo "<br> Invalid Pin"; //error message
            exit();
        }
        else 
        {
            //If pin exists, change the column 'active' to 1 where the column 'pin' is equals to the pin entered by the user. 
            $sql = "UPDATE lecturer SET active = '1' WHERE pin = '$pin'"; 
            mysqli_query($mysqli, $sql); //executes sql query
    
            //If the 'Active' value is 1, the user will be redirected to the Home page.
            header("Location: home.php?Home=Verified");
            exit(); 
        }
    }
}
?>