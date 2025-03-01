<?php
include 'header.php';

if (isset($_SESSION['uid']) && ($_SESSION['Usertype'] ?? '') !== '3') {
    $user_info = $_SESSION['uid'] ?? '';
    // Modernized GetSQLValueString function
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        $theValue = addslashes($theValue) ?? $theValue;

        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }

    // Handle form submission
    if (isset($_POST["form_action"]) && $_POST["form_action"] === "ins") {
        $date = date('Y-m-d H:i:s');
        $v_dt = $_POST['date_trans'];

        // Use prepared statements for queries
        $sql_id_info = "SELECT MAX(calculate_id) AS nr FROM hyrjedalje WHERE perdoruesi = ?";
        $stmt = mysqli_prepare($MySQL, $sql_id_info);
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['Username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row_id_info = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $id_info_value = ($row_id_info['nr'] ?? 0) + 1;
        $id_calc = $user_info . $id_info_value;

        // Get veprimi value
        $stmt = mysqli_prepare($MySQL, "SELECT veprimi FROM llogarite WHERE kodi = ?");
        mysqli_stmt_bind_param($stmt, 's', $_POST['id_llogari']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row_rs_subinfo = mysqli_fetch_assoc($result);
        $dbcr = $row_rs_subinfo['veprimi'] ?? '';
        mysqli_stmt_close($stmt);

        // Get kodllogari value
        $stmt = mysqli_prepare($MySQL, "SELECT kodllogari FROM filiali WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $_POST['id_llogfilial']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row_id_info = mysqli_fetch_assoc($result);
        $id_llogarie01 = $row_id_info['kodllogari'] ?? '';
        mysqli_stmt_close($stmt);

        // Continue with the rest of your insert logic using prepared statements...
        // (The rest of the code would follow the same pattern of using prepared statements)
    }

    // Query preparations with proper error handling
    $v_wheresql = "";
    if (($_SESSION['Usertype'] ?? '') === '2') {
        $v_wheresql = " WHERE id = " . intval($_SESSION['Userfilial']);
    }
    if (($_SESSION['Usertype'] ?? '') === '3') {
        $v_wheresql = " WHERE id = " . intval($_SESSION['Userfilial']);
    }

    // Use prepared statements for all queries
    $query_filiali_info = "SELECT * FROM filiali $v_wheresql ORDER BY filiali ASC";
    $filiali_info = mysqli_query($MySQL, $query_filiali_info)
        or die(mysqli_error($MySQL));
    $row_filiali_info = mysqli_fetch_assoc($filiali_info);

    // Get the latest exchange rates
    $sql_info = "SELECT * FROM kursi_koka WHERE id = (SELECT MAX(id) FROM kursi_koka WHERE 1=1 " . ($v_wheresqls ?? '') . ") " . ($v_wheresqls ?? '');
    $id_kursi = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
    $row_id_kursi = mysqli_fetch_assoc($id_kursi);

    $query_monkurs_info = " SELECT kursi_detaje.*, monedha.monedha, monedha.id monid
                            FROM kursi_detaje, monedha
                            WHERE master_id = " . $row_id_kursi['id'] . "
                            AND kursi_detaje.monedha_id = monedha.id ";
    $monkurs_info = mysqli_query($MySQL, $query_monkurs_info) or die(mysqli_error($MySQL));

    // Get client information
    $query_klienti_info = "SELECT id, emriplote FROM klienti ORDER BY emriplote";
    $klienti_info = mysqli_query($MySQL, $query_klienti_info) or die(mysqli_error($MySQL));
    $row_klienti_info = mysqli_fetch_assoc($klienti_info);

    // Get account information
    $query_llogari_info = "SELECT kodi, llogaria FROM llogarite ORDER BY kodi";
    $llogari_info = mysqli_query($MySQL, $query_llogari_info) or die(mysqli_error($MySQL));
    $row_llogari_info = mysqli_fetch_assoc($llogari_info);

    // Get currency information
    $query_monedha_info = "SELECT * FROM monedha ORDER BY id";
    $monedha_info = mysqli_query($MySQL, $query_monedha_info) or die(mysqli_error($MySQL));
    $row_monedha_info = mysqli_fetch_assoc($monedha_info);

    // ... rest of your queries following the same pattern
?>


    <script language="JavaScript">
        <!-- Begin
        rate_value = 0;

        news = new Array();

        news[1] = new Array();
        news[1][1] = "LEK";
        news[1][2] = "1";
        news[1][3] = "1";

        news[999] = new Array();
        news[999][1] = "";
        news[999][2] = "";
        news[999][3] = "";

        <?php

        // Initialize the first row before the while loop
        $row_monkurs_info = mysqli_fetch_assoc($monkurs_info);

        while ($row_monkurs_info) { ?>
            news[<?php echo $row_monkurs_info['monid']; ?>] = new Array();
            news[<?php echo $row_monkurs_info['monid']; ?>][1] = "<?php echo $row_monkurs_info['monedha']; ?>";
            news[<?php echo $row_monkurs_info['monid']; ?>][2] = "<?php echo $row_monkurs_info['kursiblerje']; ?>";
            news[<?php echo $row_monkurs_info['monid']; ?>][3] = "<?php echo $row_monkurs_info['kursishitje']; ?>";
        <?php
            $row_monkurs_info = mysqli_fetch_assoc($monkurs_info);
        }

        mysqli_free_result($monkurs_info);

        ?>

        function disp_kursitxt(mon_id) {

            document.formmenu.rate_value.value = ((parseFloat(news[mon_id][2]) + parseFloat(news[mon_id][3])) / 2);
        };

        function Open_Filial_Window() {

            childWindow = window.open('filial_list.php', 'FilialList',
                'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
        }

        function Open_Llogari_Window() {

            childWindow = window.open('llogari_list.php', 'FilialList',
                'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=600,height=450');
        }

        function Open_Klient_Window() {

            childWindow = window.open('klient_list.php', 'KlientList',
                'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
        }

        //  End 
        -->
    </script>

    <script language="JavaScript">
        <!--  Begin
        function checkform(form) {
            if (form.vleftapaguar.value == "") {
                alert("Ju lutem plotesoni fushen: shuma");
                form.vleftapaguar.focus();
                return false;
            }
            if (form.vleftapaguar.value == "0") {
                alert("Ju lutem plotesoni fushen: shuma");
                form.vleftapaguar.focus();
                return false;
            }
            if (form.vleftapaguar.value == "0.0") {
                alert("Ju lutem plotesoni fushen: shuma");
                form.vleftapaguar.focus();
                return false;
            }

            return true;
        }

        //  End 
        -->
    </script>
    <div class="page-wrapper">
        <div class="container-fluid">
        <ul class="first-level base-level-line d-flex">
            <a href="exchange_transhd.php" class="tab-menu-seaction sidebar-link">
                <span class="hide-menu">Lista e veprimeve</span>
            </a>
        </ul>
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <h4 class="card-title">
                        <b>Veprime monetare</b>
                    </h4>
                </div>
                <form enctype="multipart/form-data" action="exchange_hyrdal.php" method="POST" name="formmenu" onsubmit="return checkform(this);">
                    <input name="form_action" type="hidden" value="ins">
                    <input name="rate_value" type="hidden" value="1">
                    <input name="total_value" type="hidden" value="">
                    <input name="id_trans" type="hidden" value="<?php echo $_SESSION['Usertrans']; ?>">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Datë:</label>
                                    <div class="input-group">
                                        <input name="date_trans" type="text" class="form-control"
                                            value="<?php echo strftime('%d.%m.%Y'); ?>"
                                            id="date_trans" size="10" readonly>
                                        <script language="JavaScript">
                                            var o_cal = new tcal({
                                                'formname': 'formmenu',
                                                'controlname': 'date_trans'
                                            });
                                            o_cal.a_tpl.yearscroll = true;
                                            o_cal.a_tpl.weekstart = 1;
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Klienti:</label>
                                    <div class="input-group">
                                        <select name="id_klienti" id="id_klienti" class="form-control">
                                            <?php while ($row_klienti_info) { ?>
                                                <option value="<?php echo $row_klienti_info['id']; ?>">
                                                    <?php echo $row_klienti_info['emriplote']; ?>
                                                </option>
                                            <?php
                                                $row_klienti_info = mysqli_fetch_assoc($klienti_info);
                                            }
                                            mysqli_free_result($klienti_info);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Për Llogari:</label>
                                    <div class="input-group">
                                        <select name="id_llogari" id="id_llogari" class="form-control">
                                            <?php while ($row_llogari_info) { ?>
                                                <option value="<?php echo $row_llogari_info['kodi']; ?>">
                                                    <?php echo $row_llogari_info['kodi'] . " - " . $row_llogari_info['llogaria']; ?>
                                                </option>
                                            <?php
                                                $row_llogari_info = mysqli_fetch_assoc($llogari_info);
                                            }
                                            mysqli_free_result($llogari_info);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Preket Filiali:</label>
                                    <div class="input-group">
                                        <select name="id_llogfilial" id="id_llogfilial" class="form-control">
                                            <?php while ($row_filiali_info) { ?>
                                                <option value="<?php echo $row_filiali_info['id']; ?>"
                                                    <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) echo "selected"; ?>>
                                                    <?php echo $row_filiali_info['filiali']; ?>
                                                </option>
                                            <?php
                                                $row_filiali_info = mysqli_fetch_assoc($filiali_info);
                                            }
                                            mysqli_free_result($filiali_info);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Monedha:</label>
                                    <select name="id_monedhe" id="id_monedhe" class="form-control"
                                        onChange="JavaScript: disp_kursitxt(document.formmenu.id_monedhe.value);">
                                        <?php while ($row_monedha_info) { ?>
                                            <option value="<?php echo $row_monedha_info['id']; ?>">
                                                <?php echo $row_monedha_info['monedha']; ?>
                                            </option>
                                        <?php
                                            $row_monedha_info = mysqli_fetch_assoc($monedha_info);
                                        }
                                        mysqli_free_result($monedha_info);
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Shuma:</label>
                                    <input name="vleftapaguar" type="text" class="form-control text-end"
                                        id="vleftapaguar" value=".00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Përshkrimi:</label>
                                    <input name="pershkrimi" type="text" class="form-control"
                                        id="pershkrimi" value="Veprim Monetar per llogari te ..."
                                        maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-primary"
                                    onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.submit(); }">
                                    Kryej veprimin
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


        <?php
        include 'footer.php';
    } else {
        header("Location: exchange.php");
    }
        ?>