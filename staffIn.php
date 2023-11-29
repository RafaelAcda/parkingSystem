<?php
    session_start();

    $plate_Number = '';
    $timezone = date_default_timezone_set('Asia/Manila');
    $current = date('Y-m-d h:i:s a');
    $time = new DateTime($current);
    $date = $time->format('Y-m-d');
    $time = $time->format('H:i:s');
    $condition = true;

    if (!isset($_SESSION['slots'])) {
        $_SESSION['slots'] = array_fill(1, 20, false);
    }

    if(isset($_POST['submit'])) {
        $plate_Number = $_POST['plate_Number'];

        $conn = new mysqli('localhost','root','','db_ba3101')or die("Could not connect to mysql".mysqli_error($conn));
        $sql = "SELECT plate_Number FROM tbclient WHERE plate_Number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $plate_Number);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $bookedSlotsCount = count(array_filter($_SESSION['slots']));

            if($bookedSlotsCount < 20) {
                $sql = "INSERT INTO tblogs (plate_Number, recordDate, time_In) VALUES
                (?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sss', $plate_Number, $date, $time);
                $stmt->execute();
                $Test = 'Inserted successfully';


                $availableSlots = array_search( false, $_SESSION['slots']);
                if ($availableSlots !== false) {
                    $_SESSION['slots'][$availableSlots] = true;
                }
            } else {
                echo 'No available slots';
            }
            
        } else {
            $condition = false;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>IN</title>
        <link rel ="stylesheet" href = "CSS/staffIn.css">
    </head>
<body>
<div class="left-side">
    
    </div>

    <div class="right-side">
      
    </div>
    <form method="post">
        <h2>Enter plate number</h2>
        <input type ="text" name ="plate_Number"><br><br>
        <button type="submit" name="submit">Click</button><br><br>
        <?php 

        if($condition == false) {
            echo '<a href="guestIn.php" class="btn">Guest</a>';
        }
        
        ?>
        <a href="loginStaff.php" class="btn">Logout</a>
    </form>
  <table>
        <thead>
            <tr>
                <th>Slot Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($_SESSION['slots'] as $slotNumber => $isBooked) {
            $status = $isBooked ? 'Not Available' : 'Available';
            echo "<tr>";
            echo "<td>Slot $slotNumber</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</body>
</html>