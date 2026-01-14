<?php

session_start();

$conn = new mysqli("localhost", "root", "", "restaurant_db");

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных");
}

