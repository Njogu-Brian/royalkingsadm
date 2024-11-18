<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = getenv('DB_SERVER') ?: "localhost";
$username = getenv('DB_USERNAME') ?: "royalce1_portaladmin";
$password = getenv('DB_PASSWORD') ?: "9HSe5fG5gmK@*5";
$dbname = getenv('DB_NAME') ?: "royalce1_admissions";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$requiredFields = [
    'class', 'surname', 'firstName', 'middleName', 'gender', 'dob',
    'country', 'county', 'residence', 'religion', 'motherFullName', 
    'motherPhone', 'motherEmail', 'motherID', 'nomineeFullName', 
    'nomineeContact'
];

$errors = [];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "The field '$field' is required.";
    }
}

if (!empty($errors)) {
    echo implode("<br>", $errors);
    exit;
}

$passportPhoto = '';
if (isset($_FILES['passportPhoto']) && $_FILES['passportPhoto']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['passportPhoto']['tmp_name'];
    $fileName = time() . '_' . basename($_FILES['passportPhoto']['name']);
    $fileType = $_FILES['passportPhoto']['type'];
    $fileSize = $_FILES['passportPhoto']['size'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024;

    if ($fileSize > $maxFileSize) {
        die("File is too large. Maximum size allowed is 2MB.");
    }

    if (!in_array($fileType, $allowedTypes)) {
        die("Invalid file type. Please upload a JPEG, PNG, or GIF image.");
    }

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFile = $targetDir . basename($fileName);

    if (move_uploaded_file($fileTmpPath, $targetFile)) {
        $passportPhoto = $targetFile;
    } else {
        die("Error uploading the file.");
    }
}

$stmt = $conn->prepare("INSERT INTO admission_form (
    class, surname, first_name, middle_name, gender, dob, birth_certificate_number, passport_photo, country, postal_address, city, county, residence, religion, blood_group, allergies, pending_immunization, diseases, preferred_doctor, doctor_mobile, preferred_hospital, emergency_contact_name, emergency_contact_phone, emergency_contact_email, parental_state, family_name, mother_full_name, mother_phone, mother_email, mother_id, father_full_name, father_phone, father_email, father_id, guardian_title, guardian_full_name, guardian_phone, guardian_email, guardian_id, nominee_full_name, nominee_contact, previous_school_name, previous_school_adm_no, assessment_no, reason_for_leaving
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "ssssssssssssssssssssssssssssssssssssssssssssss",
    $_POST['class'], $_POST['surname'], $_POST['firstName'], $_POST['middleName'], $_POST['gender'], $_POST['dob'],
    $_POST['birthCertNo'], $passportPhoto, $_POST['country'], $_POST['postalAddress'], $_POST['city'], $_POST['county'],
    $_POST['residence'], $_POST['religion'], $_POST['bloodGroup'], $_POST['allergies'], $_POST['pendingImmunization'],
    $_POST['diseases'], $_POST['preferredDoctor'], $_POST['doctorMobile'], $_POST['preferredHospital'], $_POST['emergencyContactName'],
    $_POST['emergencyContactPhone'], $_POST['emergencyContactEmail'], $_POST['parentalState'], $_POST['familyName'], $_POST['motherFullName'],
    $_POST['motherPhone'], $_POST['motherEmail'], $_POST['motherID'], $_POST['fatherFullName'], $_POST['fatherPhone'], $_POST['fatherEmail'],
    $_POST['fatherID'], $_POST['guardianTitle'], $_POST['guardianFullName'], $_POST['guardianPhone'], $_POST['guardianEmail'], $_POST['guardianID'],
    $_POST['nomineeFullName'], $_POST['nomineeContact'], $_POST['previousSchoolName'], $_POST['previousSchoolAdmNo'], $_POST['assessmentNo'], $_POST['reasonForLeaving']
);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'admissions@royalkingsschools.sc.ke';
    $mail->Password = getenv('EMAIL_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('admissions@royalkingsschools.sc.ke', 'Royal Kings Schools');
    $mail->addAddress('info@royalkingsschools.sc.ke');

    $mail->isHTML(true);
    $mail->Subject = 'New Admission Draft Submission';
    $mail->Body = "A new admission draft has been submitted.<br>"
        . "<a href='https://www.royalkingsschools.sc.ke/admissions'>View Admission Drafts</a>";

    $mail->send();
    echo "Record saved and email notification sent.";
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

$stmt->close();
$conn->close();
?>
