<?php
header('Content-Type: application/json');
ob_start();
try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$mealID = $data['mealID'];
$userID = $data['userID'];

if (!$mealID || !$userID) {
    echo json_encode(["success" => false, "message" => "Invalid MealID or UserID"]);
    exit;
} else {
    $removeThisMealFromFavourites = $connection->prepare("DELETE FROM favouritedmeals WHERE userID = ? AND MealID = ?");
    $removeThisMealFromFavourites->bind_param("ii", $userID, $mealID);
    if ($removeThisMealFromFavourites->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "couldnt remove meal from favourites"]);
    }
}
$removeThisMealFromFavourites->close();
ob_end_flush();
?>