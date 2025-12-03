<?php
session_start();
include 'connection.php';
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
            <a href="#hero" class="logo-link">
                <h1 class="logo">beautybody</h1>
            </a>
            <nav>
                <a href="#services">Services</a>
                <a href="#booking">Schedule & Booking</a>
                <?php if(isset($_SESSION['email'])){ ?>
                    
                    <a href="history.php">History</a>
                    <a href="profil_user.php">Profil</a>    
                    <a href="./auth/logout.php">Logout</a>
                <?php } else { ?>
                    <a href="./auth/register.php" class="nav-btn">Login / Register</a>
                <?php } ?>
            </nav>
            
        </div>
    </header>

    <main>
        <section id="hero" class="section-padded">
            <div class="container">
                <h2>Your Self-Care, Now Easier</h2>
                <p>Order Body massage & Relaxation Spa, Hair & Scalp Care, Nail Art & Therapy services with experts in just a few clicks</p>
                <a href="booking.php" class="btn btn-primary">Book Now!</a>
                
            </div>
        </section>
        
        <section id="services" class="section-padded bg-light">
            <div class="container">
                <h3>Our services</h3>
                <div class="image-grid">
        
                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/sauna2.jpg" alt="sauna room">
                                <p class="image-description">comfort sauna room</p>
                            </div>
                        </a>
                    </div>
                    
                    
                    <div class="video-item">
                        <a href="#booking" aria-label="sauna">
                            <div class="video-placeholder">
                                <video loop autoplay muted> 
                                    <source src="video/sauna.mp4" type="video/mp4"> 
                                </video>
                                <p class="video-description">comfy sauna</p>
                            </div>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/massageRoom.jpg" alt="massage room">
                                <p class="image-description">massage room</p>
                            </div>
                        </a>
                    </div>

                    <div class="image-item">
                        <a href="#booking" aria-label="">
                            <div class="image-placeholder">
                                <img src="photo/facialTreatmentBed.png" alt="Facial Treatment">
                                <p class="image-description">Facial treatment</p>
                            </div>
                        </a>
                    </div>
                    
                    <div class="video-item">
                        <a href="#booking" aria-label="laser treatment">
                            <div class="video-placeholder">
                                <video loop autoplay muted> 
                                    <source src="video/laser treatment.mp4" type="video/mp4"> 
                                </video>
                                <p class="video-description">laser facial treatment</p>
                            </div>
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
                                <img src="photo/nailPolish.jpg" alt="nail polish & art">
                                <p class="image-description">nail polish & art</p>
                            </div>
                        </a>
                    </div>

                    <div class="video-item">
                        <a href="#booking" aria-label="Lihat Video dan Pesan Skin Treatment">
                            <div class="video-placeholder">    
                                <video loop autoplay muted> 
                                    <source src="video/nail polish.mp4" type="video/mp4"> 
                                </video>
                                <p class="video-description">nail polish</p>
                            </div>
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
                <h3>Schedule & Services</h3>
                <?php if(isset($_SESSION['email'])){ ?>
                <div class="schedule-display">
                    <h4>Weekly Operating Hours</h4>
                    
                    <table id="schedule-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Open</th>
                                <th>Closed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT day, start_shift, end_shift, is_closed FROM schedules ORDER BY FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
                            $result = $connection->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $status = ($row['is_closed'] == 1) ? 'Tutup' : 'Buka';
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['day']) . "</td>";

                                    if ($row['is_closed'] == 1) {
                                        echo "<td colspan='2' class='closed-cell'>Libur</td>";
                                    } else {
                                        echo "<td>" . date('H:i', strtotime($row['start_shift'])) . "</td>";
                                        echo "<td>" . date('H:i', strtotime($row['end_shift'])) . "</td>";
                                    }
                                    
                                    echo "<td class='status-" . strtolower($status) . "'>" . $status . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Data jadwal operasional belum diinput oleh Admin.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="services-list-display" style="margin-top: 40px;">
                    <h4>Our Services</h4>
                    <table id="services-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Duration (minutes)</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_services = "SELECT id, category, duration_minutes, price FROM Services ORDER BY category";
                            $result_services = $connection->query($sql_services); 

                            if ($result_services && $result_services->num_rows > 0) {
                                while($service = $result_services->fetch_assoc()) {
                                    $status_service = 'Tersedia'; 
                                    $status_class = 'status-buka';
                                    
                                    $duration = $service['duration_minutes'] . " menit";

                                    $price = "Rp " . number_format($service['price'], 0, ',', '.');
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($service['category']) . "</td>";
                                    echo "<td>" . $duration . "</td>";
                                    echo "<td>" . $price . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Belum ada layanan yang terdaftar.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bagian Booking  -->
                <div class="booking-form" style="margin-top: 60px;"> 
                    <h4>Please select the service and time you want</h4>   
                    <form action="./booking.php" method="POST">
                        <br> 
                        <button type="submit" class="btn btn-primary">Book Now!</button>
                        
                        <p id="booking-message" style="margin-top: 15px;"></p>
                    </form>
                </div>


                <?php $connection->close();
                } else { ?>
                <p id="auth-prompt" style="font-size: 1.5rem; line-height: 1.5; max-width: 600px; margin-top: 2rem;">
                    To see our service availability schedule, please <a href="./auth/login.php">LOGIN</a> or <a href="./auth/register.php">REGISTER</a> first.
                </p>
                <?php } ?>
        </section>

        <section id="contact" class="section-padded">
            <div class="container">
                <div class="about-us">
                    <p><strong>About us</strong></p>
                    <p><a href="#help">Help and Support</a></p> 
                    <p><a href="#facebook">Blog</a></p> 
                    <p><a href="#instagram">Sitemap</a></p>
                </div>  
                <div class="social-media">
                    <p><strong>Social Media</strong></p>
                    <p><a href="#facebook">Facebook</a></p> 
                    <p><a href="#instagram">Instagram</a></p>
                    <p><a href="#X">X</a></p>
                </div>  
                <div class="customer-service">
                    <p><strong>Contact Us</strong></p>
                    <p><a href="#customer-service">Customer Service</a></p> 
                </div>
            </div>  
        </section>
    </main>
   
    <footer>
        <div class="container"> 
            <p>Copyright &copy; 2025 BEAUTYBODY, Inc.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>

</body>
</html>