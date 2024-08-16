<?php
    session_start();
    include('admin/dist/php/database.php');
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Animal Husbandry Knowledge Sharing In South Shan State</title>
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
        <div class="container-fluid header position-relative overflow-hidden p-0">
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
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="#about" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Our Farm</a>
                        <a href="blog.php" class="nav-item nav-link">Our Blog</a>
                        <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                    </div>
                </div>
            </nav>


            <!-- Hero Header Start -->
            <div class="hero-header overflow-hidden px-5">
                <div class="rotate-img">
                    <img src="img/sty-1.png" class="img-fluid w-100" alt="">
                    <div class="rotate-sty-2"></div>
                </div>
                <div class="row gy-5 align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                        <h1 class="display-4 text-dark mb-4 wow fadeInUp" data-wow-delay="0.3s">Welcome to Our Farm!</h1>
                        <p class="fs-4 mb-4 wow fadeInUp" data-wow-delay="0.5s">
                            Explore our wide range of animals, from cows and chickens to pigs and fish. Enjoy fresh, high-quality products like milk, eggs, and meat. We also offer guided tours, educational programs, and fun activities for the whole family. Experience the best of farm life with us!
                        </p>
                        <a href="#about" class="btn btn-primary rounded-pill py-3 px-5 wow fadeInUp" data-wow-delay="0.7s">Get Started</a>
                    </div>
                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.2s">
                        <img src="img/Logo.png" class="img-fluid w-100 h-100" alt="Logo"
                        style="border-radius: 50%;">
                    </div>
                </div>
            </div>
            <!-- Hero Header End -->
        </div>
        <!-- Navbar & Hero End -->


        <!-- About Start -->
        <div class="container-fluid overflow-hidden py-5"  style="margin-top: 6rem;" id="about">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div>
                            <img src="img/COVID-19-Relief_Small-Farms-.jpg" style="border-top-left-radius: 30%; border-bottom-right-radius: 30%;" class="img-fluid w-100" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                        <h4 class="mb-1 text-primary">About Us</h4>
                        <h1 class="display-5 mb-4">Learn about all the animals and products in our Farm</h1>
                        <p class="mb-4">
                            Discover the variety of animals and high-quality products on our farm. Learn about our cows, chickens, pigs, and more, along with the fresh milk, eggs, meat, and wool we produce. Join us for an informative and enjoyable experience, exploring the best our farm has to offer.
                        </p>
                        <a href="service.php" class="btn btn-primary rounded-pill py-3 px-5">About More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Feature Start -->
        <div class="container-fluid feature overflow-hidden py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h1 class="display-5 mb-4">See All Animals in Our Farm</h1>
                    <p class="mb-0">
                        Explore the diverse range of animals on our farm! From friendly goats and playful sheep to majestic horses and curious chickens, you'll get to see and learn about all the animals that call our farm home. Come and experience the charm and beauty of farm life up close.
                    </p>
                </div>
                <div class="row g-4 justify-content-center text-center mb-5">
                <?php 
                    $sql = "SELECT * FROM animal_type ORDER BY animal_type ASC LIMIT 4";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                    while ($row = $stmt->fetch()) {
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="text-center p-4">
                            <div class="d-inline-block rounded bg-light p-4 mb-4">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['animal_photo'], ENT_QUOTES, 'UTF-8'); ?>" width="200px" height="150px" /> 
                            </div>
                            <div class="feature-content">
                                <a href="#" class="h4">
                                    <?php echo htmlspecialchars($row["animal_type"], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <p class="mt-4 mb-0">
                                    <?php echo htmlspecialchars($row["animal_des"], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="my-3">
                            <a href="service.php#animals" class="btn btn-primary d-inline rounded-pill px-5 py-3">Show More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End -->


        <!-- Feature Start -->
        <div class="container-fluid feature overflow-hidden py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h1 class="display-5 mb-4">See All Products in Our Farm</h1>
                    <p class="mb-0">
                        Discover the wide range of high-quality products our farm offers. From fresh milk, eggs, and meat to wool and cheese, our farm provides nutritious and delicious items for your family. Explore our products and experience the best of farm-fresh goodness
                    </p>
                </div>
                <div class="row g-4 justify-content-center text-center mb-5">
                <?php 
                    $sql = "SELECT * FROM product_type ORDER BY product_id ASC LIMIT 4";
                    $stmt = $db->query($sql);
                    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

                    while ($row = $stmt->fetch()) {
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="text-center p-4">
                            <div class="d-inline-block rounded bg-light p-4 mb-4">
                                <img src="<?php echo htmlspecialchars('admin/upload/' . $row['product_photo'], ENT_QUOTES, 'UTF-8'); ?>" width="200px" height="150px" /> 
                            </div>
                            <div class="feature-content">
                                <a href="#" class="h4">
                                    <?php echo htmlspecialchars($row["product_name"], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <p class="mt-4 mb-0">
                                    <?php echo htmlspecialchars($row["product_description"], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="my-3">
                            <a href="service.php#products" class="btn btn-primary d-inline rounded-pill px-5 py-3">More Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feature End -->


        <!-- Blog Start -->
        <div class="container-fluid blog py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                    <h4 class="text-primary">Our Blog</h4>
                    <h1 class="display-5 mb-4">Join Us For New Blog</h1>
                    <p class="mb-0">
                        Here, we share fascinating facts and insights about the animals on our farm. From their unique behaviors and characteristics to their care and contributions, our blog offers a deeper understanding of farm life. Join us in exploring the wonderful world of our animals!
                    </p>
                </div>
                <div class="row g-4 justify-content-center text-center">
                    <?php 
                        $sql = "SELECT * 
                                FROM knowledge_type
                                INNER JOIN breed_technology
                                    ON knowledge_type.breed_id = breed_technology.breed_id
                                ORDER BY knowledge_id DESC LIMIT 4";
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
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="my-3">
                            <a href="blog.php#target" class="btn btn-primary d-inline rounded-pill px-5 py-3">More Blogs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Blog End -->


        <!-- FAQ Start -->
        <div class="container-fluid FAQ bg-light overflow-hidden py-5">
            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                       <div class="accordion" id="accordionExample">
                            <div class="accordion-item border-0 mb-4">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseTOne">
                                        Why Choose Our Husbandry Service?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body my-2">
                                        <p>
                                            Choosing our husbandry service ensures the highest standards of animal care and product quality. Our experienced team uses sustainable and humane practices to keep our animals healthy and happy. We pride ourselves on delivering fresh, nutritious products directly from our farm to your table.
                                        </p>
                                        <p>
                                            Additionally, we offer personalized customer service and educational programs to help you understand more about our farming practices. Trust us to provide the best in animal husbandry and farm products.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-4">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        How do we ensure the welfare of the animals?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body my-2">
                                        <p>
                                            We ensure the welfare of our animals through strict adherence to humane and sustainable farming practices. Our experienced staff provides daily care, high-quality feed, and clean living environments. Regular health checks and veterinary care keep our animals healthy and happy.
                                        </p>
                                        <p>
                                            Additionally, we implement enrichment activities to promote natural behaviors and reduce stress. Our commitment to animal welfare is a top priority in all aspects of our farm operations.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Do you offer tours or educational programs?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body my-2">
                                        <p>
                                            Yes, we offer guided tours and educational programs for visitors of all ages. Our tours provide an in-depth look at our farm operations, including animal care and product production. Educational programs cover topics like sustainable farming practices and animal welfare.
                                        </p>
                                        <p>
                                            These experiences are designed to be both informative and enjoyable. We aim to educate the community about the importance of responsible farming.
                                        </p>
                                    </div>
                                </div>
                            </div>
                       </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                        <div class="FAQ-img RotateMoveRight rounded">
                            <img src="img/about-1.png" class="img-fluid w-100" alt="">
                        </div>
                    </div>
                    <div class="col-12 wow fadeInUp text-center" data-wow-delay="0.1s">
                        <div class="my-3">
                            <a href="contact.php#target" class="btn btn-primary d-inline rounded-pill px-5 py-3">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FAQ End -->


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