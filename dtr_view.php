<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('Europe/Tirane');

require_once('ConMySQL.php');

if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? '';

  $v_begindate = '';
  $v_perioddate = '';
  $v_perioddate2 = '';
  $v_view_dt = '';
  
  $monthNames = [
    '01' => 'Jan', '02' => 'Shk', '03' => 'Mar', '04' => 'Pri',
    '05' => 'Maj', '06' => 'Qer', '07' => 'Kor', '08' => 'Gus',
    '09' => 'Sht', '10' => 'Tet', '11' => 'Nen', '12' => 'Dhj'
  ];

  if (!empty($_POST['p_date1'])) {
    $date1 = DateTime::createFromFormat('d.m.Y', $_POST['p_date1']);
    if ($date1) {
      $v_perioddate = " and ek.date_trans = '" . $date1->format('Y-m-d') . "'";
      $v_perioddate2 = " and hyrjedalje.date_trans = '" . $date1->format('Y-m-d') . "'";
      
      $v_monthdisp = $monthNames[$date1->format('m')] ?? '';
      $v_view_dt = $date1->format('d') . " " . $v_monthdisp . " " . $date1->format('Y');
    }
  }

  if (!empty($_POST['p_date2'])) {
    $date2 = DateTime::createFromFormat('d.m.Y', $_POST['p_date2']);
    if ($date2 && isset($date1)) {
      $v_perioddate = " and ek.date_trans >= '" . $date1->format('Y-m-d') . "'
                      and ek.date_trans <= '" . $date2->format('Y-m-d') . "' ";
      
      $v_perioddate2 = " and hyrjedalje.date_trans >= '" . $date1->format('Y-m-d') . "'
                       and hyrjedalje.date_trans <= '" . $date2->format('Y-m-d') . "' ";
      
      $v_monthdisp = $monthNames[$date2->format('m')] ?? '';
      $v_view_dt .= " - " . $date2->format('d') . " " . $v_monthdisp . " " . $date2->format('Y');
    }
  }

  $v_branch_id = (int)($_POST['id_llogfilial'] ?? 0);

  if (isset($_POST["view"]) && $_POST["view"] === "excel") {
    require_once 'Spreadsheet/Excel/Writer.php';

    $v_file = "rep/VeprimetDitore_" . date('YmdHis') . ".xls";
    $workbook = new Spreadsheet_Excel_Writer($v_file);

    $format1 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'center',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'aqua'
    ]);
    $format1->setTextWrap();

    $format2 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'aqua',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'gray'
    ]);
    $format2->setTextWrap();

    $format3 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'aqua',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'gray'
    ]);
    $format3->setTextWrap();

    $format4 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'white'
    ]);
    $format4->setTextWrap();

    $format5 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'white'
    ]);
    $format5->setTextWrap();

    $format6 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'yellow'
    ]);
    $format6->setTextWrap();

    $format7 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'yellow'
    ]);
    $format7->setTextWrap();

    $format8 = $workbook->addFormat([
      'Size'       => 11,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 0,
      'FgColor'    => 'white'
    ]);
    $format8->setTextWrap();

    $format9 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'white',
      'FontFamily' => 'Calibri',
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'red'
    ]);
    $format9->setTextWrap();

    $format10 = $workbook->addFormat([
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'white',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'red'
    ]);
    $format10->setTextWrap();

    set_time_limit(0);

    $worksheet1 = $workbook->addWorksheet('Veprimet');

    $worksheet1->write(0,  0,  "", $format8);
    $worksheet1->write(0,  1,  "", $format8);
    $worksheet1->write(0,  2,  "", $format8);
    $worksheet1->write(0,  3,  "", $format8);
    $worksheet1->write(0,  4,  "", $format8);
    $worksheet1->write(0,  5,  "", $format8);
    $worksheet1->write(0,  6,  "", $format8);
    $worksheet1->write(0,  7,  "", $format8);
    $worksheet1->write(0,  8,  "", $format8);
    $worksheet1->write(0,  9,  "", $format8);
    $worksheet1->write(0, 10,  "", $format8);

    $worksheet1->write(1,  0, "", $format8);
    $worksheet1->write(1,  1, "Raport per transaksionet ditore/periodike (" . $v_view_dt . ")", $format8);
    $worksheet1->write(1,  2, "", $format8);
    $worksheet1->write(1,  3, "", $format8);
    $worksheet1->write(1,  4, "", $format8);
    $worksheet1->write(1,  5, "", $format8);
    $worksheet1->write(1,  6, "", $format8);
    $worksheet1->write(1,  7, "", $format8);
    $worksheet1->write(1,  8, "", $format8);
    $worksheet1->write(1,  9, "", $format8);
    $worksheet1->write(1, 10, "", $format8);
    $worksheet1->setMerge(1, 1, 1, 9);

    $worksheet1->write(2,  0,  "", $format8);
    $worksheet1->write(2,  1,  "", $format8);
    $worksheet1->write(2,  2,  "", $format8);
    $worksheet1->write(2,  3,  "", $format8);
    $worksheet1->write(2,  4,  "", $format8);
    $worksheet1->write(2,  5,  "", $format8);
    $worksheet1->write(2,  6,  "", $format8);
    $worksheet1->write(2,  7,  "", $format8);
    $worksheet1->write(2,  8,  "", $format8);
    $worksheet1->write(2,  9,  "", $format8);
    $worksheet1->write(2, 10,  "", $format8);

    $worksheet1->setRow(3, 30);
    $worksheet1->write(3,  0,  "", $format8);
    $worksheet1->write(3,  1, "Nr. Fature", $format1);
    $worksheet1->write(3,  2, "Date", $format1);
    $worksheet1->write(3,  3, "Emri / Mbiemri", $format1);
    $worksheet1->write(3,  4, "Blere", $format1);
    $worksheet1->write(3,  5, "Shuma e blere", $format1);
    $worksheet1->write(3,  6, "Shitur", $format1);
    $worksheet1->write(3,  7, "Shuma e shitur", $format1);
    $worksheet1->write(3,  8, "Kursi", $format1);
    $worksheet1->write(3,  9, "Shuma e paguar", $format1);
    $worksheet1->write(3, 10, "", $format8);

    $worksheet1->setColumn(0,  0,  2);
    $worksheet1->setColumn(1,  1, 15);
    $worksheet1->setColumn(2,  2, 18);
    $worksheet1->setColumn(3,  3, 25);
    $worksheet1->setColumn(4,  4, 10);
    $worksheet1->setColumn(5,  5, 15);
    $worksheet1->setColumn(6,  6, 10);
    $worksheet1->setColumn(7,  7, 15);
    $worksheet1->setColumn(8,  8, 10);
    $worksheet1->setColumn(9,  9, 20);
    $worksheet1->setColumn(10, 10,  2);

    $v_rowno = 3;
  }

?>


  <!-- --------------------------------------- -->
  <!--          Aplikacioni xChange            -->
  <!--                                         -->
  <!--  Kontakt:                               -->
  <!--                                         -->
  <!--           GlobalTech.al                 -->
  <!--                                         -->
  <!--        info@globaltech.al               -->
  <!-- --------------------------------------- -->


  <html>

  <head>

    <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System - Raport per transaksionet ditore/periodike</title>

    <link href="rep.css" rel="stylesheet" type="text/css">

  </head>

  <body leftmargin=0 topmargin=0 marginheight="0" marginwidth="0" bgcolor=#ffffff vlink="#0000ff" link="#0000ff">

    <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
      <TBODY>
        <TR>
          <TD bgColor=#ffffff rowSpan=2 vAlign=center width=188>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<IMG src="images/logo.png" width="auto" height="36"><br>
            </p>
          </TD>
          <TD bgColor=#ffffff height=40 vAlign=top><IMG alt="" height=1 src="images/stretch.gif" width=5></TD>
          <TD align=center bgColor=#ffffff height=40 vAlign=middle><span class="titull"><b> <?php echo $_SESSION['CNAME']; ?> - Web Exchange System</b></span></TD>
          <TD align=right bgColor=#ffffff vAlign=bottom>
            <span class="ReportDateUserN">
              Printuar dt. </span><span class="ReportDateUserB"><?php echo strftime('%Y-%m-%d'); ?></span>
            <span class="ReportDateUserN">P&euml;rdoruesi: </span><span class="ReportDateUserB"><?php echo $user_info; ?>
            </span>
          </TD>
        </TR>
      </TBODY>
    </TABLE>
    <TABLE bgColor=#ff0000 border=0 cellPadding=0 cellSpacing=0 width="100%">
      <TBODY>
        <TR>
          <TD bgColor=#ff0000 class="OraColumnHeader">&nbsp; </TD>
        </TR>
      </TBODY>
    </TABLE>
    <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
      <TBODY>
        <TR>
          <TD bgColor=#ff0000 vAlign=top class="OraColumnHeader"><IMG alt="" border=0 height=17 src="images/topcurl.gif" width=30></TD>
          <TD vAlign=top width="100%">
            <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
                <TR>
                  <TD bgColor=#000000 height=1><IMG alt="" border=0 height=1 src="images/stretch.gif" width=1></TD>
                </TR>
                <TR>
                  <TD bgColor=#9a9c9a height=1><IMG alt="" border=0 height=1 src="images/stretch.gif" width=1></TD>
                </TR>
                <TR>
                  <TD bgColor=#b3b4b3 height=1><IMG alt="" border=0 height=1 src="images/stretch.gif" width=1></TD>
                </TR>
              </TBODY>
            </TABLE>
          </TD>
        </TR>
      </TBODY>
    </TABLE>
    <TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
      <TR>
        <TD width="100%">
          <DIV align="center">
            <table class="OraTable">
              <caption><span class="ReportTitle"> Raport per transaksionet ditore/periodike </span></caption>
              <?php
              $query_filiali_info = "SELECT * FROM filiali WHERE id = ?";
              $stmt = mysqli_prepare($MySQL, $query_filiali_info);
              mysqli_stmt_bind_param($stmt, 'i', $v_branch_id);
              mysqli_stmt_execute($stmt);
              $result = mysqli_stmt_get_result($stmt);
              
              while ($row_filiali_info = mysqli_fetch_assoc($result)) {
              ?>
                <caption><span class="ReportSubTitle"> <?php echo strtoupper($row_filiali_info['filiali']); ?> </span></caption>
              <?php
              }
              mysqli_stmt_close($stmt);
              ?>
              <caption><span class="ReportSubTitle"> <?php echo $v_view_dt; ?> </span></caption>
              <thead>
                <tr>
                  <th height="0" colspan="12">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="line">
                        <td height="0">
                          <DIV class=line></DIV>
                        </td>
                      </tr>
                    </table>
                  </th>
                </tr>
                <?php if ((isset($_POST["view"])) && ($_POST["view"] != "excel")) {  ?>
                  <tr>
                    <th class="OraColumnHeader"> Nr. </th>
                    <th class="OraColumnHeader"> Nr. Fature </th>
                    <th class="OraColumnHeader"> Dt. Trans. </th>
                    <th class="OraColumnHeader"> Emri / Mbiemri </th>
                    <th class="OraColumnHeader"> Blere </th>
                    <th class="OraColumnHeader"> Shitur </th>
                    <th class="OraColumnHeader"> Shuma Blere </th>
                    <th class="OraColumnHeader"> Kursi </th>
                    <th class="OraColumnHeader"> Shuma Shitur </th>
                    <th class="OraColumnHeader"> Komisioni </th>
                    <th class="OraColumnHeader"> Shuma e paguar </th>
                    <th class="OraColumnHeader"> Perdoruesi </th>
                  </tr>
                <?php  }  ?>
              </thead>
              <tbody>

                <?php

                set_time_limit(0);

                $v_wheresql = "";
                if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and ek.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
                if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and ek.perdoruesi    = '" . $_SESSION['Username'] . "' ";

                $RepInfo_sql = " select ek.*, ed.*, k.emri, k.mbiemri, m1.monedha as mon1, m2.monedha as mon2
                       from exchange_koke as ek,
                            exchange_detaje as ed,
                            klienti as k,
                            monedha as m1,
                            monedha as m2
                      where ek.chstatus       = 'T'
                        and ek.tipiveprimit   = 'CHN'
                        and ek.id             = ed.id_exchangekoke
                        and ek.id_llogfilial  = " . $v_branch_id . "
                         " . $v_perioddate . "
                         " . $v_wheresql   . "
                        and ek.id_klienti     = k.id
                        and ek.id_monkreditim = m1.id
                        and ed.id_mondebituar = m2.id
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

                  if ((isset($_POST["view"])) && ($_POST["view"] == "excel")) {

                    //------------- write excel information --------------------------------------------------
                    $v_rowno++;
                    $worksheet1->write($v_rowno,        0, "", $format8);
                    $worksheet1->write($v_rowno,        1, $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id'], $format4);
                    $worksheet1->write($v_rowno,        2, substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8), $format4);
                    $worksheet1->write($v_rowno,        3, $row_RepInfo['emri'] . " " . $row_RepInfo['mbiemri'], $format4);

                    $worksheet1->write($v_rowno,        4, $row_RepInfo['mon2'], $format4);
                    $worksheet1->writeNumber($v_rowno,  5, number_format($row_RepInfo['vleftadebituar'], 2, '.', ''), $format5);

                    $worksheet1->write($v_rowno,        6, $row_RepInfo['mon1'], $format4);
                    $worksheet1->writeNumber($v_rowno,  7, number_format($row_RepInfo['vleftakredituar'], 2, '.', ''), $format5);

                    $worksheet1->writeNumber($v_rowno,  8, number_format($v_kursi, 4, '.', ''), $format5);

                    $worksheet1->writeNumber($v_rowno,  9, number_format($row_RepInfo['vleftapaguar'], 2, '.', ''), $format5);

                    $worksheet1->write($v_rowno,       10, "", $format8);
                    //------------- write excel information --------------------------------------------------
                  }

                ?>
                  <?php if ((isset($_POST["view"])) && ($_POST["view"] != "excel")) {  ?>
                    <tr>
                      <td height="0" colspan="12">
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
                      <td align="center" class="OraCellGroup"><?php echo $rowno; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8); ?></td>
                      <td align="center" class="OraCellGroup3"><?php echo $row_RepInfo['emri']; ?> <?php echo $row_RepInfo['mbiemri']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['mon2']; ?></td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['mon1']; ?></td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftadebituar'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($v_kursi, 4, '.', ','); ?>&nbsp;&nbsp;</td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftakredituar'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftakomisionit'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                      <td align="right" class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftapaguar'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                      <td align="center" class="OraCellGroup2"><?php echo $row_RepInfo['perdoruesi']; ?></td>
                    </tr>
                  <?php  }  ?>
                <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                };
                mysqli_free_result($RepInfoRS);
                ?>
                <?php if ((isset($_POST["view"])) && ($_POST["view"] == "excel")) {

                  $v_rowno++;
                  $worksheet1->write($v_rowno,  0,  "", $format8);
                  $worksheet1->write($v_rowno,  1,  "", $format8);
                  $worksheet1->write($v_rowno,  2,  "", $format8);
                  $worksheet1->write($v_rowno,  3,  "", $format8);
                  $worksheet1->write($v_rowno,  4,  "", $format8);
                  $worksheet1->write($v_rowno,  5,  "", $format8);
                  $worksheet1->write($v_rowno,  6,  "", $format8);
                  $worksheet1->write($v_rowno,  7,  "", $format8);
                  $worksheet1->write($v_rowno,  8,  "", $format8);
                  $worksheet1->write($v_rowno,  9,  "", $format8);
                  $worksheet1->write($v_rowno, 10,  "", $format8);
                  //----------------------------------------------------
                  $workbook->close();
                  //----------------------------------------------------
                ?>
                  <tr>
                    <td height="0" colspan="12">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr class="line">
                          <td height="0">
                            <DIV class=line></DIV>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr bgcolor="#A7CCBA">
                    <td align="center" class="OraCellGroup" colspan="12"><b>&nbsp;<a href="<?php echo $v_file; ?>"><?php echo $v_file; ?></a>&nbsp;</b></td>
                  </tr>
                <?php    }    ?>
                <tr>
                  <td height="0" colspan="12">
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
                  <td height="0" colspan="12">
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
                  <td align="center" class="OraCellGroup4" colspan="3">&nbsp;<b>Monedha</b>&nbsp;</td>
                  <td align="right" class="OraCellGroup4" colspan="3">&nbsp;<b>Shuma e hyr&euml;</b>&nbsp;</td>
                  <td align="right" class="OraCellGroup4" colspan="3">&nbsp;<b>Komisioni</b>&nbsp;</td>
                  <td align="right" class="OraCellGroup4" colspan="3">&nbsp;<b>Shuma e dal&euml;</b>&nbsp;</td>
                </tr>
                <tr>
                  <td height="0" colspan="12">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="line">
                        <td height="0">
                          <DIV class=line></DIV>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <?php

                $RepInfo_sql = " select info.mon, sum(info.vlerakredit) as vlerakredit, sum(info.komision) as komision, sum(info.vleradebit) as vleradebit
                       from (
                                    select 0 as vlerakredit, sum(ek.vleftakomisionit) as komision, sum(ek.vleftapaguar) as vleradebit, m1.id, m1.monedha as mon
                                      from exchange_koke as ek,
                                           klienti as k,
                                           monedha as m1
                                     where ek.chstatus       = 'T'
                                       and ek.tipiveprimit   = 'CHN'
                                       and ek.id_llogfilial  = " . $v_branch_id . "
                                       " . $v_perioddate . "
                                       " . $v_wheresql   . "
                                       and ek.id_klienti     = k.id
                                       and ek.id_monkreditim = m1.id
                                  group by m1.id, m1.monedha
                                    union all
                                    select sum(ed.vleftadebituar) as vlerakredit, 0 as komision, 0 as vleradebit, m2.id, m2.monedha as mon
                                      from exchange_koke as ek,
                                           exchange_detaje as ed,
                                           klienti as k,
                                           monedha as m2
                                     where ek.chstatus       = 'T'
                                       and ek.tipiveprimit   = 'CHN'
                                       and ek.id             = ed.id_exchangekoke
                                       and ek.id_llogfilial  = " . $v_branch_id . "
                                       " . $v_perioddate . "
                                       " . $v_wheresql   . "
                                       and ek.id_klienti     = k.id
                                       and ed.id_mondebituar = m2.id
                                  group by m2.id, m2.monedha
                            ) info
                     group by info.mon, info.id
                     order by info.id ";

                $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
                $row_RepInfo = $RepInfoRS->fetch_assoc();

                while ($row_RepInfo) {
                ?>
                  <tr>
                    <td align="center" class="OraCellGroup" colspan="3"> <?php echo $row_RepInfo['mon']; ?> </td>
                    <td align="right" class="OraCellGroup2" colspan="3"><?php echo number_format($row_RepInfo['vlerakredit'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                    <td align="right" class="OraCellGroup2" colspan="3"><?php echo number_format($row_RepInfo['komision'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                    <td align="right" class="OraCellGroup2" colspan="3"><?php echo number_format($row_RepInfo['vleradebit'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="0" colspan="12">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr class="line">
                          <td height="0">
                            <DIV class=line></DIV>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                };
                mysqli_free_result($RepInfoRS);
                ?>
                <tr>
                  <td height="0" colspan="12">
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
                  <td align="left" class="OraCellGroup4" colspan="12">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Totali i transaksioneve:</b>&nbsp;&nbsp;<b>[ <?php echo $rowno; ?> ]</b>&nbsp;</td>
                </tr>
                <tr>
                  <td height="0" colspan="12">
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
                  <td height="5" colspan="12"></td>
                </tr>
            </table>
          </DIV>
        </TD>
      </TR>
    </TABLE>

    <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
      <TBODY>
        <TR>
          <TD bgColor=#000000 colSpan=2><IMG alt=" " border=0 height=1 src="images/stretch.gif" width=1></TD>
        </TR>
        <TR>
          <TD bgColor=#ff0000 colSpan=2 class="OraColumnHeader"><IMG alt=" " border=0 height=4 src="images/stretch.gif" width=1></TD>
        </TR>
        <TR>
          <TD bgColor=#000000 colSpan=2><IMG alt=" " border=0 height=1 src="images/stretch.gif" width=1></TD>
        </TR>
        <TR>
          <TD bgColor=#ffffff>&nbsp;<span class="ReportDateUserB"> </span></TD>
          <TD align=right bgColor=#ffffff>&nbsp;</TD>
        </TR>
      </TBODY>
    </TABLE>

  </body>

  </html>
<?php  }  ?>


<script>
  // Disable right-click context menu
  document.addEventListener('contextmenu', event => event.preventDefault());
</script>

<script>
  // Disable keyCode
  document.addEventListener('keydown', e => {
    const blockedKeys = [112, 114, 116, 117, 118, 121, 122, 123];
    
    if (blockedKeys.includes(e.keyCode) || 
        e.ctrlKey || 
        e.shiftKey || 
        e.altKey || 
        (e.ctrlKey && e.shiftKey) || 
        (e.ctrlKey && e.shiftKey && e.altKey)) {
      e.preventDefault();
    }
  });
</script>