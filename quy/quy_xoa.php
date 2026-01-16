<?php
include '../header.php';
include '../connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM quy_clb WHERE id = $id");

echo "<script>location.href='quy_clb.php'</script>";
