<?php 
include "config/DB.php"; 

// Fetch Income Data
if ($conn) {
    $sql_income = "SELECT id, Income_Source, amount, description, my_date FROM income ORDER BY id DESC";
    $result_income = mysqli_query($conn, $sql_income);
    
    if (!$result_income) {
        die("Error retrieving income data: " . mysqli_error($conn));
    }
    
    $income_count = mysqli_num_rows($result_income);
    
    // Fetch Expense Data
    $sql_expense = "SELECT id, amount, description, categorie, my_date FROM expense ORDER BY id DESC";
    $result_expense = mysqli_query($conn, $sql_expense);
    
    if (!$result_expense) {
        die("Error retrieving expense data: " . mysqli_error($conn));
    }
    
    $expense_count = mysqli_num_rows($result_expense);
    
    // Calculate totals
    $total_income = 0;
    $total_expense = 0;
    
    // Get total income
    $sql_total_income = "SELECT SUM(amount) as total FROM income";
    $result_total_income = mysqli_query($conn, $sql_total_income);
    if ($row = mysqli_fetch_assoc($result_total_income)) {
        $total_income = $row['total'] ?? 0;
    }
    
    // Get total expense
    $sql_total_expense = "SELECT SUM(amount) as total FROM expense";
    $result_total_expense = mysqli_query($conn, $sql_total_expense);
    if ($row = mysqli_fetch_assoc($result_total_expense)) {
        $total_expense = $row['total'] ?? 0;
    }
    
    $current_balance = $total_income - $total_expense;
    
    // Get expense data by category for chart
    $sql_expense_by_category = "SELECT categorie, SUM(amount) as total FROM expense GROUP BY categorie";
    $result_expense_by_category = mysqli_query($conn, $sql_expense_by_category);
    
    $categories = [];
    $category_amounts = [];
    
    while ($row = mysqli_fetch_assoc($result_expense_by_category)) {
        $categories[] = ucfirst($row['categorie']);
        $category_amounts[] = floatval($row['total']);
    }
    
    // Get monthly income and expense data for line chart
    $sql_monthly = "
        SELECT 
            DATE_FORMAT(my_date, '%Y-%m') as month,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
        FROM (
            SELECT my_date, amount, 'income' as type FROM income
            UNION ALL
            SELECT my_date, amount, 'expense' as type FROM expense
        ) as combined
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6
    ";
    $result_monthly = mysqli_query($conn, $sql_monthly);
    
    $months = [];
    $monthly_income = [];
    $monthly_expense = [];
    
    while ($row = mysqli_fetch_assoc($result_monthly)) {
        $months[] = date('M Y', strtotime($row['month'] . '-01'));
        $monthly_income[] = floatval($row['income']);
        $monthly_expense[] = floatval($row['expense']);
    }
    
    // Reverse arrays to show oldest to newest
    $months = array_reverse($months);
    $monthly_income = array_reverse($monthly_income);
    $monthly_expense = array_reverse($monthly_expense);
    
    // Reset result pointers for tables
    mysqli_data_seek($result_income, 0);
    mysqli_data_seek($result_expense, 0);
    
} else {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Finance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        .btn-income { @apply bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-md; }
        .btn-expense { @apply bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg shadow-md; }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            animation: slideIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-2xl mr-2">📊</span>
                    <h1 class="text-xl font-bold text-gray-900">Finance Tracker</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-red-500 hover:text-red-700 text-sm font-medium">
                        Déconnexion
                    </button>
                
                </div>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Revenus</p>
                <p class="mt-1 text-3xl font-extrabold text-gray-900">
                    $<?php echo number_format($total_income, 2); ?>
                </p>
                <p class="text-sm text-green-500"><?php echo $income_count; ?> revenus enregistrés</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Dépenses</p>
                <p class="mt-1 text-3xl font-extrabold text-gray-900">
                    -$<?php echo number_format($total_expense, 2); ?>
                </p>
                <p class="text-sm text-red-500"><?php echo $expense_count; ?> dépenses enregistrées</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Solde Actuel</p>
                <p class="mt-1 text-3xl font-extrabold text-blue-600">
                    $<?php echo number_format($current_balance, 2); ?>
                </p>
                <p class="text-sm text-gray-500">Revenus - Dépenses</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pie Chart - Expenses by Category -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Dépenses par Catégorie</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="expensePieChart"></canvas>
                </div>
            </div>

            <!-- Line Chart - Monthly Trends -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Tendances Mensuelles</h3>
                <div class="h-64">
                    <canvas id="monthlyLineChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">

            <!-- Income Section -->
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <span class="text-green-600">💰</span> Income History
                    </h2>
                    <button class="btn-income" onclick="openIncomeModal()">
                        + Ajouter Revenu
                    </button>
                </div>
                
                <p class="text-sm text-gray-500 mb-4">Showing <strong><?php echo $income_count; ?></strong> total income records.</p>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Source</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if ($income_count > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_income)): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['Income_Source']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-green-600">$<?php echo number_format($row['amount'], 2); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date('Y-m-d', strtotime($row['my_date'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 transition duration-150 mr-3">Edit</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition duration-150 ml-3">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No income records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expense Section -->
            <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-red-500">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                        <span class="text-red-600">💸</span> Expense History
                    </h2>
                    <button class="btn-expense" onclick="openExpenseModal()">
                        + Ajouter Dépense
                    </button>
                </div>
                
                <p class="text-sm text-gray-500 mb-4">Showing <strong><?php echo $expense_count; ?></strong> total expense records.</p>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if ($expense_count > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_expense)): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['categorie']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-red-600">$<?php echo number_format($row['amount'], 2); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo date('Y-m-d', strtotime($row['my_date'])); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="update_exp.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 transition duration-150 mr-3">Edit</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="delete_expenses.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 transition duration-150 ml-3">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No expense records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Income Modal -->
    <div id="incomeModal" class="modal">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-t-4 border-green-500 m-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="text-green-500">📈</span> Add Income
            </h2>

            <form action="add_income.php" method="post" class="space-y-4">

                <div>
                    <label for="income_Source" class="block text-sm font-medium text-gray-700 mb-1">Income Source</label>
                    <input type="text" name="income_Source" id="income_Source" placeholder="e.g., Salary, Freelance..." required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" placeholder="0.00" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text"
                        name="description"
                        id="description"
                        placeholder="e.g., Groceries, Salary, etc."
                        required
                        class="w-full h-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date Received</label>
                    <input type="date" name="date" id="date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                </div>

                <button type="submit" name="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:-translate-y-0.5">
                    Save Income
                </button>

                <div class="text-center mt-2">
                    <button type="button" onclick="closeIncomeModal()" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Expense Modal -->
    <div id="expenseModal" class="modal">
        <div class="modal-content bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-t-4 border-red-500 m-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="text-red-500">📉</span> Add Expense
            </h2>

            <form action="add_expenses.php" method="POST" class="space-y-4">

                <div>
                    <label for="expense_amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" name="amount" id="expense_amount" step="0.01" min="0" placeholder="0.00" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>
                
                <div>
                    <label for="expense_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text"
                        name="description"
                        id="expense_description"
                        placeholder="e.g., Groceries, Rent, etc."
                        required
                        class="w-full h-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>

                <div>
                    <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" id="expense_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
                        <option value="food">Food</option>
                        <option value="housing">Housing</option>
                        <option value="transport">Transport</option>
                        <option value="leisure">Leisure</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <button type="submit" name="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:-translate-y-0.5">
                    Save Expense
                </button>

                <div class="text-center mt-2">
                    <button type="button" onclick="closeExpenseModal()" class="text-sm text-gray-500 hover:text-gray-700">Cancel</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Pie Chart - Expenses by Category
        const pieCtx = document.getElementById('expensePieChart').getContext('2d');
        const expensePieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode($category_amounts); ?>,
                    backgroundColor: [
                        '#EF4444', // red
                        '#F59E0B', // amber
                        '#10B981', // green
                        '#3B82F6', // blue
                        '#8B5CF6', // violet
                        '#EC4899', // pink
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Line Chart - Monthly Trends
        const lineCtx = document.getElementById('monthlyLineChart').getContext('2d');
        const monthlyLineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Revenus',
                        data: <?php echo json_encode($monthly_income); ?>,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    },
                    {
                        label: 'Dépenses',
                        data: <?php echo json_encode($monthly_expense); ?>,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        function openIncomeModal() {
            document.getElementById('incomeModal').classList.add('show');
        }

        function closeIncomeModal() {
            document.getElementById('incomeModal').classList.remove('show');
        }

        function openExpenseModal() {
            document.getElementById('expenseModal').classList.add('show');
        }

        function closeExpenseModal() {
            document.getElementById('expenseModal').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const incomeModal = document.getElementById('incomeModal');
            const expenseModal = document.getElementById('expenseModal');
            if (event.target === incomeModal) {
                closeIncomeModal();
            }
            if (event.target === expenseModal) {
                closeExpenseModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeIncomeModal();
                closeExpenseModal();
            }
        });
    </script>

</body>
</html>