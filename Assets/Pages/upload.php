<?php
use Dotenv\Dotenv;
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    echo $username;
} else {
    header("Location: ../../../index.php");
    exit();
}

$username = htmlspecialchars($_GET['un']);

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Database connection
$connection = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit']) && isset($_FILES['profile-pic'])) {
    echo "<pre>";
    print_r($_FILES['profile-pic']);
    echo "</pre>";

    $img_name = $_FILES['profile-pic']['name'];
    $img_size = $_FILES['profile-pic']['size'];
    $tmp_name = $_FILES['profile-pic']['tmp_name'];
    $error = $_FILES['profile-pic']['error'];

    if ($error === 0) {
        if ($img_size > 500000) {
            $em = "Sorry, your file is too large.";
            header("Location: ./Admin/profile.php?error=$em");
        } else {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = array("jpg", "jpeg", "png");

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $img_upload_path = '../img/profilePics/' . $new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);

                // Insert into Database
                $sql = "INSERT INTO images(userName,image_url) VALUES('$username','$new_img_name')";
                if (mysqli_query($connection, $sql)) {
                    echo "<p class='text-white'>Records inserted successfully.</p>";
                } else {
                    echo "<p class='text-white'>ERROR: Could not able to execute $sql. " . mysqli_error($connection) . "</p>";
                    echo "<p class='text-white'>ERROR: Could not able to execute $sql. " . mysqli_error($conn) . "</p>";
                }
                header("Location: ./Admin/profile.php?success=Image uploaded successfully");
            } else {
                $em = "You can't upload files of this type";
                header("Location: ./Admin/profile.php?error=$em");
            }
        }
    } else {
        $em = "unknown error occurred!";
        header("Location: ./Admin/profile.php?error=$em");
    }

} else {
    header("Location: ./Admin/profile.php?error=Please select an img file");
}