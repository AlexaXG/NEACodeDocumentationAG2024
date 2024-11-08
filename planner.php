<?php
session_start();

class Database {
    private $connection;
    public function __construct($hostname, $username, $password, $database) {
        $this->connection = new mysqli($hostname, $username, $password, $database);
        if ($this->connection->connect_error) {
            die("Connection failure " . $this->connection->connect_error);
        }
    }
    public function getConnection() {
        return $this->connection;
    }
    public function close() {
        $this->connection->close();
    }
}

class Activity {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addActivity($userId, $activityName, $startTime, $endTime, $note, $recurrence) {
        $stmt = $this->db->prepare("INSERT INTO activities (userid, activity_name, start_time, end_time, note, recurrence) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $userId, $activityName, $startTime, $endTime, $note, $recurrence);
        $stmt->execute();
        $stmt->close();
    }

    public function getActivities($userId) {
        $stmt = $this->db->prepare("SELECT * FROM activities WHERE userid = ? ORDER BY start_time");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $activities = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $activities;
    }
}

$dbConnection = new Database('localhost', 'root', '', 'neadatabasealexg'); 
$connection = $dbConnection->getConnection();

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    $_SESSION['toast_message'] = "UserID isn't set";
    header("Location: " . "http://localhost/login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activityName = $_POST['activity_name'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $note = $_POST['note'];
    $recurrence = $_POST['recurrence'];
    $userId = $_SESSION['userid'];

    if (strtotime($endTime) <= strtotime($startTime)) {
        $_SESSION['toast_message'] = "End time cannot happen before the start time!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $activity = new Activity($connection);
    $activity->addActivity($userId, $activityName, $startTime, $endTime, $note, $recurrence);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$activity = new Activity($connection);
$activities = $activity->getActivities($_SESSION['userid']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
        #toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 200px;
            padding: 15px;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        #toast.show {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>
    <h1>Daily Planner</h1>
    <h2>Add Activity</h2>
    <form method="post" action="">
        <label for="activity_name">Activity Name:</label><br>
        <input type="text" id="activity_name" name="activity_name" required><br><br>

        <label for="start_time">Start Time:</label><br>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_time">End Time:</label><br>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <label for="note">Note:</label><br>
        <textarea id="note" name="note"></textarea><br><br>

        <label for="recurrence">Recurrence:</label><br>
        <select id="recurrence" name="recurrence">
            <option value="None">None</option>
            <option value="Every Week">Every Week</option>
            <option value="Every Monday">Every Monday</option>
            <option value="Every Tuesday">Every Tuesday</option>
            <option value="Every Wednesday">Every Wednesday</option>
            <option value="Every Thursday">Every Thursday</option>
            <option value="Every Friday">Every Friday</option>
        </select><br><br>

        <input type="submit" value="Add Activity">
    </form>
        
    <h2>Your Activities</h2>
    <table>
        <thead>
            <tr>
                <th>Activity Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Note</th>
                <th>Recurrence</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($activities)): ?>
                <tr>
                    <td colspan="6">No activities found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                        <td><?php echo htmlspecialchars($activity['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($activity['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($activity['note']); ?></td>
                        <td><?php echo htmlspecialchars($activity['recurrence']); ?></td>
                        <td><?php echo htmlspecialchars($activity['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div id="toast"></div>
</body>

</html>
<script>
    function showToast(message) {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);
    }
</script>
<?php
if (isset($_SESSION['toast_message'])) {
    echo "<script>showToast('" . addslashes($_SESSION['toast_message']) . "');</script>";
    unset($_SESSION['toast_message']);
}
$dbConnection->close();
?>