<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Booking Body Treatment | beautybody</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header id="main-header">
        <div class="container">
            <h1 class="logo">beautybody</h1>
            <nav>
                <a href="#services">Services</a>
                <a href="#booking">Schedule & Booking</a>
                <?php if(isset($_SESSION['email'])){ ?>
                    <a href="#history">History</a>
                    <a href="./auth/logout.php">Logout</a>  
                <?php } else { ?>
                    <a href="./auth/register.php" class="nav-btn">Login / Register</a>
                <?php } ?>
            </nav>
            
        </div>
    </header>

    <main>
        <!-- <video width="900" height="3500" autoplay loop muted plays-inline>
                <source src="video/bg.mp4" type="video/mp4">
        </video> -->
        <section id="hero" class="section-padded">
            <div class="container">
                <h2>Your Self-Care, Now Easier</h2>
                <p>Order Body massage & Relaxation Spa, Hair & Scalp Care, Nail Art & Therapy services with experts in just a few clicks..</p>
                <a href="#booking" class="btn btn-primary">Book Now!</a>
                
            </div>
        </section>
        
        <section id="services" class="section-padded bg-light">
            <div class="container">
                <h3>Our services</h3>
                <div class="image-grid">
        
                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/massage1.png" alt="massage and spa">
                                <p class="image-description">massage and spa</p>
                            </div>
                        </a>
                    </div>
                    
                    
                    <div class="video-item">
                        <a href="#booking" aria-label="sauna">
                            <video width="551.25" height="312.95" loop autoplay muted> 
                                <source src="video/sauna.mp4" type="video/mp4"> 
                            </video>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/sauna.jpg" alt="sauna">
                                <p class="image-description">sauna relaxation</p>
                            </div>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/laser1.png" alt="laser treatment">
                                <p class="image-description">laser treatment</p>
                            </div>
                        </a>
                    </div>
                    
                    <div class="video-item">
                        <a href="#booking" aria-label="laser treatment">
                            <video width="551.25" height="312.95" loop autoplay muted> 
                                <source src="video/laser treatment.mp4" type="video/mp4"> 
                            </video>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/hair n scalp care.png" alt="hair n scalp care">
                                <p class="image-description">hair & scalp care</p>
                            </div>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/nail polish.jpg" alt="nail polish & art">
                                <p class="image-description">nail polish & art</p>
                            </div>
                        </a>
                    </div>

                    <div class="video-item">
                        <a href="#booking" aria-label="Lihat Video dan Pesan Skin Treatment">
                            <video width="551.25" height="312.95" loop autoplay muted> 
                                <source src="video/nail polish.mp4" type="video/mp4"> 
                            </video>
                        </a>
                    </div>
                    
                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/mask.jpg" alt="face mask">
                                <p class="image-description">face mask</p>
                            </div>
                        </a>
                    </div>
                    <!-- photo and video attributed by: pixabay and pexels -->
                    <!-- Video by cottonbro studio: https://www.pexels.com/video/a-person-massaging-a-woman-s-face-7582846/ -->
                    
                </div>
            </div>
        </section>
        
        <section id="booking" class="section-padded bg-light">
            <div class="container">
                <h3>Schedule & Booking</h3>
                <?php if(isset($_SESSION['email'])){ ?>
                
                <div id="booking-interface" style="display: none;">
                    <label for="treatment-select">Pilih Layanan:</label>
                    <select id="treatment-select"></select>
                    
                    <div id="calendar-view"></div>
                    
                    <div id="time-slots-container"></div>
                    <table>
                        <th>Facial & Skin Treatment</th>
                        <th>Body Message & Sauna</th>
                        <th>Spa & skin wrap</th>
                        <th>Hair & Scalp Care</th>
                        <th>Nail Polish & Art</th>
                    </table>
                </div>
            </div>
            <?php } else { ?>
            <p id="auth-prompt" style="font-size: 1.5rem; line-height: 1.5; max-width: 600px; margin-top: 2rem;">
                To see our service availability schedule, please <a href="./auth/login.php">LOGIN</a> or <a href="./auth/register.php">REGISTER</a> first.
            </p>
            <?php } ?>
        </section>

        <section id="history" class="section-padded bg-light" style="display: none;">
            <div class="container">
                <h3>Riwayat Booking Anda</h3>
                <div id="history-content">
                    <p>Memuat riwayat...</p>
                </div>
            </div>
        </section>

    </main>
    
    <div id="auth-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAuthModal()">&times;</span>
            
            <div id="login-container">
                <h4>Masuk ke Akun Anda</h4>
                <form id="login-form">
                    <label for="login-email">Email:</label>
                    <input type="email" id="login-email" name="email" required>
                    
                    <label for="login-password">Kata Sandi:</label>
                    <input type="password" id="login-password" name="password" required>
                    
                    <button type="submit" class="btn btn-primary">Login</button>
                    <p id="login-message" class="form-message"></p>
                </form>
                <p>Belum punya akun? <span class="action-link" onclick="toggleAuthForm(false)">Daftar di sini</span></p>
            </div>
            
            <div id="register-container" style="display: none;">
                <h4>Buat Akun Baru</h4>
                <form id="register-form" method="post" action="./auth/register.php">
                    <label for="reg-username">Nama Pengguna:</label>
                    <input type="text" id="reg-username" name="username" required>
                    
                    <label for="reg-email">Email:</label>
                    <input type="email" id="reg-email" name="email" required>
                    
                    <label for="reg-password">Kata Sandi (min. 6 kar.):</label>
                    <input type="password" id="reg-password" name="password" required>
                    
                    <button type="submit" class="btn btn-secondary">Daftar</button>
                    <p id="register-message" class="form-message"></p>
                </form>
                <p>Sudah punya akun? <span class="action-link" onclick="toggleAuthForm(true)">Login di sini</span></p>
            </div>

        </div>
    </div>

    <footer>
        <div class="container">
            <p>Copyright &copy; 2025 BEAUTYBODY, Inc.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>