<?php
include "../config/DB.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name = cleanInput($_POST['full_name']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // 2. Check if email already exists using a prepared statement
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Email already registered!";
        } else {
            // 3. Generate security data
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $user_unique_id = "SW-" . strtoupper(bin2hex(random_bytes(4)));

            // 4. Insert User
            $sql = "INSERT INTO users (full_name, email, password, user_unique_id) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $hashed_password, $user_unique_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $new_user_id = mysqli_insert_id($conn);

                    // 5. Create Primary Card (Wallet)
                    $card_name = "Main Wallet";
                    $balance = 0.00;
                    $is_primary = 1;
                    
                    $card_sql = "INSERT INTO cards (user_id, card_name, balance, is_primary) VALUES (?, ?, ?, ?)";
                    $card_stmt = mysqli_prepare($conn, $card_sql);
                    mysqli_stmt_bind_param($card_stmt, "isdi", $new_user_id, $card_name, $balance, $is_primary);
                    mysqli_stmt_execute($card_stmt);

                    $success = "Account created! <a href='login.php' class='font-bold underline'>Login here</a>";
                } else {
                    $error = "Registration failed: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Finance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8">
        
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">📊</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Finance Tracker</h1>
            <p class="text-gray-600">Create your account</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" id="full_name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                    placeholder="John Doe" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                    placeholder="john@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required minlength="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                        placeholder="••••••">
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none"
                        placeholder="••••••">
                </div>
            </div>
            
            <button type="submit" name="register"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300">
                Register Account
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="login.php" class="text-green-600 hover:text-green-700 font-medium">Login</a>
            </p>
        </div>
    </div>
</body>
</html>