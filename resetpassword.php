<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lecturer - Reset Password</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="login.php">Login</a>
<a href="register.php">Register</a>
</div>
<form action="" method="post">
<center>
<br>
<h1>Reset Your Password Here</h1> <br>
<input class="input1" type="text" name="email" placeholder="Enter Your Email Address"/><br>
<input class="input1" type="password" name="npassword" placeholder="Enter New Password Min - 8" min="8"/><br>
<input class="input1" type="password" name="cpassword" placeholder="Confirm Password Min - 8" min="8"/><br>
<input class="input1" type="text" name="contact" placeholder="Phone Number"/></br>
<button class="button2" type="submit" name="reset">Reset Password</button></center> <br></div>
</form>
</body>
</html>

<?php
include "DB_Connect.php"; //database connection

if(isset($_POST["reset"])) //If the button named 'submit' and called 'post' is pressed then:
{
    //retrieves data from all input fields on the page and stores it in variables
    $email = $mysqli->escape_string($_POST["email"]);
    $new = $mysqli->escape_string($_POST["npassword"]);
    $confirm = $mysqli->escape_string($_POST["cpassword"]);
    $contact = $mysqli->escape_string($_POST["contact"]);

    //checks if any field is empty
    if(empty($email) || empty($new) || empty($contact) || empty($confirm)) 
    {
        echo "<br> You cannot leave any field empty!"; //Inform user that they left a field empty.
        exit();
    }

    //if the new password and the confirmation of new password match then:
    if($new == $confirm)
    {
        //selects all from lecturer using a particular email that was entered
        $e = $mysqli->query("SELECT * FROM lecturer WHERE Email='$email'") or die(mysqli_error($mysqli)); 
        $check = $e->fetch_assoc(); //fetches all associated columns
        
        if($contact == $check["Phone"]) //if the contact number entered matches the one in the database with the same email
        {
            //Encrypts password so that it isn't visible in the database.
            $encryptedpassword = password_hash($new, PASSWORD_BCRYPT);

            //Creating a verification pin.
            $pin = $mysqli->escape_string(md5 (rand(0,1000)));
            $pin = substr($pin, 0, 5);

            //Update lecturer details in the database table along the verificaiton pin created above.
            $sql = "UPDATE lecturer SET Keyword='$encryptedpassword', Pin='$pin', Active=0 WHERE Email='$email' AND Phone='$contact'";
            if ($mysqli->query($sql)) //If query runs smoothly.
            { 
                $to = "$email"; //recipient of the email
                $subject = "Password Reset Successful - SEGI Exam Invigilation System"; //subject of the email

                //text in the email
                $txt = "Please use this code: $pin to activate your SEGI Exam Invigilation account again at http://localhost/segiexaminvigilation/verification.php. Thank you.";
                $headers = "From: SEGI Exam Department"; //header of the email

                if (mail($to,$subject,$txt,$headers)) //if mail is successful
                {
                    header ('Location: verification.php?RegistrationSuccessful'); //redirect user to verification page
                    exit();    
                }
                else //otherwise
                {
                    echo "<br> Registration unsuccessful. Try again later."; //show error message
                }        
            }
        }
    }

    //if the new password and the confirmation of new password do not match then:
    else
    {
        //show error message
        echo "<br> <br> <center>Both passwords do not match. Please try again.</center>";
    }
}
?>