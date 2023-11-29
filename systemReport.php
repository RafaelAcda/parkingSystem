<?php 

    $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));

    $sql = "SELECT DISTINCT `recordDate` FROM `tblogs`";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $dates = array();
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row['recordDate'];
        }
    }
    $timezone = date_default_timezone_set('Asia/Manila');
    $current = date('Y-m-d h:i:s a');
    $time = new DateTime($current);
    $dateNow = $time->format('Y-m-d');

    $selectedDate = $dateNow;
    if(isset($_POST['submit'])) {
        $selectedDate = $_POST['recordedDate'];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Records</title>
        <link rel ="stylesheet" href = 'CSS/systemReport.css '>
    </head>
<body>
    <div class="reportContent">
        <h2>Records for the date <?php echo $selectedDate ?></h2>
        <a href="adminForm.php" class="btn">Back</a>
    </div>
    <div class="table_wrapper">
        
            <form method="post">
            <select name="recordedDate">
                <?php
                    foreach ($dates as $date) {
                        echo "<option value=\"$date\">$date</option>";
                    }
                ?>
            </select>
            <button type="submit" name="submit">Show Records</button>
            </form>
        <h3>Professors<h3>
        <div class="table_container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plate Number</th>
                        <th>Vehicle Type</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                    </tr>
                        <tbody>
                            <?php
                                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                $users = $conn->query("SELECT * FROM `tblogs` 
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                JOIN `tbempinfo` ON `tbclient`.`emp_ID` = `tbempinfo`.`empid`
                                WHERE `recordDate` = '$selectedDate'
                                AND `tbclient`.`type` = 'Professor';");
                                while($row = $users->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row['recordDate'] ?>
                                </td>
                                <td>
                                    <?php echo $row['plate_Number'] ?>
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
                                <td>
                                    <?php echo $row['time_Out'] ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                </thead>
            </table>
        </div>
        <h3>Students<h3>
        <div class="table_container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plate Number</th>
                        <th>Vehicle Type</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                    </tr>
                        <tbody>
                            <?php
                                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                $users = $conn->query("SELECT * FROM `tblogs` 
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                JOIN `tbstudinfo` ON `tbclient`.`student_ID` = `tbstudinfo`.`studid`
                                WHERE `recordDate` = '$selectedDate'
                                AND `tbclient`.`type` = 'Student';");
                                while($row = $users->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row['recordDate'] ?>
                                </td>
                                <td>
                                    <?php echo $row['plate_Number'] ?>
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
                                <td>
                                    <?php echo $row['time_Out'] ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                </thead>
            </table>
        </div>
        <h3>Guests<h3>
        <div class="table_container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plate Number</th>
                        <th>Vehicle Type</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                    </tr>
                        <tbody>
                            <?php
                                $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($con));
                                $users = $conn->query("SELECT * FROM `tblogs` 
                                JOIN `tbclient` ON `tblogs`.`plate_Number` = `tbclient`.`plate_Number`
                                JOIN `tbguestinfo` ON `tbclient`.`guest_ID` = `tbguestinfo`.`guest_ID`
                                WHERE `recordDate` = '$selectedDate'
                                AND `tbclient`.`type` = 'Student';");
                                while($row = $users->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <?php echo $row['recordDate'] ?>
                                </td>
                                <td>
                                    <?php echo $row['plate_Number'] ?>
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
                                <td>
                                    <?php echo $row['time_Out'] ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                </thead>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>