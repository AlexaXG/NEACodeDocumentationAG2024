<?php session_start();
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
        <p>Your Allergies:</p>
        <div class='login-text'></div>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
            <div class="variables">
                <label for="allergy"></label> <br />
                <input type="text" name="allergy" placeholder="Type an allergy..." id="allergy" autocomplete="off"
                    required /><br />
                <div class="small-text">
                    <h3>If you do not have allergies, simply skip by pressing 'Confirm'</h3></br>
                </div>
                <br />
                <input type="submit" value="Submit Selection" name="s">
                <!--submit button does not redirect, instead refreshes the page, as the user has an unknown amount of allergies -->
                <a href="http://localhost/userGoals.php">
                    <input type="button" value="Confirm" name="s"> </a>
                <!--this simply redirects the user to the next page if they do not have allergies -->
                <a href="http://localhost/Activitylevel.php">
                    <input type="button" value="Back" /> </a>
                <a href="http://localhost/userGoals.php">
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
        $Allergy = trim($_GET["allergy"]);
        //removing any unnecessary characters such as whitespace around the string
        $Allergy = strtolower($Allergy);

        if (empty($Allergy)) {
            die("Allergy not set");
        }
        try {
            
            $AllergyCheck = $connection->prepare("SELECT AllergyName, AllergyID From Allergy Where LOWER(AllergyName) = ?");
            $AllergyCheck->bind_param("s", $Allergy);
            //initially selects the allergy name and ID by the inputted allergy to lower case
            if ($AllergyCheck->execute()) {
                $AllergyCheck->store_result();
                $AllergyCheck->bind_result($AllergyName, $AllergyID);
                $AllergyCheck->fetch();
                $_SESSION["allergyid"] = $AllergyID;
            } else {
                die(mysqli_error());
            }
            if ($AllergyCheck->num_rows > 0) {
                //if it already exists, it will not insert it as a new allergy, and instead does the following
                $duplicateCheck = $connection->prepare("SELECT * From userAllergies Where AllergyID = ? AND UserID = ?");
                $duplicateCheck->bind_param("ii", $AllergyID, $userid);
                //this checks if the user already has that inputted allergy associated with their userid
                if ($duplicateCheck->execute()) {
                    $duplicateCheck->store_result();
                } else {
                    die(mysqli_error());
                }

                if ($duplicateCheck->num_rows == 0) {
                    //if its not a duplicate:
                    $connUserAllergy = $connection->prepare("insert into userallergies (userID, AllergyID) values (?, ?)");
                    //it will add this allergyID and userID into the "userAllergies" table, associating them together
                    if (!$connUserAllergy) {
                        die(mysqli_error());
                    } else {
                        $connUserAllergy->bind_param("ii", $userid, $AllergyID);
                        $connUserAllergy->execute();
                        //redirect and termination
                        header("http://localhost/userAllergies.php");
                        exit();
                    }
                } else {
                    die("<div class='error-message'>This allergy has already been added.</div>");
                    //else statement checking if the allergy is already associated with the user
                }
            } else {
                //if the allergy does NOT exist:
                $result = $connection->prepare("Insert Into Allergy (AllergyName) values (?)");
                //it will create a new allergy 
                $result->bind_param("s", $Allergy);
                if ($result->execute()) {
                    $AllergyID = $connection->insert_id;
                    //selects the ID that was created
                    $_SESSION["allergyid"] = $AllergyID;
                    $_SESSION["userid"] = $userid;
                    $_SESSION["username"] = $username;
                    header("http://localhost/userAllergies.php");
                    exit();
                } else {
                    die(mysqli_error());
                }
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