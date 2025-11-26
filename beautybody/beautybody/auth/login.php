<?php
session_start();
require_once '../connection.php';
if(isset($_SESSION['username'])){
    header('Location: ../dashboard.php');
    exit;
}
$error='';
if($_SERVER['REQUEST_METHOD'] == "POST"){
    // $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if(empty($email) || empty($password)){
        $error = "Harap masukkan email dan password";
    }else{
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header("Location: ../dashboard.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | beautybody</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
           background: #FFF8F0; 
        }
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
                    <h3 class="title">Login</h3>
                    <!-- error check: username,pw -->
                    <?php if($error):?>
                        <div class="alert alert-danger"><?php echo $error;?></div>
                    <?php endif;?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="text" name="email" class="form-control" required value="<?php
                            isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';?>">
                        </div>
                        <div class="mb-3>
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div> <br>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="text-center mt-3">Belum punya akun? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>