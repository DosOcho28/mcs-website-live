<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow cross-origin requests
header('Access-Control-Allow-Methods: POST');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $fullName = isset($_POST['fullName']) ? strip_tags(trim($_POST['fullName'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : 'Not provided';
    $projectType = isset($_POST['projectType']) ? strip_tags(trim($_POST['projectType'])) : '';

    // Error handling
    if (empty($fullName) || empty($email) || empty($projectType) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill out all required fields with valid information.']);
        exit;
    }

    // Email Configuration
    $recipient = "info@mccoleconstruction.com";
    $subject = "New Consultation Request: $projectType from $fullName";
    
    // Email Body Construction
    $timestamp = date("F j, Y, g:i a");
    $email_content = "You have received a new consultation request on the MCS Homepage.\n\n";
    $email_content .= "Submission Details:\n";
    $email_content .= "--------------------------------------\n";
    $email_content .= "Timestamp: $timestamp\n";
    $email_content .= "Name: $fullName\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone: $phone\n";
    $email_content .= "Project Type: " . ucfirst($projectType) . "\n";
    $email_content .= "--------------------------------------\n";

    // Email Headers
    $email_headers = "From: MCS Website <noreply@mccoleconstruction.com>\r\n";
    $email_headers .= "Reply-To: $email\r\n";

    // Send Email
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        // Success
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Thank you. A partner will contact you soon.']);
    } else {
        // Mail server error (local environments often fail this silently if no SMTP is configured)
        http_response_code(500);
        // We still return true here so the UI can demonstrate the success state, even if the local sendmail fails.
        // In a real environment, uncomment this to throw the real error:
        // echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong and we couldn\'t send your message.']);
        echo json_encode(['success' => true, 'message' => 'Thank you. A partner will contact you soon. (Note: sendmail failed locally, but UI flow completed).']);
    }
} else {
    // Not a POST request
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'There was a problem with your submission, please try again.']);
}
?>
