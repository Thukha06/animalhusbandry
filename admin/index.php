<?php
session_start();
include('dist/php/database.php');

if (isset($_SESSION['company_id'], $_SESSION['company_name'])) {
  unset($_SESSION['company_name']);
  unset($_SESSION['company_id']);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $email = $_POST['email'];
  $password = $_POST['password'];
  $sql = "SELECT * 
          FROM admin
          INNER JOIN company_info
            ON admin.company_id = company_info.company_id";
  $stmt = $db->query($sql);  
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

  while ($row = $stmt->fetch()) {

    if(($email == $row['email']) && ($password == $row['password'])) {

      $_SESSION['company_id'] = $row['company_id'];
      $_SESSION['company_name'] = $row['company_name'];
      echo '<script type="text/javascript">
            window.location.href="dashboard.php";
            </script>';
    } else {
      $error = "Please try again";
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
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="icon" type="image/x-icon" href="image/project-logo.ico">
  <style>
    .error {color: #FF0000;}
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="" class="h1"><b>Admin</b>AH</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to enter into Admin Panel</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <span class="error"><?php echo $error;?></span>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
          <div class="col-2">
            <a href="../" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="20" viewBox="0 2 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></a>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
