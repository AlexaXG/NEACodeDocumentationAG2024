<?php
header('Content-Type: application/json');

$mealType = $_GET['mealType'];
$userID = $_GET['userID'];
$preference = $_GET['preference'];

try {
    $connection = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    exit;
}
if ($mealType === "Any") {
    if ($preference === "No Preference") {
        $noFiltersQuery = "
            SELECT meals.* 
            FROM meals
            LEFT JOIN usermealhistory ON usermealhistory.UserID = ? AND usermealhistory.MealID = meals.MealID
            WHERE (usermealhistory.ViewCount IS NULL OR DATEDIFF(NOW(), usermealhistory.LastViewedAt) >= 1)
            ORDER BY RAND() LIMIT 3;
        ";
        $requestMealsFromDatabase = $connection->prepare($noFiltersQuery);
        $requestMealsFromDatabase->bind_param('i', $userID);
    } else {
        $onlyPreferenceFilterQuery = "
            SELECT meals.* 
            FROM meals 
            JOIN mealpreferences ON meals.MealID = mealpreferences.MealID
            JOIN userpreferences ON userpreferences.UserID = ?
            JOIN preference ON mealpreferences.preferenceID = preference.PreferenceID
            LEFT JOIN usermealhistory ON usermealhistory.UserID = ? AND usermealhistory.MealID = meals.MealID
            WHERE preference.PreferenceName = ?
            AND (usermealhistory.ViewCount IS NULL OR DATEDIFF(NOW(), usermealhistory.LastViewedAt) >= 1)
            ORDER BY RAND() LIMIT 3;
        ";
        $requestMealsFromDatabase = $connection->prepare($onlyPreferenceFilterQuery);
        $requestMealsFromDatabase->bind_param('iiss', $userID, $userID, $preference);
    }
} else {
    if ($preference === "No Preference") {
        $onlyMealTypeFilterQuery = "
            SELECT meals.* 
            FROM meals 
            JOIN eachmealsmealtype ON meals.MealID = eachmealsmealtype.MealID
            JOIN mealtypes ON eachmealsmealtype.MealTypeID = mealtypes.MealTypeID
            LEFT JOIN usermealhistory ON usermealhistory.UserID = ? AND usermealhistory.MealID = meals.MealID
            WHERE mealtypes.MealTypeName = ?
            AND (usermealhistory.ViewCount IS NULL OR DATEDIFF(NOW(), usermealhistory.LastViewedAt) >= 1)
            ORDER BY RAND() LIMIT 3;
        ";
        $requestMealsFromDatabase = $connection->prepare($onlyMealTypeFilterQuery);
        $requestMealsFromDatabase->bind_param('is', $userID, $mealType);
    } else {
        $allFiltersQuery = "
            SELECT meals.* 
            FROM meals
            JOIN eachmealsmealtype ON meals.MealID = eachmealsmealtype.MealID
            JOIN mealtypes ON eachmealsmealtype.MealTypeID = mealtypes.MealTypeID
            JOIN mealpreferences ON meals.MealID = mealpreferences.MealID
            JOIN userpreferences ON userpreferences.UserID = ?
            JOIN preference ON mealpreferences.preferenceID = preference.PreferenceID
            LEFT JOIN usermealhistory ON usermealhistory.UserID = ? AND usermealhistory.MealID = meals.MealID
            WHERE mealtypes.MealTypeName = ?
            AND preference.PreferenceName = ?
            AND (usermealhistory.ViewCount IS NULL OR DATEDIFF(NOW(), usermealhistory.LastViewedAt) >= 1)
            ORDER BY RAND() LIMIT 3;
        ";
        $requestMealsFromDatabase = $connection->prepare($allFiltersQuery);
        $requestMealsFromDatabase->bind_param('iiss', $userID, $userID, $mealType, $preference);
    }
}
$requestMealsFromDatabase->execute();
$result = $requestMealsFromDatabase->get_result();
$mealsReceived = $result->fetch_all(MYSQLI_ASSOC);

if (count($mealsReceived) > 0) {
    echo json_encode($mealsReceived);
    foreach ($mealsReceived as $meal) {
        $mealID = $meal['MealID'];
        $updateUserViewCount = $connection->prepare("
                INSERT INTO usermealHistory (UserID, MealID, ViewCount, LastViewedAt)
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE
                ViewCount = ViewCount + 1, LastViewedAt = NOW()
            ");
        $updateUserViewCount->bind_param('ii', $userID, $mealID);

        if (!$updateUserViewCount->execute()) {
            echo json_encode(['error' => 'Failed to update user meal history for MealID ' . $mealID]);
            exit;
        }
    }
} else {
    echo json_encode([]);
}

?>