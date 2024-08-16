<?php
    session_start();
    include('admin/dist/php/database.php');
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Our Farms | Animal Husbandry Knowledge Sharing In South Shan State</title>
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

    <style>
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -15px; /* Adjust spacing between columns */
        }

        .col-md-6,
        .col-lg-4,
        .col-xl-3 {
            padding: 15px;
        }

        .service-item {
            display: flex;
            flex-direction: column;
            height: 100%; /* Ensure full height in column */
            border: 1px solid #ddd; /* Optional: Add border */
            border-radius: 8px; /* Optional: Add border radius */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Optional: Add box shadow */
            overflow: hidden; /* Optional: Ensure content does not overflow */
        }

        .service-icon {
            text-align: center;
            padding: 20px;
        }

        .service-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px;
            text-align: center;
        }

        .service-content h4 {
            margin-bottom: 15px;
        }

        .service-content p {
            flex: 1;
            margin-bottom: 15px;
        }

        .service-content .btn {
            margin-top: auto; /* Push the button to the bottom of the card */
        }
    </style>

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
                        <a href="service.php" class="nav-item nav-link active">Our Farm</a>
                        <a href="blog.php" class="nav-item nav-link">Our Blog</a>
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
                <h3 class="display-3 mb-4 wow fadeInDown" data-wow-delay="0.1s">See Our Farm</h1>
                <ol class="breadcrumb justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active text-primary">Service</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->

        <?php 
        if (isset($_GET['action']) && $_GET['action'] == 'more' && isset($_GET['animal_id'])) {

            $animal_id = $_GET['animal_id'];
            $sql = "SELECT breed_technology.*,
                    animal_type.animal_type, animal_type.animal_des
                    FROM breed_technology 
                    INNER JOIN animal_type
                        ON breed_technology.animal_id = animal_type.animal_id
                    WHERE breed_technology.animal_id = :animalId
                    ORDER BY breed_technology.breed_type ASC";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':animalId', $animal_id);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $animalType = htmlspecialchars($rows[0]["animal_type"], ENT_QUOTES, 'UTF-8');
                $animalDes = htmlspecialchars($rows[0]["animal_des"], ENT_QUOTES, 'UTF-8');
        ?>
        <!-- Service Start -->
        <div class="container-fluid service py-5" id="target">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h4 class="mb-1 text-primary">
                        <?php echo htmlspecialchars($animalType, ENT_QUOTES, 'UTF-8'); ?>
                    </h4>
                    <h1 class="display-5 mb-4">
                        See All Types of <?php echo htmlspecialchars($animalType, ENT_QUOTES, 'UTF-8'); ?> in Our Farm
                    </h1>
                    <p class="mb-2">
                        <?php echo htmlspecialchars($animalDes, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>
                <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'more' && isset($_GET['breed_id'])) {

                        $breed_id = $_GET['breed_id'];
                        $sql = "SELECT knowledge_type.*, breed_animal.stock_animal
                                FROM knowledge_type 
                                INNER JOIN breed_animal
                                    ON knowledge_type.breed_id = breed_animal.breed_id
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
                            <?php echo htmlspecialchars($rows[0]['breed_type'], ENT_QUOTES, 'UTF-8'); ?>
                        </h4>
                        <p class="mb-4">
                            <?php echo htmlspecialchars($rows[0]['breed_des'], ENT_QUOTES, 'UTF-8'); ?>
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
                    foreach ($rows as $row) {
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item text-center rounded p-4">
                            <div class="service-icon d-inline-block bg-light rounded p-4 mb-4">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['breed_photo'], ENT_QUOTES, 'UTF-8'); ?>" width="200px" height="150px" /> 
                            </div>
                            <div class="service-content">
                                <h4 class="mb-4">
                                    <?php echo htmlspecialchars($row['breed_type'], ENT_QUOTES, 'UTF-8'); ?>
                                </h4>
                                <p class="mb-4">
                                    <?php echo htmlspecialchars($row['breed_des'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <a href="service.php?action=more&animal_id=<?php echo htmlspecialchars($animal_id) ?>&breed_id=<?php echo htmlspecialchars($row['breed_id']); ?>#target" class="btn btn-light rounded-pill text-primary py-2 px-4">See More</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <!-- Service End -->
        <?php } } else { ?>
        <!-- Service Start -->
        <div class="container-fluid service py-5" id="animals">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h4 class="mb-1 text-primary">Our Farms</h4>
                    <h1 class="display-5 mb-4">See All Animals in Our Farm</h1>
                    <p class="mb-2">
                        Explore the diverse range of animals on our farm! From friendly goats and playful sheep to majestic horses and curious chickens, you'll get to see and learn about all the animals that call our farm home. Come and experience the charm and beauty of farm life up close.
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                <?php 
                    $sql = "SELECT * FROM animal_type ORDER BY animal_type ASC";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                    while ($row = $stmt->fetch()) {
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item text-center rounded p-4">
                            <div class="service-icon d-inline-block bg-light rounded p-4 mb-4">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['animal_photo'], ENT_QUOTES, 'UTF-8'); ?>" width="200px" height="150px" /> 
                            </div>
                            <div class="service-content">
                                <h4 class="mb-4">
                                    <?php echo htmlspecialchars($row["animal_type"], ENT_QUOTES, 'UTF-8'); ?>
                                </h4>
                                <p class="mb-4">
                                    <?php echo htmlspecialchars($row["animal_des"], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <a href="service.php?action=more&animal_id=<?php echo htmlspecialchars($row['animal_id']); ?>#target" class="btn btn-light rounded-pill text-primary py-2 px-4">See More</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
        <div class="container-fluid service py-5" id="products">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h1 class="display-5 mb-4">See All Products in Our Farm</h1>
                    <p class="mb-2">
                        Discover the wide range of high-quality products our farm offers. From fresh milk, eggs, and meat to wool and cheese, our farm provides nutritious and delicious items for your family. Explore our products and experience the best of farm-fresh goodness
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                <?php 
                    $sql = "SELECT * FROM product_type ORDER BY product_id ASC";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                    while ($row = $stmt->fetch()) {
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item text-center rounded p-4">
                            <div class="service-icon d-inline-block bg-light rounded p-4 mb-4">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['product_photo'], ENT_QUOTES, 'UTF-8'); ?>" width="200px" height="150px" /> 
                            </div>
                            <div class="service-content">
                                <h4 class="mb-4">
                                    <?php echo htmlspecialchars($row["product_name"], ENT_QUOTES, 'UTF-8'); ?>
                                </h4>
                                <p class="mb-4">
                                    <?php echo htmlspecialchars($row["product_description"], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
        <!-- Service End -->
        <?php } ?>

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