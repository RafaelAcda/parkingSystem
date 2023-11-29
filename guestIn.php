<?php

    $timezone = date_default_timezone_set('Asia/Manila');
    $current = date('Y-m-d h:i:s a');
    $time = new DateTime($current);
    $date = $time->format('Y-m-d');
    $time = $time->format('H:i:s');
    $fName = $lName = $plate_Number = $vehicle_Type = $Contact = $Department = $sr_Code = '';
    $fName_err = $lName_err = $plate_Number_err = $vehicle_Type_err = $Contact_err = $Department_err = $sr_Code_err = '';
    $complete = 0;

    if(isset($_POST['submit'])) {
        $Role = "Guest";
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
    }

    if($complete == 5) {
        $fName = strtolower($_POST['fName']);
        $lName = strtolower($_POST['lName']);
        $plate_Number = $_POST['plate_Number'];
        $vehicle_Type = $_POST['vehicle_Type'];
        $Contact = $_POST['Contact'];
        $Role = 'Guest';

        $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));

                $sql = "SELECT * FROM `tbclient` WHERE `plate_Number` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $plate_Number);
                $stmt->execute();
                $stmt->store_result();

                if($stmt->num_rows == 0) {
                        $guestSql = "SELECT * FROM `tbclient` JOIN `tbguestinfo` ON `tbguestinfo`.`guest_ID` = `tbclient`.`guest_ID`
                        WHERE `lastname` = ? AND `firstname` = ?";
                        $guestStmt = $conn->prepare($guestSql);
                        $guestStmt->bind_param("ss", $lName, $fName);
                        $guestStmt->execute();
                        $guestData = $guestStmt->get_result()->fetch_assoc();

                        if($guestStmt->num_rows >= 1) {
                            $guestDatafName = strtolower($guestData['firstname']);
                            $guestDatalName = strtolower($guestData['lastname']);
                        } else {
                            $guestDatafName = '';
                            $guestDatalName = '';
                        }

                        if($guestDatafName == $fName && $guestDatalName == $lName) {
                            $insertguestSql = "INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`)
                            VALUES (?, ?, NULL, NULL, ?, ?, ?)";
                            $insertguestStmt = $conn->prepare($insertguestSql);
                            $insertguestStmt->bind_param("ssiss",$plate_Number, $vehicle_Type, $guestData['guest_ID'], $guestData['Contact'], $Role);
                            if($insertguestStmt->execute()) {

                                $sql = "INSERT INTO `tblogs` (`plate_Number`, `recordDate`, `time_In`)
                                VALUES (?, ?, ?)";

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("sss", $plate_Number, $date, $time);
                                if($stmt->execute()) {
                                    header("Location: staffIn.php");
                                    exit;
                                } else {
                                    echo "Error inserting data: " . $stmt->error;
                                }
                                
                            } else {
                                echo "Error inserting data: " . $insertguestStmt->error;
                            }
                        } else {
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
                                $sql = "INSERT INTO `tblogs` (`plate_Number`, `recordDate`, `time_In`)
                                VALUES (?, ?, ?)";

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("sss", $plate_Number, $date, $time);
                                if($stmt->execute()) {
                                    header("Location: staffIn.php");
                                    exit;
                                } else {
                                    echo "Error inserting data: " . $stmt->error;
                                }
                            } else {
                                echo "Error inserting data: " . $insertClientstmt->error;
                            }
                        }
                } else {
                    echo "Data already exists in the database.";
                    $conn->close();
                }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Register - Client</title>
        <link rel="stylesheet" href="CSS/guestin.css">
    </head>
<body>
    <div class="signup">
        <h1>Enter the required Information</h1>
        <p><span class = "error">* required field</span></p>
        <form method="POST">
            First Name: <input type="text" name="fName" value = <?php echo isset($_POST["fName"]) ? $_POST["fName"] : ''; ?>>
            <span class = "error">* <?php echo $fName_err?></span><br><br>

            Last Name: <input type="text" name="lName" value = <?php echo isset($_POST["lName"]) ? $_POST["lName"] : ''; ?>>
            <span class = "error">* <?php echo $lName_err?></span><br><br>

            Plate Number: <input type="text" name="plate_Number" value = <?php echo isset($_POST["plate_Number"]) ? $_POST["plate_Number"] : ''; ?>>
            <span class = "error">* <?php echo $plate_Number_err?></span><br><br>

            Vehicle type: <input type="text" name="vehicle_Type" value = <?php echo isset($_POST["vehicle_Type"]) ? $_POST["vehicle_Type"] : ''; ?>>
            <span class = "error">* <?php echo $vehicle_Type_err?></span><br><br>

            Contact: <input type="text" name="Contact" value = <?php echo isset($_POST["Contact"]) ? $_POST["Contact"] : ''; ?>>
            <span class = "error">* <?php echo $Contact_err?></span><br><br>
            <input type = "submit" name = "submit" value = "Submit">
            <a href="staffIn.php" class="btn">Back</a>
        </form>
    </div>
</body>
</html>