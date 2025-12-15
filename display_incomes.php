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
