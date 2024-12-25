<?php
session_start();
if (!isset($_SESSION["username"]) || !isset($_SESSION["userid"])) {
    $_SESSION['toast_message'] = "Session not set!";
    header("location: login.php");
} else {
    $username = $_SESSION["username"];
    $userid = $_SESSION["userid"];
}
//assigns session variables, if they are not set, itll assign valid strings instead

header('Content-Type: application/json');
$_SESSION["title-text2"] = "Your Account:";

echo "<span class='welcome-text'>Your<span id='webName'> Account:</span></span>";
echo "<span class='output-text'>Username: </span>" . "<span id='username-output' class='output-value'>" . htmlspecialchars($username)
    . "</span>" . "<button id='edit-username-btn'>Edit</button>" . "\n" . "<span class='output-text'>UserID: </span>" . "<span class='output-value'>"
    //most of this code is HTML code to output from php, so it can also be loaded without having predetermined text on the html page itself
    . htmlspecialchars($userid) . "</span>";
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('edit-username-btn');
    const usernameOutput = document.getElementById('username-output');

    editButton.addEventListener('click', function () {
        // Replace the output value with a text input and a save button
        const currentUsername = usernameOutput.textContent;
        usernameOutput.innerHTML = `
            <input type="text" id="username-input" value="${currentUsername}" />
            <button id="save-username-btn">Save</button>
        `;

        // Add functionality to the save button
        const saveButton = document.getElementById('save-username-btn');
        saveButton.addEventListener('click', function () {
            const newUsername = document.getElementById('username-input').value;

            // Send the new username to the server (use fetch or AJAX)
            fetch('updateUsername.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: newUsername })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the displayed username
                    usernameOutput.textContent = newUsername;
                } else {
                    alert('Failed to update username');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>