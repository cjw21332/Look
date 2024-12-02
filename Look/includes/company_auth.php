<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    header('Location: company_login.php');
    exit();
}
?>