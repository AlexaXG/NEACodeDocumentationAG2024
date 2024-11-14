<?php session_start();
ob_start();
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymmieMeals-Your Allergies</title>
    <link rel="stylesheet" href="/css/pageStyling.css">
</head>
<style>
    .small-text {
        font-size: 15px;
        display: block;
         text-align: center;
    }
</style>
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
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="get">
            <div class="main-content">
            <div class="welcome-container">
                    <span class="welcome-text"><span id="webName">Your Allergies:</span></span>
                </div>
                <div class="inputbox" id="allergyContent">
                <span class="small-text">
                    <h3>In one entry, separate each allergy by a comma</h3></br>
</span>
                <label for="allergy"></label> 
                <input type="text" name="allergy" placeholder="Type an allergy..." id="allergy" autocomplete="off"
                    required /><br />
                
                <button type="submit" name="s">Confirm</button>
                <!--submit button does not redirect, instead refreshes the page, as the user has an unknown amount of allergies -->
                <a href="http://localhost/php/userGoals.php">
                                <button type="button">Skip</button>
                
                <!--this simply redirects the user to the next page if they do not have allergies -->
                <a href="http://localhost/php/Activitylevel.php">
                    <button type="button">Back</button>
                </div>
                
            </div>
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
                        header("http://localhost/php/userAllergies.php");
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
                    header("http://localhost/php/userAllergies.php");
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