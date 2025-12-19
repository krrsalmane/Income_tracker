<?php
include "../config/DB.php";

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

$error = "";
$step = 1; // Step 1: Email/Password, Step 2: OTP

// STEP 1: Email and Password validation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check user in database
        $sql = "SELECT id, full_name, email, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                
                // Generate OTP
                $otp_code = generateOTP();
                $user_id = $user['id'];
                $ip_address = getUserIP();
                $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                // Save OTP to database
                $sql_otp = "INSERT INTO otp_codes (user_id, otp_code, ip_address, expires_at) VALUES (?, ?, ?, ?)";
                $stmt_otp = mysqli_prepare($conn, $sql_otp);
                mysqli_stmt_bind_param($stmt_otp, "isss", $user_id, $otp_code, $ip_address, $expires_at);
                mysqli_stmt_execute($stmt_otp);
                
                // Send OTP by email
                $email_subject = "Your Verification Code - Finance Tracker";
                $email_message = "
                    <html>
                    <body style='font-family: Arial, sans-serif;'>
                        <h2>Verification Code</h2>
                        <p>Hello {$user['full_name']},</p>
                        <p>Your verification code is:</p>
                        <h1 style='color: #10B981; font-size: 32px; letter-spacing: 5px;'>{$otp_code}</h1>
                        <p>This code expires in 10 minutes.</p>
                        <p>If you didn't request this code, please ignore this email.</p>
                    </body>
                    </html>
                ";
                
                sendEmail($user['email'], $email_subject, $email_message);
                
                // Store temp user ID in session
                $_SESSION['temp_user_id'] = $user_id;
                $_SESSION['temp_user_email'] = $user['email'];
                
                // Go to step 2 (OTP input)
                $step = 2;
                
            } else {
                $error = "Incorrect email or password.";
            }
        } else {
            $error = "Incorrect email or password.";
        }
    }
}

// STEP 2: OTP validation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    
    $otp_entered = cleanInput($_POST['otp_code']);
    $temp_user_id = $_SESSION['temp_user_id'] ?? null;
    
    if (empty($otp_entered)) {
        $error = "Please enter the OTP code.";
        $step = 2;
    } elseif (!$temp_user_id) {
        $error = "Session expired. Please start over.";
        $step = 1;
    } else {
        // Verify OTP
        $sql = "SELECT id FROM otp_codes 
                WHERE user_id = ? 
                AND otp_code = ? 
                AND is_used = FALSE 
                AND expires_at > NOW()
                ORDER BY created_at DESC 
                LIMIT 1";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $temp_user_id, $otp_entered);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $otp_record = mysqli_fetch_assoc($result);
            
            // Mark OTP as used
            $sql_update = "UPDATE otp_codes SET is_used = TRUE WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "i", $otp_record['id']);
            mysqli_stmt_execute($stmt_update);
            
            // Save known IP
            $ip_address = getUserIP();
            $sql_ip = "INSERT INTO known_ips (user_id, ip_address) 
                      VALUES (?, ?) 
                      ON DUPLICATE KEY UPDATE last_seen = CURRENT_TIMESTAMP";
            $stmt_ip = mysqli_prepare($conn, $sql_ip);
            mysqli_stmt_bind_param($stmt_ip, "is", $temp_user_id, $ip_address);
            mysqli_stmt_execute($stmt_ip);
            
            // Create user session
            $_SESSION['user_id'] = $temp_user_id;
            
            // Clean temp variables
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_user_email']);
            
            // Redirect to dashboard
            header("Location: ../index.php");
            exit();
            
        } else {
            $error = "Invalid or expired OTP code.";
            $step = 2;
        }
    }
}

// If returning to step 2 after error
if (isset($_SESSION['temp_user_id']) && empty($_POST)) {
    $step = 2;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Finance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8">
        
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">📊</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Finance Tracker</h1>
            <p class="text-gray-600">
                <?php echo ($step == 1) ? 'Login to your account' : 'Two-step verification'; ?>
            </p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <!-- STEP 1: Email and Password Form -->
            <form method="POST" action="" class="space-y-4">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                        placeholder="john@example.com"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                        placeholder="••••••••"
                    >
                </div>
                
                <button 
                    type="submit" 
                    name="login"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300"
                >
                    Login
                </button>
            </form>
            
        <?php else: ?>
            <!-- STEP 2: OTP Form -->
            <div class="mb-6 text-center">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">
                        📧 A verification code has been sent to<br>
                        <strong><?php echo htmlspecialchars($_SESSION['temp_user_email'] ?? ''); ?></strong>
                    </p>
                </div>
                <p class="text-gray-600 text-sm">
                    Please enter the 6-digit code to continue.
                </p>
            </div>
            
            <form method="POST" action="" class="space-y-4">
                
                <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-1 text-center">
                        Verification Code
                    </label>
                    <input 
                        type="text" 
                        name="otp_code" 
                        id="otp_code" 
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                        class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"
                        placeholder="000000"
                        autofocus
                    >
                </div>
                
                <button 
                    type="submit" 
                    name="verify_otp"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300"
                >
                    Verify
                </button>
                
                <div class="text-center">
                    <a href="login.php" class="text-sm text-gray-600 hover:text-gray-800">
                        ← Back to login
                    </a>
                </div>
            </form>
            
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="text-green-600 hover:text-green-700 font-medium">
                        Register
                    </a>
                </p>
            </div>
        <?php endif; ?>
        
    </div>
    
</body>
</html>