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
                <a href="#services">Layanan</a>
                <a href="#booking">Jadwal & Booking</a>
                <button id="login-nav-btn" class="nav-btn">Login / Register</button>
                <div id="user-info" style="display: none;">
                    Selamat datang, <span id="username-display"></span>!
                    <button id="logout-btn" class="nav-btn">Logout</button>
                    <a href="#history">Riwayat</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        
        <section id="hero" class="section-padded">
            <div class="container">
                <h2>Perawatan Diri Anda, Sekarang Lebih Mudah</h2>
                <p>Pesan layanan spa and body massage, skin treatment, atau perawatan kulit terbaik Anda hanya dalam beberapa klik.</p>
                <a href="#booking" class="btn btn-primary">Pesan Sekarang</a>
            </div>
        </section>
        
        <section id="services" class="section-padded bg-light">
            <div class="container">
                <h3>Layanan Kami</h3>
                <div class="service-list">
                    <div class="service-item">Body Massage (60 Min)</div>
                    <div class="service-item">Skin Treatment (60 Min)</div>
                    <div class="service-item">Hair Treatment (90 Min)</div>
                    <div class="service-item">Nail Treatment (45 Min)</div>
                </div>
            </div>
        </section>

        <section id="booking" class="section-padded bg-light">
            <div class="container">
                <h3>Jadwal & Pemesanan</h3>
                
                <p id="auth-prompt" style="font-size: 1.5rem; line-height: 1.5; max-width: 600px; margin-top: 2rem;">
                    Untuk melihat jadwal ketersediaan layanan kami, harap <span class="action-link" onclick="toggleAuthForm(true)">LOGIN</span> atau <span class="action-link" onclick="toggleAuthForm(false)">REGISTER</span> terlebih dahulu.
                </p>
                    <div id="booking-interface" style="display: none;">
                        <label for="treatment-select">Pilih Layanan:</label>
                        <select id="treatment-select"></select>
                        
                        <div id="calendar-view"></div>
                        
                        <div id="time-slots-container">
                            </div>
                    </div>
                </div>
            </div>
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
                <form id="register-form">
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