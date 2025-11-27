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
                <a href="#schedule">Schedule</a>
                <a href="./booking.php">Booking</a>
                <?php if (isset($_SESSION['email'])) { ?>
                    <a href="./history.php">History</a>
                    <a href="profil_user.php">Profil</a>
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

        <section id="schedule" class="section-padded bg-light">
            <div class="container">
                <h3>Schedule</h3>
                <?php if (isset($_SESSION['email'])) { ?>

                    <div id="schedule-interface">
                        <h4>Jadwal Operasional Klinik</h4>
                        <div class="schedule-table-container">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam Operasional</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'connection.php';
                                    $query = "SELECT * FROM schedules ORDER BY FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')";
                                    $result = $connection->query($query);

                                    $hari_indonesia = [
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu',
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu'
                                    ];

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $hari_inggris = $row['day'];
                                            $hari_indonesia_text = $hari_indonesia[$hari_inggris] ?? ucfirst($hari_inggris);
                                            $is_closed = $row['is_closed'];

                                            echo "<tr>";
                                            echo "<td>{$hari_indonesia_text}</td>";

                                            if ($is_closed == 1) {
                                                echo "<td>-</td>";
                                                echo "<td class='closed'>Libur</td>";
                                            } else {
                                                $start_time = date('H:i', strtotime($row['start_shift']));
                                                $end_time = date('H:i', strtotime($row['end_shift']));
                                                echo "<td>{$start_time} - {$end_time}</td>";
                                                echo "<td class='open'>Buka</td>";
                                            }

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>Data jadwal tidak tersedia</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php } else { ?>
                    <p id="auth-prompt" style="font-size: 1.5rem; line-height: 1.5; max-width: 600px; margin-top: 2rem;">
                        To see our schedule, please <a href="./auth/login.php">LOGIN</a> or <a href="./auth/register.php">REGISTER</a> first.
                    </p>
                <?php } ?>
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

    <footer>
        <div class="container">
            <p>Copyright &copy; 2025 BEAUTYBODY, Inc.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>

</body>

</html>