<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
  session_start();

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    include('assets/config.php');
    $response = array();

// Set default response
$response['status'] = 'error';
$response['message'] = 'Something went wrong!';

try {
    if(isset($_POST['otp']) && isset($_POST['email'])){
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $otp = trim(mysqli_real_escape_string($conn, $_POST['otp']));

        if(!isset($_SESSION['otp']) || empty($_SESSION['otp'])) {
            $response['status'] = 'error';
            $response['message'] = 'OTP expired or not generated. Please request a new OTP.';
        } else {
            $generatedOtp = trim($_SESSION['otp']);

            if($otp === $generatedOtp){
            $response['status'] = 'success';
                $response['message'] = 'OTP matched successfully';
            unset($_SESSION['otp']);
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid OTP! Please try again.';
        }
        }
    } else if(isset($_POST['password']) && isset($_POST['email'])){
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE `users` SET `password_hash` = ? WHERE `users`.`email` = ?";
        $stmt2 = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt2, "ss", $passwordHash, $email);

        $sql2 = "SELECT `id` FROM `users` WHERE `users`.`email`=?";
        $stmt3 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt3, "s", $email);
        mysqli_stmt_execute($stmt3);
        $result = mysqli_stmt_get_result($stmt3);

        if(mysqli_stmt_execute($stmt2) && mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $_SESSION['uid'] = $row['id'];
            $response['status'] = 'update_success';
            $response['message'] = 'Password successfully updated';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Unable to update password...!';
        }

        mysqli_stmt_close($stmt2);
    } else if(isset($_POST['email'])){
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        function domain_exists($email, $record = 'MX'){
            list($user, $domain) = explode('@', $email);
            return checkdnsrr($domain, $record);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && domain_exists($email)) {  
            $query = "SELECT * FROM `users` WHERE `email`=?";
            $stmt = mysqli_prepare($conn, $query);
            
            if($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) > 0){
                    $OTP = generateOTP();
                    
                    // Load PHPMailer classes
                    require 'vendor/phpmailer/phpmailer/src/Exception.php';
                    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
                    require 'vendor/phpmailer/phpmailer/src/SMTP.php';

                    $mail = new PHPMailer(true);
                    
                    try {
                        // Server settings
                        $mail->SMTPDebug = 0; // Disable debug output for production
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'erpvirtualacademyverify@gmail.com';
                        $mail->Password = 'lkqa ckfu eekc knkb';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->isHTML(false);
                        // Disable SSL verification (only for testing)
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        // Recipients
                        $mail->setFrom('erpvirtualacademyverify@gmail.com', 'Virtual Academy Login OTP');
                        $mail->addReplyTo('erpvirtualacademyverify@gmail.com', 'Virtual Academy Login OTP');
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset OTP';
                        $mail->Body = '
                                     <p>Dear '.$email.',</p>
                                     <br> <br>
                                     Welcome to <b>Virtual Academy</b>, <br>
                                     We are received a request to reset your password, we need to verify your account before reseting password, please enter the below OTP to reset your password.
                                     <h1><b>'.$OTP.'</b></h1>
                                     <p>This OTP will expire after 5 minutes.</p>
                                     <p>This email is computer generated so please do not reply to this email.</p>
                                     <p>Thank you for using <b>Virtual Academy</b>.</p>
                                     <p>If you did not request this, please ignore this email.</p>
                                     <br>
                                     <p><b>Best regards,</b></p>
                                     <p><b>Virtual Academy Digital</b></p>';
                        $mail->AltBody = 'Your OTP is: ' . $OTP;

                        if($mail->send()) {
                    $response['status'] = 'success';
                            $response['email'] = $email;
                            $_SESSION['otp'] = $OTP;
                            $response['message'] = 'OTP has been sent to your email.';
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Failed to send email. Please try again later.';
                        }
                } catch (Exception $e) {
                        $response['status'] = 'error';
                        $response['message'] = 'Email sending failed: ' . $e->getMessage();
                        error_log("Email Error: " . $e->getMessage());
                }
                } else {
                    $response['status'] = 'error';
                $response['message'] = 'Email not found!';
            }
            mysqli_stmt_close($stmt);
          } else {
                $response['status'] = 'error';
                $response['message'] = 'Database error. Please try again.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid email address!';
          }
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An error occurred: ' . $e->getMessage();
    error_log("Error in forgotPassword.php: " . $e->getMessage());
}

// Ensure proper JSON encoding
header('Content-Type: application/json');
    echo json_encode($response);
exit();

function generateOTP(){
    return rand(100000, 999999);
}
?>