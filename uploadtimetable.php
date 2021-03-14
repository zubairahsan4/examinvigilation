<?php
$user_id = ""; //Set the variable that will store HRID from session to zero.
session_start(); //Start session.
if(isset($_SESSION['HRID'])) //Check if the user has logged in or not using this session.
{
    //If the user has logged in, then store the HRID in a variable called user_id and let them continue on this page.
    $user_id = $_SESSION['HRID']; 
}
else 
{
    //If the user has not logged in, redirect them back to the login page.
    header("Location: login.php");
    exit();
}

//Database Connection.
include "DB_Connect.php";

$sql = "SELECT * FROM lecturer"; //retrieves all data from the lecturer table
$query = mysqli_query($mysqli, $sql); //executes the sql query
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Upload Timetable for Lecturers</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="style.css"/></head>
<body>
<div class="menu">
<a href="login.php?Logout">Logout</a>
</div>
<br><br>
<form action="uploadtimetable.php" method="post" name="postimage" enctype="multipart/form-data">
<center>
<div class='container'>
<div class='div'>
<center><h2> Upload Timetable </h2><hr></center>
<h3>Select image to upload: </h3> <input type="file" name="image[]" value="1" required/> 
<h3>Choose lecturer: 
<select class='input1' name='lecturer' required>
    <option value='Select' selected disabled>Select...</option>
<?php
while ($row = mysqli_fetch_array($query)) //while loop retrieves details of lecturers one by one
{
    //lecturer's id and name are stored and displayed in the html dropdown list
    echo "<option value='" . $row['Lecturer_ID']. "'>" . $row['Name'] . " </option>";
}
?>
</select>
</h3>
<center><button class="button2" type="submit" name="upload">Upload/Replace Timetable</button><br><br></center></div>

<?php
if(isset($_POST["upload"])) //when the button named "upload" and called 'Upload/Replace Timetable' is pressed 
{
    $lecturerid = $_POST['lecturer']; //get lecturer id from the selected name in the drop down list

    $sql = "DELETE FROM timetable WHERE Lecturer_ID='$lecturerid'"; //deletes the existing timetable for this lecturer if there is one
    mysqli_query($mysqli, $sql); //executes sql query

    //retrieves the chosen lecturer's details using lecturer id
    $lecname = $mysqli->query("SELECT * FROM lecturer WHERE Lecturer_ID='$lecturerid'") or die(mysqli_error($mysqli)); 
    $getname = $lecname->fetch_assoc(); //fetch associated fields to this row

    $name = $getname["Name"]; //gets the name of the lecturer

    foreach($_FILES["image"]["tmp_name"] as $key=>$tmp_name) //for loop to upload file (timetable)
    {      
        //Variables to store image and their name.
        $file_name=$_FILES["image"]["name"][$key]; 
        $file_tmp=$_FILES["image"]["tmp_name"][$key]; 
       
        //Storing the path, where image will be stored, in a variable.
        $imagepath = "images/";

        //A method to distinguish files of the same name stored in a variable.
        $ext=pathinfo($file_name,PATHINFO_EXTENSION);
        
        if(!file_exists("images/".$file_name)) //If file of the same name doesn't exist in the image directory then:
        {
            if(move_uploaded_file($file_tmp=$_FILES["image"]["tmp_name"][$key],"images/".$file_name))
            {
                //If the file has been succesfully moved to the directory then:
                //Insert lecturerid with imagepath and the name of the file into the timetable table in the database.
                $sql = "INSERT INTO timetable (Lecturer_ID, Image_path, Image_name) VALUES ('$lecturerid','$imagepath','$file_name')";
                
                if ($mysqli->query($sql)) //if query is successful
                {
                    echo "Timetable for Mr/Ms $name is successfully uploaded."; //show success message
                }
                else //otherwise
                {
                    echo "Error. Try again later."; //show error message
                }
            }
        }
        else //If file of the same name exists in the image directory then:
        {
            //If a file of the same name already exists then change the name.
            $filename=basename($file_name,$ext);
            $file_name=$filename.time().".".$ext;
           
            if(move_uploaded_file($file_tmp=$_FILES["image"]["tmp_name"][$key],"images/".$file_name))
            {   
                //If the file with an extended name has been succesfully moved to the directory then:
                //Insert lecturerid with imagepath and the name of the file into the timetable table in the database.
                $sql = "INSERT INTO timetable (Lecturer_ID, Image_path, Image_name) VALUES ('$lecturerid','$imagepath','$file_name')";
                
                if ($mysqli->query($sql)) //if query is successful
                {
                    echo "Timetable for Mr/Ms $name is successfully uploaded."; //show success message
                }
                else //otherwise
                {
                    echo "Error. Try again later."; //show error message
                }
            }          
        }
    }    
}
?>
