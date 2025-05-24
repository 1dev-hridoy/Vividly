<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./auth");
    exit();
}
