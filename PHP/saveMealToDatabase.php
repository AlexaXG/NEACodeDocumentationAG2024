<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();
ob_start();

try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $MealID = $data['MealID'];
    $MealName = $data['MealName'];
    $RecipeURL = $data['RecipeURL'];
    $RecipeImg = $data['RecipeImg'];
    $Protein = $data['Protein'];
    $Fats = $data['Fats'];
    $Carbs = $data['Carbs'];
    $MealTypes = $data['MealTypes'];
    $userID = $data['UserID'];
    $preferences = $data['Preferences'];

    $checkIfMealExists = $connection->prepare("SELECT * FROM meals WHERE MealID = ?");
    $checkIfMealExists->bind_param("i", $MealID);
    $checkIfMealExists->execute();
    $result = $checkIfMealExists->get_result();

    if ($result->num_rows === 0) {
        $insertMealToDB = $connection->prepare("INSERT INTO meals (MealID, MealName, RecipeURL, RecipeImg, Protein, Fats, Carbs) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertMealToDB->bind_param("isssddd", $MealID, $MealName, $RecipeURL, $RecipeImg, $Protein, $Fats, $Carbs);
        if (!$insertMealToDB->execute()) {
            die(json_encode(["error" => "Failed to insert meal: " . $insertMealToDB->error]));
        } else {
            if (is_string($MealTypes)) {
                $tempMealType = $MealTypes;
                $MealTypes = [$tempMealType];
            }
            foreach ($MealTypes as $MealTypeName) {
                $checkMealType = $connection->prepare("SELECT MealTypeID FROM MealTypes WHERE MealTypeName = ?");
                $checkMealType->bind_param("s", $MealTypeName);
                $checkMealType->execute();
                $mealTypeResult = $checkMealType->get_result();

                if ($mealTypeResult->num_rows > 0) {
                    $mealTypeRow = $mealTypeResult->fetch_assoc();
                    $MealTypeID = $mealTypeRow['MealTypeID'];
                } else {
                    $insertMealType = $connection->prepare("INSERT INTO MealTypes (MealTypeName) VALUES (?)");
                    $insertMealType->bind_param("s", $MealTypeName);
                    $insertMealType->execute();
                    $MealTypeID = $connection->insert_id;
                }
                $linkMealTypesAndMeal = $connection->prepare("INSERT INTO eachMealsMealType (MealID, MealTypeID) VALUES (?, ?)");
                $linkMealTypesAndMeal->bind_param("ii", $MealID, $MealTypeID);
                $linkMealTypesAndMeal->execute();
            }
            if (is_string($preferences)) {
                $tempPreference = $preferences;
                $preferences = [$tempPreference];
            }
            foreach ($preferences as $dietType) {
                $checkPreference = $connection->prepare("SELECT preferenceID FROM preference WHERE preferenceName = ?");
                $checkPreference->bind_param("s", $dietType);
                $checkPreference->execute();
                $result = $checkPreference->get_result();
            
                if ($result->num_rows == 0) {
                    $insertPreference = $connection->prepare("INSERT INTO preference (preferenceName) VALUES (?)");
                    $insertPreference->bind_param("s", $dietType);
                    $insertPreference->execute();
                    $preferenceID = $connection->insert_id;
                } else {
                    $preferenceRow = $result->fetch_assoc();
                    $preferenceID = $preferenceRow['preferenceID'];
                }
            
                $insertMealPreference = $connection->prepare("INSERT INTO mealPreferences (MealID, preferenceID) VALUES (?, ?)");
                $insertMealPreference->bind_param("ii", $MealID, $preferenceID);
                $insertMealPreference->execute();
            }
        }
    }
    $insertUserMealHistory = $connection->prepare("INSERT INTO userMealHistory (UserID, MealID, ViewCount, LastViewedAt) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE ViewCount = ViewCount + 1, LastViewedAt = NOW();");
    $insertUserMealHistory->bind_param("ii", $userID, $MealID);
    if (!$insertUserMealHistory->execute()) {
        $error = $insertUserMealHistory->error;
    } else {
        exit;
    }
}
ob_end_flush();
?>