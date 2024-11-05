<?php
session_start();
try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    die("<div class='error-message'>Something went wrong: </div>" . $e);
    //connect to database
}
echo "<span class='welcome-text'>Your<span id='webName'> Preference:</span></span>";
if (!isset($_SESSION["userid"])  || !isset($_SESSION["username"])) {
    die("No session variables set");
}
if (!isset($_SESSION["Preference"])) {
    $userid = $_SESSION["userid"];
    $connUserPreferences = $connection->prepare("SELECT Preference.PreferenceName FROM userpreferences JOIN Preference ON 
        userpreferences.PreferenceID = Preference.PreferenceID WHERE userpreferences.UserID = ?;");
        //selects preference name from user preferences where the preferenceID in userpreferences table matches the preferenceID in preference table when the userid is set
    $connUserPreferences->bind_param("s", $userid);
    $connUserPreferences->execute();
    $connUserPreferences->bind_result($Preference);
    $connUserPreferences->fetch();
    header('Content-Type: application/json');
    echo htmlspecialchars($Preference);
} else {
    $Preference = isset($_SESSION["Preference"]) ? $_SESSION["Preference"] : 'N/A';
    //if the session variable is set itll assign it to the variable, otherwise itll assign 'N/A' as another valid string

    header('Content-Type: application/json');
    echo htmlspecialchars($Preference);
}
?>