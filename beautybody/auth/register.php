<?php
session_start();
include 'beautybody/connection.php';

if(isset($_SESSION['username'])){
    header('Location: beautybody/dashboard.php');
    exit;
}
$error='';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if(empty($username) || empty($password)){
        $error = "Username dan password tidak boleh kosong!";
    }else{
        $hash_pw = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, ,email, password) VALUES (?,?,?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hash_pw);
        if($stmt->execute()){
            header("Location: login.php");
            exit;
        }else{
            $error = 'Username sudah digunakan!';
        }
    }
}
?>