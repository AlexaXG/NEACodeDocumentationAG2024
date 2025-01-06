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
    <link rel="stylesheet" href="/css/pageStyling.css">
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
                <a href=""><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                <a class="button" href="http://localhost/PHP/signup.php">
                    <button type="button">Register here!</button></a>
                <a class="button" href="http://localhost/PHP/Login.php">
                    <button type="button">Have an account?</button></a>
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
                <br>
                <br>
                
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
                <label for="password1"></label>
                <input type="password" name="password2" id="password2" placeholder="Confirm password" required
                    autocomplete="off" />
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
                </script>
                <button type="submit" name="s">Confirm</button>
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
            putenv("JAVA_HOME=C:/Program Files/Java/jdk-19");
            putenv("PATH=C:/Program Files/Java/jdk-19/bin;" . getenv("PATH"));
            $javaJDKPath = getenv('JAVA_HOME') . "/bin/java";
            $javaCompiledPath = "C:/Users/algub/OneDrive/Documents/NetBeansProjects/secureSaltingAlgorithm/src";
            $saltLength = 18;
            $command = "\"$javaJDKPath\" -cp \"$javaCompiledPath\" securesaltingalgorithm.SecureSalt $saltLength";
            $salt = shell_exec($command);
            $salt = substr($salt, 0, 18);
            $saltedPassword = $password . $salt;
            $newPassword = hash("sha256", $saltedPassword);

            try {
                $result = $connection->prepare("UPDATE user SET passwords = ?, salt = ? WHERE username = ?");
                $result->bind_param("sss", $newPassword, $salt, $username);
                //in essence does the same function as the sign-up page, but instead of inserting values it updates at the username.
                if (!$result->execute()) {
                    die(mysqli_error());
                } else {
                    header("Location: http://localhost/PHP/Login.php");
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