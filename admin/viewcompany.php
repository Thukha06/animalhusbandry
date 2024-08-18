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

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['companyId'])) {
    $company_id = $_GET['companyId'];
    $errors = [];

    // Validate the company_id
    if (empty($company_id) || !is_numeric($company_id)) {
        $errors[] = "Error: Invalid company ID.<br>Company ID has not been passed.";
    }

    if (empty($errors)) {
        try {
            // Prepare SQL query to delete the record
            $sql = "DELETE FROM company_info WHERE company_id = :companyId";
            $stmt = $db->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':companyId', $company_id, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                $success = "A company record was deleted successfully.";
            } else {
                $errors[] = "Failed to delete record.";
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

if (isset($_GET['action']) && $_GET['action'] == 'unassign' && isset($_GET['companyId'])) {
  $company_id = $_GET['companyId'];
  $errors = [];

  // Validate the company_id
  if (empty($company_id) || !is_numeric($company_id)) {
    $errors[] = "Error: Invalid company ID.<br>Company ID has not been passed.";
  }

  if ($company_id == $_SESSION['company_id']) {
    $errors[] = "You cannot unassign yourself from the admin role.";
  }

  if (empty($errors)) {
    try {
      // Step 1: Check the number of rows for the given company_id
      $sql_count = "SELECT COUNT(*) AS row_count FROM admin";

      $stmt_count = $db->prepare($sql_count);
      $stmt_count->execute();
      $row = $stmt_count->fetch(PDO::FETCH_ASSOC);

      if ($row['row_count'] > 1) {
          // Step 2: Perform the delete operation if more than one row exists
          $sql_delete = "DELETE FROM admin WHERE company_id = :companyId";

          $stmt_delete = $db->prepare($sql_delete);
          $stmt_delete->bindParam(':companyId', $company_id, PDO::PARAM_INT);

          if ($stmt_delete->execute()) {
              $success = "Unassign Admin successful.";
          } else {
              $errors[] = "Failed to unassign admin.";
          }
      } else {
          $errors[] = "Failed to unassign admin.<br>At least one admin should be present.";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST['companyname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $company_id = $_POST['companyId'];
    $errors = [];

    // Validate inputs
    if (empty($company_name)) {
        $errors[] = "Error: Empty field.<br>Company name is required.";
    }

    // Trim whitespace from the email address
    $email = trim($email);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    if (empty($phone) || !is_numeric($phone)) {
        $errors[] = "Phone number must be numeric.";
    }

    if (empty($address)) {
        $errors[] = "Error: Empty field.<br>Address is required.";
    }

    if (empty($company_id) || !is_numeric($company_id)) {
        $errors[] = "Error: Invalid company ID.<br>Company ID has not been passed.";
    }

    if (empty($errors)) {
        try {
            // Prepare SQL query to update the record
            $sql = "UPDATE company_info SET company_name = :company_name, email = :email, phone = :phone, address = :address WHERE company_id = :companyId";
            $stmt = $db->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':company_name', $company_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':companyId', $company_id, PDO::PARAM_INT);

            // Execute the statement
            if ($stmt->execute()) {
                $success = "A company info was updated successfully.";
            } else {
                $errors[] = "Failed to update a company info.";
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

function getCompanyData($db) {
  $sql = "SELECT c.*, 
            CASE 
                WHEN a.company_id IS NOT NULL THEN 'Yes'
                ELSE 'No'
            END AS is_admin
          FROM company_info c
          LEFT JOIN admin a
            ON c.company_id = a.company_id";
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
                <a href="company.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create Company Info</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="viewcompany.php" class="nav-link active">
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
                if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['companyId'])) {
              ?>
                <li class="breadcrumb-item"><a href="viewcompany.php">View Company Info</a></li>
                <li class="breadcrumb-item active">Edit Company Info</li>
              <?php } else { ?>
                <li class="breadcrumb-item active">View Company Info</li>
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
          <!-- left column -->
          <div class="col-md-12">
          <?php 
          if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['companyId'])) {

            $company_id = $_GET['companyId'];
            $sql = "SELECT * FROM company_info WHERE company_id = :companyId";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':companyId', $company_id);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
             $company_name = $row['company_name'];
             $email = $row['email'];
             $phone = $row['phone'];
             $address = $row['address'];
             $company_id = $row['company_id'];
            }
          ?>
          <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Edit Company Info</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="card-body">
                  <input type="hidden" name="companyId" value="<?php echo $company_id; ?>">
                    <div class="form-group">
                      <label for="companyname">Company Name</label>
                      <input type="text" name="companyname" class="form-control" id="companyname" placeholder="Enter company name" value="<?php echo $company_name; ?>">
                    </div>

                  <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo $email; ?>">
                    </div>
                  </div>
                   <div class="col-md-6">
                    <div class="form-group">
                      <label for="pnone">Phone</label>
                      <input type="text" name="phone" class="form-control" id="phone" value="<?php echo $phone; ?>">
                    </div>
                  </div>
                  </div>

                    <div class="form-group">
                      <label for="autoResizeTextarea">Address</label>
                      <textarea class="form-control" name="address" rows="3" id="autoResizeTextarea" style="line-height: 2;" placeholder="Enter Address value="<?php echo $address; ?>"><?php echo $address; ?></textarea>
                    </div>
                    
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                    <input type="submit" name="submit" class="btn btn-primary" value="Confirm">
                  </div>
                </form>
              </div>
              </div>
            </div>
          <?php } else { ?>
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">DataTable for viewing company info</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Company Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $rows = getCompanyData($db);
                    foreach ($rows as $row) { ?>
                  <tr>
                    <td>
                      <?php echo $row['company_name'];
                          if ($row['is_admin'] == 'Yes') { ?>
                      <span class="right badge badge-danger">Admin</span>
                      <?php } ?>
                    </td>
                    <td>
                      <?php echo $row['email'] ?>
                    </td>
                    <td>
                      <?php echo $row['phone'] ?>
                    </td>
                    <td>
                      <?php echo $row['address'] ?>
                    </td>
                    <td style="width: 150px;">
                     <div class="row">
                        <div class="col-5">
                          <?php 
                          $company_id = $row['company_id'];
                          echo "<a class='button' href='viewcompany.php?action=edit&companyId=$company_id' onclick='return ConfirmEdit();'><button class='btn btn-block btn-success btn-sm'>Edit</button></a>" ?>
                        </div>
                        <div class="col-7">
                          <?php if ($row['is_admin'] == 'Yes') {
                            echo "<a class='button' href='viewcompany.php?action=unassign&companyId=$company_id' onclick='return ConfirmDelete();'><button class='btn btn-block btn-danger btn-sm'>Unassign</button></a>"; 
                          } else { 
                            echo "<a class='button' href='viewcompany.php?action=delete&companyId=$company_id' onclick='return ConfirmDelete();'><button class='btn btn-block btn-danger btn-sm'>Delete</button></a>"; } ?>
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
  const textarea = document.getElementById('autoResizeTextarea');

  // Function to resize the textarea
  function resizeTextarea() {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 10 + 'px';
  }

  // Resize on input
  textarea.addEventListener('input', resizeTextarea);

  // Initial resize to fit content after it has been set
  resizeTextarea();
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