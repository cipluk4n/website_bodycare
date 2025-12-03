<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'beautybody';

$connection = new mysqli($host, $user, $password, $db);
if($connection->connect_error){
    die('Koneksi gagal' . $connection->connect_error());
}
?>