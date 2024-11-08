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
    <link rel="stylesheet" href="pageStyling.css">
</head>

<body>
    <div class="page-banner">
        <div class="item-container">
            <div class="button-container">
                <a href=""><img class="logoImg" src="GymmieMeals.png"></a>
                <a class="button" href="http://localhost/Login.php">
                <button type="button">Have an account?</button></a>
                    <a class="button" onclick="copyToCB()" onmouseover="replaceText()" onmouseout="replaceTextBack()">
                        <button type="button" value="Contact Support" id="contact-support">
                            Contact Support</button>
                        <div id="SupportEmail" style="display:none;">fakeContactSupport@fakeSupportMail.com</div>

                    </a>
            </div>
            <script>
                function toggleVis1() {
                    const svgData = document.POSTElementById("toggleEye");
                    svgData.src = svgData.src.includes("closedEye.svg") ? "openEye.svg" : "closedEye.svg";

                    var toggle = document.POSTElementById("password1");
                    var toggle1 = document.POSTElementById("password2");
                    if (toggle.type === "password") {
                        toggle.type = "text";
                        toggle1.type = "text";
                    } else {
                        toggle.type = "password";
                        toggle1.type = "password";
                    } // toggles visibility of both input fields simultaneously
                }
                function replaceText() {
                    var buttonText = document.POSTElementById("contact-support");
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
                    var buttonText = document.POSTElementById("contact-support");
                    buttonText.innerText = "Contact Support";
                    // this simply does the opposite of replaceText()
                }
                function copyToCB() {
                    var textToCopy = document.POSTElementById('SupportEmail').innerText;
                    navigator.clipboard.writeText(textToCopy);
                    alert("Support email copied to clipboard.");
                    // upon the user clicking the button, it'll automatically copy the email to their clipboard and give them a
                    //pop up notification 
                }
            </script>
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
                        <a href="http://localhost/signupAttributes.php">
                            <button type="button">Back</button>
                        </a>
                        </div>
                    </div>
                </div>
        </form>
        <?php
        if (isset($_SESSION["userid"]) || isset($_SESSION["username"])) {
            $userid = $_SESSION["userid"];
            $username = $_SESSION["username"];
        } else {
            header("location: http://localhost/LandingPage.php");
        }
        if (!isset($_POST["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            die( "<div id='error-container' class='error-message'>Something went wrong . $e </div>");
        }
        $ActivityLevel = $_POST["active"];
            try {
                $userid = $_SESSION["userid"];
                $result = $connection->prepare("UPDATE user SET activity=? WHERE user.userID = ?");
                $result->bind_param("ss", $ActivityLevel, $userid);
                //adding the users activity to their relevant field in the 'user' table
                if (!$result->execute()) {
                    die( "<div id='error-container' class='error-message'>Something went wrong inserting values.</div>");
                } else {
                    $_SESSION["ActivityLevel"] = $ActivityLevel;
                    header("Location: http://localhost/userPreferences.php");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                die("$e");
            }
        ?>
    </div>
</body>

</html>

<?php
ob_end_flush();
?>