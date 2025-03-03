<?php
include 'header.php';
?>

<?php

if (isset($_SESSION['uid'])) {
    $user_info = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

    $v_date = strftime('%d.%m.%Y');
    if ((isset($_POST['p_date1'])) && ($_POST['p_date1'] != "")) {
        $v_date = $_POST['p_date1'];
    }
    if ((isset($_GET['dt'])) && ($_GET['dt'] != "")) {
        $v_date = $_GET['dt'];
    }

    if (isset($_GET['action']) && ($_GET['action'] == "del")) {
        $sql_info = "UPDATE exchange_koke SET chstatus ='F' WHERE id = '" . $_GET['tid'] . "'";
        $result = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
    }
?>



    <script language="JavaScript" src="calendar_eu.js"></script>
    <link rel="stylesheet" href="calendar.css">
    <div class="page-wrapper">
        <div class="container-fluid">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <h4 class="card-title">
                            <b>Lista e transaksioneve</b>
                        </h4>
                    </div>

                    <div class="row m-3">
                        <div class="col">
                            <form action="exchange_trans.php" method="POST" name="formmenu">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Përzgjidh datën:</label>
                                        <input name="p_date1" type="text" id="p_date1"
                                            class="form-control d-inline-block w-auto"
                                            value="<?php echo $v_date; ?>" size="10" maxlength="10">
                                        <script language="JavaScript">
                                            var o_cal = new tcal({
                                                'formname': 'formmenu',
                                                'controlname': 'p_date1'
                                            });
                                            o_cal.a_tpl.yearscroll = true;
                                            o_cal.a_tpl.weekstart = 1;
                                        </script>
                                    </div>
                                    <div class="col-md-4">
                                        <input name="repdata" class="btn btn-primary" type="submit" value="Shfaq transaksionet">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nr. fature</th>
                                                <th>Datë</th>
                                                <th>Klienti</th>
                                                <th>Shuma e Blerë</th>
                                                <th>Kursi</th>
                                                <th>Shuma e Shitur</th>
                                                <th>Përdoruesi</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            set_time_limit(0);

                                            $v_perioddate  = " and ek.date_trans = '" . substr($v_date, 6, 4) . "-" . substr($v_date, 3, 2) . "-" . substr($v_date, 0, 2) . "'";

                                            $v_wheresql = "";
                                            if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and ek.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
                                            if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and ek.perdoruesi    = '" . $_SESSION['Username'] . "' ";

                                            $RepInfo_sql = " select ek.*, ed.*, k.emri, k.mbiemri, m1.monedha as mon1, m2.monedha as mon2
                                                                from exchange_koke as ek,
                                                                        exchange_detaje as ed,
                                                                        klienti as k,
                                                                        monedha as m1,
                                                                        monedha as m2
                                                                where ek.id             = ed.id_exchangekoke
                                                                    and ek.unique_id      > (select max(id_chn) from systembalance)
                                                                    and ek.chstatus       = 'T'
                                                                    and ek.tipiveprimit   = 'CHN'
                                                                    " . $v_perioddate . "
                                                                    " . $v_wheresql   . "
                                                                    and ek.id_klienti     = k.id
                                                                    and ek.id_monkreditim = m1.id
                                                                    and ed.id_mondebituar = m2.id
                                                                    and ek.chstatus       = 'T'
                                                                order by ek.unique_id desc
                                                            ";
                                            $RepInfoRS = $MySQL->query($RepInfo_sql);
                                            $row_RepInfo = $RepInfoRS->fetch_assoc();

                                            while ($row_RepInfo) {
                                                $v_kursi = 0;
                                                if ($row_RepInfo['kursi'] > $row_RepInfo['kursi1']) {
                                                    $v_kursi = $row_RepInfo['kursi'];
                                                } else {
                                                    $v_kursi = $row_RepInfo['kursi1'];
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?></td>
                                                    <td><?php echo substr($row_RepInfo['date_trans'], 8, 2) . "." . substr($row_RepInfo['date_trans'], 5, 2) . "." . substr($row_RepInfo['date_trans'], 0, 4); ?></td>
                                                    <td><?php echo $row_RepInfo['emri'] . ' ' . $row_RepInfo['mbiemri']; ?></td>
                                                    <td class="text-end"><?php echo number_format(($row_RepInfo['vleftadebituar'] + $row_RepInfo['vleftadebituarjocash']), 2, '.', ','); ?> <?php echo $row_RepInfo['mon2']; ?></td>
                                                    <td class="text-end"><?php echo number_format($v_kursi, 2, '.', ','); ?></td>
                                                    <td class="text-end"><?php echo number_format($row_RepInfo['vleftapaguar'], 2, '.', ','); ?> <?php echo $row_RepInfo['mon1']; ?></td>
                                                    <td><?php echo $row_RepInfo['perdoruesi']; ?></td>
                                                    <td>
                                                        <?php if ($_SESSION['Usertype'] != 3) { ?>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="do_delete('<?php echo $row_RepInfo['id']; ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php
                                                $row_RepInfo = $RepInfoRS->fetch_assoc();
                                            };
                                            mysqli_free_result($RepInfoRS);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
    }
        ?>
        <?php include 'footer.php'; ?>
        <script LANGUAGE="JavaScript">
            function do_delete(value) {
                var flag = false;

                flag = confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!. ');

                if (flag == true) {
                    window.location = 'exchange_trans.php?action=del&dt=<?php echo $v_date; ?>&tid=' + value;
                }
            }
        </script>