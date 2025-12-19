<?php 
include "config/DB.php"; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // In a real app, we would also check if the card belongs to User 1
    $sql = "DELETE FROM cards WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: cards.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>