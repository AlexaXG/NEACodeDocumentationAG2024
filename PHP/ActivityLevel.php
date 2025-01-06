<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Your Activity</title>
    <link rel="stylesheet" href="/css/pageStyling.css">
    <script src="toast.js"></script>
</head>
<style>
    #logout {
        background-color: red;
    }

    #logout:hover {
        background-color: #ffff;
        color: red;
    }  
</style>
<body>
    <div class="page-banner">
        <div class="item-container">
            <div class="button-container">
                <a href=""><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                <a class="button" href="http://localhost/php/Login.php">
                <button type="button">Have an account?</button></a>
                <a href="http://localhost/php/signup.php">
                        <button type="button" id="logout">Cancel signup</button>
                        <?php
                        $_SESSION["logout"] = true;
                        ?>
                    </a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class='login-text'>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="main-content">
                <div class="welcome-container">
                    <span class="welcome-text"><span id="webName">Your Activity:</span></span>
                </div>
                <div class="inputbox" id="activityBox">
                    <div class="radioButtons">
                        <div>
                            <input type="radio" id="activ1" name="active" value="sedentary" required>
                            <label for="activ1"><span class="labelText">Sedentary: </span></br>- Little to no
                                exercise.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="activ2" name="active" value="light">
                            <label for="activ2"><span class="labelText">Lightly Active: </span></br>- Light exercise 1-3
                                times a week.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="activ3" name="active" value="moderate">
                            <label for="activ3"><span class="labelText">Moderately Active: </span></br>- Moderate
                                exercise 3-5 times a week.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="activ4" name="active" value="very">
                            <label for="activ4"><span class="labelText">Very Active: </span></br>- Hard exercise 6-7
                                times a week.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="activ5" name="active" value="Extra">
                            <label for="activ5"><span class="labelText">Extra Active: </span></br>- Extreme exercise 6-7
                                times a week.</label><br />
                        </div><br />
                        <div class="ButtonCont">
                        <button type="submit" name="s">Confirm</button>
                        <a href="http://localhost/php/signupAttributes.php">
                            <button type="button">Back</button>
                        </a>
                        </div>
                    </div>
                </div>
                <div id="toast"></div>
        </form>
        <?php
        if (isset($_SESSION["userid"]) && isset($_SESSION["username"])) {
            $userid = $_SESSION["userid"];
            $username = $_SESSION["username"];
            if (!isset($_SESSION["Signup_in_progress"])) {
                header("location: http://localhost/php/Homepage.php");
            } 
        } else {
            header("location: http://localhost/php/login.php");
            exit();
        }
        if (!isset($_POST["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            $_SESSION['toast_message'] = "Database Issue";
        }
        $ActivityLevel = $_POST["active"];
            try {
                $userid = $_SESSION["userid"];
                $result = $connection->prepare("UPDATE user SET activity=? WHERE user.userID = ?");
                $result->bind_param("ss", $ActivityLevel, $userid);
                //adding the users activity to their relevant field in the 'user' table
                if (!$result->execute()) {
                    $_SESSION['toast_message'] = "Database Issue inserting values";
                } else {
                    $_SESSION["ActivityLevel"] = $ActivityLevel;
                    header("Location: http://localhost/php/userPreferences.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                $_SESSION['toast_message'] = "MySQLi issue" . $e;
            }
        ?>
    </div>
</body>

</html>

<?php
if (isset($_SESSION['toast_message'])) { ?>
    <script>
        document.getElementById("toast").innerHTML = '<div class="toast"><?php echo $_SESSION['toast_message']; ?></div>';
    </script>
    <?php unset($_SESSION['toast_message']); // Clear the message after displaying it
}
ob_end_flush();
?>