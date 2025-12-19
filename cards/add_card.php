<?php 
include "../config/DB.php"; // Goes up one level to find config
$user_id = 1; 

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['card_name']);
    $last4 = mysqli_real_escape_string($conn, $_POST['card_number_last4']);
    $balance = mysqli_real_escape_string($conn, $_POST['balance']);
    $is_primary = isset($_POST['is_primary']) ? 1 : 0;

    $sql = "INSERT INTO cards (user_id, card_name, card_number_last4, balance, is_primary) 
            VALUES ('$user_id', '$name', '$last4', '$balance', '$is_primary')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to cards.php in the SAME folder
        header("Location: cards.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Card - Finance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">💳 Add New Card</h2>
        
        <form action="add_card.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Choose Bank Name</label>
                <select name="card_name" required class="w-full mt-1 border p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Select Bank --</option>
                    <option value="CIH Bank">CIH Bank</option>
                    <option value="Banque Populaire">Banque Populaire</option>
                    <option value="Attijariwafa Bank">Attijariwafa Bank</option>
                    <option value="BMCE Bank">BMCE Bank</option>
                    <option value="Cash / Wallet">Cash / Wallet</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Initial Balance (DH)</label>
                <input type="number" step="0.01" name="balance" placeholder="0.00" required 
                       class="w-full mt-1 border p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" name="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                Save Bank Card
            </button>
        </form>
    </div>
</body>
</html>