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
</head>

<body>
    <div class="container">
        <p>Your Preferences:</p>
        <div class='login-text'>
        </div>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="GET">
            <div class="variables">
                <div class="activity-box">
                    <div>
                        <input type="radio" id="veg1" name="active" value="vegetarian" required>
                        <label for="veg1"><span class="labelText">Vegetarian: </span></br>- Excluding meat, fish and
                            poultry</label><br />
                    </div>
                    <div>
                        <input type="radio" id="veg2" name="active" value="vegan">
                        <label for="veg2"><span class="labelText">Vegan: </span></br>- Excludes all animal
                            products</label><br />
                    </div>
                    <div>
                        <input type="radio" id="pesc" name="active" value="pescetarian">
                        <label for="pesc"><span class="labelText">Pescetarian: </span></br>- Excludes all other meats,
                            aside from fish and seafood</label><br />
                    </div>
                    <div>
                        <input type="radio" id="glut" name="active" value="gluten free">
                        <label for="glut"><span class="labelText">Gluten-Free: </span></br>- Avoiding Gluten based
                            products</label><br />
                    </div>
                    <div>
                        <input type="radio" id="pale" name="active" value="paleo" required>
                        <label for="pale"><span class="labelText">Paleo: </span></br>- Lean meats, fruit, vegetables,
                            nuts, seeds, fish, and excluding processed food, grains and dairy</label><br />
                    </div>
                    <div>
                        <input type="radio" id="lact" name="active" value="lacto vegetarian" required>
                        <label for="lact"><span class="labelText">Lacto-Vegetarian: </span></br>- All ingredients must
                            be vegetarian and none of the ingredients can be or contain egg.</label><br />
                    </div>
                    <div>
                        <input type="radio" id="orga" name="active" value="ketogenic" required>
                        <label for="orga"><span class="labelText">Ketogenic: </span></br>- The keto diet is based more
                            on the ratio of fat, protein, and carbs</label><br />
                    </div>
                    <div>
                        <input type="radio" id="nopr" name="active" value="No Preference" required>
                        <label for="nopr"><span class="labelText">No Preference: </span></br>- Like wide varieties,
                            minimal to no dietary restrictions</label><br />
                    </div>
                </div><br />
                <input type="submit" value="Confirm" name="s">
                <a href="http://localhost/php/ActivityLevel.php">
                    <input type="button" value="Back" /> </a>
                <a href="http://localhost/php/userAllergies.php">
                    <input type="button" value="TEST BUTTON" /> </a>
            </div>
        </form>
        <?php
        if (isset($_SESSION["userid"]) || isset($_SESSION["username"])) {
            $userid = $_SESSION["userid"];
            $username = $_SESSION["username"];
        } else {
            die("no userid or username in session");
        }
        if (!isset($_GET["s"])) {
            die("");
        }
        try {
            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
        } catch (mysqli_sql_exception $e) {
            die("Something went wrong: " . $e);
        }
        $Preference = $_GET["active"];
        if (empty($Preference)) {
            die("<div class='error-message'>Must select an option.</div></div>");
        } else {
            try {
                $connUserID = $connection->prepare("select preferenceID from preference where PreferenceName = ?");
                //since a preference name, not ID was inputted, this first selects the ID and binds it to a variable
                if (!$connUserID) {
                    die(mysqli_error());
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
                    die("Something went wrong inserting values.");
                }
                $result->close();
                $_SESSION["Preference"] = $Preference;
                header("Location: http://localhost/php/userAllergies.php");
                exit();
            } catch (mysqli_sql_exception $e) {
                die("$e");
            }
        }
        ?>
    </div>
</body>

</html>
<?php
ob_end_flush();
?>