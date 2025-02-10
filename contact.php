<?php

session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['cname']);
    $em = trim($_POST['cmail']);
    $mes = trim($_POST['cmessage']);

    if (!empty($mes) && !empty($em) && !empty($name) && filter_var($em, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare("INSERT INTO contact (Name, Email, Message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $em, $mes);

        if ($stmt->execute()) {
            echo "<script type='text/javascript'> alert('Message sent successfully.')</script>";
        } else {
            echo "<script type='text/javascript'> alert('Error: " . $stmt->error . "')</script>";
        }

        $stmt->close();
    } else {
        echo "<script type='text/javascript'> alert('Please fill in all required fields with valid information.')</script>";
    }
}
?>


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Connect - Contact Us</title>
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
    function validateContactForm() {
        const form = document.forms["contactForm"];
        const name = form["cname"].value.trim();
        const email = form["cmail"].value.trim();
        const message = form["cmessage"].value.trim();

        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        if (!name) {
            alert("Please enter your name.");
            return false;
        }

        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (!message) {
            alert("Please enter your message.");
            return false;
        }

        return true;
    }
</script>

</head>
<body>
    
    <main>
        <section class="contact">
          
           
      
            <div class="content">
               <h1>Contact Us</h1>
               <p>Contact us for any inquiries or support. We're here to assist you every step of the way.</p>
            </div>  
               <div class="container">
                   <div class="contactInfo">
                   <div class="box">
                       <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                       <div class="text1">
                           <h3>Address</h3>
                           <p>365 Nava India Road,<br>Peelamedu,<br>Coimbatore 641004</p>
                        </div>
                   </div>
                   <div class="box">
                    <div class="icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="text2">
                        <h3>Phone</h3>
                        <p>9600-980-890</p>
                     </div>
                </div>
                   <div class="box">
                    <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="text3">
                        <h3>Email</h3>
                        <p>DonorConnect7@gmail.com</p>
                     </div>
                </div>
               </div>
               <div class="contactForm">
               <form name="contactForm" method="POST" onsubmit="return validateContactForm();">

                       <h2>Send Message</h2>
                       <div class="inputBox">
                           <input type="text" name="cname" required="required">
                           <span >Name</span>
                       </div>
                       <div class="inputBox">
                        <input type="email" name="cmail" required="required">
                        <span >Email</span>
                    </div>
                    <div class="inputBox">
                        <textarea name="cmessage" rows=10 columns=50 required="required"></textarea>
                        <span >Type Your Message...</span>
                    </div>
                    <div class="inputBox">
                        <input type="submit" name="button" value="send">
                        
                    </div>
                   </form>
               </div>
            </div>
            </section>
    </main>
    <hr>
   
    <section class="social">
        
        <h3 id="icon-name">Donor <span style="color:red;">Connect</span> </h3>
        <h3 id="follow">Follow Us</h3>
        <ul class="social-icons">
            <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
             <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
            
        </ul>
        
    </section>
        <hr>
        <footer>
            <div class="container">
                <div class="footer-content">
                    <h4><a href="recipient.html">Looking for Blood</a></h4>
                    <ul class="list">
                   <li><a href="request.php" >Request For Blood</a></li>
                   
                   
                    </ul>
                   </div>
                   <div class="footer-content">
                       <h4><a href="donor.html">Donate Blood</a></h4>
                    <ul class="list">
                            <li><a href="login.php">Register as Donor</a></li>
                        <li><a href="register.php">View Blood Requests</a></li>
                        
                       
                    </ul>
                   </div>
                 
                <div class="footer-content">
                        <h4>Navigation</h4>
                        <ul class="list">
                                <li><a href="index.html">Home</a></li>
                            <li><a href="about.html">About Donor Connect</a></li>
                            <li><a href="login.php">Register</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                         
                        </ul>
                </div>
             </div>
            <div class="bottom-bar">
            <p>&copy; Copyrights 2024 DONOR CONNECT , All Rights Reserved.</p>
            </div>
        </footer>
    </body>
    </html>