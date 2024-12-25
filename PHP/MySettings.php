<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Settings</title>
    <link rel="stylesheet" href="/css/pageStyling.css">
    <script src="toast.js"></script>
</head>
<style>
    .logoImg {
        height: 50px;
    }

    #loadPageHere {
        border-radius: 3px;
        box-shadow: 0 6px 20px rgba(110, 54, 120, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        height: 400px;
        width: 500px;
        padding: 20px;
    }

    #logout {
        background-color: red;
    }

    #logout:hover {
        background-color: #ffff;
        color: red;
    }
</style>

<body>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
        <div class="page-banner">
            <div class="item-container">
                <div class="button-container">
                    <a href="#"><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                    <a class="button" href="#" onclick="processAndLoadThisPage('/php/AccountContent.php')">
                        <button type="button">My Account</button></a>
                    <a class="button" href="#" onclick="processAndLoadThisPage('/php/DetailsContent.php')">
                        <button type="button">My Details</button></a>
                    <a class="button" href="#" onclick="processAndLoadThisPage('/php/PreferencesContent.php')">
                        <button type="button">My Preferences</button></a>
                    <a class="button" href="#" onclick="processAndLoadThisPage('/php/AllergiesContent.php')">
                        <button type="button">My Allergies</button></a>
                    <a class="button" href="#" onclick="processAndLoadThisPage('/php/ActivityContent.php')">
                        <button type="button">My Activity</button></a>
                    <a href="http://localhost/php/homepage.php">
                        <img class="logoImg" src="/Other Files/home.svg"></a>
                    <a href="http://localhost/php/signup.php">
                        <button type="button" id="logout">Log out</button>
                        <?php
                        $_SESSION["logout"] = true;
                        ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="welcome-container">
            </div>
            <?php
                if (!isset($_SESSION["userid"]) || !isset($_SESSION["username"])) {
                    header("location: http://localhost/php/login.php");
                    exit();
                } else {
                    if (isset($_SESSION["Signup_in_progress"])) {
                        header("location: http://localhost/php/SignupAttributes.php");
                    }
                    $userid = $_SESSION["userid"];
                    $username = $_SESSION["username"];
                }
            ?>
            <script>
                function processAndLoadThisPage(PageName) {
                    var AJAXConn = new XMLHttpRequest();
                    AJAXConn.open('GET', PageName, true);
                    //processes http method, the page that is being called, and boolean whether it is asynchronous (script still runs whilst data is being fetched)
                    AJAXConn.onload = function () {
                        if (AJAXConn.status >= 200 && AJAXConn.status < 300) {
                            //these codes are the status codes for http 200-299 which check if it was a success
                            document.getElementById('loadPageHere').innerHTML = AJAXConn.responseText;
                            //in the html line with id="loadpagehere", the response data is loaded
                        } else {
                            console.error(AJAXConn.status);
                            //logs the status value into console if there is an error
                        }
                    };
                    AJAXConn.onerror = function () {
                        console.error();
                    };
                    AJAXConn.send();
                }

                function loadTheUserData() {
                    fetch('getUserData.php')
                        //http request to that php page
                        .then(response => response.json())
                        //formats the response as json
                        .then(data => {
                            document.getElementById("username").textContent = data.username;
                            //in the html, the line with id="username" is updated to the username fetched from the php page
                            document.getElementById("userid").textContent = data.userid;
                            //similar fashion for the userid
                        })
                        .catch(error => {
                            console.error();
                        });
                }
                window.onload = function () {
                    processAndLoadThisPage('AccountContent.php');
                };

            </script>
            <div class="display-content">
                <div class='pageContent' id='loadPageHere'>
                </div>
            </div>
        </div>
        <div id="toast"></div>
    </form>
</body>

</html>
<?php if (isset($_SESSION['toast_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.innerHTML = <?php echo json_encode($_SESSION['toast_message']); ?>;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 5000);
            }
        });
    </script>
    <?php unset($_SESSION['toast_message']); ?>
<?php endif;
ob_end_flush();
?>