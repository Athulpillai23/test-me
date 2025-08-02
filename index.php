<?php include('shared/_header.php');?>

<main>
  <div class="big-wrapper dark">
    <img src="./images/shape.png" alt="" class="shape" />

    <?php include('shared/_navbar.php'); ?>
    
    <div class="container">
      <div class="row align-items-center justify-content-center">
        <div class="col-12 col-lg-6 get-started">
          <div class="text-lg-start text-center">
            <div class="big-title">
              <h1>Empowering Minds,</h1>
              <h1>Shaping Futures</h1>
            </div>
            <p class="text">
              Welcome to Virtual Academy, where we combine traditional values with modern education. Our comprehensive school management system ensures seamless learning experiences and effective communication between students, teachers, and parents.
            </p>
            <div class="cta">
              <a href="login.php" class="btn">Get started</a>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6 image-box">
          <img src="./images/children.png" alt="Students Learning" class="person" />
        </div>
      </div>
    </div>

    <?php include('shared/feature-cards.php'); ?>

    <section class="carousel-section">
      <div class="container text-center">
        <h2>Campus Life</h2>
      </div>
      <div class="carousel-box mx-auto">
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="images/carousel1.jpg" class="d-block w-100" alt="Campus Life">
            </div>
            <div class="carousel-item">
              <img src="images/carousel2.jpg" class="d-block w-100" alt="Student Activities">
            </div>
            <div class="carousel-item">
              <img src="images/carousel3.jpg" class="d-block w-100" alt="School Events">
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </section>
  </div>
</main>

<?php include('shared/_footer.php'); ?>
