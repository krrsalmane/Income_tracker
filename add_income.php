
<?php
if (isset($_POST["submit"])) {
    include "config/DB.php";

    $income_Source = $_POST["income_Source"];
    $date_Received = $_POST["date"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];

    $sql = "INSERT INTO income (Income_Source, amount, description, my_date) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    
    $stmt->bind_param("sdss", $income_Source, $amount, $description, $date_Received);

    if ($stmt->execute()) {
        header ("location: display_incomes.php");
    } else {
        echo "Error inserting record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
