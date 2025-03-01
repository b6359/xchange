<?php
include 'header.php';
  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  // mysql_select_db($database_MySQL, $MySQL);

  $id_llogfilial = "";
  $v_whereidfil = "";
  $v_wheresql    = "";
  $v_whereidcli = "";
  if ((isset($_POST['id_llogfilial']))
    && ($_POST['id_llogfilial'] != "")
    && ($_POST['id_llogfilial'] != "all")
  ) {
    $id_llogfilial = $_POST['id_llogfilial'];
    $v_wheresql    = "WHERE id_llogfilial = " . $_POST['id_llogfilial'];
    $v_whereidfil  = "  and id_llogfilial = " . $_POST['id_llogfilial'];
    $v_whereidcli  = "  and id_klienti    = " . $_POST['id_llogfilial'];
  }

  if ((isset($_SESSION['uid'])) && ($_SESSION['Usertype'] == 1)) {

    if ((isset($_POST["act"])) && ($_POST["act"] == "open")) {

      $updateSQL = "UPDATE opencloseday SET opstatus = 'O', opdate = '" . strftime('%Y-%m-%d') . "', opuser = " . $_SESSION['uid'];
      $Result1   = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

      $updateGoTo = "exchange_opclbal.php";
      header(sprintf("Location: %s", $updateGoTo));
    }
    if ((isset($_POST["act"])) && ($_POST["act"] == "close")) {

      set_time_limit(0);

      $updateSQL = "UPDATE opencloseday SET opstatus = 'C', opdate = '" . strftime('%Y-%m-%d') . "', opuser = " . $_SESSION['uid'];
      $Result1   = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

      // ---------------------------------------------------------------------------------
      $filiali_sql  = "select * from filiali order by id";
      $filiali_info = mysqli_query($MySQL, $filiali_sql) or die(mysqli_error($MySQL));
      $row_filiali_info = mysqli_fetch_assoc($filiali_info);

      while ($row_filiali_info) {

        // ---------------------------------------------------------------------------------
        $v_whereidfil2  = "  and id_llogfilial = " . $row_filiali_info['id'];
        $v_whereidcli2  = "  and id_klienti    = " . $row_filiali_info['id'];

        $query_gjendje_info = " SELECT sum(LEK) LEK,
                                       sum(USD) USD,
                                       sum(EUR) EUR,
                                       sum(CHF) CHF,
                                       sum(GBP) GBP,
                                       sum(DKK) DKK,
                                       sum(NOK) NOK,
                                       sum(SEK) SEK,
                                       sum(CAD) CAD,
                                       sum(AUD) AUD,
                                       sum(MKD) MKD,
                                       sum(LEKRATE) / count(LEKRATE) LEKRATE,
                                       sum(USDRATE) / count(USDRATE) USDRATE,
                                       sum(EURRATE) / count(EURRATE) EURRATE,
                                       sum(CHFRATE) / count(CHFRATE) CHFRATE,
                                       sum(GBPRATE) / count(GBPRATE) GBPRATE,
                                       sum(DKKRATE) / count(DKKRATE) DKKRATE,
                                       sum(NOKRATE) / count(NOKRATE) NOKRATE,
                                       sum(SEKRATE) / count(SEKRATE) SEKRATE,
                                       sum(CADRATE) / count(CADRATE) CADRATE,
                                       sum(AUDRATE) / count(AUDRATE) AUDRATE,
                                       sum(MKDRATE) / count(MKDRATE) MKDRATE,
                                       min(id_chn) id_chn,
                                       min(id_hd)  id_hd ,
                                       min(id_opb) id_opb
                                  FROM systembalance
                                 WHERE id_llogfilial = " . $row_filiali_info['id'] . " ";
        $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
        $row_gjendje_info = mysqli_fetch_assoc($gjendje_info);

        while ($row_gjendje_info) {

          $sql_info   = "select * from monedha order by id";
          $h_menu     = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
          $row_h_menu = mysqli_fetch_assoc($h_menu);

          while ($row_h_menu) {

            $sql_hyrjedalje  = " SELECT sum(info.hyre) hyre,
                                            round(( sum( info.hyre * info.hyrekursi ) / SUM( info.hyre ) ),2) hyrekursi,
                                            sum(info.dale) dale,
                                            round(( sum( info.dale * info.dalekursi ) / SUM( info.dale ) ),2) dalekursi
                                       FROM (
                                               SELECT sum(vleftakredituar) hyre,
                                                      round(( sum( vleftakredituar * rate_value ) / SUM( vleftakredituar ) ),2)  hyrekursi,
                                                      0 dale, 0 dalekursi
                                                 FROM openbalance
                                                WHERE id         > " . $row_gjendje_info['id_opb'] . "
                                                  and monedha_id = " . $row_h_menu['id'] . "
                                                  and chstatus   = 'T'
                                                  " . $v_whereidfil2 . "
                                              UNION ALL
                                               SELECT SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end) hyre,
                                                      round( ( SUM( case when drcr = 'Debitim'  then vleftapaguar * rate_value else 0 end) / case when IFNULL(SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end ), 0) = 0 then 1 else SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end ) end ), 2) hyrekursi,
                                                      SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end) dale,
                                                      round( ( SUM( case when drcr = 'Kreditim' then vleftapaguar * rate_value else 0 end) / case when IFNULL(SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end ), 0) = 0 then 1 else SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end ) end ), 2) dalekursi
                                                 FROM hyrjedalje
                                                WHERE unique_id  > " . $row_gjendje_info['id_hd'] . "
                                                  AND id_monedhe = " . $row_h_menu['id'] . "
                                                  and chstatus   = 'T'
                                                  " . $v_whereidfil2 . "
                                              UNION ALL
                                               SELECT 0 hyre, 0 hyrekursi,
                                                      SUM(vleftapaguar) dale,
                                                      round(( sum( vleftapaguar * ed.kursi1 ) / SUM( vleftapaguar ) ),2) dalekursi
                                                 FROM exchange_koke, exchange_detaje ed
                                                WHERE unique_id      > " . $row_gjendje_info['id_chn'] . "
                                                  AND exchange_koke.id  = ed.id_exchangekoke
                                                  AND id_monkreditim = " . $row_h_menu['id'] . "
                                                  AND chstatus       = 'T'
                                                  " . $v_whereidfil2 . "
                                              UNION ALL
                                               SELECT SUM( ed.vleftadebituar ) hyre,
                                                      round(( sum( ed.vleftadebituar * ed.kursi ) / SUM( ed.vleftadebituar ) ),2) hyrekursi,
                                                      0 dale, 0 dalekursi
                                                 FROM exchange_koke, exchange_detaje ed
                                                WHERE unique_id         > " . $row_gjendje_info['id_chn'] . "
                                                  AND exchange_koke.id  = ed.id_exchangekoke
                                                  AND ed.id_mondebituar = " . $row_h_menu['id'] . "
                                                  AND tipiveprimit      = 'CHN'
                                                  AND chstatus          = 'T'
                                                  " . $v_whereidfil2 . "
                                              UNION ALL
                                               SELECT SUM( ed.vleftadebituar ) hyre,
                                                      round(( sum( ed.vleftadebituar * ed.kursi ) / SUM( ed.vleftadebituar ) ),2) hyrekursi,
                                                      0 dale, 0 dalekursi
                                                 FROM exchange_koke, exchange_detaje ed
                                                WHERE unique_id         > " . $row_gjendje_info['id_chn'] . "
                                                  AND exchange_koke.id  = ed.id_exchangekoke
                                                  AND ed.id_mondebituar = " . $row_h_menu['id'] . "
                                                  AND tipiveprimit      = 'TRN'
                                                  AND chstatus          = 'T'
                                                  " . $v_whereidcli2 . "
                                             ) info ";
            $hyrjedalje_info = mysqli_query($MySQL, $sql_hyrjedalje) or die(mysqli_error($MySQL));
            $row_hyrjedalje  = mysqli_fetch_assoc($hyrjedalje_info);

            while ($row_hyrjedalje) {

              if (($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']) != 0) {

                $updateSYSBalSQL = "UPDATE systembalance SET
                                                                " . $row_h_menu['monedha'] . "     = " . ($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']) . ",
                                                                " . $row_h_menu['monedha'] . "RATE = round(" . (($row_gjendje_info[$row_h_menu['monedha']] * $row_gjendje_info[$row_h_menu['monedha'] . 'RATE'] + $row_hyrjedalje['hyre'] * $row_hyrjedalje['hyrekursi'] - $row_hyrjedalje['dale'] * $row_hyrjedalje['dalekursi']) / ($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale'])) . ", 2)
                                                         WHERE  id_llogfilial = " . $row_filiali_info['id'];
              } else {

                $updateSYSBalSQL = "UPDATE systembalance SET
                                                                " . $row_h_menu['monedha'] . "     = " . ($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']) . ",
                                                                " . $row_h_menu['monedha'] . "RATE = 1
                                                         WHERE  id_llogfilial = " . $row_filiali_info['id'];
              }
              $ResultSYSBal = mysqli_query($MySQL, $updateSYSBalSQL) or die(mysqli_error($MySQL));

              $row_hyrjedalje = $hyrjedalje_info->fetch_assoc();
            }
            mysqli_free_result($hyrjedalje_info);

            $row_h_menu = $h_menu->fetch_assoc();
          }
          mysqli_free_result($h_menu);

          $row_gjendje_info = mysqli_fetch_assoc($gjendje_info);
        }
        mysqli_free_result($gjendje_info);
        // ---------------------------------------------------------------------------------

        $row_filiali_info = mysqli_fetch_assoc($filiali_info);
      }
      mysqli_free_result($filiali_info);
      // ---------------------------------------------------------------------------------


      $id_chn = 0;
      $id_hd = 0;
      $id_opb = 0;

      $sql_chn_info = "select max(unique_id) as nr from exchange_koke ";
      $chn_info     = mysqli_query($MySQL, $sql_chn_info) or die(mysqli_error($MySQL));
      $row_chn_info = mysqli_fetch_assoc($chn_info);
      if (($row_chn_info) && ($row_chn_info['nr'] != null)) {
        $id_chn = $row_chn_info['nr'];
      }
      mysqli_free_result($chn_info);

      $sql_hd_info = "select max(unique_id) as nr from hyrjedalje ";
      $hd_info     = mysqli_query($MySQL, $sql_hd_info) or die(mysqli_error($MySQL));
      $row_hd_info = mysqli_fetch_assoc($hd_info);
      if (($row_hd_info) && ($row_hd_info['nr'] != null)) {
        $id_hd = $row_hd_info['nr'];
      }
      mysqli_free_result($hd_info);

      $sql_opb_info = "select max(id) as nr from openbalance ";
      $opb_info     = mysqli_query($MySQL, $sql_opb_info) or die(mysqli_error($MySQL));
      $row_opb_info = mysqli_fetch_assoc($opb_info);
      if (($row_opb_info) && ($row_opb_info['nr'] != null)) {
        $id_opb = $row_opb_info['nr'];
      }
      mysqli_free_result($opb_info);

      $UPDIdSQL  = "UPDATE systembalance SET perdoruesi = '" . $user_info . "',
                                         datarregjistrimit = '" . strftime('%Y-%m-%d %H:%M:%S') . "',
                                         id_chn = " . $id_chn . ",
                                         id_hd  = " . $id_hd . ",
                                         id_opb = " . $id_opb . " ";
      $ResUPDId  = mysqli_query($MySQL, $UPDIdSQL) or die(mysqli_error($MySQL));

      $InsSysBalSQL = "INSERT INTO systembalance_hist (date_trans, perdoruesi, id_llogfilial, id_chn, id_hd, id_opb, LEK, USD, EUR, CHF, GBP, DKK, NOK, SEK, CAD, AUD, MKD, LEKRATE, USDRATE, EURRATE, CHFRATE, GBPRATE, DKKRATE, NOKRATE, SEKRATE, CADRATE, AUDRATE, MKDRATE, datarregjistrimit)
                                            select date_trans, perdoruesi, id_llogfilial, id_chn, id_hd, id_opb, LEK, USD, EUR, CHF, GBP, DKK, NOK, SEK, CAD, AUD, MKD, LEKRATE, USDRATE, EURRATE, CHFRATE, GBPRATE, DKKRATE, NOKRATE, SEKRATE, CADRATE, AUDRATE, MKDRATE, datarregjistrimit
                                              from systembalance ";
      $ResInsSysBal = mysqli_query($MySQL, $InsSysBalSQL) or die(mysqli_error($MySQL));

      $updateGoTo = "exchange_opclbal.php";
      header(sprintf("Location: %s", $updateGoTo));
    }
  }

  if ((isset($_POST["view"])) && ($_POST["view"] == "excel")) {

    require_once 'Spreadsheet/Excel/Writer.php';

    $v_file = "rep/BalancaPerMonedhe_" . strftime('%Y%m%d%H%M%S') . ".xls";
    $workbook = new Spreadsheet_Excel_Writer($v_file);

    $format1    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'center',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'aqua'
    ));
    $format1->setTextWrap();

    $format2    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'aqua',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'gray'
    ));
    $format2->setTextWrap();

    $format3    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'aqua',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'gray'
    ));
    $format3->setTextWrap();

    $format4    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'white'
    ));
    $format4->setTextWrap();

    $format5    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'white'
    ));
    $format5->setTextWrap();


    $format6    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'yellow'
    ));
    $format6->setTextWrap();

    $format7    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'yellow'
    ));
    $format7->setTextWrap();

    $format8    = &$workbook->addFormat(array(
      'Size'       => 11,
      'Align'      => 'left',
      'VAlign'     => 'vcenter',
      'Color'      => 'black',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 0,
      'FgColor'    => 'white'
    ));
    $format8->setTextWrap();

    $format9    = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'white',
      'FontFamily' => 'Calibri',
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'red'
    ));
    $format9->setTextWrap();

    $format10   = &$workbook->addFormat(array(
      'Size'       => 10,
      'Align'      => 'right',
      'VAlign'     => 'vcenter',
      'Color'      => 'white',
      'FontFamily' => 'Calibri',
      'Bold'       => 1,
      'Pattern'    => 1,
      'border'     => 1,
      'FgColor'    => 'red'
    ));
    $format10->setTextWrap();

    //----------------------------------------------------------------------------------------------------
    set_time_limit(0);

    $worksheet1 = &$workbook->addWorksheet('Balanca');

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
    $worksheet1->write(1,  1, "Balanca sipas monedhave ( date " . strftime('%d.%m.%Y %H:%M:%S') . ")", $format8);
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
    $worksheet1->write(3,  1, "Monedha", $format1);
    $worksheet1->write(3,  2, "Hapja ditore", $format1);
    //$worksheet1->write(3,  3, "Kursi i hapjes", $format1);
    $worksheet1->write(3,  4, "Hyrje", $format1);
    //$worksheet1->write(3,  5, "Kursi i hyrjes", $format1);
    $worksheet1->write(3,  6, "Dalje", $format1);
    //$worksheet1->write(3,  7, "Kursi i daljes", $format1);
    $worksheet1->write(3,  8, "Gjendja ne fund", $format1);
    //$worksheet1->write(3,  9, "Kursi i gjendjes", $format1);
    $worksheet1->write(3, 10, "", $format8);

    $worksheet1->setColumn(0,  0,  2);
    $worksheet1->setColumn(1,  1, 15);
    $worksheet1->setColumn(2,  2, 15);
    $worksheet1->setColumn(3,  3, 2);
    $worksheet1->setColumn(4,  4, 20);
    $worksheet1->setColumn(5,  5, 2);
    $worksheet1->setColumn(6,  6, 20);
    $worksheet1->setColumn(7,  7, 2);
    $worksheet1->setColumn(8,  8, 25);
    $worksheet1->setColumn(9,  9, 2);
    $worksheet1->setColumn(10, 10,  2);

    $v_rowno = 3;
  }

?>

<div class="page-wrapper">
  <div class="container-fluid">
      <div class="card p-3">
        <div class="card-body d-flex align-items-center justify-content-between">
          <h4 class="card-title">
            <b>Hapje / Mbyllje e dit&euml;s</b>
          </h4>
          <button class="btn btn-outline-primary" onclick="printForm()">
            <i class="fas fa-print cursor-pointer"></i> Printo
          </button>
        </div>
        <form enctype="multipart/form-data" ACTION="exchange_opclbal.php" METHOD="POST" name="formmenu" id="formmenu">
          <input name="act" type="hidden" value="n/e">
          <input name="view" type="hidden" value="n/e">
          <div class="row mb-3">
            <div class="form-group col-md-3">
                <label for="id_llogfilial">Filiali:</label>
                <select name="id_llogfilial" class="form-select" id="id_llogfilial">
                  <option value="all" selected>T&euml; gjitha</option>
                  <?php

                  $filiali_sql  = "select * from filiali order by id asc";
                  $filiali_info = mysqli_query($MySQL, $filiali_sql) or die(mysqli_error($MySQL));
                  $row_filiali_info = mysqli_fetch_assoc($filiali_info);

                  while ($row_filiali_info) {
                  ?>
                    <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $id_llogfilial) {
                                                                              echo "selected";
                                                                            } ?>><?php echo $row_filiali_info['filiali']; ?></option>
                  <?php
                    $row_filiali_info = mysqli_fetch_assoc($filiali_info);
                  }
                  mysqli_free_result($filiali_info);
                  ?>
                </select>
            </div>
            <div class="form-group col-md-9 mt-4">
                <input name="insupd" class="btn btn-outline-primary" type="button" value=" Shfaq informacionin " onClick="JavaScript: document.formmenu.submit(); ">
                <input name="showfl" class="btn btn-outline-primary"" type="button" value=" Shfaq excel " onClick="JavaScript: document.formmenu.view.value = 'excel'; document.formmenu.submit(); ">
            
                    <?php

                    if ((isset($_SESSION['uid'])) && ($_SESSION['Usertype'] == 1)) {

                      $sql_id_info = "select opstatus from opencloseday ";
                      $id_info     = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
                      $row_id_info = mysqli_fetch_assoc($id_info);
                      $opstatus    = $row_id_info['opstatus'];
                      if ($opstatus == "C") {
                    ?>
                            <input name="openbal" class="btn btn-outline-primary" type="button" value=" Hapje dite " onClick="JavaScript: open_day(); ">
                      <?php  } else {  ?>
                            <input name="closebal" class="btn btn-outline-primary" type="button" value=" Mbyllje dite " onClick="JavaScript: close_day(); ">
                    <?php  }
                    }
                    ?>
            </div>
          </div>
          <div class="responsive-table">
              <table class="table table-bordered">
                
                <tr>
                  <td colspan="9" class="text-center">
                    Balanca sipas monedhave
                  </td>
                </tr>
                <tr bgcolor="#ffffff" height="18px">
                  <td><b>Monedha</b></td>
                  <td align="right"><b>Hapje balance</b></td>
                  <td align="right"><b>Hyrje</b></td>
                  <td align="right"><b>Dalje</b></td>
                  <td align="right"><b>Gjendja</b></td>
                </tr>
                <?php

                set_time_limit(0);

                $query_gjendje_info = " SELECT sum(LEK) LEK,
                                  sum(EUR) EUR,
                                  sum(USD) USD,
                                  sum(GBP) GBP,
                                  sum(CHF) CHF,
                                  sum(DKK) DKK,
                                  sum(NOK) NOK,
                                  sum(SEK) SEK,
                                  sum(CAD) CAD,
                                  sum(AUD) AUD,
                                  sum(MKD) MKD,
                                  sum(LEKRATE) / count(LEKRATE) LEKRATE,
                                  sum(USDRATE) / count(USDRATE) USDRATE,
                                  sum(EURRATE) / count(EURRATE) EURRATE,
                                  sum(CHFRATE) / count(CHFRATE) CHFRATE,
                                  sum(GBPRATE) / count(GBPRATE) GBPRATE,
                                  sum(DKKRATE) / count(DKKRATE) DKKRATE,
                                  sum(NOKRATE) / count(NOKRATE) NOKRATE,
                                  sum(SEKRATE) / count(SEKRATE) SEKRATE,
                                  sum(CADRATE) / count(CADRATE) CADRATE,
                                  sum(AUDRATE) / count(AUDRATE) AUDRATE,
                                  sum(MKDRATE) / count(MKDRATE) MKDRATE,
                              min(id_chn) id_chn,
                              min(id_hd)  id_hd ,
                              min(id_opb) id_opb
                        FROM systembalance
                          " . $v_wheresql . " ";
                $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
                $row_gjendje_info = mysqli_fetch_assoc($gjendje_info);

                $v_hyre_lek = 0;
                $v_hyrje_lek = 0;
                $v_dalje_lek = 0;
                $v_dale_lek = 0;

                while ($row_gjendje_info) {
                ?>

                  <?php

                  $sql_info   = "select * from monedha order by id";
                  $h_menu     = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
                  $row_h_menu = mysqli_fetch_assoc($h_menu);
                  $rownum     = 0;

                  while ($row_h_menu) {

                    if ($rownum == 1) {
                      $v_bg = "DBDBDB";
                      $rownum = 0;
                    } else {
                      $v_bg = "E9DFFD";
                      $rownum++;
                    }

                    $sql_hyrjedalje  = " SELECT sum(info.hyre) hyre,
                          round(( sum( info.hyre * info.hyrekursi ) / SUM( info.hyre ) ),2) hyrekursi,
                          sum(info.dale) dale,
                          round(( sum( info.dale * info.dalekursi ) / SUM( info.dale ) ),2) dalekursi
                      FROM (
                              SELECT sum(vleftakredituar) hyre,
                                    round(( sum( vleftakredituar * rate_value ) / SUM( vleftakredituar ) ),2)  hyrekursi,
                                    0 dale, 0 dalekursi
                                FROM openbalance
                              WHERE id         > " . $row_gjendje_info['id_opb'] . "
                                and monedha_id = " . $row_h_menu['id'] . "
                                and chstatus   = 'T'
                                " . $v_whereidfil . "
                            UNION ALL
                              SELECT SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end) hyre,
                                    round( ( SUM( case when drcr = 'Debitim'  then vleftapaguar * rate_value else 0 end) / case when IFNULL(SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end ), 0) = 0 then 1 else SUM( case when drcr = 'Debitim'  then vleftapaguar else 0 end ) end ), 2) hyrekursi,
                                    SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end) dale,
                                    round( ( SUM( case when drcr = 'Kreditim' then vleftapaguar * rate_value else 0 end) / case when IFNULL(SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end ), 0) = 0 then 1 else SUM( case when drcr = 'Kreditim' then vleftapaguar else 0 end ) end ), 2) dalekursi
                                FROM hyrjedalje
                              WHERE unique_id  > " . $row_gjendje_info['id_hd'] . "
                                AND id_monedhe = " . $row_h_menu['id'] . "
                                and chstatus   = 'T'
                                " . $v_whereidfil . "
                            UNION ALL
                              SELECT 0 hyre, 0 hyrekursi,
                                    SUM(vleftapaguar) dale,
                                    round(( sum( vleftapaguar * ed.kursi1 ) / SUM( vleftapaguar ) ),2) dalekursi
                                FROM exchange_koke, exchange_detaje ed
                              WHERE unique_id      > " . $row_gjendje_info['id_chn'] . "
                                AND exchange_koke.id  = ed.id_exchangekoke
                                AND id_monkreditim = " . $row_h_menu['id'] . "
                                AND chstatus       = 'T'
                                " . $v_whereidfil . "
                            UNION ALL
                              SELECT SUM( ed.vleftadebituar ) hyre,
                                    round(( sum( ed.vleftadebituar * ed.kursi ) / SUM( ed.vleftadebituar ) ),2) hyrekursi,
                                    0 dale, 0 dalekursi
                                FROM exchange_koke, exchange_detaje ed
                              WHERE unique_id         > " . $row_gjendje_info['id_chn'] . "
                                AND exchange_koke.id  = ed.id_exchangekoke
                                AND ed.id_mondebituar = " . $row_h_menu['id'] . "
                                AND tipiveprimit      = 'CHN'
                                AND chstatus          = 'T'
                                " . $v_whereidfil . "
                            UNION ALL
                              SELECT SUM( ed.vleftadebituar ) hyre,
                                    round(( sum( ed.vleftadebituar * ed.kursi ) / SUM( ed.vleftadebituar ) ),2) hyrekursi,
                                    0 dale, 0 dalekursi
                                FROM exchange_koke, exchange_detaje ed
                              WHERE unique_id         > " . $row_gjendje_info['id_chn'] . "
                                AND exchange_koke.id  = ed.id_exchangekoke
                                AND ed.id_mondebituar = " . $row_h_menu['id'] . "
                                AND tipiveprimit      = 'TRN'
                                AND chstatus          = 'T'
                                " . $v_whereidcli . "
                            ) info ";
                    $hyrjedalje_info = mysqli_query($MySQL, $sql_hyrjedalje) or die(mysqli_error($MySQL));
                    $row_hyrjedalje  = mysqli_fetch_assoc($hyrjedalje_info);

                    //  if ($row_h_menu['id'] == "2") echo $sql_hyrjedalje;

                    while ($row_hyrjedalje) {

                      $v_hyre_lek  += $row_gjendje_info[$row_h_menu['monedha']] * $row_gjendje_info[$row_h_menu['monedha'] . 'RATE'];
                      $v_hyrje_lek += $row_hyrjedalje['hyre'] * $row_hyrjedalje['hyrekursi'];
                      $v_dalje_lek += $row_hyrjedalje['dale'] * $row_hyrjedalje['dalekursi'];
                      $v_dale_lek  += ((($row_gjendje_info[$row_h_menu['monedha']] * $row_gjendje_info[$row_h_menu['monedha'] . 'RATE']) + ($row_hyrjedalje['hyre'] * $row_hyrjedalje['hyrekursi'])) - ($row_hyrjedalje['dale'] * $row_hyrjedalje['dalekursi']));

                      if ((isset($_POST["view"])) && ($_POST["view"] == "excel")) {

                        //------------- write excel information --------------------------------------------------
                        $v_rowno++;
                        $worksheet1->write($v_rowno,        0, "", $format8);
                        $worksheet1->write($v_rowno,        1, $row_h_menu['monedha'], $format4);
                        $worksheet1->writeNumber($v_rowno,  2, number_format($row_gjendje_info[$row_h_menu['monedha']], 2, '.', ''), $format5);
                        //$worksheet1->writeNumber($v_rowno,  3, number_format( $row_gjendje_info[$row_h_menu['monedha'].'RATE'], 2, '.', ''), $format5);
                        $worksheet1->writeNumber($v_rowno,  4, number_format($row_hyrjedalje['hyre'], 2, '.', ''), $format5);
                        //$worksheet1->writeNumber($v_rowno,  5, number_format( $row_hyrjedalje['hyrekursi'], 2, '.', ''), $format5);
                        $worksheet1->writeNumber($v_rowno,  6, number_format($row_hyrjedalje['dale'], 2, '.', ''), $format5);
                        //$worksheet1->writeNumber($v_rowno,  7, number_format( $row_hyrjedalje['dalekursi'], 2, '.', ''), $format5);
                        $worksheet1->writeNumber($v_rowno,  8, number_format(($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']), 2, '.', ''), $format5);

                        if (($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']) != 0) {
                          //    $worksheet1->writeNumber($v_rowno,  9, number_format( ( ( $row_gjendje_info[$row_h_menu['monedha']] * $row_gjendje_info[$row_h_menu['monedha'].'RATE'] + $row_hyrjedalje['hyre'] * $row_hyrjedalje['hyrekursi'] - $row_hyrjedalje['dale'] * $row_hyrjedalje['dalekursi']) / ($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']) ), 2, '.', ''), $format5);
                        } else {
                          //    $worksheet1->writeNumber($v_rowno,  9, "0.00", $format5);
                        }

                        $worksheet1->write($v_rowno,       10, "", $format8);
                        //------------- write excel information --------------------------------------------------
                      }

                  ?>
                      <tr bgcolor="#<?php echo $v_bg; ?>">
                        <td><?php echo $row_h_menu['monedha']; ?></td>
                        <td align="right"><b><?php echo number_format($row_gjendje_info[$row_h_menu['monedha']], 2, '.', ','); ?></b></td>
                        <td align="right"><?php echo number_format($row_hyrjedalje['hyre'], 2, '.', ','); ?></td>
                        <td align="right"><?php echo number_format($row_hyrjedalje['dale'], 2, '.', ','); ?></td>
                        <td align="right"><b><?php echo number_format(($row_gjendje_info[$row_h_menu['monedha']] + $row_hyrjedalje['hyre'] - $row_hyrjedalje['dale']), 2, '.', ','); ?></b></td>
                      </tr>
                    <?php $row_hyrjedalje = $hyrjedalje_info->fetch_assoc();
                    }
                    mysqli_free_result($hyrjedalje_info);
                    ?>

                  <?php $row_h_menu = $h_menu->fetch_assoc();
                  };
                  mysqli_free_result($h_menu);
                  ?>

                <?php $row_gjendje_info = $gjendje_info->fetch_assoc();
                }
                mysqli_free_result($gjendje_info);
                // ---------------------------------------------------------------------------------
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
                <?php    }    ?>
              </table>
          </div>
        </form>
        <?php if (isset($v_file) && $v_file != "") {  ?>
        <br /><br />
        <b>    <a href="<?php echo $v_file; ?>"><?php echo substr($v_file, 4, 100); ?></a></b>
        <br /><br />
      <?php  }  ?>
    </div>
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