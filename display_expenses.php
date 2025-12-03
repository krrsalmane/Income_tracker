<?php
include "config/DB.php"; 
if($conn){

   
    $sql_query = "SELECT id,amount, description, categorie,my_date FROM expense order by id desc";
    $spent = mysqli_query($conn,$sql_query);
    if (!$spent) {
        die("Error retrieving data: " . mysqli_error($conn));
    }

    $row_count = mysqli_num_rows($spent);
} else {
    die("Database connection failed.");
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center min-h-screen p-4">

    <div class="w-full max-w-6xl bg-white p-8 rounded-xl shadow-2xl border-t-8 border-red-600 mt-8 mb-8">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
                <span class="text-red-600">💸</span> Expense History
            </h1>
            <a href="expenses.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 shadow-md">
                + Add New Expense
            </a>
        </div>
        
        <p class="text-sm text-gray-500 mb-4">Showing **<?php echo $row_count; ?>** total expense records.</p>

        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider w-36">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">

                    <?php
                    if ($row_count > 0) {
                        while ($row = mysqli_fetch_assoc($spent)) {
                            echo "
                                <tr>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-700'>" . htmlspecialchars($row['categorie']) . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-red-600'>$" . number_format($row['amount'], 2) . "</td>
                                    <td class='px-6 py-4 text-sm text-gray-500 max-w-xs truncate'>" . htmlspecialchars($row['description']) . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-700'>" . date('Y-m-d', strtotime($row['my_date'])) . "</td>
                                    <td class='px-6 py-4 whitespace-nowrap text-center text-sm font-medium'>
                                        <a href='edit.php?id={$row['id']}' class='text-blue-600 hover:text-blue-900 transition duration-150 mr-3'>Edit</a>
                                        <span class='text-gray-300'>|</span>
                                        <a href='delete.php?id={$row['id']}' class='text-red-600 hover:text-red-900 transition duration-150 ml-3'>Delete</a>
                                    </td>
                                </tr>
                            ";
                        }
                    } else {
                        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 py-10">No expense records found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
    </div>

</body>
</html>