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

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['animalId'])) {
  $animal_id = $_GET['animalId'];
  $errors = [];

  // Validate the animal_id
  if (empty($animal_id) || !is_numeric($animal_id)) {
      $errors[] = "Error: Invalid animal ID.<br>Animal ID has not been passed.";
  }

  if (empty($errors)) {
      try {
          // Prepare SQL query to select the photo path
          $sql = "SELECT animal_photo FROM animal_type WHERE animal_id = :animalId";
          $stmt = $db->prepare($sql);

          // Bind parameters
          $stmt->bindParam(':animalId', $animal_id, PDO::PARAM_INT);
          $stmt->execute();

          $result = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($result) {
              $previous_photo_path = "upload/" . $result["animal_photo"];

              // Delete the previous photo if it exists
              if (!empty($previous_photo_path) && file_exists($previous_photo_path)) {
                // Prepare SQL query to delete the record
                $sql = "DELETE FROM animal_type WHERE animal_id = :animalId";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':animalId', $animal_id, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                  if (unlink($previous_photo_path)) {
                    $success = "Animal record and photo deleted successfully.";
                  } else {
                      $errors[] = "Error: Unable to delete the previous photo.";
                  }
                } else {
                    $errors[] = "Failed to delete animal record.";
                }
              } else {
                  $errors[] = "No previous photo found or file does not exist.";
              }
          } else {
              $errors[] = "No record found with the provided animal ID.";
          }
      } catch (PDOException $e) {
          $errors[] = "Error: Please delete the related records first.";
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
    // Retrieve and sanitize POST data
    $animal_id = isset($_POST['animal_id']) ? trim($_POST['animal_id']) : null;
    $animaltype = isset($_POST['animaltype']) ? trim($_POST['animaltype']) : null;
    $animaldes = isset($_POST['animaldes']) ? trim($_POST['animaldes']) : null;
    $current_photo = isset($_POST['current_photo']) ? trim($_POST['current_photo']) : null;
    $errors = [];

    // Validate inputs
    if (empty($animal_id)) {
        $errors[] = "Error: Empty field.<br>Animal ID is required.";
    }

    if (empty($animaltype)) {
        $errors[] = "Error: Empty field.<br>Animal type name is required.";
    }

    if (empty($animaldes)) {
        $errors[] = "Error: Empty field.<br>Animal description is required.";
    }

    if (empty($errors)) {
        if (!empty($_FILES["uploaded_file"]) && $_FILES['uploaded_file']['error'] == 0) {
            // Check if the file is JPEG or PNG image
            $dir = "upload/";
            $filename = $_FILES['uploaded_file']['name'];
            $filepath = $dir . basename($filename);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, ["jpg", "png"])) {
                // Check if the file with the same name already exists on the server
                if (!file_exists($filepath)) {
                    $previous_photo_path = "upload/" . $current_photo;
                    if (!empty($current_photo) && file_exists($previous_photo_path)) {
                        if (!unlink($previous_photo_path)) {
                            $errors[] = "Error: Unable to delete the previous photo.";
                        }
                    }

                    if (empty($errors)) {
                        $sql = "UPDATE animal_type SET animal_type = :animalType, animal_photo = :animalPhoto, animal_des = :animalDes WHERE animal_id = :animalId";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':animalType', $animaltype);
                        $stmt->bindParam(':animalPhoto', $filename);
                        $stmt->bindParam(':animalDes', $animaldes);
                        $stmt->bindParam(':animalId', $animal_id);

                        if ($stmt->execute()) {
                            move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $filepath);
                            $success = "Animal type \"".$animaltype."\" updated successfully.";
                        } else {
                            $errors[] = "Error: A problem occurred during the update.";
                        }
                    }
                } else {
                    $errors[] = "Error: File already exists.";
                }
            } else {
                $errors[] = "Error: Only .jpg or .png images are accepted for upload.";
            }
        } else {
            $sql = "UPDATE animal_type SET animal_type = :animalType, animal_des = :animalDes WHERE animal_id = :animalId";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':animalType', $animaltype);
            $stmt->bindParam(':animalDes', $animaldes);
            $stmt->bindParam(':animalId', $animal_id);

            if ($stmt->execute()) {
                $success = "Animal type \"".$animaltype."\" updated successfully.";
            } else {
                $errors[] = "Error: A problem occurred during the update.";
            }
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

          <li class="nav-item menu-open">
            <a href="animaltype.php" class="nav-link active">
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
                <a href="viewanimal.php" class="nav-link active">
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
            <ol class="breadcrumb float-sm-left">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <?php 
                if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['animalId'])) {
              ?>
                <li class="breadcrumb-item"><a href="viewanimal.php">View Animal Type</a></li>
                <li class="breadcrumb-item active">Edit Animal Type</li>
              <?php } else { ?>
                <li class="breadcrumb-item active">View Animal Type</li>
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
          if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['animalId'])) {

            $animal_id = $_GET['animalId'];
            $sql = "SELECT * FROM animal_type WHERE animal_id = :animalId";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':animalId', $animal_id);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
              $animal_type = $row['animal_type'];
              $animal_photo = $row['animal_photo'];
              $animal_des = $row['animal_des'];
            }
          ?>
          <div class="col-md-2">
          </div>
          <!-- left column -->
          <div class="col-md-8">
          <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Edit Animal Type</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                <div class="card-body">
                <input type="hidden" name="animal_id" value="<?php echo $animal_id; ?>">
                  <div class="form-group">
                    <label for="animaltype">Animal Type</label>
                    <input type="text" name="animaltype" class="form-control" id="animaltype" placeholder="Enter animal type name" value="<?php echo $animal_type; ?>">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputFile">Animal Photo</label>
                    <div class="input-group">
                      <div class="custom-file">
                      <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($animal_photo); ?>">
                        <input type="file" name="uploaded_file" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile"><?php echo $animal_photo; ?></label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                        <label for="autoResizeTextarea">Animal Description</label>
                        <textarea class="form-control" name="animaldes" rows="3" id="autoResizeTextarea" style="line-height: 2;" placeholder="Enter Description"><?php echo $animal_des; ?></textarea>
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
                <h3 class="card-title">DataTable for viewing animal types</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Animal Type</th>
                    <th>Photo</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $sql = "SELECT * FROM animal_type";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                    while ($row = $stmt->fetch()) { ?>
                  <tr>
                    <td>
                      <?php echo $row['animal_type'] ?>
                    </td>
                    <td style="width: 150px;">
                      <img src="upload/<?php echo $row['animal_photo'] ?>" width="150px" height="auto">
                    </td>
                    <td style="max-width: 300px;">
                    <?php
                    // Check if the text is longer than 700 characters
                        $animalDes = $row['animal_des'];
                        if (strlen($animalDes) > 700) {
                            $animalDes = substr($animalDes, 0, 700) . '...'; 
                            // Truncate the text to 700 characters and add ellipsis
                        }
                       echo $animalDes; ?>
                    </td>
                    <td>
                    <div class="row mb-2">
                        <div class="col-12">
                          <?php 
                          $animal_id = $row['animal_id'];
                          echo "<a href='viewanimal.php?action=edit&animalId=$animal_id' onclick='return ConfirmEdit();'><button class='btn btn-block btn-success btn-sm'>Edit</button></a>" ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                          <?php 
                          
                          echo "<a href='viewanimal.php?action=delete&animalId=$animal_id' onclick='return ConfirmDelete();'><button class='btn btn-block btn-danger btn-sm'>Delete</button></a>" ?>
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
          <!--/.col (right) -->
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
