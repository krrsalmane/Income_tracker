<?php 

include "config/DB.php"; 


if ($conn) {
    $sql_query = "SELECT id, Income_Source, amount, description, my_date FROM income ORDER BY id DESC";
    $result = mysqli_query($conn, $sql_query);

    if (!$result) {
        die("Error retrieving data: " . mysqli_error($conn));
    }

    $row_count = mysqli_num_rows($result);
} else {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center min-h-screen p-4">

    <div class="w-full max-w-6xl bg-white p-8 rounded-xl shadow-2xl border-t-8 border-green-600 mt-8 mb-8">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
                <span class="text-green-600">💰</span> Income History
            </h1>
            <a href="incomes.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 shadow-md">
                + Add New Income
            </a>
        </div>
        
        <p class="text-sm text-gray-500 mb-4">Showing <strong><?= $row_count ?></strong> total income records.</p>

        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

                    <?php if ($row_count > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($row['Income_Source']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-green-600">$<?= number_format($row['amount'], 2) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= htmlspecialchars($row['description']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= date('Y-m-d', strtotime($row['my_date'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="edit.php?id=<?= $row['id'] ?>  " class="text-blue-600 hover:text-blue-900 transition duration-150 mr-3">Edit</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="delete.php?id=<?= $row['id'] ?>  " class="text-red-600 hover:text-red-900 transition duration-150 ml-3">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">No income records found.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
        
    </div>

</body>
</html>
