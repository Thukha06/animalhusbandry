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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
  // Retrieve and sanitize POST data
  $company_id = isset($_POST['company_id']) ? trim($_POST['company_id']) : null;
  $password = isset($_POST['password']) ? trim($_POST['password']) : null;
  $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : null;
  $errors = [];

  // Validate inputs
  if (empty($company_id)) {
    $errors[] = "Error: Unselected value.<br>Please select a company name.";
  }

  if (empty($password)) {
    $errors[] = "Error: Empty field.<br>Password is required.";
  }

  if (empty($confirmPassword)) {
    $errors[] = "Error: Empty field.<br>Confirm password is required.";
  }

  if ($password != $confirmPassword && isset($password, $confirmPassword)) {
    $errors[] = "Passwords do not match.<br>Please try again.";
  }

  if (empty($errors)) {
    try {
        // SQL query to insert data
        $sql = "INSERT INTO admin (company_id, password) VALUES (:company_id, :password)";
        
        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            $success = "A New Admin has been assigned successfully.";
        } else {
            $errors[] = "Failed to assign a new admin.";
        }
    } catch (PDOException $e) {
        $errors[] = "Error: " . $e->getMessage();
    }
  }

  if (!empty($errors)) {
    echo "<script>
            localStorage.setItem('errors', '" . json_encode($errors) . "');
            window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=assign';
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  // Retrieve and sanitize POST data
  $companyname = isset($_POST['companyname']) ? trim($_POST['companyname']) : null;
  $email = isset($_POST['email']) ? trim($_POST['email']) : null;
  $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
  $address = isset($_POST['address']) ? trim($_POST['address']) : null;
  $errors = [];

  // Validate inputs
  if (empty($companyname)) {
      $errors[] = "Error: Empty field.<br>Company name is required.";
  }

  // Trim whitespace from the email address
  $email = trim($email);
  if (empty($email)) {
      $errors[] = "Error: Empty field.<br>Email is required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format.";
  }

  if (empty($phone)) {
      $errors[] = "Error: Empty field.<br>Phone number is required.";
  } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
      $errors[] = "Phone number must be between 7 to 15 digits and may include a leading '+'.";
  }

  if (empty($address)) {
      $errors[] = "Error: Empty field.<br>Address is required.";
  }

  if (empty($errors)) {
      try {
          // SQL query to insert data
          $sql = "INSERT INTO company_info (company_name, email, phone, address) VALUES (:companyname, :email, :phone, :address)";
          
          // Prepare the SQL statement
          $stmt = $db->prepare($sql);

          // Bind parameters
          $stmt->bindParam(':companyname', $companyname, PDO::PARAM_STR);
          $stmt->bindParam(':email', $email, PDO::PARAM_STR);
          $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
          $stmt->bindParam(':address', $address, PDO::PARAM_STR);

          // Execute the statement and check for success
          if ($stmt->execute()) {
              $success = "A company information was added successfully.";
          } else {
              $errors[] = "Failed to insert company information.";
          }
      } catch (PDOException $e) {
          $errors[] = "Error: " . $e->getMessage();
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
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Logo -->
  <link rel="icon" type="image/x-icon" href="image/project-logo.ico">
  <style>
      #strength {
          height: 10px;
          width: 100%;
          background-color: #ddd;
      }
      #strengthMeter {
          height: 10px;
          width: 0;
          background-color: red;
      }
  </style>
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
          
          <li class="nav-item menu-open">
            <a href="company.php" class="nav-link active">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Company Info
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="company.php" class="nav-link active">
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
              if (isset($_GET['action']) && $_GET['action'] == 'assign') {
            ?>
              <li class="breadcrumb-item"><a href="company.php">Company Info</a></li>
              <li class="breadcrumb-item active">Assign New Admin</li>
            <?php } else { ?>
              <li class="breadcrumb-item active">Company Info</li>
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
          <div class="col-md-2">
          </div>
          <!-- left column -->
          <div class="col-md-8">
            <!-- general form elements -->
            <?php
              if (isset($_GET['action']) && $_GET['action'] == 'assign') {
            ?>
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Assign New Admin</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="card-body">
                  <?php
                    $sql = "SELECT c.*
                            FROM company_info c
                            LEFT JOIN admin a 
                              ON c.company_id = a.company_id
                            WHERE a.company_id IS NULL
                            ORDER BY c.company_name ASC";
                    $stmt = $db->query($sql);

                    if ($stmt->execute()) {
                        $result = $stmt->fetchAll(); ?>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Select Company Name</label>
                        <select name="company_id" id="company_id" class="form-control select2">
                          <?php if ($result != NULL) { ?>
                            <option value="">Select Company Name Here</option>
                          <?php } else { ?>
                            <option value="">No Company Name to Assign Admin</option>
                          <?php } foreach ($result as $row) { ?>
                              <option value="<?php echo $row['company_id'] ?>"><?php echo $row['company_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                  <?php } ?>
                  
                  
                  <div class="form-group">
                    <label for="password">Enter Password
                      <small id="strengthText"></small>
                    </label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password Here">
                    <div id="strength" style="margin-top: 5px;">
                      <div id="strengthMeter"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="confirmPassword">Re-enter Password</label>
                    <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Re-enter Password Here">
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                      <input type="submit" name="assign" class="btn btn-primary" value="Submit">
                </div>
                </form>
            </div>
            <!-- /.card -->
            <?php } else { ?>
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Company Info</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Company Name</label>
                    <input type="text" name="companyname" class="form-control" id="exampleInputEmail1" placeholder="Enter your name" required>
                  </div>

                  <div class="row">
                   <div class="col-md-6">
                   <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter your email" required>
                   </div>
                   </div>
                   <div class="col-md-6">
                   <div class="form-group">
                    <label for="exampleInputEmail1">Phone</label>
                    <input type="tel" name="phone" class="form-control" id="exampleInputEmail1" pattern="\+[0-9]{12,15}" placeholder="+123-456-7890" required>
                   </div>
                   </div>
                  </div>

                    <div class="form-group">
                        <label for="exampleInputPassword1">Address</label>
                        <textarea class="form-control" name="address" rows="3" id="exampleInputPassword1" style="line-height: 2;" placeholder="Enter Address" required></textarea>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <div class="row">
                    <div class="col">
                      <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                    </div>
                    </form>
                    <div class="col-md-3">
                        <?php 
                          echo "<a href='company.php?action=assign' class='btn btn-block btn-success'>Assign Admin</a>" ?>
                    </div>
                  </div>
                </div>

            </div>
            <!-- /.card -->
            <?php } ?>
          </div>
          <!--/.col (right) -->
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
  </footer>->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2@11.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
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
    //Initialize Select2 Elements
    $('.select2').select2()

    bsCustomFileInput.init();
  });
</script>
<script>
  const password = document.getElementById('password');
  const strengthMeter = document.getElementById('strengthMeter');
  const strengthText = document.getElementById('strengthText');

  password.addEventListener('input', function() {
      const val = password.value;
      let strength = 0;

      if (val.length > 5) strength += 1;
      if (val.length > 7) strength += 1;
      if (/[A-Z]/.test(val)) strength += 1;
      if (/[0-9]/.test(val)) strength += 1;
      if (/[^A-Za-z0-9]/.test(val)) strength += 1;

      switch (strength) {
          case 0:
              strengthMeter.style.width = '0%';
              strengthText.textContent = '';
              break;
          case 1:
              strengthMeter.style.width = '20%';
              strengthMeter.style.backgroundColor = 'red';
              strengthText.textContent = 'Very Weak';
              break;
          case 2:
              strengthMeter.style.width = '40%';
              strengthMeter.style.backgroundColor = 'orange';
              strengthText.textContent = 'Weak';
              break;
          case 3:
              strengthMeter.style.width = '60%';
              strengthMeter.style.backgroundColor = 'yellow';
              strengthText.textContent = 'Medium';
              break;
          case 4:
              strengthMeter.style.width = '80%';
              strengthMeter.style.backgroundColor = 'lightgreen';
              strengthText.textContent = 'Strong';
              break;
          case 5:
              strengthMeter.style.width = '100%';
              strengthMeter.style.backgroundColor = 'green';
              strengthText.textContent = 'Very Strong';
              break;
      }
  });
</script>
</body>
</html>
