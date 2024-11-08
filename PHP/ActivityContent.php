<?php
session_start();
try {
    // COOL TEST?
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    die("<div class='error-message'>Something went wrong: </div>" . $e);
    //connect to DB
}
echo "<span class='welcome-text'>Your<span id='webName'> Activity:</span></span>";
if (!isset($_SESSION["userid"])  || !isset($_SESSION["username"])) {
    die("No session variables set");
}
if (!isset($_SESSION["ActivityLevel"])) {
    //checks if activity level session variable is set
    $userid = $_SESSION["userid"];
    $connUserDetails = $connection->prepare("SELECT activity from user where userid = ?");
    $connUserDetails->bind_param("s", $userid);
    $connUserDetails->execute();
    $connUserDetails->bind_result($ActivityLevel);
    $connUserDetails->fetch();
    //if its not set itll select it from user and bind it to a variable

    header('Content-Type: application/json');
    echo htmlspecialchars($ActivityLevel);
    
} else {
    $ActivityLevel = isset($_SESSION["ActivityLevel"]) ? $_SESSION["ActivityLevel"] : 'N/A';
    //if its not set, assigns a valid string "N/A"
    header('Content-Type: application/json');
    echo htmlspecialchars($ActivityLevel);
}
?>