<?php
try {
    $conn = new mysqli("localhost", "root", "", "neaDatabaseAlexG");
} catch (mysqli_sql_exception $e) {
    die("Something went wrong: " . $e);
}
?>


