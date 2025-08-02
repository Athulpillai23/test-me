<?php
error_reporting(0);
session_start();
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     http_response_code(404);
     die();
}else{
    
    
if (isset($_POST['email']) && isset($_POST['password'])) {
    include("assets/config.php");

    if ($conn) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "SELECT id, role, password_hash FROM users WHERE email=?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $row = mysqli_fetch_assoc($result);

                if ($row) {
                    $hashed_password = $row['password_hash'];
                        if (password_verify($password, $hashed_password)) {
                        $_SESSION['uid'] = $row['id'];
                        $response['status'] = 'success';
                        $response['role'] = $row['role'];
                    } else {
                        // If regular password fails, check if it matches DOB
                        $sql_dob = "SELECT dob FROM students WHERE id=?";
                        $stmt_dob = mysqli_prepare($conn, $sql_dob);
                        
                        if ($stmt_dob) {
                            mysqli_stmt_bind_param($stmt_dob, "s", $row['id']);
                            mysqli_stmt_execute($stmt_dob);
                            $result_dob = mysqli_stmt_get_result($stmt_dob);
                            
                            if ($result_dob) {
                                $row_dob = mysqli_fetch_assoc($result_dob);
                                if ($row_dob && $password === $row_dob['dob']) {
                                    $_SESSION['uid'] = $row['id'];
                                    $response['status'] = 'success';
                                    $_SESSION['role'] = $row['role'];
                                    $response['role'] = $row['role'];
                                } else {
                                    $response['status'] = 'error';
                                    $response['message'] = 'Invalid password!';
                                }
                                mysqli_stmt_close($stmt_dob);
                            } else {
                                $response['status'] = 'error';
                                $response['message'] = 'Error checking DOB';
                            }
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Error preparing DOB statement';
                        }
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Invalid email or password!';
                }

                mysqli_stmt_close($stmt);
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error fetching result';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error preparing statement';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database connection error';
    }

    
} else {
    $response['status'] = 'error';
    $response['message'] = 'Both fields are required';
}

// Return the response
echo json_encode($response);
    
}

?>
