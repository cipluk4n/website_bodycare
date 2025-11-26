<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $treatment_id = $_POST['treatment_id'];
    
    // Get day of week
    $day_of_week = strtolower(date('l', strtotime($date)));
    
    // Get schedule for that day
    $schedule_query = $connection->prepare("SELECT * FROM schedules WHERE day = ?");
    $schedule_query->bind_param("s", $day_of_week);
    $schedule_query->execute();
    $schedule_result = $schedule_query->get_result();
    $schedule = $schedule_result->fetch_assoc();
    
    if($schedule['is_closed'] == 1) {
        echo json_encode([]);
        exit;
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
    $duration = $service['duration_minutes'] * 60;
    $time_slots = [];
    
    $current_time = $start_time;
    while($current_time + $duration <= $end_time) {
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
    
    while($booking = $booked_result->fetch_assoc()) {
        $booked_start = strtotime($booking['start_time']);
        $booked_end = strtotime($booking['end_time']);
        
        // Remove overlapping time slots
        $time_slots = array_filter($time_slots, function($slot) use ($booked_start, $booked_end, $duration) {
            $slot_time = strtotime($slot);
            return !($slot_time < $booked_end && ($slot_time + $duration) > $booked_start);
        });
    }
    
    echo json_encode(array_values($time_slots));
}
?>