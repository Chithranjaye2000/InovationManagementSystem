<?php
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // header("Location: ../../../index.php");
    echo "<script>window.location.href='../../../index.php';</script>";
    exit();
}

include '../dbconnection.php';
// Insert user skills into database
$sql = "INSERT INTO user_skills (userName,skill) VALUES ('$username','$skill')";
if ($connection->query($sql) === TRUE) {
    $em = "Skill update successfully.";
    header("Location: ./user-management.php?skillupdatestatus=success&msg=$em");
} else {
    $em = "Skill update failed.";
    header("Location: ./user-management.php?skillupdatestatus=error&msg=$em");
}
{

$connection->close();
}