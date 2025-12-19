<?php 
include "../config/DB.php"; 
$user_id = 1; 

$sql = "SELECT * FROM cards WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cards</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">My Bank Cards</h1>
            <a href="add_card.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold">
                + Add New Card
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php while($card = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white border p-6 rounded-2xl shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($card['card_name']); ?></h3>
                    <p class="text-2xl font-bold text-blue-600 mt-2"><?php echo number_format($card['balance'], 2); ?> DH</p>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-8">
            <a href="../index.php" class="text-gray-600 hover:text-blue-600">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>