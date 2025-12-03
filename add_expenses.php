<?php



if (isset($_POST["submit"])) {
    include "config/DB.php";

    $categorie = $_POST["category"];
    $date_Received = $_POST["date"];
    $description = $_POST["description"];
    $amount = $_POST["amount"];

    $sql = "INSERT INTO expense ( amount, description, categorie,my_date) 
            VALUES (?, ?, ?, ?)";

    $exp = $conn->prepare($sql);

    
    $exp->bind_param( "dsss" ,$amount, $description,$categorie, $date_Received);

    if ($exp->execute()) {
        header ("location: display_expenses.php");
    } else {
        echo "Error inserting record: " . $exp->error;
    }

    $exp->close();
    $conn->close();
}
?>