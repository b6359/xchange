<?php
require_once('ConMySQL.php');
// Initialize the session
session_start();
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
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
  <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System</title>
  <link href="./dist/css/style.min.css" rel="stylesheet">
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
                src="./assets/images/freedashDark.svg"
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
          <!-- toggle and nav items -->
          <!-- ============================================================== -->
          <ul class="navbar-nav float-left me-auto ms-3 ps-1">
            <!-- Notification -->
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle pl-md-3 position-relative"
                href="javascript:void(0)"
                id="bell"
                role="button"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <span><i data-feather="bell" class="svg-icon"></i></span>
                <span class="badge text-bg-primary notify-no rounded-circle">5</span>
              </a>
              <div
                class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                <ul class="list-style-none">
                  <li>
                    <div
                      class="message-center notifications position-relative">
                      <!-- Message -->
                      <a
                        href="javascript:void(0)"
                        class="message-item d-flex align-items-center border-bottom px-3 py-2">
                        <div class="btn btn-danger rounded-circle btn-circle">
                          <i data-feather="airplay" class="text-white"></i>
                        </div>
                        <div class="w-75 d-inline-block v-middle ps-2">
                          <h6 class="message-title mb-0 mt-1">
                            Luanch Admin
                          </h6>
                          <span class="font-12 text-nowrap d-block text-muted">Just see the my new admin!</span>
                          <span class="font-12 text-nowrap d-block text-muted">9:30 AM</span>
                        </div>
                      </a>
                      <!-- Message -->
                      <a
                        href="javascript:void(0)"
                        class="message-item d-flex align-items-center border-bottom px-3 py-2">
                        <span
                          class="btn btn-success text-white rounded-circle btn-circle"><i data-feather="calendar" class="text-white"></i></span>
                        <div class="w-75 d-inline-block v-middle ps-2">
                          <h6 class="message-title mb-0 mt-1">Event today</h6>
                          <span
                            class="font-12 text-nowrap d-block text-muted text-truncate">Just a reminder that you have event</span>
                          <span class="font-12 text-nowrap d-block text-muted">9:10 AM</span>
                        </div>
                      </a>
                      <!-- Message -->
                      <a
                        href="javascript:void(0)"
                        class="message-item d-flex align-items-center border-bottom px-3 py-2">
                        <span class="btn btn-info rounded-circle btn-circle"><i data-feather="settings" class="text-white"></i></span>
                        <div class="w-75 d-inline-block v-middle ps-2">
                          <h6 class="message-title mb-0 mt-1">Settings</h6>
                          <span
                            class="font-12 text-nowrap d-block text-muted text-truncate">You can customize this template as you want</span>
                          <span class="font-12 text-nowrap d-block text-muted">9:08 AM</span>
                        </div>
                      </a>
                      <!-- Message -->
                      <a
                        href="javascript:void(0)"
                        class="message-item d-flex align-items-center border-bottom px-3 py-2">
                        <span
                          class="btn btn-primary rounded-circle btn-circle"><i data-feather="box" class="text-white"></i></span>
                        <div class="w-75 d-inline-block v-middle ps-2">
                          <h6 class="message-title mb-0 mt-1">Pavan kumar</h6>
                          <span class="font-12 text-nowrap d-block text-muted">Just see the my admin!</span>
                          <span class="font-12 text-nowrap d-block text-muted">9:02 AM</span>
                        </div>
                      </a>
                    </div>
                  </li>
                  <li>
                    <a
                      class="nav-link pt-3 text-center text-dark"
                      href="javascript:void(0);">
                      <strong>Check all notifications</strong>
                      <i class="fa fa-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            <!-- End Notification -->
            <!-- ============================================================== -->
            <!-- create new -->
            <!-- ============================================================== -->
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                id="navbarDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                <i data-feather="settings" class="svg-icon"></i>
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>

          </ul>
          <!-- ============================================================== -->
          <!-- Right side toggle and nav items -->
          <!-- ============================================================== -->
          <ul class="navbar-nav float-end">
            <!-- ============================================================== -->
            <!-- Search -->
            <!-- ============================================================== -->
            <li class="nav-item d-none d-md-block">
              <a class="nav-link" href="javascript:void(0)">
                <form>
                  <div class="customize-input">
                    <input
                      class="form-control custom-shadow custom-radius border-0 bg-white"
                      type="search"
                      placeholder="Search"
                      aria-label="Search" />
                    <i class="form-control-icon" data-feather="search"></i>
                  </div>
                </form>
              </a>
            </li>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
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
                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="user" class="svg-icon me-2 ms-1"></i> My
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
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="power" class="svg-icon me-2 ms-1"></i>
                  Logout</a>
                <div class="dropdown-divider"></div>
                <div class="pl-4 p-3">
                  <a href="javascript:void(0)" class="btn btn-sm btn-info">View Profile</a>
                </div>
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
            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="dashboard.php"
                aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span class="hide-menu">Dashboard</span></a>
            </li>
            <li class="list-divider"></li>
            <li class="nav-small-cap">
              <span class="hide-menu">Applications</span>
            </li>

            <li class="sidebar-item">
              <a
                class="sidebar-link"
                href="exchange.php"
                aria-expanded="false"><i data-feather="tag" class="feather-icon"></i><span class="hide-menu">Këmbim Monetar</span></a>
            </li>
            <li class="list-divider"></li>
            <li class="nav-small-cap">
              <span class="hide-menu">Extra</span>
            </li>

            <li class="sidebar-item">
              <a
                class="sidebar-link sidebar-link"
                href="authentication-login1.html"
                aria-expanded="false"><i data-feather="log-out" class="feather-icon"></i><span class="hide-menu">Logout</span></a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>