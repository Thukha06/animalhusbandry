<?php
session_start();
include('dist/php/database.php');

if (isset($_SESSION['company_id'], $_SESSION['company_name'])) {
  $company_name = $_SESSION['company_name'];
} else {
  header("Location: index.php");
  exit();
}

$error = null;
$success = null;

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['dead_record_id'])) {
    $dead_record_id = $_GET['dead_record_id'];
    $errors = [];

    // Validate the dead_record_id
    if (empty($dead_record_id) || !is_numeric($dead_record_id)) {
        $errors[] = "Error: Invalid dead record ID.<br>dead record ID has not been passed.";
    }

    if (empty($errors)) {
        try {
            $sql = "DELETE FROM dead_records WHERE dead_record_id = :dead_record_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':dead_record_id', $dead_record_id);

            if ($stmt->execute()) {
                $success = "Dead record deleted successfully.";
            } else {
                $errors[] = "Failed to delete dead record.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
      echo "<script>
              localStorage.setItem('errors', '" . json_encode($errors) . "');
              window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
      exit();
    }
  
    // Handle success message
    if (!empty($success)) {
      echo "<script>
              localStorage.setItem('success_message', '" . htmlspecialchars($success) . "');
              localStorage.removeItem('formData'); // Clear form data on success
              window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
      exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $breed_id = isset($_POST['breed_id']) ? trim($_POST['breed_id']) : null;
    $stock_animal = isset($_POST['stock_animal']) ? trim($_POST['stock_animal']) : null;
    $number_quantity = isset($_POST['number_quantity']) ? trim($_POST['number_quantity']) : null;
    $type_disease = isset($_POST['type_disease']) ? trim($_POST['type_disease']) : null;
    $dead_date = isset($_POST['dead_date']) ? trim($_POST['dead_date']) : null;
    $errors = [];

    // Validate inputs
    if (empty($breed_id) || !is_numeric($breed_id)) {
        $errors[] = "Error: Empty field.<br>Invalid breed ID.";
    }

    if (empty($stock_animal) || !is_numeric($stock_animal)) {
        $errors[] = "Error: Invalid value.<br>Stock animal must be a number.";
    }

    if (empty($number_quantity)) {
        $errors[] = "Error: Empty field.<br>Dead Count is required.";
    } elseif (!is_numeric($number_quantity)) {
        $errors[] = "Error: Invalid value.<br>Dead Count must be a number.";
    }

    if (empty($type_disease)) {
        $errors[] = "Error: Empty field.<br>Type of disease is required.";
    }

    if (empty($dead_date) || !strtotime($dead_date)) {
        $errors[] = "Error: Invalid date format.";
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE dead_records 
                    SET number_quantity = :number_quantity, type_disease = :type_disease, 
                    dead_date = :dead_date
                    WHERE breed_id = :breedId";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':number_quantity', $number_quantity);
            $stmt->bindParam(':type_disease', $type_disease);
            $stmt->bindParam(':dead_date', $dead_date);
            $stmt->bindParam(':breedId', $breed_id);

            if ($stmt->execute()) {
                $success = "Dead record updated successfully.";
            } else {
                $errors[] = "Failed to update dead record.";
            }

            $sql = "UPDATE breed_animal 
                    SET stock_animal = :stock_animal
                    WHERE breed_id = :breedId";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':stock_animal', $stock_animal);
            $stmt->bindParam(':breedId', $breed_id);

            if ($stmt->execute()) {
                $success .= " Stock animal updated successfully.";
            } else {
                $errors[] = "Failed to update stock animal.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
      echo "<script>
              localStorage.setItem('errors', '" . json_encode($errors) . "');
              window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
      exit();
    }
  
    // Handle success message
    if (!empty($success)) {
      echo "<script>
              localStorage.setItem('success_message', '" . htmlspecialchars($success) . "');
              localStorage.removeItem('formData'); // Clear form data on success
              window.location.href = '" . $_SERVER['PHP_SELF'] . "';
            </script>";
      exit();
    }
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
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Logo -->
  <link rel="icon" type="image/x-icon" href="image/project-logo.ico">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Log out</a>
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
          <img src="image/project-logo.ico" class="img-circle elevation-2" alt="User Image">
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
            <a href="dashboard.php" class="nav-link">
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
          
          <li class="nav-item menu-open">
            <a href="deadrecord.php" class="nav-link active">
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
                <a href="viewdeadrecord.php" class="nav-link active">
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
            <ol class="breadcrumb float-sm-left">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <?php 
                if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['dead_record_id'])) {
              ?>
                <li class="breadcrumb-item"><a href="viewdeadrecord.php">View Dead Records</a></li>
                <li class="breadcrumb-item active">Edit Dead Records</li>
              <?php } else { ?>
                <li class="breadcrumb-item active">View Dead Records</li>
              <?php } ?>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <?php 
          if (isset($_GET['action']) && $_GET['action'] == 'edit' 
              && isset($_GET['dead_record_id'])) {

            $dead_record_id = $_GET['dead_record_id'];

            $sql = "SELECT breed_technology.breed_type, breed_animal.stock_animal,
                    dead_records.number_quantity, dead_records.type_disease, dead_records.dead_date, dead_records.breed_id
                    FROM dead_records
                    INNER JOIN breed_technology
                      ON dead_records.breed_id = breed_technology.breed_id
                    INNER JOIN breed_animal
                      ON dead_records.breed_id = breed_animal.breed_id
                    WHERE dead_records.dead_record_id = :dead_record_id";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':dead_record_id', $dead_record_id);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
              $breed_type = $row['breed_type'];
              $stock_animal = $row['stock_animal'];
              $number_quantity = $row['number_quantity'];
              $type_disease = $row['type_disease'];
              $dead_date = $row['dead_date'];
              $breed_id = $row['breed_id'];
            }
          ?>
          <div class="col-md-2">
          </div>
          <!-- left column -->
          <div class="col-md-8">
          <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Edit Dead Records</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="card-body">
                <input type="hidden" name="breed_id" value="<?php echo $breed_id; ?>">
                  <div class="form-group">
                    <label for="breed_type">Breed Name</label>
                    <input type="text" name="breed_type" class="form-control" id="breed_type" placeholder="Enter breed name" value="<?php echo $breed_type; ?>" disabled>
                  </div>

                  <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="stock_animal">In-stock</label>
                      <input type="number" name="stock_animal" class="form-control" id="stock_animal" placeholder="Enter stock quantity" value="<?php echo $stock_animal; ?>" readonly>
                    </div>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <label for="number_quantity">Dead Count</label>
                      <input type="number" name="number_quantity" class="form-control" id="number_quantity" placeholder="Enter dead counts" value="<?php echo $number_quantity; ?>">
                    </div>
                  </div>
                  </div>

                  <div class="form-group">
                    <label for="type_disease">Disease</label>
                    <input type="text" name="type_disease" class="form-control" id="type_disease" placeholder="Enter disease name" value="<?php echo $type_disease; ?>">
                  </div>
                  <div class="form-group">
                    <label for="dead_date">Dead Date</label>
                    <input type="date" name="dead_date" class="form-control" id="dead_date" placeholder="Enter dead date" value="<?php echo $dead_date; ?>">
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <input type="submit" name="submit" class="btn btn-primary" value="Confirm">
                </div>
              </form>
            </div>
            </div>
            <?php } else { ?>
            <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DataTable for viewing dead records</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Breed Name</th>
                    <th>In-stock</th>
                    <th>Dead Counts</th>
                    <th>Disease</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $sql = "SELECT breed_technology.breed_type, breed_animal.stock_animal,
                            dead_records.dead_record_id, dead_records.number_quantity, dead_records.type_disease, dead_records.dead_date
                            FROM dead_records
                            INNER JOIN breed_technology
                              ON dead_records.breed_id = breed_technology.breed_id
                            INNER JOIN breed_animal
                              ON dead_records.breed_id = breed_animal.breed_id;";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    while ($row = $stmt->fetch()) { ?>
                    <tr>
                    <td>
                      <?php echo $row['breed_type'] ?>
                    </td>
                    <td>
                      <?php echo $row['stock_animal'] ?>
                    </td>
                    <td>
                      <?php echo $row['number_quantity'] ?>
                    </td>
                    <td>
                      <?php echo $row['type_disease'] ?>
                    </td>
                    <td>
                      <?php echo $row['dead_date'] ?>
                    </td>
                    <td>
                    <div class="row">
                        <div class="col-6">
                          <?php 
                          $dead_record_id = $row['dead_record_id'];
                          echo "<a class='button' href='viewdeadrecord.php?action=edit&dead_record_id=$dead_record_id' onclick='return ConfirmEdit();'><button class='btn btn-block btn-success btn-sm'>Edit</button></a>" ?>
                        </div>
                        <div class="col-6">
                          <?php 
                          
                          echo "<a class='button' href='viewdeadrecord.php?action=delete&dead_record_id=$dead_record_id' onclick='return ConfirmDelete();'><button class='btn btn-block btn-danger btn-sm'>Delete</button></a>" ?>
                        </div>
                    </div>
                    </td>
                  </tr>
                  <?php } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
            <?php } ?>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
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
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2@11.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const numberQuantityInput = document.getElementById('number_quantity');
      const stockAnimalInput = document.getElementById('stock_animal');
      const initialDeadCount = parseInt(numberQuantityInput.value);
      const initialStock = parseInt(stockAnimalInput.value);

      numberQuantityInput.addEventListener('input', function () {
          let currentDeadCount = parseInt(numberQuantityInput.value);

          // Prevent the dead count from going below zero
          if (currentDeadCount < 0) {
              currentDeadCount = 0;
              numberQuantityInput.value = 0;
          }

          const difference = currentDeadCount - initialDeadCount;
          let newStockValue = initialStock - difference;

          // Prevent the stock value from going below zero
          if (newStockValue < 0) {
              newStockValue = 0;
              numberQuantityInput.value = initialDeadCount + initialStock;
          }

          stockAnimalInput.value = newStockValue;
      });
  });
</script>
<script>
  function ConfirmDelete(){
    var msg=confirm("Are you sure want to delete?");
    if(msg)
  return true;
    else return false;
  }
  
  function ConfirmEdit(){
    var msg=confirm("Are you sure want to Edit?");
    if(msg)
  return true;
    else return false;
  }

  $(function() {
    var Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    function showToast(type, message) {
      Toast.fire({
          icon: type,
          title: message
      });
    }

    function displayToasts(messages) {
      let delay = 0;
      messages.forEach((message, index) => {
          setTimeout(() => {
              showToast('error', message); // Use 'success' or 'error' based on your need
          }, delay);
          delay += 3500; // Increase delay to ensure toasts don’t overlap (3000ms + buffer)
      });
    }

    // Get messages from localStorage
    const successMessage = localStorage.getItem('success_message');
    const errorMessages = JSON.parse(localStorage.getItem('errors')) || [];

    // Show success message if available
    if (successMessage) {
      showToast('success', successMessage);
      localStorage.removeItem('success_message');
    }

    // Show errors if available
    if (errorMessages.length > 0) {
      displayToasts(errorMessages);
      localStorage.removeItem('errors');
    }
  });

  $(function () {
    bsCustomFileInput.init();
  });
</script>
</body>
</html>
