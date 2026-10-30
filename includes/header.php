<?php
require_once __DIR__ . '/../config.php';
$pageTitle = $pageTitle ?? 'FixServices';
$u = current_user();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | FixServices</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/shreyash/FixServices/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark brand-nav">
 <div class="container">
  <a class="navbar-brand fw-bold" href="index.php">🔧 FixServices</a>
  <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="nav">
   <div class="navbar-nav ms-auto align-items-lg-center">
    <a class="nav-link" href="services.php">Services</a>
 
    <?php if ($u && $u['role']==='customer'): ?> 
      <a class="nav-link" href="my_bookings.php">My Bookings</a>
      <a class="nav-link" href="complaint.php">Complaints</a>   
<a class="nav-link" href="about.php">About Us</a>
<?php endif; ?>
    <?php if ($u && $u['role']==='provider'): ?>
      <a class="nav-link" href="provider_dashboard.php">Provider Dashboard</a><?php endif; ?>
    <?php if ($u && $u['role']==='admin'): ?>
      <a class="nav-link" href="admin.php">Admin</a>
      
      <?php endif; ?>
    <?php if ($u): ?>
      <span class="navbar-text mx-lg-2">Hi, <?= e($u['name']) ?></span>
      <a class="btn btn-light btn-sm ms-lg-2" href="logout.php">Logout</a>
    <?php else: ?>
      <a class="nav-link" href="login.php">Login</a>
      <a class="btn btn-warning btn-sm ms-lg-2" href="register.php">Join FixServices</a>
    <?php endif; ?>
   </div>
  </div>
 </div>
</nav>
<main class="container py-4">
<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
<div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show"><?= e($f['message']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>


