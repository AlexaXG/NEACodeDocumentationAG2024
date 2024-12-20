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
                        <li><a class="button" href="http://localhost/PHP/Homepage.php"></li>
                        <button type="button">My Tracking</button></a>
                        <!-- simply refreshes the page as tracking is dynamically loaded onto the homepage-->
                        <li><a class="button" href="http://localhost/MyMeals.php"></li>
                        <button type="button">My Meals</button></a>
                        <li><a class="button" href="http://localhost/PHP/MyGoals.php"></li>
                        <button type="button">My Goals</button></a>
                        <li><a class="button" href="http://localhost/PHP/MySchedule.php"></li>
                        <button type="button">My Schedule</button></a>
                        <li><a class="button" href="http://localhost/PHP/LandingPage.php"></li>
                        <button type="button">Test Button </button></a>
                    </ul>
                    <a href="http://localhost/PHP/MySettings.php">
                        <img class="logoImg" src="/Other Files/settingCog.png"></a>
                </nav>
            </div>
        </div>
        <div class="main-content">
            <div class="welcome-container">
                <?php
                if (!isset($_SESSION["userid"]) || !isset($_SESSION["username"])) {
                    die("You must log in first!");
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
                        // echo $preference; maybe there is no return from the query
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
                            onclick="searchRecommendedRecipes()">Feeling lazy?</button>
                        <div id="errorMessage"></div>
                    </div>
                    <div class="outputbox" id='BLDSelect'>
                        <div class="title-text">Select your meal:</div>
                        <div class="output-text">Select the type of meal you want to search for:</div>
                        <select id="BLDChoice" name="BLDC" class="genderIn">
                            <option value="op1">Any</option>
                            <option value="op2">Breakfast</option>
                            <option value="op3">Lunch</option>
                            <option value="op4">Dinner</option>
                            <option value="op5">Snack</option>
                            <option value="op6">Dessert</option>
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
                    </script>
                </div>
                <div class="outputbox" id='searchResults'>
                    <div class="title-text">Recipe Results:</div>
                    
                    <div class="mealDisplay" id="displayResultsHere"><div class="shadowing"></div></div>
                </div>
                <div class="outputbox" id="queriesBox">
                    <div class="title-text">Specific Search Queries:</div>
                    <?php
                    echo $preference;
                    ?>
                </div>
            </div>

        </div>
    </form>
    <script src="../JS/app.js"></script>
</body>

</html>
<?php
ob_end_flush();
?>