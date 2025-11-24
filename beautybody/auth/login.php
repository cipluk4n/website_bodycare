<?php
session_start();
include 'beautybody/connection.php';
if(isset($_SESSION['username'])){
    header('Location: dashboard.php');
    exit;
}
$error='';
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if(empty($email) || empty($password)){
        $error = "Harap masukkan email dan password";
    }else{
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: beautybody/dashboard.php");
                exit;
            }else{
                $error = "Password salah!";
            }
        }else{
            $error = "Email tidak ditemukan!";
        }
    }
}
?>