<style>
    .mess{
        margin-top:90px;
    }
</style>
<?php
session_start();

include("header.php");

if (isset($_SESSION['role']) && $_SESSION['role']  === 'admin') {
    $path = 'C:/xampp/secure_data/contact_messages.txt';
    $messages = file_get_contents($path);
    echo "<div class='mess'>";
    echo nl2br(htmlspecialchars($messages));
    echo "</div>";

} else {
    echo "Access denied.";
}
?>
