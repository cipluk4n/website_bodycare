<?php
session_start();
if(!isset($_SESSION['email'])) {
    header('Location: ./auth/login.php');
    exit();
}

include 'connection.php';

// Get user info
$user_email = $_SESSION['email'];
$user_query = $connection->prepare("SELECT id, username FROM users WHERE email = ?");
$user_query->bind_param("s", $user_email);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'];

// Get booking history
$history_query = $connection->prepare("
    SELECT 
        b.id,
        b.date,
        b.start_time,
        b.status,
        s.category,
        s.price,
        s.duration_minutes
    FROM booking b
    JOIN services s ON b.treatment_id = s.id
    WHERE b.user_id = ?
    ORDER BY b.date DESC, b.start_time DESC
");
$history_query->bind_param("i", $user_id);
$history_query->execute();
$history_result = $history_query->get_result();

$bookings = [];
while($row = $history_result->fetch_assoc()) {
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History | beautybody</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .history-container {
            margin-top: 2rem;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .history-table th {
            background: var(--primary-color);
            color: white;
            padding: 1.2rem;
            text-align: left;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .history-table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
        }
        
        .history-table tr:last-child td {
            border-bottom: none;
        }
        
        .history-table tr:hover {
            background-color: var(--secondary-bg);
        }
        
        .status {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            min-width: 100px;
        }
        
        .status-pending {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-canceled {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .no-history {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .no-history h3 {
            color: var(--text-color);
            margin-bottom: 1rem;
        }
        
        .no-history p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .booking-id {
            font-family: monospace;
            background: var(--secondary-bg);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        .price {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .duration {
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .history-table {
                display: block;
                overflow-x: auto;
            }
            
            .history-table th,
            .history-table td {
                padding: 0.8rem 0.5rem;
                font-size: 0.9rem;
            }
            
            .status {
                min-width: 80px;
                font-size: 0.8rem;
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
                <?php if(isset($_SESSION['email'])){ ?>
                    <a href="./history.php" class="active">History</a>
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
                <h2>Riwayat Booking Anda</h2>
                <p>Lihat semua riwayat perawatan yang telah Anda booking.</p>
                
                <div class="history-container">
                    <?php if(count($bookings) > 0): ?>
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>ID Booking</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Layanan</th>
                                    <th>Durasi</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bookings as $booking): ?>
                                    <?php
                                    // Format status
                                    $status_text = $booking['status'];
                                    $status_class = 'status-' . $booking['status'];
                                    
                                    // Translate status to Indonesian
                                    if($booking['status'] == 'completed') {
                                        $status_text = 'selesai';
                                    } elseif($booking['status'] == 'pending') {
                                        $status_text = 'pending';
                                    } elseif($booking['status'] == 'confirmed') {
                                        $status_text = 'dikonfirmasi';
                                    } elseif($booking['status'] == 'canceled') {
                                        $status_text = 'dibatalkan';
                                    }
                                    
                                    // Format date
                                    $formatted_date = date('d M Y', strtotime($booking['date']));
                                    
                                    // Format time
                                    $formatted_time = date('H:i', strtotime($booking['start_time']));
                                    ?>
                                    <tr>
                                        <td><span class="booking-id">#<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                        <td><?php echo $formatted_date; ?></td>
                                        <td><?php echo $formatted_time; ?></td>
                                        <td><?php echo htmlspecialchars($booking['category']); ?></td>
                                        <td><span class="duration"><?php echo $booking['duration_minutes']; ?> menit</span></td>
                                        <td class="price">Rp <?php echo number_format($booking['price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="status <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-history">
                            <h3>Belum Ada Riwayat Booking</h3>
                            <p>Anda belum melakukan booking perawatan apapun.</p>
                            <a href="./booking.php" class="btn btn-primary">Booking Sekarang</a>
                        </div>
                    <?php endif; ?>
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