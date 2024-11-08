<?php
ob_start();
session_start();
class Database
{
    private $connection;
    public function __construct($hostname, $username, $password, $database)
    {
        $this->connection = new mysqli($hostname, $username, $password, $database);
        if ($this->connection->connect_error) {
            die("Connection failure " . $this->connection->connect_error);
        }
    }
    public function getConnection()
    {
        return $this->connection;
    }
    public function close()
    {
        $this->connection->close();
    }
}
class Activity
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function addActivity($userid, $activityName, $startTime, $endTime, $note, $recurrence)
    {
        $stmt = $this->db->prepare("INSERT INTO activities (userid, activityName, startTime, endTime, note, recurrence) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $userid, $activityName, $startTime, $endTime, $note, $recurrence);
        $stmt->execute();
        $stmt->close();
    }
    public function getActivities($userid)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities WHERE userid = ? ORDER BY startTime");
        $stmt->bind_param("i", $userid);
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
    $activityName = $_POST['activityName'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $note = $_POST['note'];
    $recurrence = $_POST['recurrence'];
    $userid = $_SESSION['userid'];

    if (strtotime($endTime) <= strtotime($startTime)) {
        $_SESSION['toast_message'] = "End time cannot happen before the start time!";
        exit();
    }

    $activity = new Activity($connection);
    $activity->addActivity($userid, $activityName, $startTime, $endTime, $note, $recurrence);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$activity = new Activity($connection);
$activities = $activity->getActivities($_SESSION['userid']);
?>
<!DOCTYPE HTML>

<body lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GymmieMeals-Homepage</title>
        <link rel="stylesheet" href="pageStyling.css"> <!-- referencing the updated Styling page-->
    </head>

    <!-- <div> -->
    <div class="MAINCONTAINER">
        <div class="page-banner">
            <div class="item-container">
                <nav class="button-container">
                    <img class="logoImg" src="/Other Files/GymmieMeals.png">
                    <ul>
                        <li><a class="button" href="http://localhost/PHP/Homepage.php"></li>
                        <button type="button">My Tracking</button></a>
                        <li><a class="button" href="http://localhost/PHP/MyMeals.php"></li>
                        <button type="button">My Meals</button></a>
                        <li><a class="button" href="http://localhost/PHP/MyGoals.php"></li>
                        <button type="button">My Goals</button></a>
                        <li><a class="button" href="http://localhost/PHP/MySchedule.php"></li>
                        <button type="button">My Schedule</button></a>
                        <li><a class="button" href="http://localhost/PHP/LandingPage.php"></li>
                        <button type="button">Test Button </button></a>
                    </ul>
                    <a href="http://localhost/PHP/MySettings.php">
                        <img class="logoImg" src="settingCog.png"></a>
                </nav>
            </div>
        </div>
        <form class="form1" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <!--<div class="parent-container">-->
            <div class="parent-container">
                <div class="empty"></div>
                <div class="columnNames">
                    <div class="cell">Monday</div>
                    <div class="cell">Monday</div>
                    <div class="cell">Monday</div>
                    <div class="cell">Monday</div>
                    <div class="cell">Monday</div>
                    <div class="cell">Monday</div>
                    <div class="cell">Sunday</div>
                </div>
                <div class="time-labels">
                    <div class="time-label">Hour</div>
                    <div class="time-label">00:00</div>
                    <div class="time-label">01:00</div>
                    <div class="time-label">02:00</div>
                    <div class="time-label">03:00</div>
                    <div class="time-label">04:00</div>
                    <div class="time-label">05:00</div>
                    <div class="time-label">06:00</div>
                    <div class="time-label">07:00</div>
                    <div class="time-label">08:00</div>
                    <div class="time-label">09:00</div>
                    <div class="time-label">10:00</div>
                    <div class="time-label">11:00</div>
                    <div class="time-label">12:00</div>
                    <div class="time-label">13:00</div>
                    <div class="time-label">14:00</div>
                    <div class="time-label">15:00</div>
                    <div class="time-label">16:00</div>
                    <div class="time-label">17:00</div>
                    <div class="time-label">18:00</div>
                    <div class="time-label">19:00</div>
                    <div class="time-label">20:00</div>
                    <div class="time-label">21:00</div>
                    <div class="time-label">22:00</div>
                    <div class="time-label">23:00</div>
                    <div class="time-label"></div>
                </div>
                <div class="content">
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                    <div class="cell"></div>
                </div>
            </div>
            <!-- <div class="time-labels">
                <div class="time-label">Hour</div>
                <div class="time-label">00:00</div>
                <div class="time-label">01:00</div>
                <div class="time-label">02:00</div>
                <div class="time-label">03:00</div>
                <div class="time-label">04:00</div>
                <div class="time-label">05:00</div>
                <div class="time-label">06:00</div>
                <div class="time-label">07:00</div>
                <div class="time-label">08:00</div>
                <div class="time-label">09:00</div>
                <div class="time-label">10:00</div>
                <div class="time-label">11:00</div>
                <div class="time-label">12:00</div>
                <div class="time-label">13:00</div>
                <div class="time-label">14:00</div>
                <div class="time-label">15:00</div>
                <div class="time-label">16:00</div>
                <div class="time-label">17:00</div>
                <div class="time-label">18:00</div>
                <div class="time-label">19:00</div>
                <div class="time-label">20:00</div>
                <div class="time-label">21:00</div>
                <div class="time-label">22:00</div>
                <div class="time-label">23:00</div>
                <div class="time-label"></div>
            </div> -->

            <!-- <div class="day-labels">
                    <div class="day-label" id="Mon">Monday</div>
                    <div class="day-label" id="Tue">Tuesday</div>
                    <div class="day-label" id="Wed">Wednesday</div>
                    <div class="day-label" id="Thu">Thursday</div>
                    <div class="day-label" id="Fri">Friday</div>
                    <div class="day-label" id="Sat">Saturday</div>
                    <div class="day-label" id="Sun">Sunday</div>
                </div> -->

            <!-- Weekly Planner Grid -->
            <div class="ActivityCreation">
                <div class="welcome-container">
                    <?php
                    if (!isset($_SESSION["userid"]) || !isset($_SESSION["username"])) {
                        $_SESSION['toast_message'] = "You must log in first!";
                    } else {
                        $userid = $_SESSION["userid"];
                        $username = $_SESSION["username"];
                    }
                    if (!isset($_SESSION["weight"]) || !isset($_SESSION["height"]) || !isset($_SESSION["gender"]) || !isset($_SESSION["age"]) || !isset($_SESSION["ActivityLevel"])) {
                        $_SESSION['toast_message'] = "Session not set!";
                    } else {
                        $weightArg = $_SESSION["weight"];
                        $heightArg = $_SESSION["height"];
                        $genderArg = $_SESSION["gender"];
                        $ageArg = $_SESSION["age"];
                        $activityArg = $_SESSION["ActivityLevel"];
                        //assigning session variables to local variables
                    }
                    $dob = new DateTime($ageArg);
                    $now = new DateTime();
                    $ageArg = $now->diff($dob)->y;
                    //standardising dates for age and calculating the age number (age is stored as a date in the DB)
                    ?>
                </div>
                <div class="display-content">
                    <div class='schedulebox1' id='scheduleBox'>
                        <span class="welcome-text">Create an <span id="webName">Activity:</span>
                        </span><br><br>
                        <label class="output-text" for="activityName">Activity Name:</label><br>
                        <input type="text" id="activityName" name="activityName" required><br><br>

                        <label class="output-text" for="startTime">Day:</label><br>
                        <select id="Day" name="Day" class="genderIn">
                            <option value="None">Monday</option>
                            <option value="Every Week">Tuesday</option>
                            <option value="Every Monday">Wednesday</option>
                            <option value="Every Tuesday">Thursday</option>
                            <option value="Every Wednesday">Friday</option>
                            <option value="Every Thursday">Saturday</option>
                            <option value="Every Friday">Sunday</option>
                        </select><br><br>

                        <label class="output-text" for="startTime">Start Time:</label><br>
                        <input class="output-text" type="time" id="startTime" name="startTime" required><br><br>

                        <label class="output-text" for="endTime">End Time:</label><br>
                        <input type="time" id="endTime" name="endTime" required><br><br>

                        <label class="output-text" for="note">Note:</label><br>
                        <textarea id="note" name="note" placeholder="..."></textarea><br><br>

                        <label class="output-text" for="recurrence">Recurrence:</label><br>
                        <select id="recurrence" name="recurrence" class="genderIn">
                            <option value="None">None</option>
                            <option value="Every Week">Every week on this day</option>
                            <option value="Every Monday">Every day</option>
                            <option value="Every Tuesday">Every weekday</option>
                        </select><br><br>
                        <button type="submit">Add Activity</button>
                    </div>
                </div>
            </div>
    </div>
    </form>
    </div>

    <div id="toast"></div>
</body>

</html>
<?php
if (isset($_SESSION['toast_message'])) {
    echo "<script>showToast('" . addslashes($_SESSION['toast_message']) . "');</script>";
    unset($_SESSION['toast_message']);
}
ob_end_flush();
$dbConnection->close();
?>