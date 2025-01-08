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
    $favouriteThisMealQuery = $connection->prepare("INSERT INTO favouritedmeals (userID, MealID) VALUES (?, ?)");
    $favouriteThisMealQuery->bind_param("ii", $userID, $mealID);
    if ($favouriteThisMealQuery->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "couldnt insert meal to favourites"]);
    }
}
$favouriteThisMealQuery->close();
ob_end_flush();
?>