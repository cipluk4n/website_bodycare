<?php
session_start();
require_once '../connection.php';

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
        $sql = "INSERT INTO users (username, email, password) VALUES (?,?,?)";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | beautybody</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        .card{
            margin-top: 200px;
            padding:20px;
            box-shadow: 0px 14px 14px rgba(0,0,0,0.1);
        }
        .card .title{
            padding-bottom:10px;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="title">Register</h3>
                    <!-- error check: username,password -->
                    <?php if($error):?>
                        <div class="alert alert-danger"><?php echo $error?></div>
                    <?php endif;?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="text" name="email" class="form-control" required>
                        </div>
                        <div class="pw mb-3>
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div> <br>
                            <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>
                    <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
