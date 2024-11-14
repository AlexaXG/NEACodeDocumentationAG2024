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
</head>
<body>
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
        <div class="page-banner">
            <div class="item-container">
                <a href="#"><img class="logoImg" src="/Other Files/GymmieMeals.png"></a>
                <div class="button-container">
                    <a class="button" href="#" onclick="handlePageLoad('/php/AccountContent.php', this)">
                        <!-- each button calling teh handlePageLoad function loads a separate php page within the html -->
                        <button type="button">My Account</button></a>
                    <a class="button" href="#" onclick="handlePageLoad('/php/DetailsContent.php', this)">
                        <button type="button">My Details</button></a>
                    <a class="button" href="#" onclick="handlePageLoad('/php/PreferencesContent.php', this)">
                        <button type="button">My Preferences</button></a>
                    <a class="button" href="#" onclick="handlePageLoad('/php/AllergiesContent.php', this)">
                        <button type="button">My Allergies</button></a>
                    <a class="button" href="#" onclick="handlePageLoad('/php/ActivityContent.php', this)">
                        <button type="button">My Activity</button></a>
                    <a class="button" href="http://localhost/php/Homepage.php">
                        <button type="button">Home</button></a>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="welcome-container">
                
            </div>
            <script>

                function handlePageLoad(pageName, element) {
                    highlightIt(element);
                    loadThisPage(pageName);
                }
                function loadThisPage(PageName) {
                    var AJAXConn = new XMLHttpRequest();
                    //new instance of XMLHttpRequest 
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

                function loadUserData() {
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
                function highlightIt(element) {
                    var links = document.querySelectorAll('a');
                    links.forEach(link => {
                        link.classList.remove('highlighted');
                    //removes "highlighted" css class from all <a> references
                    });
                    element.classList.add('highlighted');
                    //then it adds the highlighted class to the button "this" that was clicked
                }
                window.onload = function () {
                    var defaultLink = document.querySelector('a');
                    //when the page is loaded, it will call the php page below as the default. 
                    handlePageLoad('AccountContent.php', defaultLink);
                };

            </script>
            <div class="display-content">
                <div class='outputbox' id='loadPageHere'>
                </div>
            </div>
            <script>
                let alreadyHighlighted = null;
                function highlightIt(buttonClicked) {
                    if (alreadyHighlighted && alreadyHighlighted != buttonClicked) {
                        //if alreadyHighlighted has a value and the value isnt the current clicked button
                        alreadyHighlighted.classList.remove("highlight-box");
                        //itll remove the highlight
                    }
                    if (alreadyHighlighted != buttonClicked) {
                        buttonClicked.classList.add("highlight-box");
                        //sets the current clicked button as alreadyHighlighted and adds a highlight
                        alreadyHighlighted = buttonClicked;

                    } else {
                        alreadyHighlighted.classList.remove("highlight-box");
                        //resets highlights
                        alreadyHighlighted = null;
                    }
                }
            </script>
        </div>
    </form>
</body>
</html>
<?php
ob_end_flush();
?>