<?php
function isWithinLimit($conn, $user_id, $category, $new_amount) {
    $month = date('m');
    $year = date('Y');

    // FIX: Table is category_limits, column is category
    $res = mysqli_query($conn, "SELECT monthly_limit FROM category_limits WHERE user_id = '$user_id' AND category = '$category'");
    $limit_data = mysqli_fetch_assoc($res);

    if (!$limit_data) return true; 

    $limit = $limit_data['monthly_limit'];

    // FIX: Your expense table is 'expense' and category column is 'categorie'
    $spend_res = mysqli_query($conn, "SELECT SUM(amount) as total FROM expense 
                                      WHERE user_id = '$user_id' AND categorie = '$category' 
                                      AND MONTH(my_date) = '$month' AND YEAR(my_date) = '$year'");
    $spend_data = mysqli_fetch_assoc($spend_res);
    $total_spent = $spend_data['total'] ?? 0;

    return (($total_spent + $new_amount) <= $limit);
}
?>