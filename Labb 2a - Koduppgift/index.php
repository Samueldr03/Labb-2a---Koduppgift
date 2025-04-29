<?php
session_start(); // Startar sessionen

// Om användaren inte är inloggad, skicka till login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Om användaren klickar på logga ut-länk
if (isset($_GET['logout'])) {
    session_destroy(); // Avsluta sessionen
    header("Location: login.php"); // Skicka tillbaka till login
    exit();
}
?>

<!-- HTML för inloggningssidan -->
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Inloggad</title>
</head>
<body>
    <h1>Du är inloggad</h1>

    <!-- Visa användarnamnet -->
    <p>Välkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

    <!-- Logga ut-länk -->
    <a href="index.php?logout=true">Logga ut</a>
</body>
</html>