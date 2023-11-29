<?php
    $timezone = date_default_timezone_set('Asia/Manila');
    $current = date('Y-m-d h:i:s a');
    $time = new DateTime($current);
    $date = $time->format('Y-m-d');
    $time = $time->format('H:i:s');
    $update = 'No Updates';

    if (isset($_POST['timeOut'])) {
        $plateNumber = $_POST['timeOut'];

        $conn = new mysqli('localhost', 'root', '', 'db_ba3101') or die("Could not connect to MySQL: " . mysqli_error($con));
        $query = "UPDATE `tblogs` SET `time_Out` = ? WHERE `time_Out` IS NULL AND `plate_Number` = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $time, $plateNumber);
        $stmt->execute();

        session_start();
        $bookedSlot = array_search(true, $_SESSION['slots']);

        if ($bookedSlot !== false) {
            $_SESSION['slots'][$bookedSlot] = false;
        }
        $update = 'Time Out Submitted and Slot '.$bookedSlot.' is now available';
    } else {
        $update = "No Updates";
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Staff - OUT</title>
        <link rel = "stylesheet" href = "CSS/staffout.css">
    </head>
    <body>
        <div class="staffContent">
            <div class="staffContent_header">
                <h2>Status: <?php echo $update?></h2>
                <a href="loginStaff.php" class="btn">Logout</a>
            </div>
        </div>
        <div class="table_wrapper">
            <h3>Professors</h3>
            <div class="table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
                                <th>Out</th>
                            </tr>
                            <tbody>
                                <?php
                                    $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                    $users = $conn->query("SELECT `tblogs`.`plate_number`, `tbclient`.`vehicle_Type`,`tbempinfo`.`firstname`, `tbempinfo`.`lastname`, `tbempinfo`.`department`, 
                                    `tbclient`.`Contact`, `tbclient`.`type`, `tblogs`.`time_In` , `tblogs`.`time_Out`
                                    FROM `tblogs` 
                                    JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number` 
                                    JOIN `tbempinfo` ON `tbempinfo`.`empid` = `tbclient`.`emp_ID`
                                    AND `tbclient`.`type` = 'Professor' 
                                    AND `tblogs`.`recordDate` = '$date'
                                    AND `tblogs`.`time_Out` IS NULL
                                    ORDER BY `tblogs`.`time_In` ASC;");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $firstName = ucfirst($row['firstname']);
                                            $lastName = ucfirst($row['lastname']);
                                            echo $firstName.' '.$lastName ?>
                                        </td>
                                    <td>
                                        <?php echo $row['Contact'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['time_In'] ?>
                                    </td>
                                    <td>    <!--    Time Out button     -->
                                        <form method='POST' action = "staffOut.php">
                                            <input type='hidden' name='timeOut' value='<?php echo $row['plate_number'] ?>'>
                                            <input type='submit' value='X'>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </thead>
                    </table>
                </div>
                <h4>Students</h4>
                <div class="table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
                                <th></th>
                            </tr>
                            <tbody>
                                <?php
                                    $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                    $users = $conn->query("SELECT `tblogs`.`plate_number`, `tbclient`.`vehicle_Type`,`tbstudinfo`.`firstname`, `tbstudinfo`.`lastname`, `tbstudinfo`.`studid`,
                                    `tbclient`.`Contact`, `tbclient`.`type`, `tblogs`.`time_In` , `tblogs`.`time_Out`
                                    FROM `tblogs` 
                                    JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number` 
                                    JOIN `tbstudinfo` ON `tbstudinfo`.`studid` = `tbclient`.`student_ID`
                                    AND `tbclient`.`type` = 'Student' 
                                    AND `tblogs`.`recordDate` = '$date'
                                    AND `tblogs`.`time_Out` IS NULL
                                    ORDER BY `tblogs`.`time_In` ASC");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $firstName = ucfirst($row['firstname']);
                                            $lastName = ucfirst($row['lastname']);
                                            echo $firstName.' '.$lastName ?>
                                    </td>
                                    <td>
                                        <?php echo $row['Contact'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['time_In'] ?>
                                    </td>
                                    <td>    <!--    Time Out button     -->
                                        <form method='POST' action = "staffOut.php">
                                            <input type='hidden' name='timeOut' value='<?php echo $row['plate_number'] ?>'>
                                            <input type='submit' value='X'>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </thead>
                    </table>
                </div>
                <h4>Guests</h4>
                <div class="table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
                                <th>Out</th>
                            </tr>
                            <tbody>
                                <?php
                                    $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                    $users = $conn->query("SELECT `tblogs`.`plate_number`, `tbclient`.`vehicle_Type`, `tbguestinfo`.`firstname`, `tbguestinfo`.`lastname`, 
                                    `tbclient`.`Contact` , `tbclient`.`type`, `tblogs`.`time_In` , `tblogs`.`time_Out`
                                    FROM `tblogs` 
                                    JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                    JOIN `tbguestinfo` ON `tbclient`.`guest_ID` = `tbguestinfo`.`guest_ID`
                                    AND `tbclient`.`type` = 'Guest' 
                                    AND `tblogs`.`recordDate` = '$date'
                                    AND `tblogs`.`time_Out` IS NULL
                                    ORDER BY `tblogs`.`time_In` ASC");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $firstName = ucfirst($row['firstname']);
                                            $lastName = ucfirst($row['lastname']);
                                            echo $firstName.' '.$lastName ?>
                                    </td>
                                    <td>
                                        <?php echo $row['Contact'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['time_In'] ?>
                                    </td>
                                    <td>    <!--    Time Out button     -->
                                        <form method='POST' action = "staffOut.php">
                                            <input type='hidden' name='timeOut' value='<?php echo $row['plate_number'] ?>'>
                                            <input type='submit' value='X'>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
    // JavaScript function to remove the row associated with the clicked button
    function removeRow(event) {
        event.preventDefault();
        var form = event.target;
        var row = form.parentNode.parentNode; // Get the row element
        row.parentNode.removeChild(row); // Remove the row from the table
    }
</script>