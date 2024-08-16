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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $numberquantity = isset($_POST['numberquantity']) ? $_POST['numberquantity'] : null;
  $typedisease = isset($_POST['typedisease']) ? $_POST['typedisease'] : null;
  $deaddate = isset($_POST['deaddate']) ? $_POST['deaddate'] : null;
  $breedid = isset($_POST['breedid']) ? $_POST['breedid'] : null;
  $errors = [];

  // Validate inputs
  if (empty($breedid)) {
      $errors[] = "Error: Unselected value.<br>Please select a Breed Type.";
  } elseif (!is_numeric($breedid)) {
      $errors[] = "Error: Invalid value.<br>Breed ID must be a number.";
  }

  if (empty($numberquantity)) {
      $errors[] = "Error: Empty field.<br>Dead Count is required.";
  } elseif (!is_numeric($numberquantity)) {
      $errors[] = "Error: Invalid value.<br>Dead Count must be a number.";
  }

  if (empty($typedisease)) {
      $errors[] = "Error: Empty field.<br>Type of disease is required.";
  }

  if (empty($deaddate)) {
      $errors[] = "Error: Empty field.<br>Dead date is required.";
  }

  if (empty($errors)) {
      // Fetch the current stock animal for the breed_id
      $stmt = $db->prepare("SELECT stock_animal FROM breed_animal WHERE breed_id = :breedid");
      $stmt->bindParam(':breedid', $breedid, PDO::PARAM_INT);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row) {
          $stock_animal = $row['stock_animal'];

          if ($numberquantity <= $stock_animal) {
              try {
                  // Insert into dead_records
                  $sql = "INSERT INTO dead_records (number_quantity, type_disease, dead_date, breed_id) 
                          VALUES (:numberquantity, :typedisease, :deaddate, :breedid)";
                  $stmt = $db->prepare($sql);
                  $stmt->bindParam(':numberquantity', $numberquantity, PDO::PARAM_INT);
                  $stmt->bindParam(':typedisease', $typedisease, PDO::PARAM_STR);
                  $stmt->bindParam(':deaddate', $deaddate, PDO::PARAM_STR);
                  $stmt->bindParam(':breedid', $breedid, PDO::PARAM_INT);
                  $stmt->execute();

                  // Update the stock_animal in breed_animal
                  $total = $stock_animal - $numberquantity;
                  $sql_update = "UPDATE breed_animal SET stock_animal = :total WHERE breed_id = :breedid";
                  $stmt = $db->prepare($sql_update);
                  $stmt->bindParam(':total', $total, PDO::PARAM_INT);
                  $stmt->bindParam(':breedid', $breedid, PDO::PARAM_INT);
                  $stmt->execute();

                  $success = "Dead record inserted successfully.";
              } catch (PDOException $e) {
                  $errors[] = "Error: " . $e->getMessage();
              }
          } else {
              $errors[] = "Error: Dead quantity exceeds stock number.";
          }
      } else {
          $errors[] = "Error: Breed ID not found.";
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
                <a href="deadrecord.php" class="nav-link active">
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
                <li class="breadcrumb-item active">Create Dead Records</li>
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
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Add A New Dead Record</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                  <div class="card-body">

                  <div class="form-group">
                      <label for="animalType">Animal Type</label>
                      <select name="animalid" id="animalType" class="form-control select2">
                          <option value="">Select Animal Type First</option>
                          <?php 
                          // Assuming $db is your database connection
                          $sqlc = "SELECT * FROM animal_type ORDER BY animal_type ASC";
                          $result = $db->query($sqlc);

                          foreach($result as $row) {
                            if ($row['animal_id'] == $animal_id) {
                          ?>
                              <option value="<?php echo $row['animal_id']; ?>" selected><?php echo $row['animal_type']; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $row['animal_id']; ?>"><?php echo $row['animal_type']; ?></option>
                          <?php }} ?>
                      </select>
                  </div>

                  <div class="form-group">
                      <label for="breedid">Breed Type</label>
                      <select name="breedid" id="breedid" class="form-control select2">
                          <option value="">Select Breed Type</option>
                      </select>
                  </div>

                  <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="in-stock">In-stock</label>
                      <input type="text" name="in-stock" class="form-control" id="in-stock" placeholder="Select First" disabled>
                    </div>
                  </div>
                  <div class="col-md-10">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Dead Counts</label>
                      <input type="number" name="numberquantity" class="form-control" id="exampleInputEmail1" placeholder="Enter number quatity">
                    </div>
                  </div>
                  </div>

                  <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Type of Disease</label>
                      <input type="text" name="typedisease" class="form-control" id="exampleInputEmail1" placeholder="Enter type disease">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="deaddate">Dead Date</label>
                      <input type="date" name="deaddate" class="form-control" id="deaddate">
                    </div>
                  </div>
                  </div>
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                  </div>
                </form>
              </div>
              <!-- /.card -->
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
$(document).ready(function() {
    // Function to get query parameters from the URL
    function getQueryParam(param) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Get breedId from the URL if it exists
    var breedId = getQueryParam('breedId');

    function loadBreeds() {
        var animalId = $('#animalType').val();

        if (animalId) {
            $.ajax({
                type: 'POST',
                url: 'dist/php/get_breeds.php',
                data: { animal_id: animalId, breed_id: breedId },
                success: function(response) {
                    console.log("Response from server: ", response);  // Debugging line
                    $('#breedid').html(response);
                    if (breedId) {
                        $('#breedid').val(breedId).trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr);
                    alert('AJAX error: ' + error);
                }
            });
        } else {
            $('#breedid').html('<option value="">Select Breed Type</option>');
        }
    }

    $('#animalType').change(loadBreeds);

    // Trigger the change event if there is a pre-selected option
    if ($('#animalType').val()) {
        loadBreeds();
    }

    function fetchStockData() {
        const breedId = $('#breedid').val();
        if (breedId) {
            fetch('dist/php/get_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `breed_id=${breedId}`
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('in-stock').value = data.stock;
            })
            .catch(error => console.error('Error fetching stock data:', error));
        } else {
            document.getElementById('in-stock').value = '';
        }
    }

    $('#breedid').change(fetchStockData);

    // Fetch data on page load if breedId already has a value
    if ($('#breedid').val()) {
        fetchStockData();
    }
});
</script>
<script>
  // Get the current date
  var today = new Date();

  // Format the date to YYYY-MM-DD
  var formattedDate = today.toISOString().split('T')[0];

  // Set the value of the date input
  document.getElementById('deaddate').value = formattedDate;
</script>
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
</body>
</html>