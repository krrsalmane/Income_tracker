<?php 
include "../config/DB.php"; 
$user_id = 1;

if (isset($_POST['save_limit'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $limit = mysqli_real_escape_string($conn, $_POST['monthly_limit']);

    // FIX: Match table and column names
    $sql = "INSERT INTO category_limits (user_id, category, monthly_limit) 
            VALUES ('$user_id', '$category', '$limit')
            ON DUPLICATE KEY UPDATE monthly_limit = '$limit'";

    mysqli_query($conn, $sql);
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM category_limits WHERE id = '$id' AND user_id = '$user_id'");
    header("Location: index.php");
    exit();
}
?>