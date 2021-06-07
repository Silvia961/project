<?php

$host = 'localhost';
$user = 'root';
$pass = 'root';
$db_name = 'nossocms';


$conn = new mysqli($host,$user,$pass,$db_name);

if ($conn ->connect_error){
    die ('Database connection error : ' .  $conn->connect_error);
}