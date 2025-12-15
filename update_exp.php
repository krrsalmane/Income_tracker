
<?php
include "config/DB.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID is missing in the URL");
}
$id = $_GET["id"];

$spent = mysqli_query($conn,"SELECT * from expense where id =$id");
if (mysqli_num_rows($spent) == 0) {
    die("No record found for ID $id");
}
$detail = mysqli_fetch_assoc($spent);


if (isset($_POST["update"])) {
   $categorie = $_POST["category"];
    $date_Received = $_POST["date"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];

    mysqli_query($conn, "UPDATE expense SET  amount='$amount',description='$description',categorie='$categorie',my_date='$date_Received' WHERE id=$id");

    header("Location: index.php");
}

?>





<script src="https://cdn.tailwindcss.com"></script>


<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md border-t-4 border-red-500">

        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span class="text-red-500">📉</span> Edit Expense
        </h2>

        <form  method="POST" class="space-y-4">

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" id="amount" value="<?=$detail['amount']  ?>" step="0.01" min="0" placeholder="0.00" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text"
                    name="description"
                    id="description"
                    value="<?= $detail ['description']?>"
                    placeholder="e.g., Groceries, Salary, etc."
                    required
                    class="w-full h-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" id="date" value="<?= $detail['my_date'] ?>" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition">
            </div>

            <div>
                <label for="category"  class="block text-sm font-medium text-gray-700 mb-1">Category </label>
                <select name="category" id="category"  class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-red-500 outline-none">
                    <option value="food" <?= $detail["categorie"] === "food" ? "selected" : ""  ?>>Food</option>
                    <option value="housing" <?=$detail["categorie"] === "housing" ? "selected" :"" ?>>Housing</option>
                    <option value="transport" <?=$detail["categorie" ] === "transport" ? "selected" : ""  ?>>Transport</option>
                    <option value="leisure" <?=$detail["categorie"] === "leisure" ? "selected" :""  ?>>Leisure</option>
                    <option value="other" <?=$detail["categorie"]=== "other" ? "selected" : ""  ?>>Other</option>
                </select>
            </div>

            <button type="submit" name="update"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md transform hover:-translate-y-0.5">
                Save Expense
            </button>

            <div class="text-center mt-2">
                <a href="index.php" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            </div>

        </form>
    </div>

</body>

</html>
