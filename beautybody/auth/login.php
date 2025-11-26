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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | beautybody</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .auth-card {
            background: var(--secondary-bg);
            padding: 3rem;
            border: 1px solid var(--primary-color);
            max-width: 450px;
            width: 90%;
            margin: 0 auto;
        }
        
        .auth-card h3 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .auth-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .auth-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--accent-color);
        }
        
        .auth-form input {
            width: 100%;
            padding: 0.75rem;
            background: var(--background-color);
            border: 1px solid var(--primary-color);
            color: var(--text-color);
            font-size: 1rem;
        }
        
        .auth-form input:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        
        .auth-form button {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            margin-top: 1rem;
        }
        
        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .auth-link a {
            color: var(--primary-color);
            text-decoration: none;
            border-bottom: 1px solid var(--primary-color);
            padding-bottom: 2px;
        }
        
        .auth-link a:hover {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .alert-error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            padding: 0.75rem;
            margin-bottom: 1.5rem;
            color: #ff6b6b;
        }
    </style>
</head>
<body class="auth-container">
    <header id="main-header">
        <div class="container">
            <h1 class="logo">beautybody</h1>
        </div>
    </header>

    <main class="auth-main">
        <div class="container">
            <div class="auth-card">
                <h3>Login</h3>
                
                <?php if($error): ?>
                    <div class="alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <div class="auth-link">
                    <p>Belum punya akun? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>Copyright &copy; 2025 BEAUTYBODY, Inc.</p>
        </div>
    </footer>
</body>
</html>