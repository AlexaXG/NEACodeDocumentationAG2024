<?php
session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : 'Guest';
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : 'N/A';
//assigns session variables, if they are not set, itll assign valid strings instead

header('Content-Type: application/json');
$_SESSION["title-text2"] = "Your Account:";
//session variable for parameterised HTML Classes 
echo "<span class='welcome-text'>Your<span id='webName'> Account:</span></span>";
if (!isset($_SESSION["userid"])  || !isset($_SESSION["username"])) {
    die("No session variables set");
}
echo "<span class='output-text'>Username: </span>" . "<span class='output-value'>" . htmlspecialchars($username)
. "</span>" . "\n" . "<span class='output-text'>UserID: </span>" ."<span class='output-value'>" 
//most of this code is HTML code to output from php, so it can also be loaded without having predetermined text on the html page itself
. htmlspecialchars($userid) . "</span>";
?>