<?php 
    $timezone = date_default_timezone_set('Asia/Manila');
    $current = date('Y-m-d h:i:s a');
    $time = new DateTime($current);
    $date = $time->format('Y-m-d');
    $time = $time->format('H:i:s');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel = "stylesheet" href = "CSS/adminForm.css">
    </head>
    <body>
        <div class="adminContent">
            <div class="adminContent_header">
                <h2>Admin Dashboard</h2>
                <a href="loginAdmin.php" class="btn">Logout</a>
            </div>
            <div class="card_container">
                <h3>Today's Data</h3>
                <div class="card_wrapper">
                    <div class="card_info">
                        <span class ="card_title">Professors</span>
                        <?php 
                            $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                            $users = $conn->query("SELECT COUNT(*) AS professor_count
                            FROM (
                                SELECT `tblogs`.`plate_number`, `tbclient`.`type`, `tblogs`.`time_In`, `tblogs`.`time_Out`
                                FROM `tblogs`
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                WHERE `tbclient`.`type` = 'Professor'
                                  AND `tblogs`.`recordDate` = '$date'
                                  AND `tblogs`.`time_Out` IS NOT NULL
                            ) AS professors_data");
                            $row = $users->fetch_assoc();
                        ?>
                        <span class ="card_amount">
                            <?php echo $row['professor_count'] ?>
                        </span>
                    </div>
                    <div class="card_info">
                        <span class ="card_title">Students</span>
                        <?php 
                            $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                            $users = $conn->query("SELECT COUNT(*) AS student_count
                            FROM (
                                SELECT `tblogs`.`plate_number`, `tbclient`.`type`, `tblogs`.`time_In`, `tblogs`.`time_Out`
                                FROM `tblogs`
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                WHERE `tbclient`.`type` = 'Student'
                                  AND `tblogs`.`recordDate` = '$date'
                                  AND `tblogs`.`time_Out` IS NOT NULL
                            ) AS student_data");
                            $row = $users->fetch_assoc();
                        ?>
                        <span class ="card_amount">
                            <?php echo $row['student_count'] ?>
                        </span>
                    </div>
                    <div class="card_info">
                        <span class ="card_title">Guests</span>
                        <?php 
                            $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                            $users = $conn->query("SELECT COUNT(*) AS guest_count
                            FROM (
                                SELECT `tblogs`.`plate_number`, `tbclient`.`type`, `tblogs`.`time_In`, `tblogs`.`time_Out`
                                FROM `tblogs`
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                WHERE `tbclient`.`type` = 'Guest'
                                  AND `tblogs`.`recordDate` = '$date'
                                  AND `tblogs`.`time_Out` IS NOT NULL
                            ) AS guest_data");
                            $row = $users->fetch_assoc();
                        ?>
                        <span class ="card_amount">
                            <?php echo $row['guest_count'] ?>
                        </span>
                    </div>
                    <div class="card_btn">
                        <a href="registerStaff.php" class="card_btn1">Register Staff</a>
                        <a href="registerClient.php" class="card_btn2">Register Clients</a>
                        <a href="systemReport.php" class="card_btn2">Records</a>
                    </div>
                </div>
            </div>
            <div class="table_wrapper">
                <h3>Professors</h3>
                <div class="table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Vehicle Type</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
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
                                    AND `tblogs`.`time_Out` IS NULL;");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['vehicle_Type'] ?>
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
                                <th>Vehicle Type</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
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
                                    AND `tblogs`.`time_Out` IS NULL;");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['vehicle_Type'] ?>
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
                                <th>Vehicle Type</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Time In</th>
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
                                    AND `tblogs`.`time_Out` IS NULL;");
                                    while($row = $users->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $row['plate_number'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['vehicle_Type'] ?>
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