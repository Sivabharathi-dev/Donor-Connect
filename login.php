<?php 
session_start();
include("db.php");

function getLatLong($address, $district, $pincode) {
    $apiKey = 'YOUR LOCATIONIQ API KEy'; 
    $fullAddress = $address . ', ' . $district . ', ' . $pincode; 
    $url = "https://us1.locationiq.com/v1/search.php?key={$apiKey}&q=" . urlencode($fullAddress) . "&format=json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }
    curl_close($ch);
    
   
    
    $data = json_decode($response, true);

    if (isset($data['error'])) {
        echo "<script type='text/javascript'> alert('Error from API: " . $data['error'] . "')</script>";
        return false;
    }
    
    if (!empty($data)) {
        return [
            'lat' => $data[0]['lat'],
            'lon' => $data[0]['lon']
        ];
    }
    return false;
    
}    

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nam = trim($_POST['name']);       
    $ag = intval($_POST['age']);  
    $blo = $_POST['bloodgroup'];       
    $phone = trim($_POST['email']);     
    $pas = trim($_POST['Password']);     
    $add = trim($_POST['address']);      
    $district = trim($_POST['district']); 
    $pincode = trim($_POST['pincode']);  

    if (!empty($phone) && !empty($pas) && !empty($blo) && !empty($nam) && !empty($add) && !empty($district) && !empty($pincode) && $ag >= 18 && $ag <= 65 && preg_match('/^\d{6}$/', $pincode)) {
        $latLong = getLatLong($add, $district, $pincode);

        if ($latLong) {
            $latitude = $latLong['lat'];
            $longitude = $latLong['lon'];

            $stmt = $con->prepare("INSERT INTO donor_register (Name, Age, Blood_group, Phone, Password, Address, District, Pincode, Latitude, Longitude) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssssidd", $nam, $ag, $blo, $phone, $pas, $add, $district, $pincode, $latitude, $longitude);

            if ($stmt->execute()) {
                echo "<script type='text/javascript'> alert('Successfully Registered')</script>";
            } else {
                echo "<script type='text/javascript'> alert('Error: " . $stmt->error . "')</script>";
            }
            
            $stmt->close();
        } else {
            echo "<script type='text/javascript'> alert('Failed to get latitude and longitude.')</script>";
        }
    } else {
        echo "<script type='text/javascript'> alert('Please fill in all fields correctly.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Connect - Register</title>
    <link rel="stylesheet" href="login.css"> 
    <script>
        function validateForm() {
            const name = document.forms["registerForm"]["name"].value;
            const age = document.forms["registerForm"]["age"].value;
            const bloodgroup = document.forms["registerForm"]["bloodgroup"].value;
            const phone = document.forms["registerForm"]["email"].value; 
            const password = document.forms["registerForm"]["Password"].value;
            const address = document.forms["registerForm"]["address"].value;
            const pincode = document.forms["registerForm"]["pincode"].value;

            if (name === "") {
                alert("Name must be filled out");
                return false;
            }

            if (age === "" || age < 18 || age > 65) {
                alert("Please enter a valid age (18-65)");
                return false;
            }

            if (bloodgroup === "") {
                alert("Please select a blood group");
                return false;
            }

            const phonePattern = /^[0-9]{10}$/; 
            if (!phonePattern.test(phone)) {
                alert("Please enter a valid 10-digit phone number");
                return false;
            }

            if (password === "") {
                alert("Password must be filled out");
                return false;
            }

            if (address === "") {
                alert("Address must be filled out");
                return false;
            }

            const pincodePattern = /^[0-9]{6}$/; 
            if (!pincodePattern.test(pincode)) {
                alert("Please enter a valid 6-digit pincode");
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
        <h2><a href="index.html"> Donor <span>Connect</span> </a></h2>
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
        <div class="form-container">
            <h2>Register as Donor</h2>
        </div>
        <div class="form-section">
            <form name="registerForm" method="POST" action="" onsubmit="return validateForm();">
                <div class="user-info">
                    <div class="user-inputbox">
                        <label>Name: </label>
                        <input class="nameinput" type="text" name="name" required>
                    </div>
                    <div class="user-inputbox">
                        <label>Age: </label>
                        <input class="ageinput" type="number" name="age" required>
                    </div>
                    <div class="user-inputbox">
                        <label class="bloodlabel">Blood Group:</label>
                        <select class="bloodinput" name="bloodgroup" required>
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
                        <label class="maillabel">Phone.No:</label>
                        <input class="mailinput" type="text" name="email" required pattern="\d{10}">
                    </div>
                    
                    <div class="user-inputbox">
                        <label class="passlabel">Password:</label>
                        <input id="password" class="passinput" type="password" name="Password" required>
                        <span id="toggleIcon" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                            <img src="pic/icon hide.png" id="iconImage">
                        </span>
                    </div>

                    <div class="user-inputbox">
                        <label class="addresslabel">Address: </label>
                        <textarea class="addressinput" rows="5" cols="40" name="address" required></textarea>
                    </div>

                    <div class="user-inputbox">
                        <label class="districtlabel">District:</label>
                        <select id="district" class="districtinput" name="district" required>
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
                        <label class="pincodelabel">Pincode: </label>
                        <input class="pincodeinput" type="text" name="pincode" required pattern="\d{6}">
                    </div>
                </div>

                <button type="submit">Register</button><br>
                <div class="register-link">
                    <p>Already have an account?<a href="register.php"> Login</a></p>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

