<?php
session_start(); //starts a session 
ob_start(); //output buffer, holds any data temporarily before sendingto the browser 
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Forgot Password</title>
    <link rel="stylesheet" href="pageStyling.css">
</head>
<style>
    form input[type="text"],
    input[type="button"],
    input[type="submit"],
    input[type="password"] {
        border-radius: 0px;
    }
</style>

<body>
    <div class="page-banner">
        <div class="item-container">
            <div class="button-container">
                <a href=""><img class="logoImg" src="GymmieMeals.png"></a>
                <a class="button" href="http://localhost/signup.php">
                    <button type="button">Register here!</button></a>
                <a class="button" href="http://localhost/Login.php">
                    <button type="button">Have an account?</button></a>
                <a class="button" onclick="copyToCB()" onmouseover="replaceText()" onmouseout="replaceTextBack()">
                    <button type="button" value="Contact Support" id="contact-support">
                        Contact Support</button>
                    <div id="SupportEmail" style="display:none;">fakeContactSupport@fakeSupportMail.com</div>

                </a>
            </div>
        </div>
    </div>
    <div class="container">
        </div>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="main-content">
            <div class="welcome-container">
                <span class="welcome-text"><span id="webName">Forgot Password:</span></span>
            </div>
            <div class="inputbox" id="registration">
                <label for="username"></label> <br />
                <input type="text" name="username" placeholder="Username" id="username" required autocomplete="off" />
                <br />
                <label for="password"> </label> <br />
                <input type="password" name="password" placeholder="New Password" id="password1" required
                    autocomplete="off">
                <!-- input for the first password field -->
                <br /><br />
                <label for="password1"></label>
                <input type="password" name="password2" id="password2" placeholder="Confirm password" required
                    autocomplete="off" />
                <!-- input for the second password field-->
                <a class="checkbox" onclick="toggleVis1()">
                    <img class="favImg" id="toggleEye" src="closedEye.svg" width="28px" draggable="false">
                    <label>Show Password</label>
                </a></br>
                <script>
                    function toggleVis1() {
                        const svgData = document.getElementById("toggleEye");
                        svgData.src = svgData.src.includes("closedEye.svg") ? "openEye.svg" : "closedEye.svg";

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
                <button type="submit" name="s">Confirm</button>
                <!-- button to submit the form and redirect to header-->
            </div>
        </div>
        </form>

        <?php
        if (!isset($_POST["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            die("Something went wrong: " . $e);
        }

        $username = $_POST["username"];
        strtolower($username);
        //standardisation of usernames by converting them to lowercase
        $password = $_POST["password"];
        $password2 = $_POST["password2"];

        if (empty($username) || empty($password) || empty($password2)) {
            die("<div class='error-message'> All fields are required. </div>");
        } else if ($password !== $password2) {
            die("<div class='error-message'> Passwords do not match.</div>");
        } else {
            function passwordCheck($password)
        {
            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
            return preg_match($pattern, $password);
        }   
            try {
                $getId = $connection->prepare("SELECT userid from user where username = ?");
                $getId->bind_param("s", $username);
                $getId->execute();
                $getId->bind_result($userid);
                $getId->fetch();
                $getId->close();

                $getSalt = $connection->prepare("SELECT salt from user where userid = ?");
                $getSalt->bind_param("i", $userid);
                $getSalt->execute();
                $getSalt->bind_result($salt);
                $getSalt->fetch();
                $getSalt->close();

                $saltedPassword = $password . $salt;
                $saltedPassword = hash("sha256", $password);

                $result = $connection->prepare("UPDATE user SET passwords =  (?) WHERE userid = (?)");
                $result->bind_param("si", $saltedPassword, $userid);
                //in essence does the same function as the sign-up page, but instead of inserting values it updates at the username.
                if (!$result->execute()) {
                    die(mysqli_error());
                } else {
                    header("Location: http://localhost/Login.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                die("Something went wrong." . $e);
            }
        }
        ?>
    </div>
</body>

</html>
<?php
ob_end_flush(); //stops output buffering
?>