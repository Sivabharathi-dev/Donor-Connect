<?php
// Get the selected filters from the query parameters
$selectedDistrict = isset($_GET['district']) ? $_GET['district'] : '';
$selectedBloodGroup = isset($_GET['blood_group']) ? $_GET['blood_group'] : '';
?>

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
    <section class="request">
        <h1>Blood Requests</h1>
        <p>Here you can view <b>all the blood requests</b></p>
        
        <form method="GET" action="all_request.php" style="margin-bottom: 10px;">
            <label for="district">District: </label>
            <select name="district" id="district">
    <option value="" <?= $selectedDistrict === '' ? 'selected' : ''; ?>>All Districts</option>
    <option value="Coimbatore" <?= $selectedDistrict === 'Coimbatore' ? 'selected' : ''; ?>>Coimbatore</option>
    <option value="Chennai" <?= $selectedDistrict === 'Chennai' ? 'selected' : ''; ?>>Chennai</option>
    <option value="Madurai" <?= $selectedDistrict === 'Madurai' ? 'selected' : ''; ?>>Madurai</option>
    <option value="Trichy" <?= $selectedDistrict === 'Trichy' ? 'selected' : ''; ?>>Trichy</option>
    <option value="Dindigul" <?= $selectedDistrict === 'Dindigul' ? 'selected' : ''; ?>>Dindigul</option>
    <option value="Tiruppur" <?= $selectedDistrict === 'Tiruppur' ? 'selected' : ''; ?>>Tiruppur</option>
    <option value="Kancheepuram" <?= $selectedDistrict === 'Kancheepuram' ? 'selected' : ''; ?>>Kancheepuram</option>
    <option value="Thanjavur" <?= $selectedDistrict === 'Thanjavur' ? 'selected' : ''; ?>>Thanjavur</option>
    <option value="Thirunelveli" <?= $selectedDistrict === 'Thirunelveli' ? 'selected' : ''; ?>>Thirunelveli</option>
    <option value="Thoothukudi" <?= $selectedDistrict === 'Thoothukudi' ? 'selected' : ''; ?>>Thoothukudi</option>
</select>

            
            <label for="blood_group">Blood Type: </label>
            <select name="blood_group" id="blood_group">
                <option value="" <?= $selectedBloodGroup === '' ? 'selected' : ''; ?>>All Blood Types</option>
                <option value="A+" <?= $selectedBloodGroup === 'A+' ? 'selected' : ''; ?>>A+</option>
                <option value="A-" <?= $selectedBloodGroup === 'A-' ? 'selected' : ''; ?>>A-</option>
                <option value="B+" <?= $selectedBloodGroup === 'B+' ? 'selected' : ''; ?>>B+</option>
                <option value="B-" <?= $selectedBloodGroup === 'B-' ? 'selected' : ''; ?>>B-</option>
                <option value="O+" <?= $selectedBloodGroup === 'O+' ? 'selected' : ''; ?>>O+</option>
                <option value="O-" <?= $selectedBloodGroup === 'O-' ? 'selected' : ''; ?>>O-</option>
                <option value="AB+" <?= $selectedBloodGroup === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                <option value="AB-" <?= $selectedBloodGroup === 'AB-' ? 'selected' : ''; ?>>AB-</option>
            </select>
            
            <button type="submit" style="padding: 5px 10px; background-color: #ff4d4d; color: white; border: none; border-radius: 5px; cursor: pointer;">Filter</button>
        </form>

        <!-- Table for displaying requests -->
        <?php
        include 'db.php';

        // Modify the query based on the filters
        $query = "SELECT * FROM blood_request WHERE 1 = 1";
        if (!empty($selectedDistrict)) {
            $query .= " AND District = ?";
        }
        if (!empty($selectedBloodGroup)) {
            $query .= " AND Blood_group = ?";
        }

        $stmt = $con->prepare($query);
        
        // Bind parameters based on the selected filters
        if (!empty($selectedDistrict) && !empty($selectedBloodGroup)) {
            $stmt->bind_param("ss", $selectedDistrict, $selectedBloodGroup);
        } elseif (!empty($selectedDistrict)) {
            $stmt->bind_param("s", $selectedDistrict);
        } elseif (!empty($selectedBloodGroup)) {
            $stmt->bind_param("s", $selectedBloodGroup);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Patient Name</th><th>Blood Type</th><th>Quantity</th><th>Mobile</th><th>Date</th><th>Reason</th><th>Location</th><th>District</th></tr>';
            while ($row = $result->fetch_assoc()) {
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
            echo '</table>';
            echo '<div style="margin-top: 20px; text-align: left">';
            echo '<a href="view_request.php" style="text-decoration: none;">';
            echo '<button style="padding: 10px 10px; font-size:15px; margin-left:10%; background-color: white; color: black; border: 2px solid; border-radius: 5px; cursor: pointer;">View Nearby Requests</button>';
            echo '</a>';
            echo '</div>';
        } 
        else {
            echo '<p>No requests found.</p>';
        }

        $stmt->close();
        mysqli_close($con);
        ?>
    </section>
</main>
<hr>
<section class="social">
    <h3 id="icon-name">Donor <span style="color:red;">Connect</span></h3>
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
                <li><a href="request.php">Request For Blood</a></li>
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
        <p>&copy; Copyrights 2024 DONOR CONNECT, All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
