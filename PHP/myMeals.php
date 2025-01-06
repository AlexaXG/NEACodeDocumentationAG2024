<?php
session_start();
ob_start();
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals - Meals</title>
    <link rel="stylesheet" href="/css/pageStyling.css"> <!-- referencing the updated Styling page-->
    <script src="toast.js"></script>
</head>
<style>
    input,
    button {
        padding: 10px;
        margin: 5px;
        font-size: 16px;
    }
</style>

<body>
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="page-banner">
            <div class="item-container">
                <nav class="button-container">
                    <img class="logoImg" src="/Other Files/GymmieMeals.png">
                    <ul>
                        <li><a class="button" href="http://localhost/php/Homepage.php"></li>
                        <button type="button">My Tracking</button></a>
                        <li><a class="button" href="http://localhost/php/MyMeals.php"></li>
                        <button type="button">My Meals</button></a>
                        <li><a class="button" href="http://localhost/php/MyGoals.php"></li>
                        <button type="button">My Goals</button></a>
                        <li><a class="button" href="http://localhost/php/MySchedule.php"></li>
                        <button type="button">My Schedule</button></a>
                        <li><a class="button" href="http://localhost/php/LandingPage.php"></li>
                        <button type="button">Test Button </button></a>
                    </ul>
                    <a href="http://localhost/php/MySettings.php">
                        <img class="logoImg" src="/Other Files/settingCog.png"></a>
                </nav>
            </div>
        </div>
        <div class="main-content">
            <div class="welcome-container">
                <?php
                if (!isset($_SESSION["userid"]) || !isset($_SESSION["username"])) {
                    header("Location: http://localhost/php/Login.php");
                    exit();
                } else {
                    $userid = $_SESSION["userid"];
                    $username = $_SESSION["username"];

                    if (!isset($_SESSION["Preference"])) {
                        try {
                            $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
                        } catch (mysqli_sql_exception $e) {
                            die("<div class='error-message'>Something went wrong: </div>" . $e);
                        }
                        $prefFetch = $connection->prepare("SELECT Preference.PreferenceName FROM userpreferences JOIN Preference ON 
                            userpreferences.PreferenceID = Preference.PreferenceID WHERE userpreferences.UserID = ?;");
                        $prefFetch->bind_param("i", $userid);
                        if (!$prefFetch->execute()) {
                            die("USVALerr " . $connection->error);
                        } else {
                            $prefFetch->bind_result($preference);
                            $prefFetch->fetch();
                            $_SESSION["Preference"] = $preference;
                        }
                    } else {
                        $preference = $_SESSION["Preference"];
                        // echo $preference; 
                    }

                    $allergies = array();
                    try {
                        $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
                    } catch (mysqli_sql_exception $e) {
                        die("<div class='error-message'>Something went wrong: </div>" . $e);
                    }
                    $allergiesFetch = $connection->prepare("SELECT AllergyName FROM userAllergies JOIN allergy ON 
                        userAllergies.allergyID = allergy.allergyID WHERE userAllergies.UserID = ?;");
                    $allergiesFetch->bind_param("i", $userid);
                    if (!$allergiesFetch->execute()) {
                        die("USVALerr " . $connection->error);
                    } else {
                        $allergiesFetch->bind_result($allergyName);
                        while ($allergiesFetch->fetch()) {
                            $allergies[] = $allergyName;
                        }
                        $allergiesFetch->close();
                    }
                }
                ?>
                <span class="welcome-text">Your <span id="webName">Meals:</span>
                </span>
            </div>
            <div class="display-content">
                <div class="searchContainer">
                    <div class="outputbox" id='searchBox'>
                        <div class="title-text">Recipe Search:</div>

                        <input type="text" class="textbox" id="userInput" placeholder="Enter a keyword"
                            autocomplete="off" />
                        <button type="button" class="searchButton" id="search"
                            onclick="searchRecipesByName()">Search</button>
                        <div id="errorMessage"></div>
                    </div>
                    <div class="outputbox" id='recommendedSearchBox'>
                        <div class="title-text">Recommended Recipes:</div>
                        <div class="output-text">This section generates recommended meals tailored to you:</div>
                        <button type="button" class="recommendedMeals" id="recommendedSearch"
                        onclick="submitMealChoiceAndSearchRecipes()">Feeling lazy?</button>
    
                        <div class="title-text" id="mealSelect">Select your meal:</div>
                        <span class="output-text">Select the type of meal you want to search for:</span>
                        <select id="mealChoice" name="BLDC" class="genderIn">
                            <option value="Any">Any</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Side Dish">Side Dish</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Salad">Salad</option>
                            <option value="Bread">Bread</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Soup">Soup</option>
                            <option value="Beverage">Beverage</option>
                            <option value="Sauce">Sauce</option>
                            <option value="Marinade">Marinade</option>
                            <option value="Fingerfood">Fingerfood</option>
                            <option value="Snack">Snack</option>
                            <option value="Drink">Drink</option>
                        </select>
                        <div id="errorMessage"></div>
                    </div>
                    <script>
                        var userPreference =
                            <?php
                            echo json_encode($preference);
                            ?>;
                        console.log("User Preference from PHP:", userPreference);

                        var userAllergies =
                            <?php
                            echo json_encode($allergies);
                            ?>;
                        console.log("User allergies from PHP:", userAllergies);
 
                        var userID =
                            <?php
                            echo json_encode($userid);
                            ?>;
                        console.log("UserID from PHP:", userID);

                    </script>
                </div>
                <div class="outputbox" id='searchResults'>
                    <div class="title-text">Recipe Results:</div>

                    <div class="mealDisplay" id="displayResultsHere">
                        <div class="shadowing"></div>
                    </div>
                </div>
                <div class="outputbox" id="queriesBox">
                    <div class="title-text">Specific Search Queries:</div>
                    <?php
                    echo $preference;
                    ?>
                </div>
            </div>

        </div>
        <div id="toast"></div>
    </form>
    <script src="/JS/mealAPISearch.js"></script>
</body>

</html>
<?php
if (isset($_SESSION['toast_message'])) { ?>
    <script>
        document.getElementById("toast").innerHTML = '<div class="toast"><?php echo $_SESSION['toast_message']; ?></div>';
    </script>
    <?php unset($_SESSION['toast_message']); 
}
ob_end_flush();
?>