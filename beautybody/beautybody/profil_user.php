<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ./auth/login.php');
    exit();
}

include 'connection.php';

// Get user info
$user_email = $_SESSION['email'];
$user_query = $connection->prepare("SELECT id, username, email, address, phone_number, profil_pic, created_at FROM users WHERE email = ?");
$user_query->bind_param("s", $user_email);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

$error_message = "";
$success_message = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    
    // Handle profile picture upload
    $profil_pic = $user['profil_pic']; // keep existing if no new upload
    
    if (isset($_FILES['profil_pic']) && $_FILES['profil_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profil_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            // Create directory if not exists
            if (!is_dir('./photo_profile')) {
                mkdir('./photo_profile', 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profil_pic']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user['id'] . '_' . time() . '.' . $file_extension;
            $upload_path = './photo_profile/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profil_pic']['tmp_name'], $upload_path)) {
                // Delete old profile picture if exists
                if (!empty($user['profil_pic']) && file_exists('./photo_profile/' . $user['profil_pic'])) {
                    unlink('./photo_profile/' . $user['profil_pic']);
                }
                $profil_pic = $new_filename;
            } else {
                $error_message = "Gagal mengupload foto profil.";
            }
        } else {
            $error_message = "Hanya file JPEG, JPG, PNG, dan GIF yang diizinkan.";
        }
    }
    
    // Update user data
    $update_query = $connection->prepare("UPDATE users SET username = ?, email = ?, address = ?, phone_number = ?, profil_pic = ? WHERE id = ?");
    $update_query->bind_param("sssssi", $username, $email, $address, $phone_number, $profil_pic, $user['id']);
    
    if ($update_query->execute()) {
        $_SESSION['email'] = $email; // Update session email if changed
        $success_message = "Profil berhasil diperbarui!";
        // Refresh user data
        $user_query->execute();
        $user_result = $user_query->get_result();
        $user = $user_result->fetch_assoc();
    } else {
        $error_message = "Terjadi kesalahan saat memperbarui profil.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $verify_query = $connection->prepare("SELECT password FROM users WHERE id = ?");
    $verify_query->bind_param("i", $user['id']);
    $verify_query->execute();
    $verify_result = $verify_query->get_result();
    $user_data = $verify_result->fetch_assoc();
    
    if (password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $password_query = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
                $password_query->bind_param("si", $hashed_password, $user['id']);
                
                if ($password_query->execute()) {
                    $success_message = "Password berhasil diubah!";
                } else {
                    $error_message = "Terjadi kesalahan saat mengubah password.";
                }
            } else {
                $error_message = "Password baru harus minimal 6 karakter.";
            }
        } else {
            $error_message = "Password baru dan konfirmasi password tidak cocok.";
        }
    } else {
        $error_message = "Password saat ini salah.";
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $password = $_POST['delete_password'];
    
    // Verify password
    $verify_query = $connection->prepare("SELECT password FROM users WHERE id = ?");
    $verify_query->bind_param("i", $user['id']);
    $verify_query->execute();
    $verify_result = $verify_query->get_result();
    $user_data = $verify_result->fetch_assoc();
    
    if (password_verify($password, $user_data['password'])) {
        // Delete profile picture if exists
        if (!empty($user['profil_pic']) && file_exists('./photo_profile/' . $user['profil_pic'])) {
            unlink('./photo_profile/' . $user['profil_pic']);
        }
        
        // Delete user account
        $delete_query = $connection->prepare("DELETE FROM users WHERE id = ?");
        $delete_query->bind_param("i", $user['id']);
        
        if ($delete_query->execute()) {
            session_destroy();
            header('Location: ./auth/login.php');
            exit();
        } else {
            $error_message = "Terjadi kesalahan saat menghapus akun.";
        }
    } else {
        $error_message = "Password salah. Akun tidak dapat dihapus.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User | beautybody</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            margin-bottom: 1rem;
        }

        .profile-content {
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-label {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--secondary-bg);
            border: 2px dashed var(--primary-color);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            background: var(--primary-color);
            color: white;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .account-info {
            background: var(--secondary-bg);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .account-info p {
            margin: 0.5rem 0;
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
            }
            
            .profile-header {
                padding: 1.5rem;
            }
            
            .profile-picture {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
            <h1 class="logo">beautybody</h1>
            <nav>
                <a href="dashboard.php#services">Services</a>
                <a href="dashboard.php#schedule">Schedule</a>
                <a href="./booking.php">Booking</a>
                <?php if (isset($_SESSION['email'])) { ?>
                    <a href="./history.php">History</a>
                    <a href="profil_user.php" class="active">Profil</a>
                    <a href="./auth/logout.php">Logout</a>
                <?php } else { ?>
                    <a href="./auth/register.php" class="nav-btn">Login / Register</a>
                <?php } ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="section-padded">
            <div class="container">
                <div class="profile-container">
                    <div class="profile-header">
                        <img src="<?php echo !empty($user['profil_pic']) ? './photo_profile/' . $user['profil_pic'] : './photo/profile_placeholder.jpg'; ?>" 
                             alt="Profile Picture" class="profile-picture">
                        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <div class="profile-content">
                        <?php if ($error_message): ?>
                            <div class="alert alert-error"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <!-- Account Information -->
                        <div class="account-info">
                            <h3>Informasi Akun</h3>
                            <p><strong>Tanggal Bergabung:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                            <p><strong>ID Pengguna:</strong> <?php echo $user['id']; ?></p>
                        </div>

                        <!-- Edit Profile Form -->
                        <div class="form-section">
                            <h3>Edit Profil</h3>
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="username">Nama Pengguna</label>
                                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Alamat</label>
                                    <textarea id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Nomor Telepon</label>
                                    <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Foto Profil</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" id="profil_pic" name="profil_pic" accept="image/jpeg,image/jpg,image/png,image/gif">
                                        <label for="profil_pic" class="file-input-label">Pilih Foto Profil</label>
                                    </div>
                                    <small>Format: JPEG, JPG, PNG, GIF (Maksimal 2MB)</small>
                                </div>

                                <button type="submit" name="update_profile" class="btn btn-primary">Perbarui Profil</button>
                            </form>
                        </div>

                        <!-- Change Password Form -->
                        <div class="form-section">
                            <h3>Ubah Password</h3>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="current_password">Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">Password Baru</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Password Baru</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-secondary">Ubah Password</button>
                            </form>
                        </div>

                        <!-- Delete Account Form -->
                        <div class="form-section">
                            <h3>Hapus Akun</h3>
                            <p style="color: #dc3545; margin-bottom: 1rem;">
                                <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.
                            </p>
                            <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!');">
                                <div class="form-group">
                                    <label for="delete_password">Masukkan Password Anda untuk Konfirmasi</label>
                                    <input type="password" id="delete_password" name="delete_password" required>
                                </div>

                                <button type="submit" name="delete_account" class="btn btn-danger">Hapus Akun Permanen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>Copyright &copy; 2025 BEAUTYBODY, Inc.</p>
        </div>
    </footer>

    <script>
        // Preview image before upload
        document.getElementById('profil_pic').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const passwordInputs = this.querySelectorAll('input[type="password"]');
                passwordInputs.forEach(input => {
                    if (input.value.length > 0 && input.value.length < 6) {
                        e.preventDefault();
                        alert('Password harus minimal 6 karakter.');
                        return;
                    }
                });
            });
        });
    </script>
</body>
</html>