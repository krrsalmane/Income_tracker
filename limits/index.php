<?php 
include "../config/DB.php"; 
$user_id = 1; 

$sql = "SELECT * FROM category_limits WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mes Limites</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🎯 Gestion des Limites</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
                <h2 class="text-xl font-bold mb-4">Définir une limite</h2>
                <form action="update_limit.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-red-500 outline-none">
                    <option value="food">Food</option>
                    <option value="housing">Housing</option>
                    <option value="transport">Transport</option>
                    <option value="leisure">Leisure</option>
                    <option value="other">Other</option>
                </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Limite Mensuelle (DH)</label>
                        <input type="number" name="monthly_limit" required class="w-full mt-1 border p-2 rounded-lg">
                    </div>
                    <button type="submit" name="save_limit" class="w-full bg-purple-600 text-white py-2 rounded-lg font-bold hover:bg-purple-700">
                        Enregistrer
                    </button>
                </form>
            </div>

            <div class="md:col-span-2 space-y-4">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($row['category']); ?></h3>
                            <p class="text-gray-500 text-sm">Limite: <?php echo number_format($row['monthly_limit'], 2); ?> DH</p>
                        </div>
                        <a href="update_limit.php?delete_id=<?php echo $row['id']; ?>" class="text-red-500 text-sm hover:underline" onclick="return confirm('Supprimer?')">Supprimer</a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-400 text-center py-10 border-2 border-dashed rounded-xl">Aucune limite définie.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>