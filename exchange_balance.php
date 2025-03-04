<?php
include 'header.php';
$v_file = "";
$v_dt1  = strftime('%d.%m.%Y');
if ((isset($_POST['date_trans1'])) && ($_POST['date_trans1'] != "")) {
  $v_dt1 = $_POST['date_trans1'];
}
$v_dt2  = strftime('%d.%m.%Y');
if ((isset($_POST['date_trans2'])) && ($_POST['date_trans2'] != "")) {
  $v_dt2 = $_POST['date_trans2'];
}

$v_view = "n/e";
if ((isset($_POST['view'])) && ($_POST['view'] != "")) {
  $v_view = $_POST['view'];
}

if ($v_view == "excel") {

  include("kembimi.php");
}

?>
<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">
<div class="page-wrapper">
  <div class="container-fluid">
    <ul class="first-level base-level-line d-flex">
      <a href="exchange_balance_p.php" class="tab-menu-seaction sidebar-link">
        <span class="hide-menu">Bilanci sipas Llogarive</span>
      </a>
    </ul>
    <div class="container_12">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <h4 class="card-title">
            <b>Bilanci sipas veprimeve p&euml;r dat&euml;n</b>
          </h4>
          <button class="btn btn-outline-primary" onclick="printForm()">
            <i class="fas fa-print cursor-pointer"></i> Printo
          </button>
        </div>
        <form class="p-5" enctype="multipart/form-data" ACTION="exchange_balance.php" METHOD="POST" name="formmenu" id="formmenu">
          <input name="act" type="hidden" value="n/e">
          <input name="view" type="hidden" value="n/e">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group mb-3">
                <label class="form-label">Nga datë:</label>
                <input class="form-control" name="date_trans1" type="text" value="<?php echo $v_dt1; ?>" id="date_trans" size="10" readonly>
                <script language="JavaScript">
                  var o_cal = new tcal({
                    'formname': 'formmenu',
                    'controlname': 'date_trans1'
                  });
                  o_cal.a_tpl.yearscroll = true;
                  o_cal.a_tpl.weekstart = 1;
                </script>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group mb-3">
                <label class="form-label">Deri datë:</label>
                <input class="form-control" name="date_trans2" type="text" value="<?php echo $v_dt2; ?>" id="date_trans" size="10" readonly>
                <script language="JavaScript">
                  var o_cal = new tcal({
                    'formname': 'formmenu',
                    'controlname': 'date_trans2'
                  });
                  o_cal.a_tpl.yearscroll = true;
                  o_cal.a_tpl.weekstart = 1;
                </script>
              </div>
            </div>
            <div class="col-md-3 d-flex align-items-center mt-2">
              <div class="form-group mb-3">
                <input name="insupd" class="btn btn-primary" type="button" value=" Shfaq informacionin " onClick="JavaScript: document.formmenu.view.value = 'n/e'; document.formmenu.submit(); ">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <tr valign="middle">
                <td colspan="5">
                  <div class="ctxheading7"><B>Hapja e balancave ditore</B></div>
                </td>
              </tr>
              <tr>
                <td><b>Llogaria</b></td>
                <td><b>Monedha</b></td>
                <td align="right"><b>Hyrje</b></td>
              </tr>
              <?php

              set_time_limit(0);

              $v_wheresql = "";
              if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and openbalance.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
              if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and openbalance.perdoruesi    = '" . $_SESSION['Username'] . "' ";

              $query_gjendje_info = " SELECT openbalance.id, filiali.filiali, monedha.monedha, sum(openbalance.vleftakredituar) as vleftakredituar
                                        FROM openbalance, monedha, filiali
                                      WHERE openbalance.monedha_id    = monedha.id
                                        AND openbalance.chstatus      = 'T'
                                        AND openbalance.id_llogfilial = filiali.id
                                        AND openbalance.date_trans   >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                        AND openbalance.date_trans   <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                          " . $v_wheresql . "
                                    GROUP BY openbalance.id, filiali.filiali, monedha.monedha
                                    ORDER BY filiali.filiali, openbalance.monedha_id ";
              $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              $row_gjendje_info = $gjendje_info->fetch_assoc();

              while ($row_gjendje_info) {
              ?>
                <tr>
                  <td><?php echo $row_gjendje_info['filiali']; ?></td>
                  <td><?php echo $row_gjendje_info['monedha']; ?></td>
                  <td align="right"><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
                </tr>
              <?php $row_gjendje_info = $gjendje_info->fetch_assoc();;
              }
              mysqli_free_result($gjendje_info);
              // ---------------------------------------------------------------------------------
              ?>
              <tr>
                <td colspan="5">
                  <div class="ctxheading7"><B>Veprimet e brendshme (hyrje/dalje)</B></div>
                </td>
              </tr>
              <tr>
                <td><b>Klienti</b></td>
                <td><b>Monedha</b></td>
                <td align="right"><b>Hyrje</b></td>
                <td align="right"><b>Dalje</b></td>
                <td align="right"><b>Gjendje</b></td>
              </tr>
              <?php

              set_time_limit(0);

              $v_wheresql = "";
              if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and hyrjedalje.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
              if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and hyrjedalje.perdoruesi    = '" . $_SESSION['Username'] . "' ";

              //mysql_select_db($database_MySQL, $MySQL);
              $query_gjendje_info = " SELECT hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha,
                                            SUM( case when hyrjedalje.drcr = 'D' then hyrjedalje.vleftapaguar else 0 end) vleftadebit,
                                            SUM( case when hyrjedalje.drcr = 'K' then hyrjedalje.vleftapaguar else 0 end) vleftakredit
                                        FROM hyrjedalje, monedha, klienti
                                      WHERE hyrjedalje.id_monedhe  = monedha.id
                                        AND hyrjedalje.id_klienti  = klienti.id
                                        AND hyrjedalje.chstatus    = 'T'
                                        AND hyrjedalje.date_trans >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                        AND hyrjedalje.date_trans <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                          " . $v_wheresql . "
                                    GROUP BY hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha
                                    ORDER BY klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe ";
              $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              $row_gjendje_info = $gjendje_info->fetch_assoc();

              while ($row_gjendje_info) {
              ?>
                <tr>
                  <td><?php echo $row_gjendje_info['emri'] . " " . $row_gjendje_info['mbiemri']; ?></td>
                  <td><?php echo $row_gjendje_info['monedha']; ?></td>
                  <td align="right"><?php echo number_format($row_gjendje_info['vleftadebit'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
                  <td align="right"><?php echo number_format($row_gjendje_info['vleftakredit'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
                  <td align="right"><?php echo number_format(($row_gjendje_info['vleftadebit'] - $row_gjendje_info['vleftakredit']), 2, '.', ','); ?>&nbsp; &nbsp;</td>
                </tr>
              <?php $row_gjendje_info = $gjendje_info->fetch_assoc();
              }
              mysqli_free_result($gjendje_info);
              // ---------------------------------------------------------------------------------
              ?>

              <tr>
                <td colspan="5">
                  <div class="ctxheading7"><B>K&euml;mbimet ditore</B></div>
                </td>
              </tr>
              <tr>
                <td><b>Llogaria</b></td>
                <td><b>Monedha</b></td>
                <td align="right"><b>Hyrje</b></td>
                <td align="right"><b>Dalje</b></td>
                <td align="right"><b>Gjendje</b></td>
              </tr>
              <?php

              $v_wheresql = "";
              if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and ek.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
              if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and ek.perdoruesi    = '" . $_SESSION['Username'] . "' ";

              //mysql_select_db($database_MySQL, $MySQL);
              $query_gjendje_info = " select tab_info.llogaria, tab_info.monedha, sum(tab_info.vleftakredituar) vleftakredituar, sum(tab_info.vleftadebituar) vleftadebituar
                                        from (
                                                  select ek.id_llogkomision llogaria, m1.id, m1.monedha, sum(ek.vleftakomisionit) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by ek.id_llogkomision, m1.id, m1.monedha
                                                  having (sum(ek.vleftakomisionit) <> 0)
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(0) vleftakredituar, sum( ed.vleftadebituar ) vleftadebituar
                                                    from exchange_koke ek, exchange_detaje ed, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.id             = ed.id_exchangekoke
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ed.id_mondebituar = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'TRN'
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(0) vleftakredituar, sum(ek.vleftapaguar) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'TRN'
                                                      and ek.id_klienti     = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                            ) tab_info
                                    group by tab_info.llogaria, tab_info.id
                                    order by tab_info.llogaria, tab_info.id";
              $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              $row_gjendje_info = $gjendje_info->fetch_assoc();

              while ($row_gjendje_info) {
              ?>
                <tr>
                  <td><?php echo $row_gjendje_info['llogaria']; ?></td>
                  <td><?php echo $row_gjendje_info['monedha']; ?></td>
                  <td align="right"><?php echo number_format($row_gjendje_info['vleftadebituar'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
                  <td align="right"><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
                  <td align="right"><?php echo number_format(($row_gjendje_info['vleftadebituar'] - $row_gjendje_info['vleftakredituar']), 2, '.', ','); ?>&nbsp; &nbsp;</td>
                </tr>
              <?php $row_gjendje_info = $gjendje_info->fetch_assoc();;
              }
              mysqli_free_result($gjendje_info);
              // ---------------------------------------------------------------------------------
              ?>
              <?php
              $query_gjendje_info = " select tab_info.monedha, sum(tab_info.vleftakredituar) vleftakredituar, sum(tab_info.vleftadebituar) vleftadebituar
                                      from  (

                                                  select ek.id_llogkomision llogaria, m1.id, m1.monedha, sum(ek.vleftakomisionit) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by ek.id_llogkomision, m1.id, m1.monedha
                                                  having (sum(ek.vleftakomisionit) <> 0)
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(0) vleftakredituar, sum( ed.vleftadebituar ) vleftadebituar
                                                    from exchange_koke ek, exchange_detaje ed, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'CHN'
                                                      and ek.id             = ed.id_exchangekoke
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ed.id_mondebituar = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'TRN'
                                                      and ek.id_llogfilial  = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                                union all
                                                  select filiali.filiali llogaria, m1.id, m1.monedha, sum(0) vleftakredituar, sum(ek.vleftapaguar) vleftadebituar
                                                    from exchange_koke ek, filiali, monedha m1
                                                    where ek.chstatus       = 'T'
                                                      and ek.tipiveprimit   = 'TRN'
                                                      and ek.id_klienti     = filiali.id
                                                      and ek.date_trans    >= '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "'
                                                      and ek.date_trans    <= '" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . "'
                                                      and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                                group by filiali.filiali, m1.id, m1.monedha
                                            ) tab_info
                                  group by tab_info.id
                                  order by tab_info.id ";
              $gjendje_info = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              $row_gjendje_info = $gjendje_info->fetch_assoc();

              while ($row_gjendje_info) {
              ?>
                <tr>
                  <td>&nbsp;</td>
                  <td><b><?php echo $row_gjendje_info['monedha']; ?></b></td>
                  <td align="right"><b><?php echo number_format($row_gjendje_info['vleftadebituar'], 2, '.', ','); ?></b>&nbsp; &nbsp;</td>
                  <td align="right"><b><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?></b>&nbsp; &nbsp;</td>
                  <td align="right"><b><?php echo number_format(($row_gjendje_info['vleftadebituar'] - $row_gjendje_info['vleftakredituar']), 2, '.', ','); ?></b>&nbsp; &nbsp;</td>
                </tr>
              <?php $row_gjendje_info = $gjendje_info->fetch_assoc();
              }
              mysqli_free_result($gjendje_info);
              ?>

              <tr>
                <td height="20" colspan="5"></td>
              </tr>
            </table>
          </div>

        </form>
      </div>
    </div>
    <?php if ($v_file != "") {  ?>
      <br /><br />
      <b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href="<?php echo $v_file; ?>"><?php echo substr($v_file, 4, 100); ?></a>&nbsp;</b>
      <br /><br />
    <?php  }  ?>


    <br />

  </div>
  <?php include 'footer.php'; ?>

  <script>
    function printForm() {
      var printContents = document.getElementById('formmenu').innerHTML;
      var originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  </script>