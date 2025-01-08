<?php
header('Content-Type: application/json');
ob_start();
try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$userID = $_GET['userID'];

if (!$userID) {
    echo json_encode(["success" => false, "message" => "Invalid UserID"]);
    exit;
} else {
    $requestFavouritedMealsFromDatabase = $connection->prepare("SELECT meals.MealID, meals.MealName, meals.RecipeURL, meals.RecipeImg, meals.Protein, meals.Fats, meals.Carbs 
        FROM favouritedmeals favouritedmeals 
        JOIN meals meals ON favouritedmeals.MealID = meals.MealID 
        WHERE favouritedmeals.userID = ?;");
    $requestFavouritedMealsFromDatabase->bind_param("i", $userID);
    if ($requestFavouritedMealsFromDatabase->execute()) {
        $result = $requestFavouritedMealsFromDatabase->get_result();
        $mealsReceived = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["success" => true, "meals" => $mealsReceived]);
    } else {
        echo json_encode(["success" => false, "message" => "couldnt get meals from favourites"]);
    }
}
$requestFavouritedMealsFromDatabase->close();
ob_end_flush();
?>