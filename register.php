<?php

session_start();  
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $ema = trim($_POST['email']);
    $pas = trim($_POST['Password']);

    if (!empty($ema) && !empty($pas)) {
       
        if (preg_match("/^[0-9]{10}$/", $ema)) {
            $stmt = $con->prepare("SELECT id, Password FROM donor_register WHERE Phone = ? LIMIT 1");
            $stmt->bind_param("s", $ema);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $user_data = $result->fetch_assoc();

                if ($pas === $user_data['Password']) {
                  
                    $_SESSION['donor_id'] = $user_data['id'];
                    $_SESSION['email'] = $ema;
                    header("Location: donor.html");
                    die;
                } else {
                    echo "<script type='text/javascript'> alert('Wrong username or password')</script>";
                }
            } else {
                echo "<script type='text/javascript'> alert('Wrong username or password')</script>";
            }

            $stmt->close();
        } else {
            echo "<script type='text/javascript'> alert('Invalid phone number format')</script>";
        } 
    } else {
        echo "<script type='text/javascript'> alert('Please fill in both fields')</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Connect - Login</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
    function validateLoginForm() {
        const email = document.forms["loginForm"]["email"].value;
        const password = document.forms["loginForm"]["Password"].value;

        const phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(email)) {
            alert("Please enter a valid 10-digit phone number");
            return false;
        }


        return true;
    }
    
    function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    const iconImage = document.getElementById("iconImage");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        iconImage.src = "pic/icon show.png"; 
    } else {
        passwordInput.type = "password";
        iconImage.src = "pic/icon hide.png"; 
    }
}

</script>

</head>
<body>
        
    <header>
        
        <h2>  
            <a href="index.html"> Donor <span>Connect</span> </a>
             </h2>
             <img src="pic/logo24.png" style="margin-right:468px">
           <nav>
               <ul>
                  
                   <li><a href="about.html">About Us</a></li>
                   <li><a href="login.php">Register</a></li>
                   <li><a href="register.php">Donate Blood</a></li>
                    <li><a href="recipient.html">Need Blood</a></li>
                   
               </ul>
           </nav>
           
    </header> 
    
    <main>
        
        <div class="login-section">
        <form name="loginForm" method="POST" action="" onsubmit="return validateLoginForm();">

                        <h1>Donor Login</h1>
                        <div class="input-box">
                    <input  type="number" placeholder="Phone Number" name="email" required >
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
    <input type="password" id="password" placeholder="Password" name="Password" required>
    <span id="toggleIcon" onclick="togglePasswordVisibility()" style="cursor: pointer;" >
        <img src="pic/icon hide.png" id="iconImage" style="width: 20px;  position: absolute; top: 23px; right: 35px;">
    </span>
</div>

                 
                    <button type="submit">Login</button>
                    <div class="register-link">
                        <p>Don't have an account?<a href="login.php"> Register</a></p>
                    </div>
                 
                </form>
              
              
          
            </div>
   </main>

 
</body>
</html>  