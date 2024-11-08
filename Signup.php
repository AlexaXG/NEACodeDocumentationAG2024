<?php
session_start(); //starts a session 
ob_start(); //output buffer, holds any data temporarily before sendingto the browser 
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Sign Up</title>
    <link rel="stylesheet" href="pageStyling.css">
</head>
<style>
    .checklist {
        margin-top: 5px;
        font-size: 12px;
        list-style-type: none;
        padding-left: 0;
    }
    #usernameCheck{
        margin-right: 50px;
    }
    .checklistitem {
        color: #ff0000;
    }
    #username-length2 {
        color: #008000;
        font-weight: bold;
    }

    .checklistitem.valid {
        color: #008000;
        font-weight: bold;
    }
    .checklistitem.notValid {
        color: #ff0000;
    }
</style>

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
        </div>
    </div>
    <div class="container">
    </div>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="main-content">
            <div class="welcome-container">
                <span class="welcome-text"><span id="webName">Registration:</span></span>
            </div>
            <div class="inputbox" id="registration">
                <label for="username"></label> <br />
                <input type="text" name="username" placeholder="Username" id="username" required autocomplete="off" />
                <ul class="checklist" id="usernameCheck">
                    <li class="checklistitem" id="username-length"> - At least 4 characters</li>
                    <li class="checklistitem" id="username-length2"> - No more than 16 characters</li>
                    <li class="checklistitem" id="username-letters"> - Letters, numbers and underscores</li>
                </ul>
                <label for="password"> </label>
                <input type="password" name="password" placeholder="Password" id="password1" required
                    autocomplete="off">
                <ul class="checklist" id="passwordCheck">
                    <li class="checklistitem" id="password-length"> - At least 8 characters</li>
                    <li class="checklistitem" id="password-upper"> - Contains an uppercase letter</li>
                    <li class="checklistitem" id="password-lower"> - Contains a lowercase letter</li>
                    <li class="checklistitem" id="password-number"> - Contains a number</li>
                    <li class="checklistitem" id="password-special"> - Contains a special character (e.g., !@#$%^&*)</li>
                </ul>
                <script>
                    // Username checklist validation
                    const usernameInput = document.getElementById('username');
                    const usernameLength = document.getElementById('username-length');
                    const usernameLength2 = document.getElementById('username-length2');
                    const usernameLetters = document.getElementById('username-letters');

                    usernameInput.addEventListener('input', function () {
                        if (usernameInput.value.length >= 4) {
                            usernameLength.classList.add('valid');
                        } else {
                            usernameLength.classList.remove('valid');
                        }
                        if (usernameInput.value.length > 16) {
                            document.getElementById("username-length2").style.color = '#ff0000';
                            document.getElementById("username-length2").style.fontWeight = 'normal';
                        } 
                        if (/^[a-zA-Z0-9]+$/.test(usernameInput.value)) {
                            usernameLetters.classList.add('valid');
                        } else {
                            usernameLetters.classList.remove('valid');
                        }
                    });

                    const passwordInput = document.getElementById('password1');
                    const passwordLength = document.getElementById('password-length');
                    const passwordUppercase = document.getElementById('password-upper');
                    const passwordLowercase = document.getElementById('password-lower');
                    const passwordNumber = document.getElementById('password-number');
                    const passwordSpecial = document.getElementById('password-special');

                    passwordInput.addEventListener('input', function () {
                        if (passwordInput.value.length >= 8) {
                            passwordLength.classList.add('valid');
                        } else {
                            passwordLength.classList.remove('valid');
                        }
                        if (/[A-Z]/.test(passwordInput.value)) {
                            passwordUppercase.classList.add('valid');
                        } else {
                            passwordUppercase.classList.remove('valid');
                        }
                        if (/[a-z]/.test(passwordInput.value)) {
                            passwordLowercase.classList.add('valid');
                        } else {
                            passwordLowercase.classList.remove('valid');
                        }
                        if (/\d/.test(passwordInput.value)) {
                            passwordNumber.classList.add('valid');
                        } else {
                            passwordNumber.classList.remove('valid');
                        }
                        if (/[\W_]/.test(passwordInput.value)) {
                            passwordSpecial.classList.add('valid');
                        } else {
                            passwordSpecial.classList.remove('valid');
                        }
                    });
                </script>
                <!-- input for the first password field -->
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
    </form>
    <?php
    if (!isset($_POST["s"])) {
        die("");
        //if the form has not been submitted (i.e: submit button has not been pressed) it will stop further execution
    }
    try {
        $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        //attempts a connection to mySQL database, defining the port, username, password, and database name.
    } catch (mysqli_sql_exception $e) {
        die("<div id='error-container' class='error-message'>Something went wrong: . $e</div>");
        //error handling
    }

    $username = $_POST["username"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];
    $createdDate = date("Y-m-d H:i:s"); //current timestamp
    
    //sets local variables by calling their values from the POST method
    if (empty($username) || empty($password) || empty($password2)) {
        die("<div id='error-container' class='error-message'>All fields required.</div>");
    } else if ($password !== $password2) {
        die("<div id='error-container' class='error-message'>Passwords do not match.</div>");
    } else {
        //selection to ensure username and password fall under a criteria

        $javaJDKPath = "C:/Program Files/Java/jdk-17/bin/java";
        $javaCompiledPath = "C:/Users/algub/OneDrive/Documents/NetBeansProjects/secureSaltingAlgorithm/src";
        $saltLen = 18;
        $command = "\"$javaJDKPath\" -cp \"$javaCompiledPath\" securesaltingalgorithm.SecureSalt $saltLen";
        $salt = shell_exec($command);

        $saltedPassword = $password . $salt;
        $hashPW = hash("sha256", $saltedPassword);


        $userCheck = $connection->prepare("SELECT username From user Where user.username = ?");
        //sql statement preparation with parameterised values
        $userCheck->bind_param("s", $username);
        //binding parameters to the sql statement
        if ($userCheck->execute()) {
            $userCheck->store_result();
        } else {
            die(mysqli_error());
        }
        if ($userCheck->num_rows > 0) {
            //checks the number of rows returned from the SQL statement
            die("<div id='error-container' class='error-message>Username already exists.</div>");
        } else {
            try {
                $connResult = $connection->prepare("INSERT INTO user (userName, Passwords, CreatedDate, salt) values (?, ?, ?, ?)");
                //prepares another parameterised sql statement to insert the entered information correctly
                $connResult->bind_param("ssss", $username, $hashPW, $CreatedDate, $salt);
                if (!$connResult->execute()) {
                    die("<div id='error-container' class='error-message'>Something went wrong inserting values.</div>");
                } else {
                    $connUserID = $connection->prepare("select userID from user where username = ?");
                    //selects the automatically generated userid after insertion of username/password
                    $connUserID->bind_param("s", $username);
                    $connUserID->execute();
                    $connUserID->bind_result($userid);
                    //binds the result to a variable
                    $connUserID->fetch();

                    $_SESSION["username"] = $username;
                    $_SESSION["userid"] = $userid;
                    //stores username and userid in session for use throughout the website
                    header("Location: http://localhost/signupAttributes.php");
                    //automatic redirect and program termination
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                die("<div id='error-container' class='error-message'>Something went wrong: . $e</div>");
            }
        }
    }
    ?>
    </div>
</body>

</html>
<?php
ob_end_flush(); //stops output buffering
?>