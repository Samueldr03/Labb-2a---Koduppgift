<?php
session_start(); // Startar sessionen för att hålla koll på inloggad användare

$filename = 'users.txt'; // Fil där användardata lagras
$message = ''; // Variabel för att visa felmeddelanden eller bekräftelser

// Om användaren redan är inloggad, skicka till index.php
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Kontrollera om formuläret har skickats in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Kontrollera om användarnamn eller lösenord är tomma
    if (empty($username) || empty($password)) {
        $message = "Du måste ange både användarnamn och lösenord.";
    } else {
        $normalizedUsername = strtolower($username); // Användarnamn jämförs oavsett versaler
        $users = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Läs alla användare
        $userExists = false;

        // Kolla om användarnamnet redan finns
        foreach ($users as $user) {
            list($savedUsername, $savedHashedPassword) = explode(":", $user);
            if (strtolower($savedUsername) === $normalizedUsername) {
                $userExists = true;
                $actualUsername = $savedUsername; // Spara korrekt formaterat användarnamn
                break;
            }
        }

        // Registrering av ny användare
        if (isset($_POST['register'])) {
            if ($userExists) {
                $message = "Användarnamnet finns redan.";
            } else {
                // Hasha lösenordet innan det sparas (säkerhet)
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                file_put_contents($filename, "$username:$hashedPassword\n", FILE_APPEND);
                $message = "Ny användare registrerad!";
            }

        // Inloggning
        } elseif (isset($_POST['login'])) {
            $authenticated = false;

            // Gå igenom alla användare och verifiera inloggning
            foreach ($users as $user) {
                list($savedUsername, $savedHashedPassword) = explode(":", $user);
                if (strtolower($savedUsername) === $normalizedUsername && password_verify($password, $savedHashedPassword)) {
                    $authenticated = true;
                    $actualUsername = $savedUsername;
                    break;
                }
            }

            if ($authenticated) {
                $_SESSION['username'] = $actualUsername; // Spara inloggad användare i session
                header("Location: index.php"); // Skicka till index.php
                exit();
            } else {
                $message = "Felaktigt användarnamn eller lösenord.";
            }
        }
    }
}
?>

<!-- HTML-delen med formulär -->
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Login / Registrera</title>
</head>
<body>
    <h2>Logga in eller registrera ny användare</h2>
    
    <form method="post">
        <!-- Fält för användarnamn -->
        <label>Användarnamn:</label><br>
        <input type="text" name="username" required><br><br>

        <!-- Fält för lösenord (dolt) -->
        <label>Lösenord:</label><br>
        <input type="password" name="password" required><br><br>

        <!-- Knappar för logga in och registrera -->
        <button type="submit" name="login">Logga in</button>
        <button type="submit" name="register">Spara ny användare</button>
    </form>

    <!-- Visar felmeddelande -->
    <p style="color: red;"><?php echo $message; ?></p>
</body>
</html>