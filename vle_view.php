<?php
include 'header.php';


$v_whereval = "";
if ((isset($_POST['p_vlera1'])) && ($_POST['p_vlera1'] != "")) {
  $v_whereval .= " and vleftapaguar >= " . $_POST['p_vlera1'];
}
if ((isset($_POST['p_vlera2'])) && ($_POST['p_vlera2'] != "")) {
  $v_whereval .= " and vleftapaguar <= " . $_POST['p_vlera2'];
}
$v_perioddate = "";
$v_perioddate2 = "";
$v_view_dt = "";
$v_begindate = "";
if ((isset($_POST['p_date1'])) && ($_POST['p_date1'] != "")) {

  $v_perioddate  = " and ek.date_trans = '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'";

  $v_perioddate2 = " and hyrjedalje.date_trans = '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'";

  $v_tempdate   = substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2);
  $v_view_dt    = substr($v_tempdate, 8, 2);
  $v_beginmonth = substr($v_tempdate, 5, 2);
  $v_monthdisp = "";
  if ($v_beginmonth == "01") {
    $v_monthdisp = "Jan";
  }
  if ($v_beginmonth == "02") {
    $v_monthdisp = "Shk";
  }
  if ($v_beginmonth == "03") {
    $v_monthdisp = "Mar";
  }
  if ($v_beginmonth == "04") {
    $v_monthdisp = "Pri";
  }
  if ($v_beginmonth == "05") {
    $v_monthdisp = "Maj";
  }
  if ($v_beginmonth == "06") {
    $v_monthdisp = "Qer";
  }
  if ($v_beginmonth == "07") {
    $v_monthdisp = "Kor";
  }
  if ($v_beginmonth == "08") {
    $v_monthdisp = "Gus";
  }
  if ($v_beginmonth == "09") {
    $v_monthdisp = "Sht";
  }
  if ($v_beginmonth == "10") {
    $v_monthdisp = "Tet";
  }
  if ($v_beginmonth == "11") {
    $v_monthdisp = "Nen";
  }
  if ($v_beginmonth == "12") {
    $v_monthdisp = "Dhj";
  }

  $v_view_dt    .= " " . $v_monthdisp . " " . substr($v_tempdate, 0, 4);
}

$v_enddate = "";
if ((isset($_POST['p_date2'])) && ($_POST['p_date2'] != "")) {

  $v_perioddate  = " and ek.date_trans >= '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'
                           and ek.date_trans <= '" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . "' ";

  $v_perioddate2 = " and hyrjedalje.date_trans >= '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'
                           and hyrjedalje.date_trans <= '" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . "' ";

  $v_tempdate   = substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2);
  $v_view_dt   .= " - " . substr($v_tempdate, 8, 2);
  $v_beginmonth = substr($v_tempdate, 5, 2);
  $v_monthdisp = "";
  if ($v_beginmonth == "01") {
    $v_monthdisp = "Jan";
  }
  if ($v_beginmonth == "02") {
    $v_monthdisp = "Shk";
  }
  if ($v_beginmonth == "03") {
    $v_monthdisp = "Mar";
  }
  if ($v_beginmonth == "04") {
    $v_monthdisp = "Pri";
  }
  if ($v_beginmonth == "05") {
    $v_monthdisp = "Maj";
  }
  if ($v_beginmonth == "06") {
    $v_monthdisp = "Qer";
  }
  if ($v_beginmonth == "07") {
    $v_monthdisp = "Kor";
  }
  if ($v_beginmonth == "08") {
    $v_monthdisp = "Gus";
  }
  if ($v_beginmonth == "09") {
    $v_monthdisp = "Sht";
  }
  if ($v_beginmonth == "10") {
    $v_monthdisp = "Tet";
  }
  if ($v_beginmonth == "11") {
    $v_monthdisp = "Nen";
  }
  if ($v_beginmonth == "12") {
    $v_monthdisp = "Dhj";
  }

  $v_view_dt    .= " " . $v_monthdisp . " " . substr($v_tempdate, 0, 4);
}

$v_klient_id  = "";
$v_klient_id2 = "";
$v_klient_id3 = "FALSE";
if ((isset($_POST['id_klienti'])) && ($_POST['id_klienti'] != "all")) {
  $v_klient_id  = " and ek.id_klienti = " . $_POST['id_klienti'];
  $v_klient_id2 = " and hyrjedalje.id_klienti = " . $_POST['id_klienti'];
  $v_klient_id3 = " id = " . $_POST['id_klienti'];
}

?>

<style>
  .table-divider-td {
    padding: 0 !important;
  }

  .table-divider {
    margin: 0;
    border: 0;
    border-top: 1px solid #5f76e8;
    /* Bootstrap's default border color */
    opacity: 1;
  }

  .container-fluid {
    padding: 15px;
  }
</style>
<div class="page-wrapper">
  <div class="container-fluid">
    <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System - Raport per transaksionet ditore/periodike</title>
    <span class="ReportDateUserN">
      Printuar dt. </span><span class="ReportDateUserB"><?php echo strftime('%Y-%m-%d'); ?></span>

    <div class="table-responsive">
      <TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
        <TR>
          <TD width="100%">
            <DIV align="center">
              <table class="OraTable table">
                <caption><span class="ReportTitle"> Raport per klient </span></caption>
                <?php
                //mysql_select_db($database_MySQL, $MySQL);
                $query_filiali_info = "select * from klienti where " . $v_klient_id3;
                $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
                $row_filiali_info = $filiali_info->fetch_assoc();
                while ($row_filiali_info) {
                ?>
                  <caption><span class="ReportSubTitle"> <?php echo strtoupper($row_filiali_info['emri']) . " " . strtoupper($row_filiali_info['mbiemri']); ?> </span></caption>
                <?php
                  $row_filiali_info = $filiali_info->fetch_assoc();
                }
                mysqli_free_result($filiali_info);
                ?>
                <caption><span class="ReportSubTitle"> <?php echo $v_view_dt; ?> </span></caption>
                <thead>
                  <tr>
                    <td colspan="13" class="table-divider-td">
                      <hr class="table-divider">
                    </td>
                  </tr>
                  <tr>
                    <th class="OraColumnHeader" colspan="13"> K&euml;mbime valutore </th>
                  </tr>
                  <tr>
                    <th class="OraColumnHeader"> Nr. </th>
                    <th class="OraColumnHeader"> Nr. Fature </th>
                    <th class="OraColumnHeader"> Dt. Trans. </th>
                    <th class="OraColumnHeader"> Emri / Mbiemri </th>
                    <th class="OraColumnHeader"> Hyr&euml; </th>
                    <th class="OraColumnHeader"> Dal&euml; </th>
                    <th class="OraColumnHeader"> Shuma e Hyr&euml; </th>
                    <th class="OraColumnHeader"> Kursi </th>
                    <th class="OraColumnHeader"> Shuma e Dal&euml; </th>
                    <th class="OraColumnHeader"> Komisioni </th>
                    <th class="OraColumnHeader"> Totali i Dal&euml; </th>
                    <th class="OraColumnHeader"> Dt. veprimi </th>
                    <th class="OraColumnHeader"> Perdoruesi </th>
                  </tr>
                </thead>
                <tbody>

                  <?php

                  set_time_limit(0);

                  //($database_MySQL, $MySQL);
                  $RepInfo_sql = " select ek.*, ed.*, k.emri, k.mbiemri, m1.monedha as mon1, m2.monedha as mon2
                         from exchange_koke as ek,
                              exchange_detaje as ed,
                              klienti as k,
                              monedha as m1,
                              monedha as m2
                        where ek.chstatus       = 'T'
                          and ek.id             = ed.id_exchangekoke
                          " . $v_klient_id  . "
                          " . $v_perioddate . "
                          " . $v_whereval   . "
                          and ek.id_klienti     = k.id
                          and ek.id_monkreditim = m1.id
                          and ed.id_mondebituar = m2.id
                     order by ek.unique_id desc
                     ";

                  $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
                  $row_RepInfo = $RepInfoRS->fetch_assoc();
                  $rowno       = 0;

                  while ($row_RepInfo) {
                    $rowno++;

                    $v_kursi = 0;
                    if ($row_RepInfo['kursi'] > $row_RepInfo['kursi1']) {
                      $v_kursi = $row_RepInfo['kursi'];
                    } else {
                      $v_kursi = $row_RepInfo['kursi1'];
                    }
                  ?>
                    <tr>
                      <td align="center" class="OraCellGroup"><?php echo $rowno; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo substr($row_RepInfo['date_trans'], 8, 2) . "." . substr($row_RepInfo['date_trans'], 5, 2) . "." . substr($row_RepInfo['date_trans'], 0, 4); ?></td>
                      <td align="center" class="OraCellGroup3"><?php echo $row_RepInfo['emri']; ?> <?php echo $row_RepInfo['mbiemri']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['mon2']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['mon1']; ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftadebituar'], 2, '.', ','); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($v_kursi, 4, '.', ','); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftakredituar'], 2, '.', ','); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftakomisionit'], 2, '.', ','); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftapaguar'], 2, '.', ','); ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo $row_RepInfo['perdoruesi']; ?></td>
                    </tr>
                  <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                  };
                  mysqli_free_result($RepInfoRS);
                  ?>
                  <tr>
                    <td colspan="13" class="table-divider-td">
                      <hr class="table-divider">
                    </td>
                  </tr>
                  <tr>
                    <th class="OraColumnHeader" colspan="13"> Hyrje / Dalje </th>
                  </tr>
                  <tr>
                    <th class="OraColumnHeader"> Nr. </th>
                    <th class="OraColumnHeader"> Emri / Mbiemri </th>
                    <th class="OraColumnHeader"> Monedha </th>
                    <th class="OraColumnHeader" colspan="3"> Hyr&euml; </th>
                    <th class="OraColumnHeader" colspan="2"> </th>
                    <th class="OraColumnHeader" colspan="3"> Dal&euml; </th>
                    <th class="OraColumnHeader"> Dt. Trans. </th>
                    <th class="OraColumnHeader"> Perdoruesi </th>
                  </tr>
                  <?php

                  //mysql_select_db($database_MySQL, $MySQL);
                  $query_gjendje_info = " SELECT hyrjedalje.date_trans, hyrjedalje.perdoruesi, hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha,
                                           SUM( case when hyrjedalje.drcr = 'Debitim'  then hyrjedalje.vleftapaguar else 0 end) vleftadebit,
                                           SUM( case when hyrjedalje.drcr = 'Kreditim' then hyrjedalje.vleftapaguar else 0 end) vleftakredit
                                      FROM hyrjedalje, monedha, klienti
                                     WHERE hyrjedalje.id_monedhe = monedha.id
                                       AND hyrjedalje.id_klienti = klienti.id
                                       AND hyrjedalje.chstatus   = 'T'
                                       " . $v_klient_id2  . "
                                       " . $v_perioddate2 . "
                                       " . $v_whereval    . "
                                  GROUP BY hyrjedalje.date_trans, hyrjedalje.perdoruesi, hyrjedalje.id_klienti, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe, monedha.monedha
                                  ORDER BY hyrjedalje.date_trans, hyrjedalje.perdoruesi, klienti.emri, klienti.mbiemri, hyrjedalje.id_monedhe ";
                  $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
                  $row_gjendje_info = $gjendje_info->fetch_assoc();
                  $rowno2           = 0;

                  while ($row_gjendje_info) {
                    $rowno2++;

                  ?>
                    <tr>
                      <td align="center" class="OraCellGroup"> <?php echo $rowno2; ?> </td>
                      <td align="center" class="OraCellGroup3"> <?php echo $row_gjendje_info['emri']; ?> <?php echo $row_gjendje_info['mbiemri']; ?></td>
                      <td align="center" class="OraCellGroup"><?php echo $row_gjendje_info['monedha']; ?></td>
                      <td align="right" class="OraCellGroup2" colspan="3"><?php echo number_format($row_gjendje_info['vleftadebit'], 2, '.', ','); ?></td>
                      <td align="right" class="OraCellGroup2" colspan="2"></td>
                      <td align="right" class="OraCellGroup2" colspan="3"><?php echo number_format($row_gjendje_info['vleftakredit'], 2, '.', ','); ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo substr($row_gjendje_info['date_trans'], 8, 2) . "." . substr($row_RepInfo['date_trans'], 5, 2) . "." . substr($row_RepInfo['date_trans'], 0, 4); ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo $row_gjendje_info['perdoruesi']; ?></td>
                    </tr>
                    <tr>
                      <td colspan="13" class="table-divider-td">
                        <hr class="table-divider">
                      </td>
                    </tr>
                  <?php $row_gjendje_info = $gjendje_info->fetch_assoc();
                  }
                  mysqli_free_result($gjendje_info);
                  // ---------------------------------------------------------------------------------
                  ?>

                  <tr>
                    <td colspan="13" class="table-divider-td">
                      <hr class="table-divider">
                    </td>
                  </tr>
                  <tr>
                    <td align="left" class="OraCellGroup4" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Totali i transaksioneve:</b>&nbsp;&nbsp;<b>[ <?php echo $rowno; ?> ] [ <?php echo $rowno2; ?> ]</b>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="0" colspan="13">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr class="line">
                          <td height="0">
                            <DIV class=line></DIV>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td height="5" colspan="13"></td>
                  </tr>
              </table>
            </DIV>
          </TD>
        </TR>
      </TABLE>
    </div>
    <?php include 'footer.php'; ?>