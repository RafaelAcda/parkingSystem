<?php 
session_start();
if(isset($_POST['username']) && isset($_POST['password']))
{
    include "db.php";
    $Username = $_POST['username'];
    $Password = $_POST['password'];

    if(empty($Username))
    {
        $em ="Username is required";
        header("Location: loginAdmin.php?error=$em");
        exit;
    }
    else if(empty($Password))
    {
        $em ="Password is required";
        header("Location: loginAdmin.php?error=$em");
        exit;
    }
    else
    {   
        $sql = "SELECT * FROM `tbadmin` WHERE
        `userName` = ? AND `Password` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $Username, $Password);
        $stmt->execute();
        $admnData = $stmt->get_result()->fetch_assoc();

        if($stmt->affected_rows >= 1)
        {
            if($Username == $admnData['userName']) 
            {
                if($Password == $admnData['Password'])
                {
                    header("Location: adminForm.php");
                    exit;
                }
                else {
                    $em = "Incorrect User or Password";
                    header("Location: loginAdmin.php?error=$em");
                }
            }
            else {
                $em = "Incorrect User or Password";
                header("Location: loginAdmin.php?error=$em");
            }
        }
    }
}
else {
    header("Location: loginAdmin.php");
    exit;
}
?>