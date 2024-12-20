<?php
session_start();
try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    die("<div class='error-message'>Something went wrong: </div>" . $e);
}
echo "<span class='welcome-text'>Your<span id='webName'> Allergies:</span></span>";
if (!isset($_SESSION["userid"])  || !isset($_SESSION["username"])) {
    die("No session variables set");
}
    $userid = $_SESSION["userid"];
    $connUserAllergies = $connection->prepare("SELECT Allergy.AllergyName from userAllergies join Allergy on 
        userAllergies.AllergyID = Allergy.AllergyID where userAllergies.UserID = ?;");
    //selects the allergy name from the allergy table where the allergyID in the allergy table and the allergyID in the userallergies table match, where they are associated with the userid
    $connUserAllergies->bind_param("s", $userid);
    $connUserAllergies->execute();
    $AllergyResult = $connUserAllergies->get_result();

    if ($AllergyResult->num_rows >0) {
        //if there are more than 0 rows, itll fetch all rows
        while($eachRow = $AllergyResult->fetch_assoc()) {
            //$eachRow is an array and the while loop fetches every array index (every AllergyName row of data from SQL)
            $AllergyName = $eachRow["AllergyName"];
            header('Content-Type: application/json');
            echo "<br>";
            echo htmlspecialchars($AllergyName);
            //outputs the allergy removing special characters
        }
    }
    else {
        header('Content-Type: application/json');
        echo "no allergies";
    }

?>