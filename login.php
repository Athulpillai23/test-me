<?php
// error_reporting(0); // It's better to see errors during development: error_reporting(E_ALL); ini_set('display_errors', 1);
session_start(); // Should be the very first thing

// --- BEGIN DEBUG BLOCK for login.php (if session exists) ---

// --- END DEBUG BLOCK ---

if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
  include('assets/config.php'); // Make sure $conn is correctly initialized here if not already

  if (!$conn) { // Add a check for database connection
      echo "<p style='color:red; background:white;'>DEBUG login.php: Database connection in config.php failed!</p>";
      // Potentially exit or handle this error rather than proceeding with queries
  } else {
      $query = "SELECT `role` FROM `users` WHERE `users`.`id`=?";
      $stmt = mysqli_prepare($conn, $query);

      if ($stmt) { // Check if statement preparation was successful
          mysqli_stmt_bind_param($stmt, "s", $uid);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
          $row = mysqli_fetch_array($result);
          mysqli_stmt_close($stmt);

          if ($row && isset($row['role'])) {
              // Store the fetched role in session IF IT'S NOT ALREADY THE SAME
              // This could be a source of issues if $_SESSION['role'] was set by login-backend.php
              // and this part overwrites or conflicts.
              // Let's be careful here. For now, we mostly rely on login-backend.php to set the role.
              // If $_SESSION['role'] is already correctly set by login-backend.php, this query might be redundant for role checking here.
              
              $db_role = trim($row['role']); // Role from DB

              // Compare with session role if it exists
              $session_role = isset($_SESSION['role']) ? trim($_SESSION['role']) : null;

              echo "<p style='background:lightgreen;padding:5px;'>DEBUG login.php: DB role for UID $uid is '$db_role'. Session role is '$session_role'.</p>";

              $_SESSION['role'] = $row['role'];

              if ($db_role == "admin") { // Use $db_role for redirection decision
                  header('Location: admin_panel/dashboard.php'); // Comment out for debug
                  exit();
                  echo "<p style='color:blue; background:white;'>DEBUG: login.php would redirect to ADMIN panel.</p>";
              } else if ($db_role == "owner") {
                  header('Location: owner_panel/index.php'); // Comment out for debug
                  exit();
                  echo "<p style='color:blue; background:white;'>DEBUG: login.php would redirect to OWNER panel.</p>";
              } else if ($db_role == "teacher") {
                  header('Location: teacher_panel/dashboard.php'); // Comment out for debug
                  exit();
                  echo "<p style='color:blue; background:white;'>DEBUG: login.php would redirect to TEACHER panel.</p>";
              } else if ($db_role == "student") {
                  header('Location: studentpanelV1/dashboard.php'); // Comment out for debug
                  exit();
                  echo "<p style='color:blue; background:white;'>DEBUG: login.php would redirect to NEW STUDENT panel.</p>";
              } else {
                  echo "<p style='color:orange; background:white;'>DEBUG: login.php: Role '$db_role' from DB not recognized for redirection.</p>";
              }
          } else {
               echo "<p style='color:red; background:white;'>DEBUG login.php: Could not fetch role from DB for UID $uid or role not set in DB row.</p>";
          }
      } else {
          echo "<p style='color:red; background:white;'>DEBUG login.php: mysqli_prepare failed for role query. Error: " . mysqli_error($conn) . "</p>";
      }
  } // end $conn check
} else {
     //echo "<p style='background:lightyellow;padding:5px;'>DEBUG login.php: No existing session (UID not set), showing login form.</p>";
}

// The rest of your login.php (HTML form) follows
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Virtual Academy</title>
  <!-- Fontawesome CDN Link -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="login-form-style.css">
  <link rel="icon" type="image/x-icon" href="images/1.png">
</head>

<body>
  <div class="container">
    <input type="checkbox" id="flip">
    <div class="cover">
      <div class="front">
        <img src="images/company-logo.png" alt="">
        <div class="text">
          <span class="text-1">VIRTUAL ACADEMY<br></span>
          <span class="text-2">Since - 2011</span>
        </div>
      </div>

    </div>
    <div class="forms">
      <div class="form-content">
        <div class="login-form">

          <div class="title" id='board-title'>Login</div>

          <div class="alert-box">
            <div class="alert alert-danger text-center mt-3" role="alert" id="error-msg">

            </div>
          </div>

          <form action="index.php" id="login-form" method="post">
            <div class="input-boxes">
              <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" id='loginEmail' required>
              </div>
              <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter your password" id="password" required>
                <i class="bi bi-eye-fill" style="margin-left:auto;margin-right: 6px;" id="togglePassword"></i>
              </div>
              <div class="text"><a id="forgotpassword">Forgot password?</a></div>
              <div class="button input-box">
                <button type="submit" class="btn">
                  Submit
                </button>
              </div>
            </div>
          </form>


          <!-- forgot password gui -->
          <form action="index.php" id="forgotPassword-form" method="post" style="display:none;">

            <div class="input-boxes">
              <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="forgotEmail" placeholder="Enter your email" required>
              </div>

              <div class="text" style="margin-bottom: 20px;display:flex">
                <a id="backToLogin">back to login?</a>
              </div>

              <div class="button input-box">
                <button type="submit" id='sendCodeBtn'>
                  Send Code
                </button>
              </div>

            </div>
          </form>

          <form id="otpVarification-form" method="post" style="display:none;">

            <div class="input-boxes">
              <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" value="some value" id="otpDisabledEmail">
              </div>

              <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="text" name="otp" placeholder="Enter code" id="otpCode" required>
              </div>

              <div class="text" style="margin-bottom: 20px;display:flex">
                <a id="backToforgotPasswordForm">back</a>
                <a id="resendOTP" style='margin-left: auto;'>resend OTP?</a>
              </div>

              <div class="button input-box">
                <button type="submit" id='verifyCodeBtn'>
                  Verify Code
                </button>
              </div>

            </div>
          </form>


          <form id="createNewPassword-form" method="post" style="display:none;">

            <div class="input-boxes">
              <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="newpassword" id='newpassword' placeholder='Enter new password' required>
              </div>

              <div class="invalid-feedback" id='weakPasswordFeedback'></div>

              <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirmpassword" id='confirmpassword' placeholder='Confirm password' required>
              </div>

              <div class="invalid-feedback" id='passwordNotSame'>
                New password and confirm password are not same!
              </div>

              <div class="form-check mt-3 ">
                <input class="form-check-input" type="checkbox" value="" id="showPasswords">
                <label class="form-check-label" for="showPasswords" id='showPasswordLabel'>
                  Show password
                </label>
              </div>

              <div class="button input-box">
                <button type="submit" id='changePasswordBtn'>
                  Change password
                </button>
              </div>

            </div>
          </form>

          <!-- end of forgot password gui -->


        </div>

      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="index.js"></script>


</body>

</html>