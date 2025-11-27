<?php
session_start();
if (!isset($_SESSION['email'])) {
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

// Get services
$services_query = $connection->query("SELECT * FROM services");
$services = [];
while ($row = $services_query->fetch_assoc()) {
    $services[] = $row;
}

// Get schedules
$schedules_query = $connection->query("SELECT * FROM schedules");
$schedules = [];
while ($row = $schedules_query->fetch_assoc()) {
    $schedules[$row['day']] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {
    $treatment_id = $_POST['treatment_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Calculate end time based on service duration
    $service_query = $connection->prepare("SELECT duration_minutes FROM services WHERE id = ?");
    $service_query->bind_param("i", $treatment_id);
    $service_query->execute();
    $service_result = $service_query->get_result();
    $service = $service_result->fetch_assoc();

    $start_time = $time;
    $end_time = date('H:i:s', strtotime($time) + ($service['duration_minutes'] * 60));

    // Insert booking
    $insert_query = $connection->prepare("INSERT INTO booking (user_id, treatment_id, date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $insert_query->bind_param("iisss", $user_id, $treatment_id, $date, $start_time, $end_time);

    if ($insert_query->execute()) {
        $success_message = "Booking berhasil! Kami akan mengonfirmasi booking Anda segera.";
    } else {
        $error_message = "Terjadi kesalahan saat melakukan booking. Silakan coba lagi.";
    }
}

// Function to get available time slots
function getAvailableTimeSlots($connection, $date, $treatment_id)
{
    // Get day of week
    $day_of_week = strtolower(date('l', strtotime($date)));

    // Get schedule for that day
    $schedule_query = $connection->prepare("SELECT * FROM schedules WHERE day = ?");
    $schedule_query->bind_param("s", $day_of_week);
    $schedule_query->execute();
    $schedule_result = $schedule_query->get_result();
    $schedule = $schedule_result->fetch_assoc();

    if ($schedule['is_closed'] == 1) {
        return []; // Closed day
    }

    // Get service duration
    $service_query = $connection->prepare("SELECT duration_minutes FROM services WHERE id = ?");
    $service_query->bind_param("i", $treatment_id);
    $service_query->execute();
    $service_result = $service_query->get_result();
    $service = $service_result->fetch_assoc();

    // Generate time slots
    $start_time = strtotime($schedule['start_shift']);
    $end_time = strtotime($schedule['end_shift']);
    $duration = $service['duration_minutes'] * 60; // Convert to seconds
    $time_slots = [];

    $current_time = $start_time;
    while ($current_time + $duration <= $end_time) {
        $time_slots[] = date('H:i', $current_time);
        $current_time += 1800; // 30-minute intervals
    }

    // Remove booked slots
    $booked_query = $connection->prepare("
        SELECT start_time, end_time 
        FROM booking 
        WHERE date = ? AND status IN ('pending', 'confirmed')
    ");
    $booked_query->bind_param("s", $date);
    $booked_query->execute();
    $booked_result = $booked_query->get_result();

    $booked_slots = [];
    while ($booking = $booked_result->fetch_assoc()) {
        $booked_start = strtotime($booking['start_time']);
        $booked_end = strtotime($booking['end_time']);

        // Remove overlapping time slots
        $time_slots = array_filter($time_slots, function ($slot) use ($booked_start, $booked_end, $duration) {
            $slot_time = strtotime($slot);
            return !($slot_time < $booked_end && ($slot_time + $duration) > $booked_start);
        });
    }

    return array_values($time_slots);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking | beautybody</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .booking-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        .availability-calendar {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .time-slot {
            padding: 0.5rem;
            text-align: center;
            background: var(--secondary-bg);
            border: 2px solid transparent;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .time-slot:hover {
            background: var(--primary-color);
            color: white;
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--accent-color);
        }

        .time-slot.unavailable {
            background: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .date-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .date-navigation button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        .date-navigation button:hover {
            background: var(--accent-color);
        }

        .date-list {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .date-item {
            text-align: center;
            padding: 0.8rem 0.5rem;
            background: var(--secondary-bg);
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .date-item:hover {
            background: var(--primary-color);
            color: white;
        }

        .date-item.selected {
            background: var(--primary-color);
            color: white;
        }

        .date-item.unavailable {
            background: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .price-display {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            background: var(--secondary-bg);
            border-radius: 5px;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .booking-container {
                grid-template-columns: 1fr;
            }

            .date-list {
                grid-template-columns: repeat(4, 1fr);
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
                <a href="./booking.php" class="active">Booking</a>
                <?php if (isset($_SESSION['email'])) { ?>
                    <a href="dashboard.php#history">History</a>
                    <a href="profil_user.php">Profil</a>
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
                <h2>Booking Appointment</h2>
                <p>Pilih tanggal dan waktu yang tersedia untuk perawatan Anda.</p>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="booking-container">
                    <div class="availability-calendar">
                        <h3>Ketersediaan Waktu</h3>
                        <div class="date-navigation">
                            <button onclick="changeDate(-1)">← Sebelumnya</button>
                            <span id="current-week"></span>
                            <button onclick="changeDate(1)">Selanjutnya →</button>
                        </div>
                        <div class="date-list" id="date-list"></div>
                        <div class="time-slots" id="time-slots"></div>
                    </div>

                    <div class="booking-form">
                        <h3>Form Booking</h3>
                        <form method="POST" action="" id="bookingForm">
                            <input type="hidden" name="book" value="1">

                            <div class="form-group">
                                <label for="date">Tanggal Booking</label>
                                <input type="text" id="date" name="date" readonly required>
                            </div>

                            <div class="form-group">
                                <label for="treatment_id">Pilih Layanan</label>
                                <select id="treatment_id" name="treatment_id" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo $service['id']; ?>" data-price="<?php echo $service['price']; ?>">
                                            <?php echo $service['category']; ?> (<?php echo number_format($service['price'], 0, ',', '.'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="time">Waktu Booking</label>
                                <input type="text" id="time" name="time" readonly required>
                            </div>

                            <div class="price-display" id="price-display">
                                Total: Rp 0
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">Booking Now</button>
                        </form>
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
        let currentDateOffset = 0;
        let selectedDate = null;
        let selectedTime = null;
        let selectedService = null;

        // Initialize date picker
        document.addEventListener('DOMContentLoaded', function() {
            generateDateList();
            updateCurrentWeekDisplay();

            // Service change handler
            document.getElementById('treatment_id').addEventListener('change', function() {
                selectedService = this.value;
                updatePrice();
                if (selectedDate && selectedService) {
                    loadTimeSlots(selectedDate, selectedService);
                }
            });
        });

        function generateDateList() {
            const dateList = document.getElementById('date-list');
            dateList.innerHTML = '';

            const today = new Date();
            today.setDate(today.getDate() + currentDateOffset * 7);

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            for (let i = 0; i < 7; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);

                const dayName = days[date.getDay()];
                const dayNumber = date.getDate();
                const monthName = months[date.getMonth()];

                // PERBAIKAN: Gunakan method untuk mendapatkan tanggal lokal
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;

                const dateItem = document.createElement('div');
                dateItem.className = 'date-item';
                dateItem.innerHTML = `
            <div>${dayName}</div>
            <div><strong>${dayNumber}</strong></div>
            <div>${monthName}</div>
        `;

                dateItem.addEventListener('click', function() {
                    selectDate(dateString, dateItem);
                });

                dateList.appendChild(dateItem);
            }
        }

        function selectDate(dateString, element) {
            // Remove previous selection
            document.querySelectorAll('.date-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            element.classList.add('selected');

            selectedDate = dateString;
            document.getElementById('date').value = dateString;

            if (selectedService) {
                loadTimeSlots(dateString, selectedService);
            }
        }

        function selectTime(time, element) {
            // Remove previous selection
            document.querySelectorAll('.time-slot').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            element.classList.add('selected');

            selectedTime = time;
            document.getElementById('time').value = time;

            updatePrice();
        }

        function changeDate(direction) {
            currentDateOffset += direction;
            generateDateList();
            updateCurrentWeekDisplay();

            // Reset selections
            selectedDate = null;
            selectedTime = null;
            document.getElementById('date').value = '';
            document.getElementById('time').value = '';
            document.getElementById('time-slots').innerHTML = '';
        }

        function updateCurrentWeekDisplay() {
            const today = new Date();
            today.setDate(today.getDate() + currentDateOffset * 7);

            const endDate = new Date(today);
            endDate.setDate(today.getDate() + 6);

            const options = {
                day: 'numeric',
                month: 'long'
            };
            const startStr = today.toLocaleDateString('id-ID', options);
            const endStr = endDate.toLocaleDateString('id-ID', options);

            document.getElementById('current-week').textContent = `${startStr} - ${endStr}`;
        }

        function loadTimeSlots(date, serviceId) {
            // Show loading
            document.getElementById('time-slots').innerHTML = '<div>Memuat waktu tersedia...</div>';

            // AJAX request to get available time slots
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_available_slots.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const timeSlots = JSON.parse(xhr.responseText);
                    displayTimeSlots(timeSlots);
                } else {
                    document.getElementById('time-slots').innerHTML = '<div>Error loading time slots</div>';
                }
            };

            xhr.send(`date=${date}&treatment_id=${serviceId}`);
        }

        function displayTimeSlots(timeSlots) {
            const container = document.getElementById('time-slots');
            container.innerHTML = '';

            if (timeSlots.length === 0) {
                container.innerHTML = '<div>Tidak ada waktu tersedia untuk tanggal ini</div>';
                return;
            }

            timeSlots.forEach(time => {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.textContent = time;
                timeSlot.addEventListener('click', function() {
                    selectTime(time, timeSlot);
                });
                container.appendChild(timeSlot);
            });
        }

        function updatePrice() {
            const serviceSelect = document.getElementById('treatment_id');
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const price = selectedOption ? selectedOption.getAttribute('data-price') : 0;

            document.getElementById('price-display').textContent = `Total: Rp ${Number(price).toLocaleString('id-ID')}`;
        }

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!selectedDate || !selectedTime || !selectedService) {
                e.preventDefault();
                alert('Silakan pilih tanggal, waktu, dan layanan terlebih dahulu.');
            }
        });
    </script>
</body>

</html>