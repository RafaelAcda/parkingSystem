<?php 
    $fName = $lName = $Department = $Username = $Password = '';
    $fName_err = $lName_err = $Department_err = $Username_err = $Password_err = '';
    $complete = 0;
    $Status = '';
    if(isset($_POST['submit'])) {

        //      Check if any info is missing
        if(empty($_POST['fName'])) {
            $fName_err = 'First name is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['lName'])) {
            $lName_err = 'Last name is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['department'])) {
            $Department_err = 'Department is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['username'])) {
            $Username_err = 'Username is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['Password'])) {
            $Password_err = 'Password is missing';
        } else {
            $complete++;
        }

        //      No missing info

        if($complete == 5) {
            $fName = strtolower($_POST['fName']);
            $lName = strtolower($_POST['lName']);
            $Department = strtolower($_POST['department']);
            $Username = $_POST['username'];
            $Password = $_POST['Password'];
            //      Connect to db
            $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
            
            //      Check if already registered
            $sql = "SELECT * FROM `tbstaff` JOIN `tbempinfo` ON `tbempinfo`.`empid` = `tbstaff`.`emp_ID`
            WHERE `lastname` = ? AND `firstname` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $lName, $fName);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows == 0) {
                //  Insert into tbempinfo first
                $insertEmpInfosql = "INSERT INTO `tbempinfo` (lastname, firstname, department) VALUES 
                (?,?,?);";
                $insertEmpInfostmt = $conn->prepare($insertEmpInfosql);
                $insertEmpInfostmt->bind_param("sss", $lName, $fName, $Department);
                $insertEmpInfostmt->execute();
                
                //  Retreive added row in tbempinfo
                $userSql = "SELECT * FROM `tbempinfo` WHERE `lastname` = ? AND `firstname` = ?";
                $userStmt = $conn->prepare($userSql);
                $userStmt->bind_param("ss", $lName, $fName);
                $userStmt->execute();
                $row = $userStmt->get_result()->fetch_assoc();

                //  Insert into tbstaff
                $insertStaffsql = "INSERT INTO `tbstaff` (`emp_ID`,`userName`,`Password`)
                VALUES (?,?,?)";
                $insertStaffstmt = $conn->prepare($insertStaffsql);
                $insertStaffstmt->bind_param("sss", $row['empid'], $Username, $Password);
                $insertStaffstmt->execute();

                $Status = "Record inserted successfully.";
            } else {
                $Status = "Data already exists in the database.";
            }
            $conn->close();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register - Staff</title>
        <link rel="stylesheet" href="CSS/staffRegister.css">
    </head>
<body>
  

        
    <div class="signup">
    
       
        <form method="POST">
        <h1>Register your information by using this form. <br> Required fields are marked with an astrerisk (*).</h1><br>
        <?php echo $Status?><br>
   
     
            First Name: <input type="text" name="fName" value = <?php echo isset($_POST["fName"]) ? $_POST["fName"] : ''; ?>>
            <span class = "error">* <?php echo $fName_err?></span><br><br>

            Last Name: <input type="text" name="lName" value = <?php echo isset($_POST["lName"]) ? $_POST["lName"] : ''; ?>>
            <span class = "error">* <?php echo $lName_err?></span><br><br>

            Department: <input type="text" name="department" value = <?php echo isset($_POST["lName"]) ? $_POST["lName"] : ''; ?>>
            <span class = "error">* <?php echo $Department_err?></span><br><br>

            Username: <input type="text" name="username" value = <?php echo isset($_POST["username"]) ? $_POST["username"] : ''; ?>>
            <span class = "error">* <?php echo $Username_err?></span><br><br>

            Password: <input type="text" name="Password" value = <?php echo isset($_POST["Password"]) ? $_POST["Password"] : ''; ?>>
            <span class = "error">* <?php echo $Password_err?></span><br><br>
            <input type = "submit" name = "submit" value = "SUBMIT">
            <a href = "adminForm.php" class = "button">Go back</a>
        </form>
    </div>
</body>
</html>