<?php
session_start();
include('admin/dist/php/database.php');

// Handle form submission for updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $required_fields = ['contact_name', 'email', 'phone', 'contact_address'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "$field is required.";
        } else {
            $$field = $_POST[$field];
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO contact_us (contact_name, email, phone, contact_address) 
                VALUES (:contact_name, :email, :phone, :contact_address)";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':contact_name', $contact_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':contact_address', $contact_address);

        if ($stmt->execute()) {
            $status = "success";
        } else {
            $status = "error";
        }
    } else {
        // If there are errors, you can handle them as needed, such as displaying them to the user
        $status = "error";
    }

    header("Location: contact.php?status=$status#target");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['status'])) {
    $status = $_GET['status'];
} else {
    $status = "";
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Contact Us | Animal Husbandry Knowledge Sharing In South Shan State</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&family=Rubik:wght@400;500&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="img/project-logo.ico">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-fluid p-0">
            <nav class="navbar navbar-expand-lg fixed-top navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="index.php" class="navbar-brand p-0">
                    <h1 class="display-6 text-primary m-0">
                    <h1 class="display-6 text-primary m-0">
                        <img src="img/Logo.png" alt="Logo">
                        Animal Husbandry
                    </h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="index.php#about" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Our Farm</a>
                        <a href="blog.php" class="nav-item nav-link">Our Blog</a>
                        <a href="contact.php" class="nav-item nav-link active">Contact Us</a>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Navbar & Hero End -->


         <!-- Header Start -->
         <div class="container-fluid bg-breadcrumb">
            <ul class="breadcrumb-animation">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div class="container text-center py-5" style="max-width: 900px;">
                <h3 class="display-3 mb-4 wow fadeInDown" data-wow-delay="0.1s">Contact Us</h1>
                <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Contact</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->


        <!-- Contact Start -->
        <div class="container-fluid contact py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h4 class="text-primary mb-4">Contact Us</h4>
                    <h1 class="display-5 mb-4" id="target">Get In Touch With Us</h1>
                    <p class="mb-0">Connect with us for more information or any inquiries you may have. Whether you have questions about our farm, need assistance, or want to learn more about our services, we're here to help. Reach out to us via phone or email!
                    </p>
                </div>
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                        <h2 class="display-5 mb-2">Our Contact Form</h2>
                        <p class="mb-4">Fill out the contact form below to get in touch with us. We're here to answer your questions, provide information, and assist you in any way we can. We look forward to hearing from you!</p>
                        <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                            <div class="row g-3">
                                <div class="row g-3">
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating">
                                            <input type="text" name="contact_name" class="form-control" id="name" placeholder="Your Name">
                                            <label for="name">Your Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="form-floating text-primary">
                                        <p class='mb-0'>
                                        <?php
                                            echo !empty($status == 'success')? "Thank you for reaching out! Your message has been submitted." : (!empty($status == 'error')?
                                            "Something went wrong!<br>Please try again." : '') ;
                                        ?>
                                        </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Your Email">
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="phone" name="phone" class="form-control" id="phone" placeholder="Phone">
                                        <label for="phone">Your Phone</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="contact_address" placeholder="Leave a message here" id="message" style="height: 160px"></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12" id="location">
                                    <input type="submit" class="btn btn-primary w-100 py-3" name="submit" value="Send Message">
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php 
                        $sql = "SELECT * FROM company_info LIMIT 3";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetchAll()
                    ?>
                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-users fa-2x text-primary"></i></div>
                            <div class="ms-4">
                                <h4>Our Team Members</h4>
                                <?php foreach ($result as $row) {
                                    echo "<p class='mb-0'>" . $row['company_name'] . "</p>";
                                } ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-map-marker-alt fa-2x text-primary"></i></div>
                            <div class="ms-4">
                                <h4>Addresses</h4>
                                <?php foreach ($result as $row) {
                                    echo "<p class='mb-0'>" . $row['address'] . "</p>";
                                } ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-phone-alt fa-2x text-primary"></i></div>
                            <div class="ms-4">
                                <h4>Mobile</h4>
                                <?php foreach ($result as $row) {
                                    echo "<p class='mb-0'>" . $row['phone'] . "</p>";
                                } ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light d-flex align-items-center justify-content-center mb-3" style="width: 90px; height: 90px; border-radius: 50px;"><i class="fa fa-envelope-open fa-2x text-primary"></i></div>
                            <div class="ms-4">
                                <h4>Email</h4>
                                <?php foreach ($result as $row) {
                                    echo "<p class='mb-0'>" . $row['email'] . "</p>";
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="rounded h-100">
                            <iframe class="rounded w-100" 
                            style="height: 500px;" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=loilem&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" 
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->


        <!-- Footer Start -->
        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="text-dark mb-4">Reach Us</h4>
                            <a href="contact.php"> Contact Us</a>
                            <a href="contact.php#target"> Our Team</a>
                            <a href="contact.php#location"> Our Location</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="mb-4 text-dark">Quick Links</h4>
                            <a href="service.php#animals"> Our Farm</a>
                            <a href="service.php#products"> Our Products</a>
                            <a href="blog.php#target"> Our Blog</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="mb-4 text-dark">Services</h4>
                            <a href="admin/dashboard.php"> Admin Panel</a>
                            <a href=""> Product Updates</a>
                            <a href=""> Blog Updates</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="mb-4 text-dark">Contact Info</h4>
                            <a href=""><i class="fa fa-map-marker-alt me-2"></i> Shwegondaing, Delta Plaza</a>
                            <a href=""><i class="fas fa-envelope me-2"></i> goldentkm@gmail.com</a>
                            <a href=""><i class="fas fa-phone me-2"></i> +959695129912</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        
        <!-- Copyright Start -->
        <div class="container-fluid copyright py-4">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-md-0">
                        <span class="text-white"><a href="http://goldentkm.com.mm" target="_blank"><i class="fas fa-copyright text-light me-2"></i>GoldenTKM Co. Ltd</a>, All right reserved.</span>
                    </div>
                    <div class="col-md-6 text-center text-md-end text-white">
                        <!--/*** This template is free as long as you keep the below author’s credit link/attribution link/backlink. ***/-->
                        <!--/*** If you'd like to use the template without the below author’s credit link/attribution link/backlink, ***/-->
                        <!--/*** you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". ***/-->
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    </body>

</html>