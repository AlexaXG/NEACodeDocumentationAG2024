<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Homepage</title>
    <link rel="stylesheet" href="/css/pageStyling.css"> <!-- referencing the updated Styling page-->
</head>

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
                    die("You must log in first!");
                } else {
                    $userid = $_SESSION["userid"];
                    $username = $_SESSION["username"];
                }
                if (!isset($_SESSION["weight"]) || !isset($_SESSION["height"]) || !isset($_SESSION["gender"]) || !isset($_SESSION["age"]) || !isset($_SESSION["ActivityLevel"])) {
                    die("session variables not set");
                } else {
                    $weightArg = $_SESSION["weight"];
                    $heightArg = $_SESSION["height"];
                    $genderArg = $_SESSION["gender"];
                    $ageArg = $_SESSION["age"];
                    $activityArg = $_SESSION["ActivityLevel"];
                    //assigning session variables to local variables
                }
                $dob = new DateTime($ageArg);
                $now = new DateTime();
                $ageArg = $now->diff($dob)->y;
                //standardising dates for age and calculating the age number (age is stored as a date in the DB)
                ?>
                <span class="welcome-text">Your <span id="webName">Goals:</span>
                </span>
            </div>
            <div class="display-content">
                <div class='outputbox' id='bmiInfo'>
                    <div class="title-text">Your Statistics:</div>
                    <div class='output-text' id='weightCategory'>Your weight category:</div>
                    <div class='output-value' id='catVal'>
                        <?php
                        $javaJDKPath = "C:/Program Files/Java/jdk-17/bin/java";
                        //defining my file path to my JDK-17 java folder
                        $javaCompiledPath = "C:/Users/algub/OneDrive/Documents/NetBeansProjects/FindBMI/src";
                        //defining the file path to the compiled java algorithm that calculates BMI and category
                        $command = "\"$javaJDKPath\" -cp \"$javaCompiledPath\" findbmi.FindBMIValue $weightArg $heightArg 2>&1";
                        //this command does:
                        // = "using this JDK version" -cp sets classpath, findbmi.FindBMIValue is the file name of my java algorithm, followed by parameters to parse into it
                        // 2&>1 is used for debugging, redirecting error messages and outputs to the same location
                        $javaOutput = shell_exec($command);
                        //executing the command
                        list($category, $bmi) = explode(",", trim($javaOutput));
                        //splits the java algorithm output by "," and assigns each value to its own variable
                        //setting the dynamic text
                        echo htmlspecialchars($category);
                        ?>
                    </div>
                    <div class='output-text' id='bmiInfo'>Your BMI:</div>
                    <div class='output-value' id='bmiVal'>
                        <?php
                        $javaCompiledPath2 = "C:/Users/algub/OneDrive/Documents/NetBeansProjects/RecommendedCalories/src";
                        $command2 = "\"$javaJDKPath\" -cp \"$javaCompiledPath2\" recommendedcalories.CalculateCalories $weightArg $heightArg $ageArg $genderArg $activityArg";
                        //using the parameters, it calculates the necessary calories that a person must eat according to their data
                        $javaOutput2 = shell_exec($command2);
                        // echo "<pre>$command2</pre>";
                        // echo "<pre>$javaOutput2</pre>";
                        $calories = htmlspecialchars($javaOutput2);
                        echo htmlspecialchars($bmi); 
                        ?>
                    </div>
                </div>
                <div class='outputbox' id='calories'>
                    <div class="title-text">Your Calorie Intake:</div>
                    <div class='output-text' id='caloriesText'>Daily Calories:</div>
                    <div class='output-value' id='calVal'><?php echo htmlspecialchars($calories); ?></div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>
<?php
ob_end_flush();
?>