<?php 
session_start(); 
session_destroy(); //Destroy all previous sessions.
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>	
<body>
<div class="menu">
<a href="login.php" class="active">Login</a>
<a href="register.php">Register</a>
</div>
<center><br> <br>
<h1> SEGI EXAM INVIGILATION SYSTEM </h1>
<h2> LOGIN </h2><br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post"  onsubmit="return checkform(this);">
<div>    
<input class="input1" type="text" name="username" placeholder="Username" required/><br>
<input class="input1" type="password" name="password" placeholder="Password" required/><br>
<a href='resetpassword.php'>Forgot Password?</a><br><br>
<input type="radio" name="user" value="lecturer" required/>Lecturer
<input type="radio" name="user" value="examadmin" required/>Exam Dept.
<input type="radio" name="user" value="hradmin" required/>HR Dept.
</div>
<button class="button2" type="submit" name="login">Login</button><br>
</html>

<?php
session_start(); //Start a new session.

if (isset($_POST["login"])) //If the login button is pressed.
{
    //Database Connection.
    include_once "DB_Connect.php";

    //Store the username value from the page into a variable of the same name.
    $username = $mysqli->escape_string($_POST["username"]); 

    //Store the password value from the page into a variable called keyword.
    $password = $mysqli->escape_string($_POST["password"]);

    $radiovalue = $_POST["user"]; //store radio value from the three radio buttons (only one can be pressed at a time)

    if(empty($username) || empty($password)) //checks if fields are empty
    {
        if(empty($username)) 
        {
            //If the user has left the username field empty, inform the user by displaying the message below.
            echo "<br> You cannot leave 'Username' empty!";
        }
        if(empty($password))
        {            
            //If the user has left the password field empty, inform the user by displaying the message below.
            echo "<br> You cannot leave 'Password' empty!";
        }
        exit();
    }
    else //If both fields are not empty then:
    {
        if($radiovalue == "lecturer") //if the radio value is equal to 'lecturer'
        {
            //Check username if it exists in the lecturer table in database.
            $usernamecheck = $mysqli->query("SELECT * FROM lecturer WHERE username = '$username'") or die(mysqli_error($mysqli)); 
            
            if($usernamecheck->num_rows <= 0)
            {
                //If the username entered doesn't exist in the database, stay on the login page and change the header to inform user.
                $_SESSION["unsuccessful"] = "Username was not found";
                echo "<br>Username doesn't exist in the database.";
                exit();
            }
            else
            {
                //If the username exists in the database then:
                if($row = $usernamecheck->fetch_assoc())
                {      
                    //Decrypt the password from the database and match it with password entered by the user. 
                    $passwordcheck = password_verify($password, $row["Keyword"]);
                    
                    if($passwordcheck == false) 
                    {
                        //If the password doesn't match the username, stay on the login page and change the header to inform user.
                        $_SESSION["unsuccessful"] = "Password doesn't match Username";
                        echo "<br>Password doesn't match username in the database.";
                        exit();
                    }
                    elseif($passwordcheck == true)
                    {
                        //If the password matches the username in the same row in the database then:
                        $_SESSION['Lecturer_ID'] = $row['Lecturer_ID']; //Create a session to store Member_ID from the database.
                        $_SESSION['Username'] = $row['Username']; //Create a session to store Username from the database.
                        $_SESSION['VerificationCheck'] = $row['Active']; //Create a session to store value from column 'Active' from the database.
                        if($row['Active'] == false) 
                        {
                            //If the 'Active' value is 0, the user will be redirected to the Verification page.
                            header("Location: verification.php?Verification=Logged_IN_Please_Verify");
                            exit();
                        }
                        else 
                        {
                            //If the 'Active' value is 1, the user will be redirected to the Home page.
                            header("Location: home.php?Home=Verified");
                        exit();
                        }  
                    }
                }
            }
        }
        else if ($radiovalue == "examadmin")//if the radio value is equal to 'examadmin'
        {
            //Check username if it exists in the examdepartment table in database.
            $usernamecheck = $mysqli->query("SELECT * FROM examdepartment WHERE username = '$username'") or die(mysqli_error($mysqli)); 
            
            if($usernamecheck->num_rows <= 0)
            {
                //If the username entered doesn't exist in the database, stay on the login page and change the header to inform user.
                $_SESSION["unsuccessful"] = "Username was not found";
                echo "<br>Username doesn't exist in the database.";
                exit();
            }
            else
            {
                //If the username exists in the database then:
                if($row = $usernamecheck->fetch_assoc())
                {                         
                    if ($password != $row["Keyword"])
                    {
                        //If the password doesn't match the username, stay on the login page and change the header to inform user.
                        $_SESSION["unsuccessful"] = "Password doesn't match Username";
                        echo "<br>Password doesn't match username in the database.";
                        exit();
                    }
                    elseif($password == $row["Keyword"])
                    {
                        //if password matches the username
                        $_SESSION['EDID'] = $row['EDID']; //Create a session to store EDID from the database.
                        $_SESSION['Username'] = $row['Username']; //Create a session to store Username from the database.
                        header("Location: examdiet.php"); //redirect to examdiet page
                        exit();  
                    }
                }
            }
        }
        else if ($radiovalue == "hradmin")//if the radio value is equal to 'hradmin'
        {
            //Check username if it exists in the hrdepartment table in database.
            $usernamecheck = $mysqli->query("SELECT * FROM hrdepartment WHERE username = '$username'") or die(mysqli_error($mysqli)); 
            
            if($usernamecheck->num_rows <= 0)
            {
                //If the username entered doesn't exist in the database, stay on the login page and change the header to inform user.
                $_SESSION["unsuccessful"] = "Username was not found";
                echo "<br>Username doesn't exist in the database.";
                exit();
            }
            else
            {
                //If the username exists in the database then:
                if($row = $usernamecheck->fetch_assoc())
                {                         
                    if ($password != $row["Keyword"])
                    {
                        //If the password doesn't match the username, stay on the login page and change the header to inform user.
                        $_SESSION["unsuccessful"] = "Password doesn't match Username";
                        echo "<br>Password doesn't match username in the database.";
                        exit();
                    }
                    elseif($password == $row["Keyword"])
                    {
                        //if password matches the username
                        $_SESSION['HRID'] = $row['HRID']; //Create a session to store HRID from the database.
                        $_SESSION['Username'] = $row['Username']; //Create a session to store Username from the database.
                        header("Location: uploadtimetable.php"); //redirect to uploadtimetable page
                        exit();  
                    }
                }
            }
        }
    }
}

?>