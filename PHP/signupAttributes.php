<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Your Details</title>
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
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="GET">
            <div class="main-content">
                <div class="welcome-container">
                    <span class="welcome-text"><span id="webName">Your Details:</span></span>
                </div>
                <span class="small-text">
                    <h3>centimeters / Kilograms</h3>
                </span>
                <div class="inputbox" id="attr">
                    <label for="weight"></label> <br />
                    <input type="number" name="weight" placeholder="Weight" id="weightKG" autocomplete="off" min="0" max="400"
                        step="any" required/> <br />
                    <label for="height"> </label>
                    <input type="number" name="height" placeholder="Height" id="heightCM" autocomplete="off" min="0"
                       max="300" step="any" required/> <br />
                    <label for="age"> </label> 
                    <input type="date" name="age" id="ageYRS" autocomplete="off" required/> <br />

                    <label for="gender"></label>
                    <select name="gender" id="GenderENUM" type="text" required placeholder="Select gender"
                        class="genderIn">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="PNTS">Prefer not to say</option>
                    </select>
                    <br />
                    <button type="submit" name="s">Confirm</button>
                </div>
            </div>
            <div id="toast"></div>
        </form>
        <?php
        if (isset($_SESSION["userid"]) || isset($_SESSION["username"])) {
            $userid = $_SESSION["userid"];
            $username = $_SESSION["username"];
            if (!isset($_SESSION["Signup_in_progress"])) {
                header("location: http://localhost/php/Homepage.php");
            }
        } else {
            header("location: http://localhost/php/login.php");
            exit();
        }
        if (!isset($_GET["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            $_SESSION['toast_message'] = "Database Issue" . $e;
        }
        $weight = $_GET["weight"];
        $height = $_GET["height"];
        $gender = $_GET["gender"];
        $age = $_GET["age"];

        try {
            $result = $connection->prepare("UPDATE user SET weight=?, height=?, gender=?, age=? WHERE user.userID = ?");
            $result->bind_param("ddssi", $weight, $height, $gender, $age, $userid);
            if (!$result->execute()) {
                $_SESSION['toast_message'] = "Result issue, couldn't insert values. " .$e;
            } else {
                $_SESSION["weight"] = $weight;
                $_SESSION["height"] = $height;
                $_SESSION["gender"] = $gender;
                $_SESSION["age"] = $age;
                $_SESSION["Signup_in_progress"] = true;
                header("Location: http://localhost/php/ActivityLevel.php");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $_SESSION['toast_message'] = "MySQLi failed to execute correctly." . $e;
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