<?php
try {
    $conn = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    $_SESSION['toast_message'] = "Database Issue" . $e;
}
?>


