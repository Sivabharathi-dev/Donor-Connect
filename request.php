<?php 
session_start();
include("db.php");
 require_once 'send_whatsapp.php';

function getLatLong($hosadd, $district, $pincode) {
    $apiKey = 'YOUR LOCATIONIQ API KEY'; 
    $fullAddress = trim($hosadd) . ', ' . trim($district) . ', ' . trim($pincode); 
    $url = "https://us1.locationiq.com/v1/search.php?key={$apiKey}&q=" . urlencode($fullAddress) . "&format=json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch);

    
    file_put_contents('api_response_log.txt', "HTTP Code: $httpCode\nResponse: $response\n", FILE_APPEND);

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon']
            ];
        } else {
            echo "<script type='text/javascript'> alert('No latitude and longitude found. Please check the address details.')</script>";
        }
    } else {
        echo "<script type='text/javascript'> alert('Error: Unable to get location (HTTP Code: $httpCode). Please try again later.')</script>";
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $paname = trim($_POST['pname']);
    $atmob = trim($_POST['mobile']);
    $blogro = trim($_POST['bloodgro']);
    $quant = intval($_POST['quantity']);
    $reqdat = trim($_POST['day']);
    $reason = trim($_POST['reason']);
    $hosadd = trim($_POST['addres']);
    $district = trim($_POST['district']);
    $pincode = trim($_POST['pincode']);  

    if (!empty($paname) && !empty($district) && !empty($atmob) && !empty($reqdat) && !empty($hosadd) && !empty($blogro) && !empty($pincode) && !empty($reason)) {
        if (preg_match("/^[0-9]{10}$/", $atmob)) {
            if (preg_match("/^[1-9][0-9]{5}$/", $pincode)) { 
                if ($quant > 0 && $quant <= 10 && strtotime($reqdat) >= strtotime(date("Y-m-d"))) {
                
                    $latLong = getLatLong($hosadd, $district, $pincode);

                    if ($latLong) {
                        $latitude = $latLong['lat'];
                        $longitude = $latLong['lon'];

                        $stmt = $con->prepare("INSERT INTO blood_request (Patient_name, Attendee_mobile, Blood_group, Quantity, Required_date, Reason, Hospital_Address, District, Pincode, Latitude, Longitude) 
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssissssidd", $paname, $atmob, $blogro, $quant, $reqdat, $reason, $hosadd, $district, $pincode, $latitude, $longitude);

                        if ($stmt->execute()) {
                            $requestLatitude = $latitude;
                            $requestLongitude = $longitude;

                            $query = "
                                SELECT Phone, (6371 * acos(
                                    cos(radians(?)) * cos(radians(Latitude)) *
                                    cos(radians(Longitude) - radians(?)) +
                                    sin(radians(?)) * sin(radians(Latitude))
                                )) AS distance
                                FROM donor_register
                                HAVING distance <= 50
                            ";
                            $stmt2 = $con->prepare($query);
                            $stmt2->bind_param("ddd", $requestLatitude, $requestLongitude, $requestLatitude);
                            $stmt2->execute();
                            $result = $stmt2->get_result();

                            if ($result->num_rows > 0) {
                                while ($donor = $result->fetch_assoc()) {
                                    $donorPhone = $donor['Phone'];
                                    $donorPhoneWithCountryCode = '+91' . $donorPhone;

                                    $message = "🩸 *Urgent Blood Request*\n\n"
                                             . "*Patient:* $paname\n"
                                             . "*Blood Group:* $blogro\n"
                                             . "*Hospital:* $hosadd, $district\n"
                                             . "*Required By:* $reqdat\n"
                                             . "*Contact:* $atmob\n\n"
                                             . "Please consider donating if you are able. Your support can save a life!";
                                    
                                   sendWhatsAppReminder($donorPhoneWithCountryCode, $message);
                                }
                            }

                            echo "<script type='text/javascript'> alert('Request sent successfully.')</script>";
                        } else {
                            echo "<script type='text/javascript'> alert('Error: " . $stmt->error . "')</script>";
                        }

                        $stmt->close();
                    } else {
                        echo "<script type='text/javascript'> alert('Failed to get latitude and longitude.')</script>";
                    }
                } else {
                    echo "<script type='text/javascript'> alert('Invalid quantity or required date.')</script>";
                }
            } else {
                echo "<script type='text/javascript'> alert('Please enter a valid 6-digit pincode.')</script>";
            }
        } else {
            echo "<script type='text/javascript'> alert('Invalid mobile number.')</script>";
        }
    } else {
        echo "<script type='text/javascript'> alert('Please fill in all required fields.')</script>";
    }
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Connect - Request Blood</title>
    <link rel="stylesheet" href="request1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script>
    function validateRequestForm() {
        const form = document.forms["requestForm"];
        const atmob = form["mobile"].value.trim();
        const quant = form["quantity"].value.trim();
        const reqdat = form["day"].value.trim();
        const pincode = form["pincode"].value.trim();  // Pincode field

        // Validate phone number
        const phonePattern = /^[0-9]{10}$/;
        if (!phonePattern.test(atmob)) {
            alert("Please enter a valid 10-digit mobile number.");
            return false;
        }

        // Validate quantity
        if (quant <= 0 || quant > 10) {
            alert("Please enter a valid quantity (between 1 and 10).");
            return false;
        }

        // Validate date
        if (new Date(reqdat) < new Date()) {
            alert("The required date cannot be in the past.");
            return false;
        }

        // Validate pincode
        const pincodePattern = /^[1-9][0-9]{5}$/;
        if (!pincodePattern.test(pincode)) {
            alert("Please enter a valid 6-digit pincode.");
            return false;
        }

        return true;
    }
    </script>

</head>
<body>
<header>
    <h2>  
        <a href="index.html"> Donor <span>Connect</span> </a>
    </h2>
    <img src="pic/logo24.png" style="margin-right:450px">  
    <nav>
        <ul>
            <li><a href="about.html">About Us</a></li>
            <li><a href="login.php">Register</a></li>
            <li><a href="register.php">Donate Blood</a></li>
            <li><a href="recipient.html">Need Blood</a></li>
        </ul>
    </nav>
</header> 

<div class="form-container">
    <h2>Create Request for Blood</h2>
</div>
<div class="form-section">
    <form name="requestForm" method="POST" action="" onsubmit="return validateRequestForm();">

    <div class="user-info">
        <div class="user-inputbox"> 
            <label>Patient Name:</label>
            <input class="patientinput"  type="text"  name="pname" required>
        </div>

        <div class="user-inputbox"> 
            <label>Attendee Mobile:</label>
            <input class="phoneinput" type="number" name="mobile" required>
        </div>
        <div class="user-inputbox"> 
            <label>Blood Group:</label>
            <select class="bloodinp" name="bloodgro" required>
                <option value="" selected disabled>--Select Blood Group--</option>
                <option value="a+">A+</option>
                <option value="a-">A-</option>
                <option value="b+">B+</option>
                <option value="b-">B-</option>
                <option value="o+">O+</option>
                <option value="o-">O-</option>
                <option value="ab+">AB+</option>
                <option value="ab-">AB-</option>
            </select>
        </div>

        <div class="user-inputbox">
            <label>Quantity</label>
            <input class="quant" type="number" name="quantity" required>
        </div>

        <div class="user-inputbox">
            <label>Required Date</label>
            <input class="dat" type="date" name="day" required>
        </div>

        <div class="user-inputbox">
            <label>Reason:</label>
            <input class="ad"  type="text"  name="reason" required>
        </div>

        <div class="user-inputbox">
            <label>Hospital & Address: </label>
            <input class="ad"  type="text"  name="addres" required>
        </div>

        <div class="user-inputbox">
            <label>District:</label>
            <select class="districtinput" id="district" name="district" required>
                <option value="" selected disabled>Select District</option>
                <option value="CHENNAI">CHENNAI</option>
                <option value="KANCHEEPURAM">KANCHEEPURAM</option>
                <option value="COIMBATORE">COIMBATORE</option>
                <option value="TIRUPPUR">TIRUPPUR</option>
                <option value="DINDIGUL">DINDIGUL</option>
                <option value="TRICHY">TRICHY</option>
                <option value="THANJAVUR">THANJAVUR</option>
                <option value="MADURAI">MADURAI</option>
                <option value="THIRUNELVELI">THIRUNELVELI</option>
                <option value="THOOTHUKUDI">THOOTHUKUDI</option>
            </select>
        </div>

       
        <div class="user-inputbox">
            <label>Pincode:</label>
            <input class="pincodeinput" type="number" name="pincode" required>
        </div>
    </div>

    <div class="form-submit">
        <button type="submit">Submit your Request</button>
    </div>

    </form>
    </div>
<hr>
<section class="social">
    <h3 id="icon-name">Donor <span style="color:red;">Connect</span> </h3>
    <h3 id="follow">Follow Us</h3>
    <ul class="social-icons">
        <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
        <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
        <li><a href="#"><i class="fa-brands fa-twitter"></i></a></li>
    </ul>
</section>


        
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
