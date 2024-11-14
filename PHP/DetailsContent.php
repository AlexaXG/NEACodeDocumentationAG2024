<?php
session_start();
try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    $_SESSION['toast_message'] = "Database Issue" . $e;
    //establish database connection
}
echo "<span class='welcome-text'>Your<span id='webName'> Details:</span></span>";
//welcome message
if (!isset($_SESSION["userid"])  || !isset($_SESSION["username"])) {
    header("Location: http://localhost//php/Login.php");
    exit();
}
if (!isset($_SESSION["weight"]) || !isset($_SESSION["height"]) || !isset($_SESSION["gender"]) || !isset($_SESSION["age"])) {
    //selects the session variables from sql if they are not set
    $username = $_SESSION["username"];
    //fetches session variable of type username
    $connUserDetails = $connection->prepare("select weight, height, gender, age from user where username = ?");
    //parameterised sql query
    $connUserDetails->bind_param("s", $username);
    $connUserDetails->execute();
    $connUserDetails->bind_result($weight, $height, $gender, $age);
    //selects the user details from the user table
    $connUserDetails->fetch();

    header('Content-Type: application/json');
    //instructs frontend that php response is in json format
    echo "<h2>Your Weight: </h2>" . htmlspecialchars($weight) . "\n" . "<h2>Your Height: </h2>" . htmlspecialchars($height) . "\n"
        . "<h2>Your Gender: </h2>" . htmlspecialchars($gender) . "\n" . "<h2>Your Date of Birth: </h2>" . htmlspecialchars($age);
        //outputs all attributes and values trimmed without special characters on each new line
} else {
    $weight = isset($_SESSION["weight"]) ? $_SESSION["weight"] : '0.00';
    $height = isset($_SESSION["height"]) ? $_SESSION["height"] : '0.00';
    $gender = isset($_SESSION["gender"]) ? $_SESSION["gender"] : 'N/A';
    $age = isset($_SESSION["age"]) ? $_SESSION["age"] : 'N/A';
    //these variables will be set to the session variable value, and if its not set, itll default to values that are still valid, such as integers or strings shown after the colon
    
    if ($gender === "PNTS") {
        $gender = "Prefer not to say";
        //expands the PNTS string as that is how it is stored in the database, but the user may not recognise this
    }
    header('Content-Type: application/json');

    echo "<h2>Your Weight: </h2>" . htmlspecialchars($weight) . "\n" . "<h2>Your Height: </h2>" . htmlspecialchars($height) . "\n"
        . "<h2>Your Gender: </h2>" . htmlspecialchars($gender) . "\n" . "<h2>Your Date of Birth: </h2>" . htmlspecialchars($age);
}

?>