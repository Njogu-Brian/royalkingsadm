<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$servername = "localhost"; // Usually localhost
$username = "---"; // Your database username
$password = "----"; // Your database password
$dbname = "----"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data
    $childrenData = json_decode($_POST['childrenData'], true); // Get children data from AJAX
    $uploadDir = 'uploads/';
    
    foreach ($childrenData as $child) {
        // Handle file uploads
        $passportPhoto = $child['section1']['passportPhoto'];
        $birthCertificate = $child['section1']['birthCertificate'];

        // Function to handle file upload
        function uploadFile($file, $uploadDir) {
            $targetFile = $uploadDir . basename($file["name"]);
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                return $targetFile;
            } else {
                return "";
            }
        }

        // Uploading files
        $uploadedPassportPhoto = uploadFile($passportPhoto, $uploadDir);
        $uploadedBirthCertificate = uploadFile($birthCertificate, $uploadDir);

        // Prepare email content for each child
        $message = "Class: " . $child['section1']['class'] . "\n";
        $message .= "Surname: " . $child['section1']['surname'] . "\n";
        $message .= "First Name: " . $child['section1']['firstName'] . "\n";
        $message .= "Middle Name: " . $child['section1']['middleName'] . "\n";
        $message .= "Gender: " . $child['section1']['gender'] . "\n";
        $message .= "Date of Birth: " . $child['section1']['dob'] . "\n";
        $message .= "Address: " . $child['section2']['address'] . "\n";
        $message .= "Transport: " . $child['section2']['transport'] . "\n";
        $message .= "Religion: " . $child['section2']['religion'] . "\n";
        $message .= "Mother's Name: " . $child['section3']['motherName'] . "\n";
        $message .= "Mother's Phone: " . $child['section3']['motherPhone'] . "\n";
        $message .= "Mother's Email: " . $child['section3']['motherEmail'] . "\n";
        $message .= "Mother's ID: " . $child['section3']['motherID'] . "\n";
        $message .= "Father's Name: " . $child['section3']['fatherName'] . "\n";
        $message .= "Father's Phone: " . $child['section3']['fatherPhone'] . "\n";
        $message .= "Father's Email: " . $child['section3']['fatherEmail'] . "\n";
        $message .= "Father's ID: " . $child['section3']['fatherID'] . "\n";
        $message .= "Guardian's Name: " . $child['section3']['guardianName'] . "\n";
        $message .= "Guardian's Phone: " . $child['section3']['guardianPhone'] . "\n";
        $message .= "Guardian's Email: " . $child['section3']['guardianEmail'] . "\n";
        $message .= "Guardian's ID: " . $child['section3']['guardianID'] . "\n";
        $message .= "Nominee's Name: " . $child['section3']['nomineeName'] . "\n";
        $message .= "Nominee's Contact: " . $child['section3']['nomineeContact'] . "\n";
        $message .= "Previous School: " . $child['section4']['previousSchool'] . "\n";
        $message .= "KNEC Assessment No: " . $child['section4']['assessmentNo'] . "\n";
        $message .= "Reason for Leaving: " . $child['section4']['reasonLeaving'] . "\n";
        $message .= "Reason for Choosing Our School: " . $child['section4']['reasonChoosing'] . "\n";
        $message .= "Uploaded Files: " . implode(", ", [$uploadedPassportPhoto, $uploadedBirthCertificate]) . "\n";

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '-----';               // SMTP username (your Gmail address)
            $mail->Password   = '------';                   // SMTP password (your Gmail app password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption
            $mail->Port       = 587;                                   // TCP port to connect to

            // Recipients
            $mail->setFrom('------', 'Royal Kings Admissions');
            $mail->addAddress('-----');         // Add a recipient (info email)

            // Add all other email addresses input by the user
            if (!empty($child['section3']['motherEmail'])) {
                $mail->addAddress($child['section3']['motherEmail']);
            }
            if (!empty($child['section3']['fatherEmail'])) {
                $mail->addAddress($child['section3']['fatherEmail']);
            }
            if (!empty($child['section3']['guardianEmail'])) {
                $mail->addAddress($child['section3']['guardianEmail']);
            }

            // Content
            $mail->isHTML(false);                                      // Set email format to plain text
            $mail->Subject = "New Admission Form Submission";
            $mail->Body    = $message;

            // Send the email
            $mail->send();
            echo "Your application has been submitted successfully!";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
} else {
    echo "Invalid request method.";
}
?>