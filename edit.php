<?php
include "config/DB.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID is missing in the URL");
}
$id = $_GET["id"];

$result = mysqli_query($conn,"SELECT * from income where id =$id");
if (mysqli_num_rows($result) == 0) {
    die("No record found for ID $id");
}
$data = mysqli_fetch_assoc($result);


if (isset($_POST["update"])) {
   $income_Source = $_POST["income_Source"];
    $date_Received = $_POST["date"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];

    mysqli_query($conn, "UPDATE income SET Income_Source='$income_Source',description='$description', my_date='$date_Received', amount='$amount' WHERE id=$id");

    header("Location: index.php");
}

?>
<script src="https://cdn.tailwindcss.com"></script>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

 <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-t-4 border-green-500">

        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span class="text-green-500">📈</span> Add Income
        </h2>

 <form  method="post" class="space-y-4">

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Income Source</label>
                <input type="text" name="income_Source" id="income_Source" value="<?= $data['Income_Source'] ?>" placeholder="e.g., Salary, Freelance..." required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" id="amount" value="<?= $data['amount'] ?>" step="0.01" min="0" placeholder="0.00" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text"
                    name="description"
                    id="description"
                    placeholder="e.g., Groceries, Salary, etc." value="<?= $data['description']?>"
                    required
                    class="w-full h-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date Received</label>
                <input type="date" name="date" id="date" value="<?= $data['my_date'] ?>" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
            </div>

            <button type="submit" name="update"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:-translate-y-0.5">
                Save Income
            </button>

            <div class="text-center mt-2">
                <a href="index.php" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            </div>

        </form>
 </div>
</body>