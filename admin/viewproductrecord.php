<?php
session_start();
include('dist/php/database.php');

if (isset($_SESSION['company_id'], $_SESSION['company_name'])) {
  $company_name = $_SESSION['company_name'];
} else {
  header("Location: index.php");
  exit();
}

$success = null;
$error = [];

function setFeedback($type, $message, &$success, &$error) {
    if ($type === 'success') {
        $success = $message;
    } elseif ($type === 'error') {
        $error[] = $message;
    }

    if (!empty($error)) {
      echo "<script>
              localStorage.setItem('errors', '" . json_encode($error) . "');
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

// Handle delete request for product record
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['productrecordId'])) {
    $product_record_id = $_GET['productrecordId'];

    if (empty($product_record_id) || !is_numeric($product_record_id)) {
        $error[] = "Invalid product record ID.";
    } else {
        $sql = "DELETE FROM product_records WHERE product_record_id = :productrecordId";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':productrecordId', $product_record_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                setFeedback('success', "A Product Record was deleted successfully", $success, $error);
            } else {
                setFeedback('error', "Failed to delete a product record", $success, $error);
            }
        } catch (PDOException $e) {
            setFeedback('error', "Error: " . $e->getMessage(), $success, $error);
        }
    }
}

// Handle delete request for product and its photo
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    if (empty($product_id) || !is_numeric($product_id)) {
        $error[] = "Invalid product ID.";
    } else {
        $sql = "SELECT product_photo FROM product_type WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $previous_photo_path = "upload/" . $row["product_photo"];
        }

        if (!empty($previous_photo_path) && file_exists($previous_photo_path)) {
          try {
            // Delete from all related tables
            $sql = "DELETE FROM product_records WHERE product_id = :product_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            
            $sql = "DELETE FROM product_type WHERE product_id = :product_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
            
            if (unlink($previous_photo_path)) {
                  setFeedback('success', "Previous photo and product deleted successfully", $success, $error);
            } else {
                setFeedback('error', "No previous photo found or file does not exist", $success, $error);
            }
          } catch (PDOException $e) {
              setFeedback('error', "Error: Please delete the related records first.", $success, $error);
          }
        } else {
            setFeedback('error', "Error: Unable to delete the previous photo", $success, $error);
        }
    }
}

// Handle form submission for updating product record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EditRecord'])) {
    $product_record_id = $_POST['productrecordId'];
    $product_quantity = $_POST['productquantity'];
    $product_date = $_POST['date'];

    $errors = [];
    if (empty($product_record_id) || !is_numeric($product_record_id)) {
        $errors[] = "Invalid product record ID.";
    }
    if (empty($product_quantity) || !is_numeric($product_quantity)) {
        $errors[] = "Invalid product quantity.";
    }
    if (empty($product_date)) {
        $errors[] = "Invalid product date.";
    }

    if (empty($errors)) {
        $sql = "UPDATE product_records 
                SET product_quantity = :product_quantity, product_date = :product_date 
                WHERE product_record_id = :productrecordId";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':product_quantity', $product_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_date', $product_date, PDO::PARAM_STR);
        $stmt->bindParam(':productrecordId', $product_record_id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                setFeedback('success', "A Product Record was updated successfully", $success, $error);
            } else {
                setFeedback('error', "Failed to update a product record", $success, $error);
            }
        } catch (PDOException $e) {
            setFeedback('error', "Error: " . $e->getMessage(), $success, $error);
        }
    }
}

// Handle form submission for updating product type
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EditProduct'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_unit = $_POST['product_unit'];
    $product_description = $_POST['product_description'];
    $current_photo = $_POST['current_photo'];

    $errors = [];
    if (empty($product_id) || !is_numeric($product_id)) {
        $errors[] = "Invalid product ID.";
    }
    if (empty($product_name)) {
        $errors[] = "Product name is required.";
    }
    if (empty($product_unit)) {
        $errors[] = "Product unit is required.";
    }
    if (empty($product_description)) {
        $errors[] = "Product description is required.";
    }

    if (empty($errors)) {
        if (!empty($_FILES["uploaded_file"]) && $_FILES['uploaded_file']['error'] == 0) {
            $dir = "upload/";
            $filename = $_FILES['uploaded_file']['name'];
            $filepath = $dir . basename($filename);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if ($ext == "jpg" || $ext == "png") {
                if (!file_exists($filepath)) {
                    $previous_photo_path = $dir . $current_photo;
                    if (file_exists($previous_photo_path)) {
                        unlink($previous_photo_path);
                    }

                    $sql = "UPDATE product_type 
                            SET product_name = :product_name, product_unit = :product_unit, 
                            product_photo = :product_photo, product_description = :product_description 
                            WHERE product_id = :product_id";
                    $stmt = $db->prepare($sql);

                    $stmt->bindParam(':product_name', $product_name);
                    $stmt->bindParam(':product_unit', $product_unit);
                    $stmt->bindParam(':product_photo', $filename);
                    $stmt->bindParam(':product_description', $product_description);
                    $stmt->bindParam(':product_id', $product_id);

                    if ($stmt->execute()) {
                        move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $filepath);
                        setFeedback('success', "A Product was updated successfully with new photo", $success, $error);
                    } else {
                        setFeedback('error', "Failed to update a product", $success, $error);
                    }
                } else {
                    setFeedback('error', "A file with the same name already exists", $success, $error);
                }
            } else {
                setFeedback('error', "Error: Only .jpg or .png images are accepted for upload", $success, $error);
            }
        } else {
            $sql = "UPDATE product_type 
                    SET product_name = :product_name, product_unit = :product_unit, product_description = :product_description 
                    WHERE product_id = :product_id";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':product_unit', $product_unit);
            $stmt->bindParam(':product_description', $product_description);
            $stmt->bindParam(':product_id', $product_id);

            if ($stmt->execute()) {
                setFeedback('success', "A Product was updated successfully without new photo", $success, $error);
            } else {
                setFeedback('error', "Failed to update product type", $success, $error);
            }
        }
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

          <li class="nav-item menu-open">
            <a href="productrecord.php" class="nav-link active">
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
                <a href="viewproductrecord.php" class="nav-link active">
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
                if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['productrecordId'])) {
              ?>
                <li class="breadcrumb-item"><a href="viewproductrecord.php#records">View Product Records</a></li>
                <li class="breadcrumb-item active">Edit Product Records</li>
              <?php } elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['product_id'])) {  ?>
                <li class="breadcrumb-item"><a href="viewproductrecord.php#products">View Product Records</a></li>
                <li class="breadcrumb-item active">Edit Product</li>
              <?php } else { ?>
                <li class="breadcrumb-item active">View Product Records</li>
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
            if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['productrecordId'])) {

              $product_record_id = $_GET['productrecordId'];
              $sql = "SELECT * 
                      FROM product_records 
                      INNER JOIN product_type
                        ON product_records.product_id = product_type.product_id
                      WHERE product_record_id = :productrecordId";
              $stmt = $db->prepare($sql);

              $stmt->bindParam(':productrecordId', $product_record_id);
              $stmt->execute();

              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($result as $row) {
                $product_record_id = $row['product_record_id'];
                $product_record = $row['product_name'];
                $product_quantity = $row['product_quantity'];
                $product_unit = $row['product_unit'];
                $product_date = $row['product_date'];
              }
          ?>
          <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
              <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Edit Product Record</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                    <div class="card-body">
                    <input type="hidden" name="productrecordId" value="<?php echo $product_record_id; ?>">
                      <div class="form-group">
                        <label for="productrecord">Product Name</label>
                        <input type="text" name="productname" class="form-control" id="productname" placeholder="Enter product name" value="<?php echo $product_record; ?>" readonly>
                      </div>

                      <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="productquantity">Product Quantity</label>
                          <input type="number" name="productquantity" class="form-control" id="productquantity" placeholder="Enter product quantity" value="<?php echo $product_quantity; ?>">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="unit">Unit</label>
                          <input type="text" name="unit" class="form-control" id="unit" placeholder="Product Unit" value="<?php echo $product_unit; ?>" disabled>
                        </div>
                      </div>
                      </div>

                      <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" class="form-control" id="date" value="<?php echo $product_date; ?>">
                      </div>
                      
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <input type="submit" name="EditRecord" class="btn btn-primary" value="Confirm">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php
            } elseif (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['product_id'])) { 

              $product_id = $_GET['product_id'];
              $sql = "SELECT * FROM product_type WHERE product_id = :product_id";
              $stmt = $db->prepare($sql);

              $stmt->bindParam(':product_id', $product_id);
              $stmt->execute();

              $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($result as $row) {
                $product_id = $row['product_id'];
                $product_name = $row['product_name'];
                $product_unit = $row['product_unit'];
                $product_photo = $row['product_photo'];
                $product_description = $row['product_description'];
              }
          ?>
            <div class="row">
              <div class="col-md-2">
              </div>
              <div class="col-md-8">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Edit Product</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                    <div class="card-body">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="product_name">Product Name</label>
                          <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Enter product name" value="<?php echo $product_name; ?>">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="product_unit">Unit</label>
                          <input type="text" name="product_unit" class="form-control" id="product_unit" placeholder="Enter product unit" value="<?php echo $product_unit; ?>">
                        </div>
                      </div>
                    </div>

                      <div class="form-group">
                        <label for="exampleInputFile">Product Photo</label>
                        <div class="input-group">
                          <div class="custom-file">
                          <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($product_photo); ?>">
                            <input type="file" name="uploaded_file" class="custom-file-input" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile"><?php echo $product_photo; ?></label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                            <label for="autoResizeTextarea">Product Description</label>
                            <textarea class="form-control" name="product_description" rows="3" style="line-height: 2;" id="autoResizeTextarea" placeholder="Enter Description"><?php echo $product_description; ?></textarea>
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <input type="submit" name="EditProduct" class="btn btn-primary" value="Confirm">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div class="card" id="products">
              <div class="card-header">
                <h3 class="card-title">DataTable for viewing products</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Product Name</th>
                    <th>Unit</th>
                    <th>Photo</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    // Fetch product records for display
                    $sql = "SELECT * 
                            FROM product_type";

                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($rows)) {
                      foreach ($rows as $row) {
                  ?>
                  <tr>
                    <td>
                      <?php echo $row['product_name'] ?>
                    </td>
                    <td>
                      <?php echo $row['product_unit'] ?>
                    </td>
                    <td style="width: 150px;">
                      <img src="upload/<?php echo $row['product_photo'] ?>" width="150px" height="auto">
                    </td>
                    <td style="max-width: 300px;">
                    <?php
                    // Check if the text is longer than 102 characters
                        $product_description = $row['product_description'];
                        if (strlen($product_description) > 700) {
                            $product_description = substr($product_description, 0, 708) . '...'; 
                            // Truncate the text to 102 characters and add ellipsis
                        }
                       echo $product_description; ?>
                    </td>
                    <td>
                    <div class="row mb-2">
                        <div class="col-12">
                          <?php 
                          $product_id = $row['product_id'];
                          echo "<a class='button' href='viewproductrecord.php?action=edit&product_id=$product_id' onclick='return ConfirmEdit();'><button class='btn btn-block btn-success btn-sm'>Edit</button></a>" ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                          <?php 
                          echo "<a class='button' href='viewproductrecord.php?action=delete&product_id=$product_id' onclick='return ConfirmDeleteProduct();'><button class='btn btn-block btn-danger btn-sm'>Delete</button></a>" ?>
                        </div>
                    </div>
                    </td>
                  </tr>
                  <?php } } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card" id="records">
              <div class="card-header">
                <h3 class="card-title">DataTable for viewing product records</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Product Name</th>
                    <th>Product Quantity</th>
                    <th>Unit</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                    // Fetch product records for display
                    $sql = "SELECT * 
                            FROM product_records
                            INNER JOIN product_type
                              ON product_records.product_id = product_type.product_id
                            ORDER BY product_records.product_date DESC, product_type.product_name";

                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($rows)) {
                      foreach ($rows as $row) {
                  ?>
                  <tr>
                    <td>
                      <?php echo $row['product_name'] ?>
                    </td>
                    <td>
                      <?php echo $row['product_quantity'] ?>
                    </td>
                    <td>
                      <?php echo $row['product_unit'] ?>
                    </td>
                    <td>
                      <?php echo $row['product_date'] ?>
                    </td>
                    <td style="max-width: 50px;">
                    <div class="row">
                        <div class="col-6">
                          <?php 
                          $product_record_id = $row['product_record_id'];
                          echo "<a class='button' href='viewproductrecord.php?action=edit&productrecordId=$product_record_id' onclick='return ConfirmEdit();'><button class='btn btn-block btn-success btn-sm'>Edit</button></a>" ?>
                        </div>
                        <div class="col-6">
                          <?php 
                          echo "<a class='button' href='viewproductrecord.php?action=delete&productrecordId=$product_record_id' onclick='return ConfirmDelete();'><button class='btn btn-block btn-danger btn-sm'>Delete</button></a>" ?>
                        </div>
                    </div>
                    </td>
                  </tr>
                  <?php } } ?>
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
      "responsive": true, "lengthChange": false, "autoWidth": false, "ordering": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $("#example2").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
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

  function ConfirmDeleteProduct(){
    var msg=confirm("Warning! Are you sure want to delete?\nDeleting a Product will also affect all of related records.");
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
    bsCustomFileInput.init();
  });
</script>
</body>
</html>