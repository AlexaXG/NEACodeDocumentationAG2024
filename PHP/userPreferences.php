<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Your Preferences</title>
    <link rel="stylesheet" href="/css/pageStyling.css">
    <script src="toast.js"></script>
</head>

<body>
    <div class="container">
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
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="GET">
            <div class="main-content">
                <div class="welcome-container">
                    <span class="welcome-text"><span id="webName">Your Preferences:</span></span>
                </div>
                <div class="inputbox" id="preferenceSelect">
                    <div class="radioButtons">
                        <div>
                            <input type="radio" id="veg1" name="active" value="vegetarian" required>
                            <label for="veg1"><span class="labelText">Vegetarian: </span><br>- Excluding meat, fish and
                                poultry.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="veg2" name="active" value="vegan">
                            <label for="veg2"><span class="labelText">Vegan: </span><br>- Excludes all animal
                                products</label><br />
                        </div>
                        <div>
                            <input type="radio" id="pesc" name="active" value="pescetarian">
                            <label for="pesc"><span class="labelText">Pescetarian: </span><br>- Excludes all other
                                meats, aside from fish and seafood</label><br />
                        </div>
                        <div>
                            <input type="radio" id="glut" name="active" value="gluten free">
                            <label for="glut"><span class="labelText">Gluten-Free: </span><br>- Avoiding Gluten based
                                products</label><br />
                        </div>
                        <div>
                            <input type="radio" id="pale" name="active" value="paleo" required>
                            <label for="pale"><span class="labelText">Paleo: </span><br>- Lean meats, fruit, vegetables,
                                nuts, seeds, fish, and excluding processed food, grains and dairy</label><br />
                        </div>
                        <div>
                            <input type="radio" id="lact" name="active" value="lacto vegetarian" required>
                            <label for="lact"><span class="labelText">Lacto-Vegetarian: </span><br>- All ingredients
                                must be vegetarian and none of the ingredients can be or contain egg.</label><br />
                        </div>
                        <div>
                            <input type="radio" id="orga" name="active" value="ketogenic" required>
                            <label for="orga"><span class="labelText">Ketogenic: </span><br>- The keto diet is based
                                more on the ratio of fat, protein, and carbs</label><br />
                        </div>
                        <div>
                            <input type="radio" id="nopr" name="active" value="No Preference" required>
                            <label for="nopr"><span class="labelText">No Preference: </span><br>- Like wide varieties,
                                minimal to no dietary restrictions</label><br />
                        </div>
                        <br />
                        <div class="ButtonCont">
                            <button type="submit" name="s">Confirm</button>
                            <a href="http://localhost/php/ActivityLevel.php">
                                <button type="button">Back</button>
                            </a>
                        </div>
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
        $Preference = $_GET["active"];
        if (empty($Preference)) {
            $_SESSION['toast_message'] = "Must select an option";
        } else {
            try {
                $connUserID = $connection->prepare("select preferenceID from preference where PreferenceName = ?");
                //since a preference name, not ID was inputted, this first selects the ID and binds it to a variable
                if (!$connUserID) {
                    $_SESSION['toast_message'] = "connUserID Failed to execute" . $connUserID->error;
                } else {
                    $connUserID->bind_param("s", $Preference);
                    $connUserID->execute();
                    $connUserID->bind_result($PreferenceID);
                    $connUserID->fetch();
                    $connUserID->free_result();
                    $connUserID->close();
                }
                $result = $connection->prepare("INSERT INTO UserPreferences (userID, PreferenceID) VALUES (?, ?)");
                $result->bind_param("ii", $userid, $PreferenceID);
                //inserting userID and preferenceID into userPreferences which links each user to a preference
                if (!$result->execute()) {
                    $_SESSION['toast_message'] = "Result couldn't insert values" . $result->error;
                }
                $result->close();
                $_SESSION["Preference"] = $Preference;
                header("Location: http://localhost/php/userAllergies.php");
                exit();
            } catch (mysqli_sql_exception $e) {
                $_SESSION['toast_message'] = "MySQLi failed to execute correctly" . $e;
            }
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