<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Connect - View Requests</title>
    <link rel="stylesheet" href="view_request.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    <main>
        <section class="requests">
            <h1>Blood Requests</h1>
            <p>Here you can view the blood requests around <b>50km radius</b></p>
            <?php
session_start();
include 'db.php';

if (!isset($_SESSION['donor_id'])) {
    echo "You must be logged in to view blood requests.";
    exit;
}


$donor_id = $_SESSION['donor_id'];
$stmt = $con->prepare("SELECT latitude, longitude FROM donor_register WHERE id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$donor_result = $stmt->get_result();
$donor_data = $donor_result->fetch_assoc();

$donor_latitude = $donor_data['latitude'];
$donor_longitude = $donor_data['longitude'];

$stmt = $con->prepare("SELECT * FROM blood_request");
$stmt->execute();
$result = $stmt->get_result();
 
if ($result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Patient Name</th><th>Blood Type</th><th>Quantity</th><th>Mobile</th><th>Date</th><th>Reason</th><th>Location</th><th>District</th></tr>';
    while ($row = $result->fetch_assoc()) {
        
        $request_latitude = $row['Latitude'];
        $request_longitude = $row['Longitude'];
        $distance = haversine_distance($donor_latitude, $donor_longitude, $request_latitude, $request_longitude);

        if ($distance <= 50) { 
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['Patient_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Blood_group']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Attendee_mobile']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Required_date']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Reason']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Hospital_Address']) . '</td>';
            echo '<td>' . htmlspecialchars($row['District']) . '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
    
    echo '<div style="margin-top: 20px; text-align: left">';
    echo '<a href="all_request.php" style="text-decoration: none;">';
    echo '<button style="padding: 10px 10px; font-size:15px; margin-left:10%; background-color: white; color: black; border: 2px solid; border-radius: 5px; cursor: pointer;">View All Requests</button>';
    echo '</a>';
    echo '</div>';
    
} else {
    echo '<p>No requests found.</p>';
}

$stmt->close();
mysqli_close($con);

function haversine_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; 

    $lat1_rad = deg2rad($lat1);
    $lon1_rad = deg2rad($lon1);
    $lat2_rad = deg2rad($lat2);
    $lon2_rad = deg2rad($lon2);

    $dlat = $lat2_rad - $lat1_rad;
    $dlon = $lon2_rad - $lon1_rad;

    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1_rad) * cos($lat2_rad) * sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
}
?>

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