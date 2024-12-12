<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home'; 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Gamalinda Funeral Services</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/responsive.css" />
       
        
    </head>
    <body
        onload='if (window.location.href.substr(window.location.href.length - 6) == "#about") { introAboutLogoTransition(); }'
    >
        <!--navbar-->
        <nav class="navbar glass" style="height: 70px">
            <span
                ><a href="#home" style="display: flex; align-items: center">
                    
                    <h3 class="logo">&nbsp;Funeral Services</h3></a>
                </span>
            <ul class="nav-links">
                <li>
                    <a href="#home" id="pri" class="active cir_border">Home</a>
                </li>
                <li><a href="#services" id="sec" class="cir_border">Services</a></li>
                <?php if (!isset($_SESSION['username'])): ?>
                    <li><a href="#login" id="tri" class="cir_border">Login</a></li>
                <?php else: ?>
                    <li><a href="#login" id="tri" class="cir_border">Schedule</a></li>
                <?php endif; ?>
                <li>
                    <a href="#plan" id="quad" class="cir_border">Plan</a>
                </li>
                <li><a href="#about" id="quint" class="cir_border">About</a></li>
                <li>
                    <a href="#contact" id="hept" class="cir_border">Contact</a>
                </li>
                <li>
                    <div>
                        <input
                            type="checkbox"
                            class="checkbox dark"
                            id="checkbox"
                        />
                        <label for="checkbox" class="label">
                            <i class="fa fa-moon-o"></i>
                            <i class="fa fa-sun-o"></i>
                            <div class="ball"></div>
                        </label>
                    </div>
                </li>
                <div class="user-info">
                    <?php if (isset($_SESSION['username'])): ?>
                        <img src="backend/Assets/Image/avatar.jpg" alt="User Avatar">
                        <a href="backend/logout.php" class="btn-logout">Logout</a>
                    <?php endif; ?>
                </div>
            </ul>
            <img src="./img/menu-btn.png" alt="" class="menu-btn" />
        </nav>
        <!--navbar-->

        <div>
        <header id="home">
            <div class="header-content">
                <h2 id="quote">Funeral Services and Planning</h2>
                <div class="line"></div>
                <h1>Remembering our love ones</h1>
                <a
                    href="#about"
                    class="ctn"
                    onclick='removeall(); $("#quad").css("border", "2px solid whitesmoke"); $("#quad").css("border-radius", "20px");'
                    >Learn more</a
                >
            </div>
        </header>
        </div>

        <!--Services-->
        <div>
        <section class="services" id="services">
            <div class="container">
                <div>
                <div class="title">
                    <h1 class="dark">Services Offer</h1>
                    <div class="line"></div>
                </div>
                <div class="row">
                    <article class="card col">
                        <img class="card-img" src="./image/immediate.jpg" />
                        <h4 class="dark">Immediate Need</h4>
                        <p class="font-color">
                            If you have immediate need of our services, 
                            please feel free to use the form below to provide us as much information
                             as you have available to save time at the arrangement conference.
                        </p>
                        <a href="#" class="ctn">All Details</a>
                    </article>
                    <article class="card col">
                        <img src="./image/arrangement.jpg" />
                        <h4 class="dark">Pre-Arrangement</h4>
                        <p class="font-color">
                            Most of us want to plan ahead 
                            but when it comes to end-of-life planning, 
                            many have a difficult time starting the funeral pre-planning process.
                        </p>
                        <a href="#" class="ctn">All Details</a>
                    </article>
                    <article class="card col">
                        <img src="./image/flowers.jpg" />
                        <h4 class="dark">Order Flowers</h4>
                        <p class="font-color">
                            Funeral flowers have been a part of funerals for hundreds of years. 
                            For many people, sending flowers to a funeral has become a popular way to pay respects
                             to the deceased and support the family.
                        </p>
                        <a href="#" class="ctn">All Details</a>
                    </article>
                </div>
            </div>
            </div>
        </section>
        </div>
        <!--Events-->

        <!--login-->
        <section class="login" id="login">
            <?php if (!isset($_SESSION['username'])): ?>

                
                <div class="login-content">
                    <h1>Come and Login</h1>
                    <div class="line">
                    </div>
                    <p>
                        Most of us want to plan ahead but when it comes to end-of-life planning, 
                        many have a difficult time starting the funeral pre-planning process.
                    </p>

                    
                    <a href="backend/login.php" class="ctn">Login</a>
                </div>
            <?php else: ?>
                <div class="login-content">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <div class="line">

                    </div>
                    <p>
                        Lets make your Memorial person appointment here :
                    </p>
                    <br>
                    <li><a href="backend/schedule.php" id="sec" class="ctn">Schedule</a></li>
                    <br>
                    <br>
                    <li><a href="backend/viewschedule.php" id="sec" class="ctn">View your Appointment</a></li>
                </div>
            <?php endif; ?>
            
        </section>
        <!--login-->

        <!--plan-->
        <section class="plan" id="plan">
            <div class="container row">
                <div class="col content-col">
                    <h1 class="font-color">Plan here with us</h1>
                    <div class="line"></div>
                    <p>
                        Here at Gamalinda Funeral Services.</br>
                        We're committed to providing families with the best and most affordable funeral services and products.</br>
                        We also offer a diverse range of personalized funeral service options.</br>
                    </p>
                    <a href="#" class="ctn">Learn more</a>
                </div>
                <div class="image-col">
                    <div class="image-gallery">
                        <img src="./image/a1.jpg" alt="" />
                        <img src="./image/a2.jpg" alt="" />
                        <img src="./image/a3.jpg" alt="" />
                        <img src="./image/a4.jpg" alt="" />
                    </div>
                </div>
            </div>
            <br /><br /><br /><br />
        </section>
        <!--plan-->

        <!-- About -->
        <section id="about">
            <div class="title">
                <h1 class="font-color">About Us</h1>
                <div class="line"></div>
            </div>
            <br />
            <div id="about_us">
                <div class="boxx">
                    <div class="containerx">
                        <input type="radio" name="slider" id="item-1" checked />
                        <input type="radio" name="slider" id="item-2" />
                        <input type="radio" name="slider" id="item-3" />
                        
                        <div class="cards">
                            <label class="cardt" for="item-1" id="col-img-1">
                                <img src="./image/1.jpg" />
                            </label>
                            <label class="cardt" for="item-2" id="col-img-2">
                                <img src="./image/2.jpg" />
                            </label>
                            <label class="cardt" for="item-3" id="col-img-3">
                                <img src="./image/3.jpg" />
                            </label>
                            </label>
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About -->

        <!-- contact -->
        <section id="contact">
            <div class="title">
                <h1 class="font-color">Contact Us</h1>
                <div class="line"></div>
            </div>
            <div class="contact_us">
                <form class="cform" action="" method="post">
                    <div class="crow-message">
                        <h1 class="color">Send us a message</h1>
                        <div></div>
                    </div>
                    <div class="crow-in">
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Your name"
                        />
                        <input
                            type="text"
                            id="email"
                            name="email"
                            placeholder="Your Email id"
                        />
                    </div>
                    <div class="crow">
                        <div class="ccol-left">
                            <textarea
                                type="text"
                                id="remarks"
                                name="remarks"
                                placeholder="Your Remarks....."
                                style="height: 150px"
                            ></textarea>
                        </div>
                    </div>
                    <input class="crow-s" type="submit" value="Submit" />
                </form>
                <div class="cbox">
                    <div>
                        <p class="cbox-message">
                            Prefer some other way ?<br />Reach us by using the
                            details given below
                        </p>
                        <div class="cbox-line"></div>
                    </div>
                    <div class="c_boxx">
                        <a href="mailto:karlosnino@gmail.com"
                            ><i class="fa fa-envelope"></i>
                            Mail: karlosnino11@gmail.com
                        </a>
                    </div>
                    <div class="c_boxx">
                        <a href="tel:+91-12345-67890"
                            ><i class="fa fa-phone"></i>
                            Phone: (+91) 12345-67890
                        </a>
                    </div>
                    <div class="c_boxx">
                        <a href="#"
                            ><i class="fa fa-map-marker"></i>
                            Location: Pagadian City, Zamboanga del Sur
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- contact  -->
        <!-- up scroll -->
        <i class="arrow"  onclick="topFunction()" id="upbtn"></i>
        <!-- end -->
        <!--footer-->
        <section class="footer">
            <span
                >Created By Carl Rupinta | &#169; 2024 All rights
                reserved.</span
            >
            <div class="social">
                <li>
                    <a
                        href="https://www.facebook.com/karuuu11"
                        target="_blank"
                        rel="noreferrer"
                        ><i class="fa fa-globe"></i
                    ></a>
                    <a
                        href="https://github.com/karuuu0001"
                        target="_blank"
                        rel="noreferrer"
                        ><i class="fa fa-github"></i
                    ></a>
                    <a
                        href="https://www.linkedin.com/"
                        target="_blank"
                        rel="noreferrer"
                        ><i class="fa fa-linkedin-square"></i
                    ></a>
                </li>
            </div>
        </section>
        <!--footer-->

        <script>
            const menuBtn = document.querySelector(".menu-btn");
            const navlinks = document.querySelector(".nav-links");

            menuBtn.addEventListener("click", () => {
                navlinks.classList.toggle("mobile-menu");
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="js/script.js"></script>
    </body>
</html>
