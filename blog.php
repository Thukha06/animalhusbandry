<?php
    session_start();
    include('admin/dist/php/database.php');
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Our Blogs | Animal Husbandry Knowledge Sharing In South Shan State</title>
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
                        <a href="blog.php" class="nav-item nav-link active">Our Blog</a>
                        <a href="contact.php" class="nav-item nav-link">Contact Us</a>
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
                <h3 class="display-3 mb-4 wow fadeInDown" data-wow-delay="0.1s">Our Blog</h1>
                <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Blog</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->


        <!-- Blog Start -->
        <div class="container-fluid blog py-5" id="target">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h4 class="text-primary">Our Blog</h4>
                    <h1 class="display-5 mb-4">Welcome to Our Blog!</h1>
                    <p class="mb-0">
                        Here, we share fascinating facts and insights about the animals on our farm. From their unique behaviors and characteristics to their care and contributions, our blog offers a deeper understanding of farm life. Join us in exploring the wonderful world of our animals!
                    </p>
                </div>
                <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'more' && isset($_GET['breed_id'])) {

                        $breed_id = $_GET['breed_id'];
                        $sql = "SELECT knowledge_type.*, breed_technology.*, breed_animal.stock_animal
                                FROM knowledge_type 
                                INNER JOIN breed_animal
                                    ON knowledge_type.breed_id = breed_animal.breed_id
                                INNER JOIN breed_technology
                                    ON knowledge_type.breed_id = breed_technology.breed_id
                                WHERE knowledge_type.breed_id = :breedId";
                        
                        $stmt = $db->prepare($sql);

                        $stmt->bindParam(':breedId', $breed_id);
                        $stmt->execute();

                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="row g-5 pt-5">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                        <div class="feature-img h-100" style="object-fit: cover;">
                            <video class="img-fluid w-100 h-100" controls>
                                <source src="admin/video/<?php echo htmlentities($row['video_photo'])?>" type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.1s">
                        <h1 class="display-5 mb-4">
                            <?php echo htmlspecialchars($row['knowledge_type'], ENT_QUOTES, 'UTF-8'); ?>
                        </h1>
                        <h4 class="text-primary">
                            <?php echo htmlspecialchars($row['breed_type'], ENT_QUOTES, 'UTF-8'); ?>
                        </h4>
                        <p class="mb-4">
                            <?php echo htmlspecialchars($row['breed_des'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="d-flex">
                                    <div class="d-flex flex-column ms-3">
                                        <h2 class="mb-0 fw-bold">
                                            <?php echo htmlspecialchars(isset($row['stock_animal'])? $row['stock_animal'] : 'No data', ENT_QUOTES, 'UTF-8'); ?>
                                        </h2>
                                        <small class="text-dark">Current In-stock</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                    <!-- 404 Start -->
                    <div class="container-fluid py-5">
                        <div class="container py-5 text-center">
                            <div class="row justify-content-center">
                                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                                    <i class="bi bi-exclamation-triangle display-1 text-secondary"></i>
                                    <h1 class="display-1">404</h1>
                                    <h1 class="mb-4">Page Not Found</h1>
                                    <p class="mb-4">We’re sorry, the page you have looked for does not exist in our website or<br>there are no entries yet! Maybe go to our home page?</p>
                                    <a class="btn btn-primary rounded-pill py-3 px-5" href="index.php">Go Back To Home</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 404 End -->
                <?php } } else { ?>
                <div class="row g-4 justify-content-center">
                    <?php 
                        $sql = "SELECT * 
                                FROM knowledge_type
                                INNER JOIN breed_technology
                                    ON knowledge_type.breed_id = breed_technology.breed_id
                                ORDER BY knowledge_id DESC";
                        $stmt = $db->query($sql);
                        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                        while ($row = $stmt->fetch()) {
                    ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="blog-item">
                            <div class="blog-img">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['breed_photo'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100" alt="" />
                            </div>
                            <div class="blog-content text-dark border p-4">
                                <h5 class="mb-4">
                                    <?php echo htmlspecialchars($row['knowledge_type'], ENT_QUOTES, 'UTF-8'); ?>
                                </h5>
                                <a class="btn btn-light rounded-pill py-2 px-4" href="blog.php?action=more&breed_id=<?php echo htmlspecialchars($row['breed_id']); ?>#target">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <!-- Blog End -->


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
                            <a href=""> All Services</a>
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