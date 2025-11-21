Donor Connect 🩸

A reliable Blood Donation Management System designed to connect donors with people in need quickly and effectively. 

Built using Core PHP, MySQL, HTML,CSS,JS LocationIQ API, and Twilio WhatsApp API.



🌟 Key Features


📍 Location-Based Matching

Uses LocationIQ Geocoding API

Automatically converts city/state into latitude + longitude

Helps connect faster with nearby donors


📲 WhatsApp Notifications (Twilio API)

Automatic WhatsApp alerts sent to donors

Uses Twilio’s WhatsApp API for fast communication



🛠 Tech stack

Core PHP backend

MySQL database

HTML, CSS, JS


🌍 Live Demo

https://www.donorconnect.kesug.com


📂 Project Structure

/donor-connect
│── about.html
│── about.css
│── all_request.php
│── api_response_log.txt
│── composer
│── composer.bat
│── composer.json
│── composer.lock
│── composer.phar
│── contact.php
│── contact.css
│── db.php
│── donor.html
│── donor.css
│── index.html
│── index.css
│── login.php
│── login.css
│── recipient.html
│── recipient.css
│── register.php
│── register.css
│── request.php
│── request1.css
│── send_whatsapp.php
│── view_request.php
│── view_request.css
│── pic/               
│── vendor/            
└── README.md

⚠️IMPORTANT — BEFORE RUNNING THE PROJECT

This project uses two external APIs:

🔹 1. Twilio WhatsApp API

🔹 2. LocationIQ Geocoding API

The code in this repository contains ONLY placeholder fields for these APIs.
You must generate and add your own API keys for both services:

Twilio SID

Twilio Auth Token

LocationIQ API Key


Without these keys, the project will not send WhatsApp notifications or calculate distances.



📲 Step: Enable Twilio WhatsApp Sandbox

Twilio requires you to activate the WhatsApp sandbox before sending messages.

1. Go to:
Twilio Console → Messaging → WhatsApp Sandbox


2. You will see instructions like:

To join this sandbox, send a WhatsApp message to:
+14155238886

Message: join xxxxxx


3. Open WhatsApp and send:

join <your-twilio-code>


4. You will receive:

You have joined the sandbox.

➡️ After this, WhatsApp notifications through your project will work.



🔧 How to Run Locally

1. Install XAMPP or any PHP + MySQL environment.


2. Move the project into the web root:

C:\xampp\htdocs\donor-connect


3. Start Apache and MySQL from XAMPP.


4. Open phpMyAdmin:

http://localhost/phpmyadmin


5. Create a database named:

capstone


6. Create 3 tables with the following column fields:

i) blood_request Table

Table name: blood_request

Column Names

Id	
Patient_name	
Attendee_mobile	
Blood_group	
Quantity	
Required_date	
Reason	
Hospital_Address	
District	
Pincode	
Latitude	
Longitude	


SQL Example:

CREATE TABLE blood_request (
  Id INT AUTO_INCREMENT PRIMARY KEY,
  Patient_name VARCHAR(150),
  Attendee_mobile VARCHAR(30),
  Blood_group VARCHAR(5),
  Quantity INT,
  Required_date DATE,
  Reason TEXT,
  Hospital_Address VARCHAR(200),
  District VARCHAR(100),
  Pincode VARCHAR(10),
  Latitude DOUBLE,
  Longitude DOUBLE
);



ii) contact Table

Table name: contact

Id	
Name	
Email	
Message	

SQL Example:

CREATE TABLE contact (
  Id INT AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(150),
  Email VARCHAR(150),
  Message TEXT
);


iii)donor_register Table

Table name: donor_register


Id	
Name	
Age	
Blood_group	
Phone	
Password	
Address	
District	
Pincode	
Latitude	
Longitude	


SQL Example:

CREATE TABLE donor_register (
  Id INT AUTO_INCREMENT PRIMARY KEY,
  Name VARCHAR(150),
  Age INT,
  Blood_group VARCHAR(5),
  Phone VARCHAR(30),
  Password VARCHAR(255),
  Address VARCHAR(200),
  District VARCHAR(100),
  Pincode VARCHAR(10),
  Latitude DOUBLE,
  Longitude DOUBLE
);


7.Run the Project

Now open your browser and visit:

http://localhost/donor-connect/index.html
