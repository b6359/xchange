<?php
require_once('ConMySQL.php');

// Start session
session_start();

// Define session variables
$_SESSION['CNAME'] = "EXCHANGE";
$_SESSION['CADDR'] = "Durr&euml;s ";
$_SESSION['CNIPT'] = "A12345678B";
$_SESSION['CADMI'] = "Administrator";
$_SESSION['CMOBI'] = "+355 69 123 4567";
$_SESSION['DPPPP'] = "1000000";

$loginFormAction = $_SERVER['PHP_SELF'];

if (!empty($_POST['email']) && !empty($_POST['password'])) {

    $loginEmail = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize input to prevent SQL injection
    $password = preg_replace("/[\\'\"<>-]/", '', $password);

    $MM_redirectLoginSuccess = "exchange.php";
    $MM_redirectLoginFailed = "index.php";

    $conn = new mysqli($hostname_MySQL, $username_MySQL, $password_MySQL, $database_MySQL);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, username, full_name, id_trans, id_filiali, id_usertype FROM app_user WHERE e_mail=? AND password=?");
    $stmt->bind_param("ss", $loginEmail, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $myrow = $result->fetch_assoc();

        $_SESSION['uid'] = $myrow["id"];
        $_SESSION['Username'] = $myrow["username"];
        $_SESSION['full_name'] = $myrow["full_name"];
        $_SESSION['Usertrans'] = $myrow["id_trans"];
        $_SESSION['Userfilial'] = $myrow["id_filiali"];
        $_SESSION['Usertype'] = $myrow["id_usertype"];

        header("Location: " . $MM_redirectLoginSuccess);
        exit();
    } else {
        header("Location: " . $MM_redirectLoginFailed . "?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
    <title><?php echo htmlspecialchars($_SESSION['CNAME']); ?> - Web Exchange System</title>
    <link href="./dist/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">
            <div class="auth-box row">
                <div class="col-lg-7 col-md-5 modal-bg-img d-lg-flex align-items-center">
                    <img src="./assets/images/Logo.png" alt="wrapkit">
                </div>
                <div class="col-lg-5 col-md-7 bg-white">
                    <div class="p-3">
                        <div class="text-center">
                            <img src="./assets/images/big/icon.png" alt="wrapkit">
                        </div>
                        <h2 class="mt-3 text-center">Hyrje në sistem</h2>
                        <p class="text-center">Futni adresën tuaj të emailit dhe fjalëkalimin për të hyrë në panelin e administratorit.</p>
                        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                        <div class="alert alert-danger" role="alert">
                            Emri i përdoruesit ose fjalëkalimi i pavlefshëm!
                        </div>
                        <?php endif; ?>
                        <form class="mt-4" method="post" action="index.php">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="uname">Email</label>
                                        <input class="form-control" id="email" name="email" type="text"
                                            placeholder="Enter your email">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label text-dark" for="password">Fjalëkalimin</label>
                                        <input class="form-control" id="password" name="password" type="password"
                                            placeholder="Enter your password">
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button type="submit" name="commit" class="btn w-100 btn-dark">Sign In</button>
                                </div>
                                <!-- <div class="col-lg-12 text-center mt-5">
                                Nuk keni një llogari? <a href="signup.php" class="text-danger">Sign Up</a>
                                </div> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script src="./assets/libs/jquery/dist/jquery.min.js "></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="./assets/libs/popper.js/dist/umd/popper.min.js "></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.min.js "></script>
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $(".preloader ").fadeOut();
    </script>
</body>

</html>