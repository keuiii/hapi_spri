<?php
// Database connection
$conn = new mysqli("localhost","root","","happy_sprays");
if ($conn->connect_error) die("DB connection failed: ".$conn->connect_error);

$gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
$sql = "SELECT * FROM perfumes";
if($gender_filter=='Male' || $gender_filter=='Female'){
    $sql .= " WHERE gender='$gender_filter'";
}
$result = $conn->query($sql);

// Fetch all products
$products = [];
if($result->num_rows>0){
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
}
$posters = ["poster1.png","poster2.png", "poster3.png"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Happy Sprays - Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#fff; color:#000;}

/* Top Navbar */
.top-nav {position:fixed; top:0; left:0; width:100%; background:#fff; border-bottom:1px solid #ccc; padding:15px 0; text-align:center; font-family:'Playfair Display', serif; font-size:32px; font-weight:700; text-transform:uppercase; letter-spacing:2px; z-index:1000;}

/* Sub Navbar */
.sub-nav {position:fixed; top:60px; left:0; width:100%; background:#fff; border-bottom:1px solid #ccc; text-align:center; padding:12px 0; transition:top 0.3s; z-index:999; font-family:'Playfair Display', serif; text-transform:uppercase; font-weight:700; letter-spacing:1px;}
.sub-nav a {margin:0 20px; text-decoration:none; color:#000; font-size:18px; transition:color 0.3s;}
.sub-nav a:hover {color:#555;}

/* Cart Icon in Sub Navbar */
.sub-nav .cart-link {
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 20px;
  color: #000;
  text-decoration: none;
  transition: 0.3s;
}
.sub-nav .cart-link:hover {
  color: #555;
}

.cart-link {
    position: relative;
    margin-left: 20px;
    text-decoration: none;
    color: #000;
}

.cart-link svg {
    vertical-align: middle;
    transition: stroke 0.3s;
}

.cart-link:hover svg {
    stroke: #555;
}

/* Hero Slider */
.hero-slider {position:relative; margin-top:120px; width:100%; height:500px; overflow:hidden;}
.hero-slider .slides img {width:100%; height:100%; object-fit:contain; position:absolute; top:0; left:0; opacity:0; transition:opacity 1s ease-in-out;}
.hero-slider .slides img.active {opacity:1;}
.hero-slider button {position:absolute; top:50%; transform:translateY(-50%); background:rgba(255,255,255,0.7); border:none; font-size:30px; cursor:pointer; width:40px; height:5px; border-radius:2px; padding:0;}
.hero-slider .prev {left:10px;} .hero-slider .next {right:10px;}
.hero-slider button:hover {background:rgba(255,255,255,0.9);}

/* Marquee */
.marquee {width:100%; overflow:hidden; background:#fff; border-top:2px solid #000; border-bottom:2px solid #000; padding:10px 0; box-sizing:border-box;}
.marquee-content {display:inline-block; padding-left:100%; white-space:nowrap; animation:marquee 15s linear infinite;}
.marquee-content span {display:inline-flex; align-items:center; margin-right:50px; font-weight:bold; color:#000; font-family:'Playfair Display', serif;}
.marquee-content span img {margin-right:8px;}
@keyframes marquee {0% {transform:translateX(0);} 100% {transform:translateX(-100%);}}

/* Products */
h1 {text-align:center; margin:30px 0;}
.product-grid {display:grid; grid-template-columns: repeat(4, 1fr); gap:30px; padding:20px; max-width:1200px; margin:auto;}
.product-card {display:flex; flex-direction:column; text-align:center; cursor:pointer; border-radius:5px; overflow:hidden; transition: transform 0.3s, box-shadow 0.3s;}
.product-card:hover {transform:translateY(-5px); box-shadow:0 8px 15px rgba(255, 255, 255, 0.2);}
.product-card img {width:100%; height:250px; object-fit:cover; border-bottom:0; transition:border-bottom 0.3s;}
.product-card:hover img {border-bottom:2px solid #ffffffff;}
.product-card h2 {margin:10px 0 5px 0; font-size:16px; font-weight:bold;}
.product-card p {margin:0 0 10px 0; font-weight:normal;}
.product-card button {padding:6px 12px; border:none; background:#000; color:#fff; cursor:pointer; border-radius:4px; transition:0.3s; margin-bottom:10px;}
.product-card button:hover {background:#444;}

/* Poster block */
.poster-block {grid-column: span 4; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 30px 0; padding: 20px; border-radius: 8px; background: #f5f5f5; text-align: center;}
.poster-block img {width: 50%; height: auto; border-radius: 8px; margin-bottom: 15px;}
.poster-text h2 {font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #000;}

.contact-section {max-width: 600px; margin: 50px auto; text-align: center; padding: 20px;}
.contact-section h1 {font-family: 'Playfair Display', serif; margin-bottom: 20px;}
.contact-links {margin-bottom: 20px; font-size: 18px;}
.contact-links a {margin: 0 10px; text-decoration: none; color: #000; font-weight: bold; transition: color 0.3s;}
.contact-links a:hover {color: #555;}
.contact-form input, .contact-form textarea {width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #000; border-radius: 5px; font-family: inherit;}
.contact-form button {padding: 10px 20px; border: none; background: #000; color: #fff; cursor: pointer; border-radius: 5px; transition: 0.3s;}
.contact-form button:hover {background: #444;}

/* CSS */
.reviews-btn {
  display: inline-block;
  padding: 14px 24px;
  background: #000;
  color: #fff;
  border-radius: 30px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
.reviews-btn:hover {
  background: #fff;
  color: #000;
  border: 2px solid #000;
}


/* Footer */
footer {text-align:center; padding:20px 0; border-top:1px solid #000; margin-top:40px;}

/* Scroll Animation */
.scroll-animate {opacity: 0; transform: translateY(50px); transition: all 0.6s ease-out;}
.scroll-animate.visible {opacity: 1; transform: translateY(0);}
</style>

</head>
<body>

<!-- Top Nav -->
<div class="top-nav">Happy Sprays</div>

<!-- Sub Nav -->
<div class="sub-nav" id="subNav">
    <a href="index.php">HOME</a>
    <a href="index.php?gender=Male">For Him</a>
    <a href="index.php?gender=Female">For Her</a>
    <a href="#contact">CONTACT</a>
    <a href="reviews.php">REVIEWS</a>
   <a href="cart.php" class="cart-link">
  <a href="cart.php" class="cart-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 7h12l1 12H5L6 7z"/>
        <path d="M9 7V5a3 3 0 0 1 6 0v2"/>
    </svg>

</a>

</div>

<!-- Font Awesome (CDN) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<!-- Hero Slider -->
<div class="hero-slider">
    <div class="slides">
        <img src="images/ss.png" class="slide active" alt="Banner 1">
        <img src="images/ss2.png" class="slide" alt="Banner 2">
           <img src="images/ss3.png" class="slide" alt="Banner 3">
    </div>
    <button class="prev">&#8212;</button>
    <button class="next">&#8212;</button>
</div>

<!-- Marquee -->
<div class="marquee">
    <div class="marquee-content">
        <span><img src="images/icon1.png" width="20" alt=""> Happy Sprays – New Fragrances</span>
        <span><img src="images/icon2.png" width="20" alt=""> Happy Sprays – Free Delivery</span>
        <span><img src="images/icon3.png" width="20" alt=""> Happy Sprays – Limited Edition</span>
    </div>
</div>

<!-- Products -->
<h1>Our Perfumes</h1>
<div class="product-grid">
<?php
$count = 0;
foreach($products as $prod){
    echo "
<div class='product-card scroll-animate'>
    <img src='images/{$prod['image']}' alt='{$prod['name']}'>
    <h2>{$prod['name']}</h2>
    <p>₱{$prod['price']}</p>
    <button onclick=\"window.location.href='view_product.php?id={$prod['id']}'\">View Details</button>
</div>
";

   $count++;
   // Insert posters or promo blocks after every 4 products
    if($count % 4 == 0){
        $poster_img = $posters[(int)(($count/4)-1) % count($posters)];
        $promo_text = "Discover Our Exclusive Fragrances!";
        echo "
        <div class='poster-block scroll-animate'>
            <img src='images/{$poster_img}' alt='Poster'>
            <div class='poster-text'>
                <h2>{$promo_text}</h2>
            </div>
        </div>
        ";
    }

}
?>

</div>

<!-- Contact Section -->
<section class="contact-section" id="contact">
    <h1>Contact Us</h1>
    <div class="contact-links">
        <a href="https://www.facebook.com/thethriftbytf" target="_blank">Facebook</a> | 
        <a href="https://www.instagram.com/thehappysprays/" target="_blank">Instagram</a>
    </div>
    <form action="contact_submit.php" method="post" class="contact-form">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
        <button type="submit">Send Message</button>
    </form>
</section>


<!-- Sa pinaka-baba ng index.php -->
<div style="text-align:center; margin: 60px 0;">
  <a href="reviews.php" class="reviews-btn">See Previous Reviews</a>
</div>


<!-- Footer -->
<footer>© 2025 Happy Sprays. All rights reserved.</footer>

<script>
// Sub nav scroll hide/show
let lastScrollTop = 0;
const subNav = document.getElementById("subNav");
window.addEventListener("scroll", function(){
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    subNav.style.top = (scrollTop > lastScrollTop) ? "-60px" : "60px";
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
}, false);

// Hero slider
let slides = document.querySelectorAll('.slide'); let current = 0;
function showSlide(index){
    slides.forEach(slide => slide.classList.remove('active'));
    slides[index].classList.add('active');
}
setInterval(()=>{
    current = (current + 1) % slides.length;
    showSlide(current);
},5000);
document.querySelector('.prev').addEventListener('click', ()=>{current = (current - 1 + slides.length) % slides.length; showSlide(current);});
document.querySelector('.next').addEventListener('click', ()=>{current = (current + 1) % slides.length; showSlide(current);});

// Scroll Animation
const scrollElements = document.querySelectorAll('.scroll-animate');

const elementInView = (el, offset = 100) => {
    const elementTop = el.getBoundingClientRect().top;
    return elementTop <= (window.innerHeight - offset);
};

const displayScrollElement = (el) => {
    el.classList.add('visible');
};

const handleScrollAnimation = () => {
    scrollElements.forEach(el => {
        if (elementInView(el, 100)) {
            displayScrollElement(el);
        }
    });
};

window.addEventListener('scroll', handleScrollAnimation);
window.addEventListener('load', handleScrollAnimation);


let cartCount = 3; // sample, galing DB/localStorage
if(cartCount > 0){
    document.getElementById("cartCount").innerText = cartCount;
}
</script>

</body>
</html>
