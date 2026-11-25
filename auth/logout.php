<?php
// auth/logout.php
require_once '../config/database.php';
require_once '../classes/Auth.php';

$db = new Database();
$pdo = $db->getConnection();
$auth = new Auth($pdo);

$auth->logout();

header("Location: ../index.php");
exit();
?>
