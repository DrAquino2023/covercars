<?php
session_start();
if (empty($_SESSION['usuario']['is_admin'])) {
    header("Location: ../index.php");
    exit;
}

// Incluir la conexión aquí para que esté disponible en todas las páginas
require_once "../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Covercars Admin</title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <!-- Custom CSS - Si existe -->
  <?php if(file_exists('custom-admin.css')): ?>
  <link rel="stylesheet" href="custom-admin.css">
  <?php endif; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Inicio</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user-circle fa-lg"></i>
          <span class="ml-1"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Admin') ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">
            <i class="fas fa-user-shield"></i> Administrador
          </span>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link text-center">
      <i class="fas fa-car fa-2x mb-2 d-block text-primary"></i>
      <span class="brand-text font-weight-light">Covercars Admin</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
            <i class="fas fa-user"></i>
          </div>
        </div>
        <div class="info">
          <a href="#" class="d-block"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Administrador') ?></a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">PRINCIPAL</li>
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='index.php'?'active':''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-header">GESTIÓN</li>
          <li class="nav-item">
            <a href="pedidos.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='pedidos.php'?'active':''; ?>">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>Pedidos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="productos.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='productos.php'?'active':''; ?>">
              <i class="nav-icon fas fa-box"></i>
              <p>Productos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="reportes.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='reportes.php'?'active':''; ?>">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Reportes</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="usuarios.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF'])==='usuarios.php'?'active':''; ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>Usuarios</p>
            </a>
          </li>
          <li class="nav-header">SISTEMA</li>
          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Cerrar Sesión</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">