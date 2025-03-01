<?php require_once('ConMySQL.php'); ?>
<?php

set_time_limit(0);

// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//   Raporti Kembimi ne Excel
// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// Periudha e Veprimit
$v_dt1  = strftime('%d.%m.%Y');
$v_dt2  = strftime('%d.%m.%Y');
$startdt = substr($v_dt1, 0, 2) . "/" . substr($v_dt1, 3, 2) . "/" . substr($v_dt1, 6, 4);
$startrp = $v_dt1;

$enddt   = substr($v_dt2, 0, 2) . "/" . substr($v_dt2, 3, 2) . "/" . substr($v_dt2, 6, 4);
$endrp   = $v_dt2;
// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

$v_file = "rep/LlogaritjeFitimi_" . date("Y") . date("m") . date("d") . ".xlsx";

require_once   'Classes/PHPExcel.php';
$objPHPexcel   = PHPExcel_IOFactory::load('doc/Kembimi.xlsx');

// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// BEGIN CALCULATION

mysqli_select_db($MySQL, $database_MySQL);
$RepInfoM_sql = " select monedha
                          from monedha
                         where monedha <> 'LEK'
                      order by id ";
$RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
$row_RepInfoM = $RepInfoMRS->fetch_assoc();

while ($row_RepInfoM) {

  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  $objPHPexcel->setActiveSheetIndexByName('(' . $row_RepInfoM['monedha'] . ')');
  $objWorksheet1 = $objPHPexcel->getActiveSheet();
  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  // Calculate open balance
  $v_omon_b  = 0;
  $v_omon_s  = 0;
  $v_omon_rb = 0;
  $v_omon_rs = 0;

  $v_perioddate = " and ek.date_trans < '" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . "' ";

  // Blerje
  mysqli_select_db($MySQL, $database_MySQL);
  $RepInfo_sql = " select (sum(ek.vleftapaguar) / sum(ed.vleftadebituar)) kursi, sum(ed.vleftadebituar) vleftadebituar
                               from exchange_koke as ek,
                                    exchange_detaje as ed,
                                    klienti as k,
                                    monedha as m1,
                                    monedha as m2
                              where ek.chstatus       = 'T'
                                and ek.tipiveprimit   = 'CHN'
                                and ek.id             = ed.id_exchangekoke
                                and ek.id_klienti     = k.id
                                and ek.id_monkreditim = m1.id
                                and ed.id_mondebituar = m2.id
                                and m1.monedha        = 'LEK'
                                and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                " . $v_perioddate . "
                           group by m1.monedha, m2.monedha ";

  $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
  $row_RepInfo = $RepInfoRS->fetch_assoc();

  while ($row_RepInfo) {

    $v_omon_b  = $row_RepInfo['vleftadebituar'];
    $v_omon_rb = $row_RepInfo['kursi'];

    $row_RepInfo = $RepInfoRS->fetch_assoc();
  };
  mysqli_free_result($RepInfoRS);

  // Shitje
  mysqli_select_db($MySQL, $database_MySQL);
  $RepInfo_sql = " select sum(ek.vleftapaguar) vleftapaguar, (sum(ed.vleftadebituar) / sum(ek.vleftapaguar)) kursi
                               from exchange_koke as ek,
                                    exchange_detaje as ed,
                                    klienti as k,
                                    monedha as m1,
                                    monedha as m2
                              where ek.chstatus       = 'T'
                                and ek.tipiveprimit   = 'CHN'
                                and ek.id             = ed.id_exchangekoke
                                and ek.id_klienti     = k.id
                                and ek.id_monkreditim = m1.id
                                and ed.id_mondebituar = m2.id
                                and m2.monedha        = 'LEK'
                                and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                " . $v_perioddate . "
                           group by m1.monedha, m2.monedha ";

  $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
  $row_RepInfo = $RepInfoRS->fetch_assoc();

  while ($row_RepInfo) {

    $v_omon_s  = $row_RepInfo['vleftapaguar'];
    $v_omon_rs = $row_RepInfo['kursi'];

    $row_RepInfo = $RepInfoRS->fetch_assoc();
  };
  mysqli_free_result($RepInfoRS);

  $objWorksheet1->getCellByColumnAndRow(9, 7)->setValue(($v_omon_b - $v_omon_s));
  $objWorksheet1->getCellByColumnAndRow(10, 7)->setValue(($v_omon_rb));

  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  // Begin Calculate period

  $startTime = strtotime("" . substr($v_dt1, 6, 4) . "-" . substr($v_dt1, 3, 2) . "-" . substr($v_dt1, 0, 2) . " 12:00");
  $endTime   = strtotime("" . substr($v_dt2, 6, 4) . "-" . substr($v_dt2, 3, 2) . "-" . substr($v_dt2, 0, 2) . " 12:00");
  $v_rowvl   = 7;
  $v_rowid   = 0;
  for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {

    $thisDate     = date('Y-m-d', $i);
    $v_perioddate = " and ek.date_trans = '" . $thisDate . "' ";

    $v_mon_b  = 0;
    $v_mon_s  = 0;
    $v_mon_rb = 0;
    $v_mon_rs = 0;

    // Blerje
    mysqli_select_db($MySQL, $database_MySQL);
    $RepInfo_sql = " select (sum(ek.vleftapaguar) / sum(ed.vleftadebituar)) kursi, sum(ed.vleftadebituar) vleftadebituar
                                 from exchange_koke as ek,
                                      exchange_detaje as ed,
                                      klienti as k,
                                      monedha as m1,
                                      monedha as m2
                                where ek.chstatus       = 'T'
                                  and ek.tipiveprimit   = 'CHN'
                                  and ek.id             = ed.id_exchangekoke
                                  and ek.id_klienti     = k.id
                                  and ek.id_monkreditim = m1.id
                                  and ed.id_mondebituar = m2.id
                                  and m1.monedha        = 'LEK'
                                  and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                  " . $v_perioddate . "
                             group by m1.monedha, m2.monedha ";

    $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
    $row_RepInfo = $RepInfoRS->fetch_assoc();

    while ($row_RepInfo) {

      $v_mon_b  = $row_RepInfo['vleftadebituar'];
      $v_mon_rb = $row_RepInfo['kursi'];

      $row_RepInfo = $RepInfoRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoRS);

    // Shitje
    mysqli_select_db($MySQL, $database_MySQL);
    $RepInfo_sql = " select sum(ek.vleftapaguar) vleftapaguar, (sum(ed.vleftadebituar) / sum(ek.vleftapaguar)) kursi
                                 from exchange_koke as ek,
                                      exchange_detaje as ed,
                                      klienti as k,
                                      monedha as m1,
                                      monedha as m2
                                where ek.chstatus       = 'T'
                                  and ek.tipiveprimit   = 'CHN'
                                  and ek.id             = ed.id_exchangekoke
                                  and ek.id_klienti     = k.id
                                  and ek.id_monkreditim = m1.id
                                  and ed.id_mondebituar = m2.id
                                  and m2.monedha        = 'LEK'
                                  and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                  " . $v_perioddate . "
                             group by m1.monedha, m2.monedha ";

    $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
    $row_RepInfo = $RepInfoRS->fetch_assoc();

    while ($row_RepInfo) {

      $v_mon_s  = $row_RepInfo['vleftapaguar'];
      $v_mon_rs = $row_RepInfo['kursi'];

      $row_RepInfo = $RepInfoRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoRS);

    if (($v_mon_b + $v_mon_s) > 0) {

      $v_rowvl++;
      $v_rowid++;

      $objWorksheet1->getCellByColumnAndRow(1, $v_rowvl)->setValue($v_rowid);
      $objWorksheet1->getCellByColumnAndRow(2, $v_rowvl)->setValue((int)substr($thisDate, 8, 2));

      $objWorksheet1->getCellByColumnAndRow(3, $v_rowvl)->setValue($v_mon_b);
      $objWorksheet1->getCellByColumnAndRow(4, $v_rowvl)->setValue($v_mon_rb);

      $objWorksheet1->getCellByColumnAndRow(6, $v_rowvl)->setValue($v_mon_s);
      $objWorksheet1->getCellByColumnAndRow(7, $v_rowvl)->setValue($v_mon_rs);

      $objWorksheet1->getCellByColumnAndRow(10, $v_rowvl)->setValue('=IFERROR(IF((B' . $v_rowvl . '-B$7)=N$6,IF(R$6>0,IF(Q$6>0,(Q$6+R$6)/2,R$6),Q$6),""),"")');
      $objWorksheet1->getCellByColumnAndRow(11, $v_rowvl)->setValue('=IFERROR(J' . $v_rowvl . '*K' . $v_rowvl . ',"")');
      $objWorksheet1->getCellByColumnAndRow(12, $v_rowvl)->setValue('=IFERROR((J' . $v_rowvl . '*K' . $v_rowvl . ')-(L$7+F$2-I$2),"")');
    }
  }

  // End Calculate period
  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

  $row_RepInfoM = $RepInfoMRS->fetch_assoc();
};
mysqli_free_result($RepInfoMRS);

// END EURO
// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// End Kembimi Excel
$objPHPexcel->setActiveSheetIndex(0);
$objWorksheet1 = $objPHPexcel->getActiveSheet();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel2007');
$objWriter->save($v_file);
// --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

?>