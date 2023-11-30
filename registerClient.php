<?php 
    $fName = $lName = $plate_Number = $vehicle_Type = $Contact = $Department = $Course = $sr_Code = $Status = '';
    $fName_err = $lName_err = $plate_Number_err = $vehicle_Type_err = $Contact_err = $Department_err = $course_err = $sr_Code_err = '';
    $complete = 0;

    if(isset($_POST['submit'])) {
        $Role = $_POST['clientType'];
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
        if(empty($_POST['plate_Number'])) {
            $plate_Number_err = 'Plate Number is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['vehicle_Type'])) {
            $vehicle_Type_err = 'Vehicle type is missing';
        } else {
            $complete++;
        }
        if(empty($_POST['Contact'])) {
            $Contact_err = 'Contacts is missing';
        } else {
            $complete++;
        }
        if($Role == 1) {
            if(empty($_POST['sr_Code'])) {
                $sr_Code_err = 'SR-Code is missing';
            } else {
                $complete++;
            }
            if(empty($_POST['course'])) {
                $course_err = 'Course is missing';
            } else {
                $complete++;
            }
        }
        elseif ($Role == 2) {
            if(empty($_POST['Department'])) {
                $Department_err = 'Department is missing';
            } else {
                $complete++;
            }
        }
        elseif ($Role == 3) {
            $complete++;
        }

        //  If the required fields are complete proceed to inserting
        if($complete >= 6) {
            $fName = strtolower($_POST['fName']);
            $lName = strtolower($_POST['lName']);
            $plate_Number = $_POST['plate_Number'];
            $vehicle_Type = $_POST['vehicle_Type'];
            $Contact = $_POST['Contact'];
            
            if($Role == 1) {
                $sr_Code = $_POST['sr_Code'];
                $Course = $_POST['course'];
                $Role = 'Student';

                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));

                //      Check if plate number is already registered
                $sql = "SELECT `tbclient`.`plate_Number`,
                `tbstudinfo`.`lastname`, `tbstudinfo`.`firstname`
                FROM `tbclient` 
                JOIN `tbstudinfo` ON `tbstudinfo`.`studid` = `tbclient`.`student_ID`
                WHERE `plate_Number` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $plate_Number);
                $stmt->execute();
                $stmt->store_result();

                if($stmt->num_rows == 0) {

                    $targetDir = "Files/";
                    $fileName = "";

                    //  If registration includes OR/CR
                    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                        $fileName = basename($_FILES['file']['name']);
                        $targetPath = $targetDir . $fileName;
                        
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {

                            //  Check if SR-Code already exists within the database and retrieve related info
                            $srSql = "SELECT * FROM `tbclient` JOIN `tbstudinfo` ON `tbstudinfo`.`studid` = `tbclient`.`student_ID`
                            WHERE `studid` = ?";
                            $srStmt = $conn->prepare($srSql);
                            $srStmt->bind_param("i", $sr_Code);
                            $srStmt->execute();
                            $studentData = $srStmt->get_result()->fetch_assoc();

                            //  If SR-Code exists within the database
                            if($sr_Code == $studentData['studid']) {
                                $insertsrSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, ?, NULL, NULL, ?, ?, ?, ?)";
                                $insertsrStmt = $conn->prepare($insertsrSql);
                                $insertsrStmt->bind_param("ssissss",$plate_Number, $vehicle_Type, $studentData['studid'], $studentData['Contact'], $Role, $fileName, $targetPath);
                                if($insertsrStmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertsrStmt->error;
                                }
                            } else {    //  If SR-Code does not exist within the database
                                //  Insert to tbstudinfo
                                $lName = ucwords($lName);
                                $fName = ucwords($fName);
                                $Course = strtoupper($Course);
                                $insertStudinfosql = "INSERT INTO `tbstudinfo` (`studid`, `lastname`, `firstname`, `course`)
                                VALUES (?,?,?,?)";
                                $insertStdinfostmt = $conn->prepare($insertStudinfosql);
                                $insertStdinfostmt->bind_param("isss", $sr_Code, $lName, $fName, $Course);
                                $insertStdinfostmt->execute();

                                //  Retreive added row in tbstudinfo
                                $userSql = "SELECT * FROM `tbstudinfo` WHERE `studid` = ?";
                                $userStmt = $conn->prepare($userSql);
                                $userStmt->bind_param("i", $sr_Code);
                                $userStmt->execute();
                                $studentData = $userStmt->get_result()->fetch_assoc();

                                //  Insert into tbclient
                                $insertClientsql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, ?, NULL, NULL, ?, ?, ?, ?)";
                                $insertClientstmt = $conn->prepare($insertClientsql);
                                $insertClientstmt->bind_param("ssissss", $plate_Number, $vehicle_Type, $studentData['studid'], $Contact, $Role, $fileName, $targetPath);
                                if($insertClientstmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertClientstmt->error;
                                }
                            }             
                        } else {
                            $Status = "Error moving uploaded file.";
                        }
                    } else {    //  If registration does not include OR/CR

                        //  Check if SR-Code already exists within the database and retrieve related info
                        $srSql = "SELECT * FROM `tbclient` JOIN `tbstudinfo` ON `tbstudinfo`.`studid` = `tbclient`.`student_ID`
                        WHERE `studid` = ?";
                        $srStmt = $conn->prepare($srSql);
                        $srStmt->bind_param("i", $sr_Code);
                        $srStmt->execute();
                        $studentData = $srStmt->get_result()->fetch_assoc();

                        //  If SR-Code exists within the database
                        if($studentData != NULL) {
                            $insertsrSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, ?, NULL, NULL, ?, ?)";
                            $insertsrStmt = $conn->prepare($insertsrSql);
                            $insertsrStmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $studentData['studid'], $studentData['Contact'], $Role);
                            if($insertsrStmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertsrStmt->error;
                            }
                        } else {    //  If SR-Code does not exist within the database
                            //  Insert to tbstudinfo
                            $lName = ucwords($lName);
                            $fName = ucwords($fName);
                            $Course = strtoupper($Course);
                            $insertStudInfosql = "INSERT INTO `tbstudinfo` (`studid`, `lastname`, `firstname`, `course`)
                            VALUES (?,?,?,?)";
                            $insertStdInfostmt = $conn->prepare($insertStudInfosql);
                            $insertStdInfostmt->bind_param("isss", $sr_Code, $lName, $fName, $Course);
                            $insertStdInfostmt->execute();

                            //  Retreive added row in tbstudinfo
                            $userSql = "SELECT * FROM `tbstudinfo` WHERE `studid` = ?";
                            $userStmt = $conn->prepare($userSql);
                            $userStmt->bind_param("i", $sr_Code);
                            $userStmt->execute();
                            $studentData = $userStmt->get_result()->fetch_assoc();

                            //  Insert into tbclient
                            $insertClientsql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, ?, NULL, NULL, ?, ?)";    
                            $insertClientstmt = $conn->prepare($insertClientsql);
                            $insertClientstmt->bind_param("ssiss", $plate_Number, $vehicle_Type, $studentData['studid'], $Contact, $Role);
                            if($insertClientstmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertClientstmt->error;
                            }
                        }
                    }
                } else {
                    $Status = "Data already exists in the database.";
                }
                $conn->close();
            }       

            if($Role == 2) {
                $Department = $_POST['Department'];
                $Role = 'Professor';

                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));

                //      Check if plate number is already registered
                $sql = "SELECT `tbclient`.`plate_Number`,
                `tbempinfo`.`lastname`, `tbempinfo`.`firstname`
                FROM `tbclient` 
                JOIN `tbempinfo` ON `tbempinfo`.`empid` = `tbclient`.`emp_ID`
                WHERE `plate_Number` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $plate_Number);
                $stmt->execute();
                $stmt->store_result();

                if($stmt->num_rows == 0) {

                    $targetDir = "Files/";
                    $fileName = "";

                    //  If registration includes OR/CR
                    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                        $fileName = basename($_FILES['file']['name']);
                        $targetPath = $targetDir . $fileName;
                
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {

                            //  Check if Employee already exists within the database and retrieve related info
                            $empSql = "SELECT * FROM `tbclient` JOIN `tbempinfo` ON `tbempinfo`.`empid` = `tbclient`.`emp_ID`
                            WHERE `lastname` = ? AND `firstname` = ?";
                            $empStmt = $conn->prepare($empSql);
                            $empStmt->bind_param("ss", $lName, $fName);
                            $empStmt->execute();
                            $empData = $empStmt->get_result()->fetch_assoc();

                            if($empStmt->num_rows >= 1) {
                                $empDatalName = strtolower($empData['lastname']);
                                $empDatafName = strtolower($empData['firstname']);
                            } else {
                                $empDatalName = '';
                                $empDatafName = '';
                            }
                            
                            //  If employee exists within the database
                            if($empDatalName == $lName && $empDatafName == $fName) {

                                $insertempSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, NULL, ?, NULL, ?, ?, ?, ?)";
                                $insertempStmt = $conn->prepare($insertempSql);
                                $insertempStmt->bind_param("ssissss",$plate_Number, $vehicle_Type, $empData['empid'], $empData['Contact'], $Role, $fileName, $targetPath);
                                if($insertempStmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertempStmt->error;
                                }
                            } else {    //  If employee does not exists within the database
                                //  Insert to tbempinfo
                                $lName = ucwords($lName);
                                $fName = ucwords($fName);
                                $insertEmpinfosql = "INSERT INTO `tbempinfo` (lastname, firstname)
                                VALUES (?,?)";
                                $insertEmpinfostmt = $conn->prepare($insertEmpinfosql);
                                $insertEmpinfostmt->bind_param("ss", $lName, $fName);
                                $insertEmpinfostmt->execute();

                                //  Retrieve added info from tbempinfo
                                $userSql = "SELECT * FROM `tbstudinfo` 
                                WHERE `lastname` = ? AND `firstname` = ?";
                                $userStmt = $conn->prepare($userSql);
                                $userStmt->bind_param("ss", $lName, $fName);
                                $userStmt->execute();
                                $empData = $userStmt->get_result()->fetch_assoc();

                                //  Insert into tbclient
                                $insertClientSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, NULL, ?, NULL, ?, ?, ?, ?)";
                                $insertClientstmt = $conn->prepare($insertClientSql);
                                $insertClientstmt->bind_param("ssissss",$plate_Number, $vehicle_Type, $empData['empid'], $Contact, $Role, $fileName, $targetPath);
                                if($insertClientstmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertClientstmt->error;
                                }
                            }
                        } else {
                            $Status = "Error moving uploaded file.";
                        }
                    } else {    //  If registration does not include OR/CR
                        //  Check if Employee already exists within the database and retrieve related info
                        $empSql = "SELECT * FROM `tbclient` JOIN `tbempinfo` ON `tbempinfo`.`empid` = `tbclient`.`emp_ID`
                        WHERE `lastname` = ? AND `firstname` = ?";
                        $empStmt = $conn->prepare($empSql);
                        $empStmt->bind_param("ss", $lName, $fName);
                        $empStmt->execute();
                        $empData = $empStmt->get_result()->fetch_assoc();

                        if($empStmt->num_rows >= 1) {
                            $empDatalName = strtolower($empData['lastname']);
                            $empDatafName = strtolower($empData['firstname']);
                        } else {
                            $empDatalName = '';
                            $empDatafName = '';
                        }
                        //  If employee exists within the database
                        if($empDatalName == $lName && $empDatafName == $fName) {

                            $insertempSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, NULL, ?, NULL, ?, ?)";
                            $insertempStmt = $conn->prepare($insertempSql);
                            $insertempStmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $empData['empid'], $empData['Contact'], $Role);
                            if($insertempStmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertempStmt->error;
                            }
                        } else {    //  If employee does not exists within the database
                            //  Insert to tbempinfo
                            $lName = ucwords($lName);
                            $fName = ucwords($fName);
                            $insertEmpinfosql = "INSERT INTO `tbempinfo` (lastname, firstname)
                            VALUES (?,?)";
                            $insertEmpinfostmt = $conn->prepare($insertEmpinfosql);
                            $insertEmpinfostmt->bind_param("ss", $lName, $fName);
                            $insertEmpinfostmt->execute();

                            //  Retrieve added info from tbempinfo
                            $userSql = "SELECT * FROM `tbstudinfo` 
                            WHERE `lastname` = ? AND `firstname` = ?";
                            $userStmt = $conn->prepare($userSql);
                            $userStmt->bind_param("ss", $lName, $fName);
                            $userStmt->execute();
                            $empData = $userStmt->get_result()->fetch_assoc();

                            //  Insert into tbclient
                            $insertClientSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, NULL, ?, NULL, ?, ?)";
                            $insertClientstmt = $conn->prepare($insertClientSql);
                            $insertClientstmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $empData['empid'], $Contact, $Role);
                            if($insertClientstmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertClientstmt->error;
                            }
                        }
                    }
                } else {
                    $Status = "Data already exists in the database.";
                }
                $conn->close();
            }
            
            if($Role == 3) {
                $Role = 'Guest';

                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));

                //  If registration includes OR/CR
                $sql = "SELECT `tbclient`.`plate_Number`,
                `tbguestinfo`.`lastname`, `tbguestinfo`.`firstname`
                FROM `tbclient` 
                JOIN `tbguestinfo` ON `tbclient`.`guest_ID` = `tbguestinfo`.`guest_ID`
                WHERE `plate_Number` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $plate_Number);
                $stmt->execute();
                $stmt->store_result();

                if($stmt->num_rows == 0) {

                    $targetDir = "Files/";
                    $fileName = "";

                    //  If registration includes OR/CR
                    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                        $fileName = basename($_FILES['file']['name']);
                        $targetPath = $targetDir . $fileName;
                
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {

                            //  Check if Guest already exists within the database and retrieve related info
                            $empSql = "SELECT * FROM `tbclient` JOIN `tbguestinfo` ON `tbguestinfo`.`guest_ID` = `tbclient`.`guest_ID`
                            WHERE `lastname` = ? AND `firstname` = ?";
                            $empStmt = $conn->prepare($empSql);
                            $empStmt->bind_param("ss", $lName, $fName);
                            $empStmt->execute();
                            $guestData = $empStmt->get_result()->fetch_assoc();

                            $guestDatalName = $guestDatafName = ''; 
                            if($empStmt->num_rows >= 1) {
                                $guestDatalName = strtolower($guestData['lastname']);
                                $guestDatafName = strtolower($guestData['firstname']);
                            } else {
                                $guestDatafName = '';
                                $guestDatalName = ''; 
                            }
                            
                            //  If guest exists within the database
                            if($guestDatalName == $lName && $guestDatafName == $fName) {

                                $insertempSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, NULL, NULL, ?, ?, ?, ?, ?)";
                                $insertempStmt = $conn->prepare($insertempSql);
                                $insertempStmt->bind_param("ssissss",$plate_Number, $vehicle_Type, $guestData['guest_ID'], $guestData['Contact'], $Role, $fileName, $targetPath);
                                if($insertempStmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertempStmt->error;
                                }
                            } else {    //  If guest does not exists within the database
                                //  Insert to tbempinfo
                                $lName = ucwords($lName);
                                $fName = ucwords($fName);
                                $insertGstinfosql = "INSERT INTO `tbguestinfo` (lastname, firstname)
                                VALUES (?,?)";
                                $insertGstinfostmt = $conn->prepare($insertGstinfosql);
                                $insertGstinfostmt->bind_param("ss", $lName, $fName);
                                $insertGstinfostmt->execute();

                                //  Retrieve added info from tbempinfo
                                $userSql = "SELECT * FROM `tbguestinfo` 
                                WHERE `lastname` = ? AND `firstname` = ?";
                                $userStmt = $conn->prepare($userSql);
                                $userStmt->bind_param("ss", $lName, $fName);
                                $userStmt->execute();
                                $guestData = $userStmt->get_result()->fetch_assoc();

                                //  Insert into tbclient
                                $insertClientSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`)
                                VALUES (?, ?, NULL, NULL, ?, ?, ?, ?, ?)";
                                $insertClientstmt = $conn->prepare($insertClientSql);
                                $insertClientstmt->bind_param("ssissss",$plate_Number, $vehicle_Type, $guestData['empid'], $Contact, $Role, $fileName, $targetPath);
                                if($insertClientstmt->execute()) {
                                    $Status = "Data inserted successfully with file.";
                                } else {
                                    $Status = "Error inserting data: " . $insertClientstmt->error;
                                }
                            }
                        } else {
                            $Status = "Error moving uploaded file.";
                        }
                    } else {    //  If registration does not include OR/CR
                        //  Check if Guest already exists within the database and retrieve related info
                        $empSql = "SELECT * FROM `tbclient` JOIN `tbguestinfo` ON `tbguestinfo`.`guest_ID` = `tbclient`.`guest_ID`
                        WHERE `lastname` = ? AND `firstname` = ?";
                        $empStmt = $conn->prepare($empSql);
                        $empStmt->bind_param("ss", $lName, $fName);
                        $empStmt->execute();
                        $guestData = $empStmt->get_result()->fetch_assoc();

                        if($empStmt->num_rows >= 1) {
                            $guestDatalName = strtolower($guestData['lastname']);
                            $guestDatafName = strtolower($guestData['firstname']);
                        } else {
                            $guestDatafName = '';
                            $guestDatalName = '';
                        }
                        //  If guest exists within the database
                        
                        if($guestDatalName == $lName && $guestDatafName == $fName) {

                            $insertempSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, NULL, NULL, ?, ?, ?)";
                            $insertempStmt = $conn->prepare($insertempSql);
                            $insertempStmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $guestData['guest_ID'], $guestData['Contact'], $Role);
                            if($insertempStmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertempStmt->error;
                            }
                        } else {    //  If guest does not exists within the database
                            //  Insert to tbguestinfo
                            $lName = ucwords($lName);
                            $fName = ucwords($fName);
                            $insertGstinfosql = "INSERT INTO `tbguestinfo` (lastname, firstname)
                            VALUES (?,?)";
                            $insertGstinfostmt = $conn->prepare($insertGstinfosql);
                            $insertGstinfostmt->bind_param("ss", $lName, $fName);
                            $insertGstinfostmt->execute();

                            //  Retrieve added info from tbguestinfo
                            $userSql = "SELECT * FROM `tbguestinfo` 
                            WHERE `lastname` = ? AND `firstname` = ?";
                            $userStmt = $conn->prepare($userSql);
                            $userStmt->bind_param("ss", $lName, $fName);
                            $userStmt->execute();
                            $guestData = $userStmt->get_result()->fetch_assoc();

                            //  Insert into tbclient
                            $insertClientSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, NULL, NULL, ?, ?, ?)";
                            $insertClientstmt = $conn->prepare($insertClientSql);
                            $insertClientstmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $guestData['guest_ID'], $Contact, $Role);
                            if($insertClientstmt->execute()) {
                                $Status = "Data inserted successfully without file.";
                            } else {
                                $Status = "Error inserting data: " . $insertClientstmt->error;
                            }
                        }
                    }
                } else {
                    $Status = "Data already exists in the database.";
                }
                $conn->close();
            } 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register - Client</title>
        <link rel="stylesheet" href="CSS/clientRegister.css">
    </head>
<body>
<div class="content">
      <div class="header">


</div>
</div>

<div style="padding-left: 500px">

</div>
  
        </style>
    <div class="signup">
        <h1>Enter the required Information</h1>
        <p><span class = "error">* required field / Guest does not need to fill Department or SR-Code</span></p>
        <?php echo $Status?>
        <form method="POST" enctype="multipart/form-data">
            First Name: <span class = "error">* <?php echo $fName_err?></span>
            <input type="text" name="fName" value = <?php echo isset($_POST["fName"]) ? $_POST["fName"] : ''; ?>>
            <br><br>

            Last Name: <span class = "error">* <?php echo $lName_err?></span>
            <input type="text" name="lName" value = <?php echo isset($_POST["lName"]) ? $_POST["lName"] : ''; ?>>
            <br><br>

            Plate Number: <span class = "error">* <?php echo $plate_Number_err?></span>
            <input type="text" name="plate_Number" value = <?php echo isset($_POST["plate_Number"]) ? $_POST["plate_Number"] : ''; ?>>
            <br><br>

            Vehicle type: <span class = "error">* <?php echo $vehicle_Type_err?></span>
            <input type="text" name="vehicle_Type" value = <?php echo isset($_POST["vehicle_Type"]) ? $_POST["vehicle_Type"] : ''; ?>>
            <br><br>

            Contact: <span class = "error">* <?php echo $Contact_err?></span>
            <input type="text" name="Contact" value = <?php echo isset($_POST["Contact"]) ? $_POST["Contact"] : ''; ?>>
            <br><br>

            Department: <span class = "error">* <?php echo $Department_err?></span>
            <input type="text" name="Department" value = <?php echo isset($_POST["Department"]) ? $_POST["Department"] : ''; ?>>
            <br><br>

            SR-Code: <span class = "error">* <?php echo $sr_Code_err?></span>
            <input type="text" name="sr_Code" value = <?php echo isset($_POST["sr_Code"]) ? $_POST["sr_Code"] : ''; ?>>
            <br><br>

            Course: <span class = "error">* <?php echo $course_err?></span>
            <input type="text" name="course" value = <?php echo isset($_POST["course"]) ? $_POST["course"] : ''; ?>>
            <br><br>

            <label for="clientType">Choose type:</label>
            <select name ="clientType">
                <option value ="1">Student</option>
                <option value ="2">Professor</option>
                <option value ="3">Guest</option>
            </select>
            <br><br>
            OR/CR: <input type="file" name="file"><br>
            <input type = "submit" name = "submit" value = "Submit">
            <a href = "adminForm.php" class = "button">Go back</a>
        </form>
    </div>
</body>
</html>