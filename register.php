<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>	
<body>
<div class="menu">
<a href="login.php">Login</a>
<a href="register.php" class="active">Register</a>
</div>
<center> 
<h1>SEGI EXAM INVIGILATION SYSTEM</h1>
<h2> REGISTER - LECTURER </h2><br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post"  onsubmit="return checkform(this);">
<div>
<input class="input1" type="text" name="fullname" placeholder="Full Name" required/><br>
<input class="input1" type="text" name="department" placeholder="Department Name" required/><br>    
<input class="input1" type="text" name="username" placeholder="Username" required/><br>
<input class="input1" type="password" minlength="8" name="password" placeholder="Password - Min 8 Characters" required/><br>
<input class="input1" type="text" name="email" placeholder="Email Address" required/><br>
<input class="input1" type="text" name="contact" placeholder="Phone Number" required/><br>
</div>
<button class="button2" type="submit" name="register">Register</button><br> <br>
</html>

<?php
if (isset($_POST["register"])) //when the register button is pressed
{
    //Database connection.
    include_once "DB_Connect.php";
    
    //retrieve all the values from all the text fields on this page and store them in variables
    $fullname = $mysqli->escape_string($_POST["fullname"]);
    $department = $mysqli->escape_string($_POST["department"]);
    $username = $mysqli->escape_string($_POST["username"]);
    $password = $mysqli->escape_string($_POST["password"]);
    $emailaddress = $mysqli->escape_string($_POST["email"]);
    $contact = $mysqli->escape_string($_POST["contact"]);

    //checks if any field is empty
    if(empty($fullname) || empty($department) || empty($username) || empty($password) || empty($emailaddress) || empty($contact) ) 
    {
        echo "<br> You cannot leave any field empty!"; //Inform user that they left a field empty.
        exit();
    }

    $allowed_domains = array("segi.edu.my"); //only allows SEGI email addresses
    $email_domain = array_pop(explode("@", $emailaddress)); 

    if(!in_array($email_domain, $allowed_domains)) //if the email address is not in allowed in the array check
    {
        //Alert user about the unauthorised email 
        echo "Invalid Email Address";
        exit();
    }

    //Check username in the database.
    $u = $mysqli->query("SELECT * FROM lecturer WHERE Username='$username'") or die(mysqli_error($mysqli)); 
        
    //Check email address in the database.
    $e = $mysqli->query("SELECT * FROM lecturer WHERE Email='$emailaddress'") or die(mysqli_error($mysqli));

    if($u->num_rows > 0 || $e->num_rows > 0) //checks if email or username exist in the table before
	{  
        if($u->num_rows > 0) //if username exists, if yes then it will display the line below.
		{
            echo "<br> Username already exists. "; //error message
		}  
        if($e->num_rows > 0) //if email address exists, if yes then it will display the line below.
		{
            echo "<br> Email already exists. "; //error message
		}
		exit();
    }
    
    //Encrypt password so that it isn't visible in the database.
    $encryptedpassword = password_hash($password, PASSWORD_BCRYPT);
    
    //Creating a verification pin.
    $pin = $mysqli->escape_string(md5 (rand(0,1000)));
    $pin = substr($pin, 0, 5);
    
    //Insert lecturer details in the database table along the verificaiton pin created above.
    $sql = "INSERT INTO lecturer (Name, Department, Username, Keyword, Email, Phone, Pin)
    VALUES ('$fullname', '$department', '$username', '$encryptedpassword', '$emailaddress', '$contact', '$pin')";

    if ($mysqli->query($sql)) //If query runs smoothly.
    { 
        $to = "$emailaddress"; //recipient of the email
        $subject = "Verification Code for SEGI Exam Invigilation System"; //subject of the email

        //text in the email
        $txt = "Please use this code: $pin to activate your SEGI Exam Invigilation account at http://localhost/segiexaminvigilation/verification.php. Thank you.";
        $headers = "From: SEGI Exam Department"; //header of the email

        if (mail($to,$subject,$txt,$headers))//if mail is successful
        {
            header ('Location: verification.php?RegistrationSuccessful'); //redirects to verification page
            exit();    
        }
        else //otherwise
        {
            echo "<br> Registration unsuccessful. Try again later."; //error message
        }        
    }
}





