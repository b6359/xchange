<?php
require_once('ConMySQL.php');
// Start the session before any output
session_start();
date_default_timezone_set('Europe/Tirane');

if(!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
    session_unset();
    session_destroy();
    if(!headers_sent()) {
        header("Location: index.php");
        exit;
    } else {
        echo '<script>window.location.href="index.php";</script>';
        exit;
    }
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  // logout
  $GLOBALS['uid'] = "";
  $GLOBALS['Username'] = "";
  $GLOBALS['full_name'] = "";
  $GLOBALS['Usertrans'] = "";
  $GLOBALS['Userfilial'] = "";
  $GLOBALS['Usertype'] = "";
  $_SESSION['uid'] = "";
  $_SESSION['Username'] = "";
  $_SESSION['full_name'] = "";
  $_SESSION['Usertrans'] = "";
  $_SESSION['Userfilial'] = "";
  $_SESSION['Usertype'] = "";

  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['uid'];
}

?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.ico">
  <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System</title>
  <!-- <link href="./dist/css/style.min.css" rel="stylesheet"> -->
  <link href="./dist/css/style.css" rel="stylesheet">
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/popper.js/dist/umd/popper.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./dist/js/app-style-switcher.js"></script>
  <script src="./dist/js/feather.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./dist/js/sidebarmenu.js"></script>
  <script src="./dist/js/custom.min.js"></script>
  <script src="./assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="./assets/extra-libs/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
  <script src="./dist/js/pages/datatable/datatable-basic.init.js"></script>  
</head>

<body>
  <div class="preloader">
    <div class="lds-ripple">
      <div class="lds-pos"></div>
      <div class="lds-pos"></div>
    </div>
  </div>
  <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

    <header class="topbar" data-navbarbg="skin6">
      <nav class="navbar top-navbar navbar-expand-lg">
        <div class="navbar-header" data-logobg="skin6">
          <!-- This is for the sidebar toggle which is visible on mobile only -->
          <a
            class="nav-toggler waves-effect waves-light d-block d-lg-none"
            href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
          <!-- ============================================================== -->
          <!-- Logo -->
          <!-- ============================================================== -->
          <div class="navbar-brand">
            <!-- Logo icon -->
            <a href="dashboard.php">
              <img
                src="./assets/images/Logo.png"
                alt=""
                class="img-fluid" />
            </a>
          </div>
          <!-- ============================================================== -->
          <!-- End Logo -->
          <!-- ============================================================== -->
          <!-- ============================================================== -->
          <!-- Toggle which is visible on mobile only -->
          <!-- ============================================================== -->
          <a
            class="topbartoggler d-block d-lg-none waves-effect waves-light"
            href="javascript:void(0)"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation"><i class="ti-more"></i></a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse collapse" id="navbarSupportedContent">
         
          <!-- ============================================================== -->
          <!-- Right side toggle and nav items -->
          <!-- ============================================================== -->
          <ul class="navbar-nav d-flex justify-content-end w-100">
           
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <li class="nav-item d-none d-md-block">
              <a class="nav-link" href="exchange_tabel.php">
                TABELA
              </a>
            </li>
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="javascript:void(0)"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <img
                  src="./assets/images/users/1.jpg"
                  alt="user"
                  class="rounded-circle"
                  width="40" />
                <span class="ms-2 d-none d-lg-inline-block"><span>Hello,</span>
                  <span class="text-dark"><?php echo $_SESSION['full_name']; ?></span>
                  <i data-feather="chevron-down" class="svg-icon"></i></span>
              </a>
              <div
                class="dropdown-menu dropdown-menu-end dropdown-menu-right user-dd animated flipInY">
                <!-- <a class="dropdown-item" href="javascript:void(0)"><i data-feather="user" class="svg-icon me-2 ms-1"></i> My
                  Profile</a>
                <a class="dropdown-item" href="javascript:void(0)"><i
                    data-feather="credit-card"
                    class="svg-icon me-2 ms-1"></i>
                  My Balance</a>
                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="mail" class="svg-icon me-2 ms-1"></i>
                  Inbox</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="settings" class="svg-icon me-2 ms-1"></i>
                  Account Setting</a>
                <div class="dropdown-divider"></div> -->
                <a class="dropdown-item" href="<?php echo $logoutAction ?>"><i data-feather="power" class="svg-icon me-2 ms-1"></i>
                  Logout</a>
                <!--<div class="dropdown-divider"></div>
                 <div class="pl-4 p-3">
                  <a href="javascript:void(0)" class="btn btn-sm btn-info">View Profile</a>
                </div> -->
              </div>
            </li>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
          </ul>
        </div>
      </nav>
    </header>
    <aside class="left-sidebar" data-sidebarbg="skin6">
      <!-- Sidebar scroll-->
      <div class="scroll-sidebar" data-sidebarbg="skin6">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
          <ul id="sidebarnav">
            <!-- <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="dashboard.php"
                aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span class="hide-menu">Dashboard</span></a>
            </li> -->
            <li class="list-divider"></li>
            <li class="nav-small-cap">
              <span class="hide-menu">Exchange</span>
            </li>

            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange.php"
                aria-expanded="false"><i class="fas fa-exchange-alt"></i><span class="hide-menu">Këmbim Monetar</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_kalimlog.php"
                aria-expanded="false"><i class="fas fa-random"></i><span class="hide-menu">Kalim ndërmjet filialeve</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_hyrdal.php"
                aria-expanded="false"><i class="fas fa-money-bill-alt"></i><span class="hide-menu">Veprime Monetare</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_rate.php"
                aria-expanded="false"><i class="fas fa-percent"></i><span class="hide-menu">Kursi i Këmbimit</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_opclbal.php"
                aria-expanded="false"><i class="fas fa-calendar-alt"></i><span class="hide-menu">Hapje/Mbyllje Dite</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_balance.php"
                aria-expanded="false"><i class="fas fa-balance-scale"></i><span class="hide-menu">Bilanci sipas veprimeve</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange_account.php"
                aria-expanded="false"><i class="fas fa-database"></i><span class="hide-menu">Të Dhënat Bazë</span></a>
            </li>
            <li class="list-divider"></li>
            <li class="nav-small-cap">
              <span class="hide-menu">Reports</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                <i class="fas fa-file-alt"></i>
                <span class="hide-menu">Raporte </span>
              </a>
              <ul aria-expanded="false" class="collapse  first-level base-level-line">
                <li class="sidebar-item">
                  <a href="vle_rep.php" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i>
                    <span class="hide-menu"> Raport për vlera</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="cli_rep.php" class="sidebar-link">
                    <i class="fas fa-user"></i>
                    <span class="hide-menu"> Raport për Klient</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="fiu_rep.php" class="sidebar-link">
                    <i class="fas fa-file-alt"></i>
                    <span class="hide-menu"> Raport për DPPPP</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="boa_rep.php" class="sidebar-link">
                  <i class="fa fa-piggy-bank"></i>
                    <span class="hide-menu"> Banka e Shqipërisë</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="dt_rep.php" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="hide-menu"> Veprimet ditore/periodike</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="st_rep.php" class="sidebar-link">
                    <i class="fa fa-list"></i>
                    <span class="hide-menu"> Përmbledhje e veprimeve</span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="list-divider"></li>
            <li class="nav-small-cap">
              <span class="hide-menu">Extra</span>
            </li>

            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="contact.php"
                aria-expanded="false"><i class="fas fa-phone"></i><span class="hide-menu">Kontakt</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="exchange_users.php"
                aria-expanded="false"><i class="fas fa-users"></i><span class="hide-menu">Përdoruesit</span></a>
            </li>
            
            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="exchange_tabel_live.php"
                target="_blank"
                aria-expanded="false"><i class="fas fa-table"></i><span class="hide-menu">TABELA LIVE</span></a>
            </li>
            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="<?php echo $logoutAction ?>"
                aria-expanded="false"><i data-feather="log-out" class="feather-icon"></i><span class="hide-menu">Logout</span></a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>