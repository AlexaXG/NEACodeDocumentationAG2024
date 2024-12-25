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
                <a class="button" onclick="copyToCB()" onmouseover="replaceText()" onmouseout="replaceTextBack()">
                    <button type="button" value="Contact Support" id="contact-support">
                        Contact Support</button>
                    <div id="SupportEmail" style="display:none;">fakeContactSupport@fakeSupportMail.com</div>
                </a>
                <a href="http://localhost/php/signup.php">
                        <button type="button" id="logout">Log out</button>
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
                    <input type="number" name="weight" placeholder="Weight" id="weightKG" autocomplete="off" min="0"
                        step="any" required/> <br />
                    <label for="height"> </label>
                    <input type="number" name="height" placeholder="Height" id="heightCM" autocomplete="off" min="0"
                        step="any" required/> <br />
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
                <script>
                    function toggleVis1() {
                        const svgData = document.getElementById("toggleEye");
                        svgData.src = svgData.src.includes("/Other Files/closedEye.svg") ? "/Other Files/openEye.svg" : "/Other Files/closedEye.svg";

                        var toggle = document.getElementById("password1");
                        var toggle1 = document.getElementById("password2");
                        if (toggle.type === "password") {
                            toggle.type = "text";
                            toggle1.type = "text";
                        } else {
                            toggle.type = "password";
                            toggle1.type = "password";
                        } // toggles visibility of both input fields simultaneously
                    }
                    function replaceText() {
                        var buttonText = document.getElementById("contact-support");
                        if (buttonText.innerText === "Contact Support") {
                            buttonText.innerText = "ContactSupport@fakeSupportMail.com";
                            //the contact support button displays as "Contact Support", this function will replace this text
                            //with a contact email instead
                        }
                        clearTimeout(timeoutID);
                        setTimeout(function () {
                            replaceTextBack();
                        }, 5000);
                        //this function will undo the replaceText() by calling replaceTextBack() after 5 seconds.
                    }
                    function replaceTextBack() {
                        var buttonText = document.getElementById("contact-support");
                        buttonText.innerText = "Contact Support";
                        // this simply does the opposite of replaceText()
                    }
                    function copyToCB() {
                        var textToCopy = document.getElementById('SupportEmail').innerText;
                        navigator.clipboard.writeText(textToCopy);
                        alert("Support email copied to clipboard.");
                        // upon the user clicking the button, it'll automatically copy the email to their clipboard and give them a
                        //pop up notification 
                    }
                </script>
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