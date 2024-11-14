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
    <link rel="stylesheet" href="/css/pageStyling.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
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
        <script>
            function highlightIt(element) {
                var links = document.querySelectorAll('a');
                links.forEach(link => {
                    link.classList.remove('highlighted');
                    //removes "highlighted" css class from all <a> references
                });
                element.classList.add('highlighted');
                //then it adds the highlighted class to the button "this" that was clicked
            }
        </script>
        <div class="main-content">
            <div class="welcome-container">
                <?php
                if (!isset($_SESSION["userid"]) || !isset($_SESSION["username"])) {
                    header("location: http://localhost/php/login.php");
                    exit();
                } else {
                    $userid = $_SESSION["userid"];
                    $username = $_SESSION["username"];
                }
                try {
                    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
                } catch (mysqli_sql_exception $e) {
                    $_SESSION['toast_message'] = "Database Issue" . $e;
                }
                $connUserDetails = $connection->prepare("select weight, height, gender, age from user where username = ?");
                $connUserDetails->bind_param("s", $username);
                $connUserDetails->execute();
                $connUserDetails->bind_result($weight, $height, $gender, $age);
                $connUserDetails->fetch();

                if (!isset($_SESSION["weight"])) {
                    $_SESSION['toast_message'] = "Weight session variable not set";
                    $weightArg = $weight;
                } else {
                    $weightArg = $_SESSION["weight"];
                }
                if (!isset($_SESSION["height"])) {
                    $_SESSION['toast_message'] = "Height session variable not set";
                    $heightArg = $height;
                } else {
                    $heightArg = $_SESSION["height"];
                }
                if (!isset($_SESSION["gender"])) {
                    $_SESSION['toast_message'] = "Gender session variable not set";
                    $genderArg = $gender;
                } else {
                    $genderArg = $_SESSION["gender"];
                }
                if (!isset($_SESSION["age"])) {
                    $_SESSION['toast_message'] = "Age session variable not set";
                    $ageArg = $age;
                } else {
                    $ageArg = $_SESSION["age"];
                }
                $connUserDetails->close();

                $connUserActivity = $connection->prepare("SELECT activity from user where userid = ?");
                $connUserActivity->bind_param("i", $userid);
                $connUserActivity->execute();
                $connUserActivity->bind_result($ActivityLevel);
                $connUserActivity->fetch();

                if (!isset($_SESSION["ActivityLevel"])) {
                    $activityArg = $ActivityLevel;
                } else {
                    $activityArg = $_SESSION["ActivityLevel"];
                }
                $connUserActivity->close();

                $connUserGoal = $connection->prepare("SELECT goals.GoalName FROM usergoals JOIN goals ON usergoals.GoalID = goals.GoalID WHERE usergoals.userID = ?");
                $connUserGoal->bind_param("i", $userid);
                $connUserGoal->execute();
                $connUserGoal->bind_result($goalArg);
                $connUserGoal->fetch();
                $_SESSION["goal"] = $goalArg;
                $connUserGoal->close();

                $dob = new DateTime($ageArg);
                $now = new DateTime();
                $ageArg = $now->diff($dob)->y;
                //standardising dates for age and calculating the age number (age is stored as a date in the DB)
                ?>
                <span class="welcome-text">Your <span id="webName">GymmieMeals</span> Homepage,
                    <span id="nameDisplay">
                        <?php echo (htmlspecialchars($username));
                        ?>
                    </span>
                </span>
            </div>
            <div class="display-content">
                <div class="bmiAndAnnulusBox">
                    <div class='outputbox' id='bmiInfo'>
                        <div class="title-text">Weight Statistics</div>
                        <div class='output-text' id='weightCategory'>Your weight category:</div>
                        <div class='output-value' id='catVal'>
                            <?php
                            $javaJDKPath = getenv('JAVA_JDK_PATH');

                            //defining my file path to my JDK java folder
                            $javaCompiledPath = getenv('FIND_BMI_CLASS');
                            
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
                        </div> <br>
                        <div class='output-text' id='bmiText'>Your BMI:</div>
                        <div class='output-value' id='bmiVal'>
                            <?php
                            echo htmlspecialchars($bmi);
                            ?>
                        </div>
                    </div>
                    <div class="annulus">
                        <canvas id="annulusChart" style="width:400px; height:400px;"></canvas>
                    </div>
                </div>
                <div class='outputbox' id='calories'>
                    <div class="title-text">Nutritional Information</div>
                    <div class='output-text' id='caloriesText'>Daily Calories:</div>
                    <div class='output-value' id='calVal'>
                        <?php
                        $javaCompiledPath2 = getenv('RECOMMENDED_CALORIES_CLASS');
                        $command2 = "\"$javaJDKPath\" -cp \"$javaCompiledPath2\" recommendedcalories.CalculateCalories $weightArg $heightArg $ageArg $genderArg $activityArg 2>&1";
                        //using the parameters, it calculates the necessary calories that a person must eat according to their data
                        //echo("Executing command: $command2");
                        $javaOutput2 = shell_exec($command2);
                        //echo("Java Output: $javaOutput2");
                        $calories = floatval(htmlspecialchars($javaOutput2));
                        echo $calories;
                        ?>
                    </div><br>
                    <div class='output-text' id='caloriesText'>Daily Macronutrients:</div>
                    <div class='output-value' id='splitVal'>
                        <?php
                        $javaCompiledPath3 = getenv('CALCULATE_SPLITS_CLASS');
                        $command3 = "\"$javaJDKPath\" -cp \"$javaCompiledPath3\" calculatesplits.CalculateSplits $calories \"$goalArg\" 2>&1";
                        $javaOutput3 = shell_exec($command3);
                        list($fats, $carbs, $protein) = explode(",", trim($javaOutput3));
                        ?>
                        <table id="PFCTable">
                            <tr id="tags">
                                <th></th>
                                <th>Protein</th>
                                <th>Fats</th>
                                <th>Carbs</th>
                            </tr>
                            <tr>
                                <td id="bf">Breakfast</td>
                                <td>
                                    <?php
                                    echo ($protein * 0.3) . 'g';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($fats * 0.3) . 'g';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($carbs * 0.3) . 'g';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td id="lu">Lunch</td>
                                <td> <?php
                                echo ($protein * 0.4) . 'g';
                                ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($fats * 0.4) . 'g';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($carbs * 0.4) . 'g';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td id="di">Dinner</td>
                                <td> <?php
                                echo ($protein * 0.3) . 'g';
                                ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($fats * 0.3) . 'g';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo ($carbs * 0.3) . 'g';
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <script>
                            const fats = <?php echo $fats; ?>;
                            const protein = <?php echo $protein; ?>;
                            const carbs = <?php echo $carbs; ?>;
                            var xvals = ["Protein", "Fats", "Carbohydrates"];
                            var yvals = [protein, fats, carbs];
                            var colours = [
                                "#e04744", //protein
                                "#e0bc44", //fats
                                "#34a4e0" //carbs
                            ];
                            new Chart("annulusChart", {
                                type: "doughnut",
                                data: {
                                    labels: xvals,
                                    datasets: [{
                                        backgroundColor: colours,
                                        data: yvals
                                    }]
                                },
                                options: {
                                    title: {
                                        display: true
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>
<?php
if (isset($_SESSION['toast_message'])) {
    echo "<script>showToast('" . addslashes($_SESSION['toast_message']) . "');</script>";
    unset($_SESSION['toast_message']);
}
ob_end_flush();
?>