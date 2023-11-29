<?php 
session_start();
if(isset($_POST['username']) && isset($_POST['password']))
{
    include "db.php";
    $Username = $_POST['username'];
    $Password = $_POST['password'];
    $Role = $_POST['role'];
    if(empty($Username))
    {
        $em ="Username is required";
        header("Location: loginStaff.php?error=$em");
        exit;
    }
    else if(empty($Password))
    {
        $em ="Password is required";
        header("Location: loginStaff.php?error=$em");
        exit;
    }
    else
    {   
        $sql = "SELECT * FROM `tbstaff` WHERE
        `userName` = ? AND `Password` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $Username, $Password);
        $stmt->execute();
        $empData = $stmt->get_result()->fetch_assoc();

        if($stmt->affected_rows >= 1)
        {  
            if($Username == $empData['userName']) 
            {
                if($Password == $empData['Password'])
                {   
                    if($Role == 1){
                        header("Location: staffIn.php");
                        exit;
                    }
                    elseif($Role == 2){
                        header("Location: staffOut.php");
                        exit;
                    }
                }
                else {
                    $em = "Incorrect User or Password";
                    header("Location: loginStaff.php?error=$em");
                }
            }
            else {
                $em = "Incorrect User or Password";
                header("Location: loginStaff.php?error=$em");
            }
        }
    }
}
else {
    header("Location: loginStaff.php");
    exit;
}
?>