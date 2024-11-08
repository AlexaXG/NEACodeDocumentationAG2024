<?php
session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Your Allergies</title>
    <link rel="stylesheet" href="pageStyling.css">
</head>

<body>
    <div class="container">
        <p>Your Goals:</p>
        <div class='login-text'>
        </div>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="variables">
                <label for="allergy"></label> <br />
                <div class="activity-box">
                    <div>
                        <input type="radio" id="activ1" name="goal" value="WeightLoss" required>
                        <label for="activ1"><span class="labelText">Weight Loss: </span></br>- Losing around 2-4kg per
                            month.</label><br />
                    </div>
                    <div>
                        <input type="radio" id="activ2" name="goal" value="MuscleGain">
                        <label for="activ2"><span class="labelText">Build Muscle: </span></br>- Gain 0.5-1kg of muscle
                            per month.</label><br />
                    </div>
                    <div>
                        <input type="radio" id="activ3" name="goal" value="WeightMaintenance">
                        <label for="activ3"><span class="labelText">Maintain Your Weight: </span></br>- Retain your
                            current body weight.</label><br />
                    </div>
                    <div>
                        <input type="radio" id="activ4" name="goal" value="MuscleGainWeightLoss">
                        <label for="activ4"><span class="labelText">Gain Muscle & Lose Weight: </span></br>- Increase
                            protein intake whilst cutting calories.</label><br />
                    </div>
                </div>
                <!--radio buttons only allow 1 selection at a time -->
                <span class="small-text">
                    <h3>When will/did your goal start?</h3>
                </span>
                <label for="goalStartDate"></label>
                <input type="date" name="start" id="startDate" autocomplete="off" required /> <br />
                <!-- user selects a date for when the goal started-->
                <label for="tarPOSTweight"></label> <br />
                <input type="number" name="targetweight" placeholder="Target weight" id="weightKG" autocomplete="off"
                    required min="0" />
                <br /><br /><br />
                <a href="http://localhost/Homepage.php">
                    <input type="submit" value="Confirm" name="s"></a>
                <a href="http://localhost/userAllergies.php">
                    <input type="button" value="Back"></input> </a>
                <a href="http://localhost/Homepage.php">
                    <input type="button" value="TEST BUTTON"> </input> </a>
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
        if (isset($_SESSION["userid"]) || isset($_SESSION["username"])) {
            $userid = $_SESSION["userid"];
            $username = $_SESSION["username"];
        } else {
            die("No user id or name set.");
        }
        
        $goal = $_POST["goal"];
        $goalStartDate = $_POST["start"];
        $targetWeight = $_POST["targetweight"];

        $todaysDate = date("Y-m-d");
        if ($goalStartDate > $todaysDate) {
            $goalStatus = 'Awaiting';
            //checks if the set date for the goal start is after the current date
        } elseif ($goalStartDate <= $todaysDate) {
            $goalStatus = 'in progress';
            //if the start date is before or on the current date, the goal will be in progress
        }
        try {
            $goalInsert = $connection->prepare("select GoalID From goals Where GoalName = ?");
            $goalInsert->bind_param("s", $goal);
            if ($goalInsert->execute()) {
                $goalInsert->bind_result($goalID);
                $goalInsert->fetch();
                $_SESSION["goalid"] = $goalID;
                $goalInsert->close();
                //limited number of goals, so goalID is selected according to the name
            } else {
                die($connection->error);
            }
            // echo $goalID;
            $goalID = $_SESSION["goalid"];
            $userid = $_SESSION["userid"];

            $_SESSION["goal"] = $goal;
            
            $GoalPriority = 1;
            //as this will be the first goal the user will set, itll default to priority 1 (top)
            $result = $connection->prepare("Insert Into userGoals (GoalStatus, GoalStartDate, GoalPriority, UserID, GoalID, targetweight) values (?, ?, ?, ?, ?, ?)");
            $result->bind_param("ssiiii", $goalStatus, $goalStartDate, $GoalPriority, $userid, $goalID, $targetWeight);
            if ($result->execute()) {
                $result->close();
                header("Location: http://localhost/Homepage.php");
                exit();
            } else {
                die("couldnt insert values");
            }
        } catch (mysqli_sql_exception $e) {
            die("$e");
        }
        ?>
    </div>
</body>

</html>
<?php
ob_end_flush();
?>