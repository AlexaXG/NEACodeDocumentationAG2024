<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Log in</title>
    <link rel="stylesheet" href="/css/pageStyling.css">
</head>

<body>
    <div class="page-banner">
        <div class="item-container">
            <div class="button-container">
                <a href=""><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                <a class="button" href="http://localhost/php/signup.php">
                    <button type="button">Register here!</button></a>
                <a href="http://localhost/php/ForgotPass.php">
                    <button type="button">Forgot Password?</button>
                    <a class="button" onclick="copyToCB()" onmouseover="replaceText()" onmouseout="replaceTextBack()">
                        <button type="button" value="Contact Support" id="contact-support">
                            Contact Support</button>
                        <div id="SupportEmail" style="display:none;">fakeContactSupport@fakeSupportMail.com</div>

                    </a>
            </div>
        </div>
    </div>
    <div class="container">
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="main-content">
                <div class="welcome-container">
                    <span class="welcome-text"><span id="webName">Login:</span></span>
                </div>
                <div class="inputbox" id="login">
                    <label for="username"></label> <br />
                    <input type="text" name="username" placeholder="Username" id="username" /> <br />
                    <label for="password"> </label> <br />
                    <input type="password" name="password" placeholder="Password" id="password1" /> <br />
                    <label for="password1"></label>
                    <a class="checkbox" onclick="toggleVis1()">
                        <img class="favImg" id="toggleEye" src="/Other Files/closedEye.svg" width="28px" draggable="false">
                        <label>Show Password</label>
                    </a></br>
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
                    <button type="submit" name="s">Confirm</button>
                </div>
        </form>
        <?php
        if (!isset($_POST["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            die("<div id='error-container' class='error-message'>Something went wrong: . $e</div>");
        }
        $username = $_POST["username"];
        $password = $_POST["password"];
        //collects data from POST form
        try {
            if (empty($username) || empty($password)) {
                die("<div id='error-container' class='error-message'>All fields are required.</div>");
            } else {
                $userCheck = $connection->prepare("SELECT passwords, userid, weight, height, gender, age, activity From user Where user.username = ?");
                //prepares a select statement to bind all user information to variables
                $userCheck->bind_param("s", $username);
                if (!$userCheck->execute()) {
                    die("<div id='error-container' class='error-message'>USVal error: . $connection->error</div>");
                } else {
                    $userCheck->store_result();
                    if ($userCheck->num_rows == 0) {
                        $userCheck->close();
                        die("<div id='error-container' class='error-message'>Username doesn't exist.</div>");
                    } else {
                        $userCheck->bind_result($fetchedPass, $userid, $weight, $height, $gender, $age, $activity);
                        //binds results to written variables
                        while ($userCheck->fetch()) {
                            //fetch recieves 1 row at a time from the results so it iterates until all data has been collected
                            $_SESSION["fetchedPass"] = $fetchedPass;
                            $_SESSION["weight"] = $weight;
                            $_SESSION["height"] = $height;
                            $_SESSION["gender"] = $gender;
                            $_SESSION["age"] = $age;
                            $_SESSION["ActivityLevel"] = $activity;
                            $_SESSION["username"] = $username;
                            $_SESSION["userid"] = $userid;
                            //binding values for all data, including the users already hashed password
                        }
                        $userCheck->close();
                        //closing prepared statement connection
                    }
                }
                $hashPW = hash("sha256", $password);
                //hashes the new entered password at login
                if ($hashPW === $fetchedPass) {
                    $preferenceIDCheck = $connection->prepare("SELECT preferenceID From userpreferences Where userid = ?");
                    $preferenceIDCheck->bind_param("i", $userid);
                    if (!$preferenceIDCheck->execute()) {
                        die("<div id='error-container' class='error-message'>PIDerr: . $connection->error</div>");
                    } else {
                        $preferenceIDCheck->bind_result($preferenceID);
                        //prepares a statement to also fetch the preference associated with the user
                        $preferenceIDCheck->fetch();
                        $preferenceIDCheck->close();

                        $preferenceCheck = $connection->prepare("SELECT preferenceName From preference Where preferenceid = ?");
                        $preferenceCheck->bind_param("i", $preferenceID);
                    }
                    if (!$preferenceCheck->execute()) {
                        die($connection->error);
                    } else {
                        $preferenceCheck->bind_result($preference);
                        $preferenceCheck->fetch();
                        $_SESSION["Preference"] = $preference;
                        //selects the preference name after fetching the preferenceID using userID and sets it as as a session variable
                        $preferenceCheck->close();
                    }
                    unset($_SESSION["fetchedPass"]);
                    header("Location: http://localhost/php/Homepage.php");
                    //automatic redirect and program termination
                    exit();
                } else {
                    die("<div id='error-container' class='error-message'>Password is incorrect.</div>");
                }
            }
        } catch (mysqli_sql_exception $e) {
            die("<div id='error-container' class='error-message'>Something went wrong: . $e</div>");
        }
        ?>
    </div>
</body>

</html>
<?php
ob_end_flush();
?>