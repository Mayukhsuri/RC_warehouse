<?php
    
    $en = 800498237;  // Numeric - Employee 800 Number
    $fn = "Mayuhk";   // First Name
    $ln = "Suri";  // Last Name
    $rd = 1;        // Days until password reset is required

$salt = "25uoli1gIckxxO02887xwQprnzEEd5Yu";
$input = 'Password1';

//echo "[" . hash("sha256",$salt.$input) . "]";


    include('includes/connection.php');
    $resetdate = time() + (86400*rd);
sqlsrv_query($connection, "INSERT INTO users (EmployeeID, FirstName, LastName, UserGroupID, Password, PasswordResetDate, Active) VALUES ($en, '$fn', '$ln', 1, '2c5d845ca59c572267a75cf0bba32e49877723f1ee4ca025a1ae3f7120442fe1', $resetdate, 1)");

/*
// 2c5d845ca59c572267a75cf0bba32e49877723f1ee4ca025a1ae3f7120442fe1
// 2c5d845ca59c572267a75cf0bba32e49877723f1ee4ca025a1ae3f7120442fe1

*/
?>