<?php
session_start(); 
include('dist/php/database.php');

if (isset($_SESSION['company_id'], $_SESSION['company_name'])) {
  $company_name = $_SESSION['company_name'];
} else {
  header("Location: index.php");
  exit();
}

function rowCount($db, $tbname) {
  // SQL query to count rows
  $sql = "SELECT COUNT(*) as count FROM $tbname";
  $stmt = $db->prepare($sql);
  $stmt->execute();

  // Fetch the result
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function totalCount($db, $tbname, $colname) {
  // SQL query to count rows
  $sql = "SELECT SUM($colname) as total FROM $tbname";
  $stmt = $db->prepare($sql);
  $stmt->execute();

  // Fetch the result
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function totalCountJoin($db, $tbname1, $tbname2, $colname1, $colname2, $joincol) {
  // SQL query to count rows
  $sql = "SELECT SUM(t1.$colname1 * t2.$colname2) as total 
          FROM $tbname1 t1
          JOIN $tbname2 t2
            ON t1.$joincol = t2.$joincol";
  $stmt = $db->prepare($sql);
  $stmt->execute();

  // Fetch the result
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductRecords($db) {
  $sql = "SELECT SUM(pr.product_quantity * pt.product_price) AS qty, 
          pr.product_date AS date
          FROM product_records pr
          JOIN product_type pt
            ON pr.product_id = pt.product_id
          GROUP BY date
          ORDER BY date DESC
          LIMIT 14";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll();
}

function getDeadRecords($db) {
  $sql = "SELECT 
            bt.breed_type, 
            ba.stock_animal, 
            dr.number_quantity AS dead, 
            dr.dead_date AS date
          FROM 
            dead_records dr
          JOIN 
            breed_animal ba ON dr.breed_id = ba.breed_id
          JOIN 
            breed_technology bt ON dr.breed_id = bt.breed_id
          JOIN 
            ( SELECT 
                breed_id, 
                MAX(dead_date) AS latest_date
              FROM 
                dead_records
              GROUP BY 
                breed_id
            ) latest ON dr.breed_id = latest.breed_id AND dr.dead_date = latest.latest_date
          ORDER BY 
            date DESC
          LIMIT 8";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll();
}

function getStock($db) {
  $sql = "SELECT at.animal_type, SUM(ba.stock_animal) AS total_stock_animal
          FROM animal_type at
          JOIN breed_animal ba
              ON at.animal_id = ba.animal_id
          GROUP BY at.animal_type
          ORDER BY total_stock_animal DESC";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll();
}

function getStockRecord($db) {
  $sql = "SELECT bt.breed_type AS type, 
          ba.stock_animal AS stock, ba.breed_date AS date
          FROM breed_animal ba
          JOIN breed_technology bt
            ON ba.breed_id = bt.breed_id
          ORDER BY ba.breed_date DESC
          LIMIT 3";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  return $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin AH | Animal Husbandry Knowledge Sharing In South Shan State</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- Logo -->
  <link rel="icon" type="image/x-icon" href="image/project-logo.ico">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link btn btn-default" data-toggle="modal" data-target="#modal-default">Log out</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Log Out</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>You are Logging out of Admin AH. Continue?</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.location.href='index.php';">Confirm</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Admin AH</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="image/project-logo.ico" alt="AdminLTE Logo" class="img-circle elevation-2" alt="User Image" style="opacity: .8">
        </div>
        <div class="info">
          <a href="index.php" class="d-block"><?php echo $company_name; ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="animaltype.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Animal Type
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="animaltype.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Animal Type</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewanimal.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Animal Type</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="breedanimal.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Animal Stock & Breed
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="breedtechnology.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Breed</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewbreedanimal.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Stock</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="breedanimal.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Stock</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="deadrecord.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Dead Records
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="deadrecord.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Dead Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewdeadrecord.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Dead Records</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="productrecord.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Product Records
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="productrecord.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Product Records</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewproductrecord.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Product Records</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="knowledgetype.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Knowledge Type
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="knowledgetype.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Knowledge Type</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewknowledgetype.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Knowledge Type</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item ">
            <a href="company.php" class="nav-link ">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Company Info
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="company.php" class="nav-link ">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Company Info</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewcompany.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Company Info</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="viewcontact.php" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Contact Us Info
              </p>
            </a>
          </li>  
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0">Dashboard</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
              <?php 
                    // Fetch the result
                    $row = rowCount($db, 'animal_type');
                    
                    // Display the count
                    echo '<h3>' . $row['count'] . '</h3>';
                  ?>

                <p>Total Animal Types</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="viewanimal.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
              <?php 
                    // Fetch the result
                    $row = rowCount($db, 'breed_technology');
                    
                    // Display the count
                    echo '<h3>' . $row['count'] . '</h3>';
                  ?>

                <p>Total Breed Types</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="viewbreedanimal.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
              <?php 
                    // Fetch the result
                    $row = rowCount($db, 'product_type');
                    
                    // Display the count
                    echo '<h3>' . $row['count'] . '</h3>';
                  ?>

                <p>Total Products</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="viewproductrecord.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
              <?php 
                    // Fetch the result
                    $row = rowCount($db, 'knowledge_type');
                    
                    // Display the count
                    echo '<h3>' . $row['count'] . ' <sup style="font-size: 20px">Videos</sup></h3>';
                  ?>

                <p>for Knowledge Sharing</p>
              </div>
              <div class="icon">
                <i class="fas fa-video"></i>
              </div>
              <a href="viewknowledgetype.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 bg-info">
              <span class="info-box-icon"><i class="far fa-comment"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Messages from Customers</span>
                <?php 
                      // Fetch the result
                      $row = rowCount($db, 'contact_us');
                      
                      // Display the count
                      echo '<span class="info-box-number">' . $row['count'] . '</span>';
                    ?>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="ion ion-stats-bars"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Animal In-stock</span>
                <?php 
                      // Fetch the result
                      $row = totalCount($db, 'breed_animal', 'stock_animal');
                      
                      // Display the count
                      echo '<span class="info-box-number">' . $row['total'] . '</span>';
                    ?>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tag"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Product Inventory Value</span>
                <?php 
                      // Fetch the result
                      $row = totalCountJoin($db, 'product_records', 'product_type', 'product_quantity', 'product_price', 'product_id');
                      
                      // Display the count
                      echo '<span class="info-box-number"> $' . $row['total'] . '</span>';
                    ?>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 bg-danger">
              <span class="info-box-icon"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Company Members</span>
                <?php 
                      // Fetch the result
                      $row = rowCount($db, 'company_info');
                      
                      // Display the count
                      echo '<span class="info-box-number">' . $row['count'] . '</span>';
                    ?>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            

        <div class="card">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between">
                <h3 class="card-title">Products Produced Over 2 Weeks</h3>
                <a href="viewproductrecord.php#records">View Report</a>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex">
                <p class="d-flex flex-column">
                  <?php 
                    /* NOTICE! Due to the varying units of the products, the numbers are not
                    the accurate representation of data. Further methods of calculation are needed,
                    which I didn't bother with. >_<
                    20/08/2024 - Solved! Everything is working and accurate :D */
                    // Fetch data from database
                    $productCount = totalCountJoin($db, 'product_records', 'product_type', 'product_quantity', 'product_price', 'product_id');
                    $rows = getProductRecords($db);
                    $dates = array_column($rows, 'date');
                    $quantities = array_column($rows, 'qty');

                    // Split the data into two datasets
                    $first7Dates = array_slice($dates, 0, 7);
                    $next7Dates = array_slice($dates, 7, 7);
                    $first7Quantities = array_slice($quantities, 0, 7);
                    $next7Quantities = array_slice($quantities, 7, 7); 

                    // Combine the totals from the two slices
                    $totalFirst7Quantities = array_sum($first7Quantities);
                    $totalNext7Quantities = array_sum($next7Quantities);

                    // Calculate the Percentage
                    if ($totalFirst7Quantities > 0) {
                      $percen = abs((($totalNext7Quantities - $totalFirst7Quantities) / $totalFirst7Quantities) * 100);
                    } else {
                      $percen = $totalNext7Quantities > 0 ? 100 : 0; // Handle cases where totalFirst7Quantities is zero
                    }
                  ?>
                  <span>
                    <span class="text-bold text-lg">
                      <?php echo '$' . $productCount['total']; ?>
                    </span>
                     Worth of Value
                  </span>
                  <span>Produced Over Time</span>
                </p>
                <p class="ml-auto d-flex flex-column text-right">
                  <?php if ($totalFirst7Quantities > $totalNext7Quantities) { ?>
                    <span class="text-success">
                      <i class="fas fa-arrow-up"></i> <?php echo number_format($percen, 1); ?>%
                    </span>
                  <?php } else { ?>
                    <span class="text-danger">
                      <i class="fas fa-arrow-down"></i> <?php echo number_format($percen, 1); ?>%
                    </span>
                  <?php } ?>
                  <span class="text-muted">Since last week</span>
                </p>
              </div>
              <!-- /.d-flex -->

              <div class="position-relative mb-4">
                <canvas id="visitors-chart" height="200"></canvas>
              </div>

              <div class="d-flex flex-row justify-content-end">
                <span class="mr-2">
                  <i class="fas fa-square text-primary"></i> This Week
                </span>

                <span>
                  <i class="fas fa-square text-gray"></i> Last Week
                </span>
              </div>
            </div>
          </div>
          <!-- /.card -->
                  
          <div class="card">
            <div class="card-header border-0">
              <div class="d-flex justify-content-between">
                <h3 class="card-title">Dead Records</h3>
                <a href="viewdeadrecord.php">View Report</a>
              </div>
            </div>
            <div class="card-body">
              <div class="d-flex">
                <p class="d-flex flex-column">
                  <?php 
                    // Fetch data from database
                    $deadCount = totalCount($db, 'dead_records', 'number_quantity');
                    $stockCount = totalCount($db, 'breed_animal', 'stock_animal');

                    // Calculate the Percentage
                    $totalCount = $deadCount['total'] + $stockCount['total'];
                    $percen = ($deadCount['total'] / $totalCount) * 100;
                  ?>
                  <span>
                    <span class="text-bold text-lg">
                      <?php echo $deadCount['total']; ?>
                    </span>
                    Deads of
                  </span>
                  <span>
                    <span class="text-bold text-lg">
                      <?php echo $totalCount; ?>
                    </span>
                    Total Over Time
                  </span>
                </p>
                <p class="ml-auto d-flex flex-column text-right">
                  <span class="text-danger">
                    <i class="fas fa-arrow-up"></i> <?php echo number_format($percen, 1); ?>%
                  </span>
                  <span class="text-muted">in Overall Ratio</span>
                </p>
              </div>
              <!-- /.d-flex -->

              <div class="position-relative mb-4">
                <canvas id="sales-chart" height="200"></canvas>
              </div>

              <div class="d-flex flex-row justify-content-end">
                <span class="mr-2">
                  <i class="fas fa-square text-primary"></i> Stock Animal
                </span>

                <span>
                  <i class="fas fa-square text-gray"></i> Dead Animal
                </span>
              </div>
            </div>
          </div>
          <!-- /.card -->

          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-5 connectedSortable">
          
            <!-- Calendar -->
            <div class="card bg-gradient-success collapsed-card">
              <div class="card-header border-0">

                <h3 class="card-title">
                  <i class="far fa-calendar-alt"></i>
                  Calendar
                </h3>
                <!-- tools card -->
                <div class="card-tools">
                  <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <!-- /. tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body pt-0">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Total Animal Chart</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <div class="chart-responsive">
                      <canvas id="pieChart" height="150"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                  </div>
                  <!-- /.col -->
                  <div class="col-md-4">
                    <ul class="chart-legend clearfix"></ul>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center p-0">
                <ul class="nav nav-pills flex-column">
                  <?php 
                    $rows = getStockRecord($db);
                    foreach ($rows as $row) { ?>
                  <li class="nav-item">
                    <a href="viewbreedanimal.php" class="nav-link">
                      <span class="float-left text-primary">
                        <?php echo $row['date']; ?>
                      </span>
                        <?php echo $row['type']; ?>
                      <span class="float-right text-success">
                        <i class="fas fa-plus text-sm"></i> <?php echo $row['stock']; ?>
                      </span>
                    </a>
                  </li>
                  <?php } ?>
                </ul>
              </div>
              <!-- /.footer -->
            </div>
            <!-- /.card -->

            <!-- PRODUCT LIST -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Recently Added Products</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                  <?php 
                    $sql = "SELECT pt.*,
                              SUM(pr.product_quantity) AS count
                            FROM product_type pt
                            INNER JOIN product_records pr
                              ON pt.product_id = pr.product_id
                            GROUP BY pt.product_id
                            ORDER BY pt.product_id DESC LIMIT 4";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch()) { ?>
                  <li class="item">
                    <div class="product-img" style="width: 70px;">
                      <img src="upload/<?php echo $row['product_photo'] ?>" alt="Product Image" width="70px" height="auto">
                    </div>
                    <div class="product-info">
                      <a href="javascript:void(0)" class="product-title">
                        <?php echo $row['product_name'] ?>
                        <span class="badge badge-warning float-right">
                          <?php echo $row['count'] ?> Total
                        </span>
                      </a>
                      <span class="product-description">
                        <?php echo $row['product_description']; ?>
                      </span>
                    </div>
                  </li>
                  <?php } ?>
                  <!-- /.item -->
                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                <a href="viewproductrecord.php" class="uppercase">View All Products</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <script>document.write(new Date().getFullYear());</script>
    <a href="http://goldentkm.com.mm" target="_blank">GoldenTKM.com.mm</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- Page specific script -->
<script>
$(function () {
  'use strict'
    
  // Make the dashboard widgets sortable Using jquery UI
  $('.connectedSortable').sortable({
    placeholder: 'sort-highlight',
    connectWith: '.connectedSortable',
    handle: '.card-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex: 999999
  })
  $('.connectedSortable .card-header').css('cursor', 'move')
  
  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  };
  
  var mode = 'index';
  var intersect = true;

  // Data from PHP
  var first7Dates = <?php echo json_encode($first7Dates); ?>;
  var next7Dates = <?php echo json_encode($next7Dates); ?>;
  var first7Quantities = <?php echo json_encode($first7Quantities); ?>;
  var next7Quantities = <?php echo json_encode($next7Quantities); ?>;

  // Combine the dates for the x-axis labels (use the first 7 dates only for the labels)
  var labels = first7Dates.reverse();
  var first7Quantities = first7Quantities.slice().reverse();
  var next7Quantities = next7Quantities.slice().reverse();

  // Get context with jQuery - using jQuery's .get() method.
  var $visitorsChart = $('#visitors-chart');

  // Create the line chart
  var visitorsChart = new Chart($visitorsChart, {
      type: 'line',
      data: {
          labels: labels,
          datasets: [{
              label: 'This Week',
              type: 'line',
              data: first7Quantities,
              backgroundColor: 'transparent',
              borderColor: '#007bff',
              pointBorderColor: '#007bff',
              pointBackgroundColor: '#007bff',
              fill: false
              // pointHoverBackgroundColor: '#007bff',
              // pointHoverBorderColor    : '#007bff'
          },
          {
              label: 'Last Week',
              type: 'line',
              data: next7Quantities,
              backgroundColor: 'transparent',
              borderColor: '#ced4da',
              pointBorderColor: '#ced4da',
              pointBackgroundColor: '#ced4da',
              fill: false
              // pointHoverBackgroundColor: '#ced4da',
              // pointHoverBorderColor    : '#ced4da'
          }]
      },
      options: {
          maintainAspectRatio: false,
          tooltips: {
              mode: mode,
              intersect: intersect,
              callbacks: {
                label: function(tooltipItem, data) {
                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': $' + tooltipItem.yLabel;
                }
            }
          },
          hover: {
              mode: mode,
              intersect: intersect
          },
          legend: {
              display: false
          },
          scales: {
              yAxes: [{
                  gridLines: {
                      display: true,
                      lineWidth: '4px',
                      color: 'rgba(0, 0, 0, .2)',
                      zeroLineColor: 'transparent'
                  },
                  ticks: $.extend({
                      beginAtZero: true,
                      suggestedMax: 200
                  }, ticksStyle)
              }],
              xAxes: [{
                  display: true,
                  gridLines: {
                      display: false
                  },
                  ticks: ticksStyle
              }]
          }
      }
  });
  
  // Data from PHP
  <?php $rows = getDeadRecords($db); ?>
  var labels = <?php echo json_encode(array_column($rows, 'date')); ?>;
  var stockAnimalData = <?php echo json_encode(array_column($rows, 'stock_animal')); ?>;
  var deadData = <?php echo json_encode(array_column($rows, 'dead')); ?>;
  var additionalLabels = <?php echo json_encode(array_column($rows, 'breed_type')); ?>; // Additional labels
  
  // Combine the current labels and additional labels for multi-line labels
  var multiLineLabels = labels.map((label, index) => [label, ' ' + additionalLabels[index]]);
  
  // Get context with jQuery - using jQuery's .get() method.
  var $salesChart = $('#sales-chart');
  
  // Create the bar chart
  var salesChart = new Chart($salesChart, {
    type: 'bar',
    data: {
      labels: multiLineLabels.reverse(),
      datasets: [
        {
          label: 'Stock Animal',
          backgroundColor: '#007bff',
          borderColor: '#007bff',
          data: stockAnimalData.reverse()
        },
        {
          label: 'Dead Animal',
          backgroundColor: '#ced4da',
          borderColor: '#ced4da',
          data: deadData.reverse()
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips: {
        mode: mode,
        intersect: intersect
      },
      hover: {
            mode: mode,
            intersect: intersect
          },
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              gridLines: {
                display: true,
                lineWidth: '4px',
                color: 'rgba(0, 0, 0, .2)',
                      zeroLineColor: 'transparent'
                    },
                  ticks: $.extend({
                    beginAtZero: true,
                    callback: function (value) {
                      if (value >= 1000) {
                        value /= 1000;
                        value += 'k';
                      }
                      
                      return value;
                    }
                  }, ticksStyle)
              }],
              xAxes: [{
                display: true,
                gridLines: {
                  display: false
                    },
                    ticks: ticksStyle
                  }]
                }
              }
  });
  
  //-------------
  // - PIE CHART -
  //-------------
  
  // Base colors
  var baseColors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
  
  // Function to generate a random color
  function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
  }
  
  // Get context with jQuery - using jQuery's .get() method.
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
  
  <?php $rows = getStock($db); ?>
  // Data labels and values from PHP
  var pieLabels = <?php 
    echo json_encode(array_column($rows, 'animal_type')); 
    ?>;
  
  var pieDataValues = <?php 
    echo json_encode(array_column($rows, 'total_stock_animal')); 
    ?>;
  
  // Assign colors dynamically
  var pieColors = pieLabels.map((label, index) => {
    if (index < baseColors.length) {
      return baseColors[index];
    } else {
      return getRandomColor();
    }
  });
  
  var pieData = {
    labels: pieLabels,
    datasets: [
      {
        data: pieDataValues,
        backgroundColor: pieColors
      }
    ]
  }
  
  var pieOptions = {
    legend: {
      display: false
    }
  }
  
  // Create pie or doughnut chart
  var pieChart = new Chart(pieChartCanvas, {
    type: 'doughnut',
      data: pieData,
      options: pieOptions
    });
    
    // Generate legend items with colors
    $(document).ready(function() {
      var legendHtml = '';
      pieLabels.forEach((label, index) => {
        var color = pieColors[index];
        legendHtml += `<li><i class="far fa-circle" style="color: ${color};"></i> ${label}</li>`;
      });
      $('.chart-legend').html(legendHtml);
    });
    
    //-----------------
    // - END PIE CHART -
    //-----------------
    
    // The Calender
    $('#calendar').datetimepicker({
      format: 'L',
      inline: true
    })
  });
</script>
</body>
</html>
<?php $db = null; ?>