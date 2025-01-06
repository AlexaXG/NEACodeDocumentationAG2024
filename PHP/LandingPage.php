<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Landing Page</title> <!-- titles the browser tab -->
    <link rel="stylesheet" href="/css/pageStyling.css"> <!-- inheriting classes from the External CSS code -->
</head>


<body>
    <div class="page-banner">
        <div class="item-container">
            <div class="button-container">
                <a href=""><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                <a class="button" href="http://localhost/php/Signup.php">
                    <button type="button">Signup</button></a>
                <a class="button" href="http://localhost/php/Login.php">
                    <button type="button">Login</button></a>
                <a class="button" onclick="copyToCB()" onmouseover="replaceText()" onmouseout="replaceTextBack()">
                    <button type="button" value="Contact Support" id="contact-support">
                        Contact Support</button>
                    <div id="SupportEmail" style="display:none;">fakeContactSupport@fakeSupportMail.com</div>

                </a>
            </div>
        </div>
    </div>
 
    <script>
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

    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="main-content">
            <div class="welcome-container">
                <span class="welcome-text">Welcome to <span id="webName">GymmieMeals</span></span>
            </div>
            <div class="boxContainer">
                <div class='outputbox' id="welcomebox">
                </div>
                <div class='outputbox' id="welcomebox">
                </div>
                <div class='outputbox' id="welcomebox">
                </div>
            </div>
        </div>
    </form>
</body>

</html>