<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
date_default_timezone_set('Europe/Tirane');

if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  require_once('ConMySQL.php');

  // - periudha e percaktuar per raportin ditor
  if ((isset($_POST['p_date1'])) && ($_POST['p_date1'] != "")) {

    $v_perioddate  = " and ek.date_trans = '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'";
  }
  if ((isset($_POST['p_date2'])) && ($_POST['p_date2'] != "")) {

    $v_perioddate  = " and ek.date_trans >= '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'
                           and ek.date_trans <= '" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . "' ";
  }

  // - periudha e percaktuar per raportin javor
  if ((isset($_POST['p_date2'])) && ($_POST['p_date2'] != "")) {

    $v_perioddate_a  = " and ek.date_trans >= '" . substr($_POST['p_date1'], 6, 4) . "-01-01'
                             and ek.date_trans <= '" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . "' ";
    $v_perioddate_b  = " and ek.date_trans >= '" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . "'
                             and ek.date_trans <= '" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . "' ";
  }

  /////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////                                                           /////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////
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
  /////////////////////////////////////////////////////////////////////////////////////////////////

  set_time_limit(0);

  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  //   Raporti I Ri ne Excel
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  if ((isset($_POST['rep_type'])) && ($_POST['rep_type'] == "excelnew")) {

    // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // Data e Veprimit
    $startTime = substr($_POST['p_date1'], 0, 2) . "/" . substr($_POST['p_date1'], 3, 2) . "/" . substr($_POST['p_date1'], 6, 4);
    $RepDate   = substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2);
    // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $sql_info = "select * from kursi_koka where id = (select max(id) from kursi_koka where date <= '" . $RepDate . "') ";

    $id_kursi = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
    $row_id_kursi = $id_kursi->fetch_assoc();
    $kursi_id     = $row_id_kursi['id'];
    //--
    $query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                                    from kursi_detaje, monedha
                                   where kursi_detaje.monedha_id = monedha.id
                                     and kursi_detaje.master_id  = '" . $kursi_id . "'
                                     and monedha.monedha         = 'EUR' ";
    $monkurs_info = mysqli_query($MySQL, $query_monkurs_info) or die(mysqli_error($MySQL));
    $row_monkurs_info = $monkurs_info->fetch_assoc();
    while ($row_monkurs_info) {

      $v_kursieur       = $row_monkurs_info['kursiblerje'];
      $row_monkurs_info = $monkurs_info->fetch_assoc();
    };
    mysqli_free_result($monkurs_info);
    // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    $v_file = "rep/Boa_RaportNew_" . date("Y") . date("m") . date("d") . ".xlsx";

    require_once   'Classes/PHPExcel.php';
    $objPHPexcel   = PHPExcel_IOFactory::load('doc/BOARaportNew.xls');

    $objPHPexcel->setActiveSheetIndex(13);
    $objWorksheet1 = $objPHPexcel->getActiveSheet();
    $v_1 = $objWorksheet1->getCellByColumnAndRow(1,  9)->getValue();
    $v_2 = $objWorksheet1->getCellByColumnAndRow(1, 10)->getValue();
    $v_3 = $objWorksheet1->getCellByColumnAndRow(1, 11)->getValue();
    $v_4 = $objWorksheet1->getCellByColumnAndRow(1, 12)->getValue();
    $v_5 = $objWorksheet1->getCellByColumnAndRow(1, 13)->getValue();
    $v_6 = $objWorksheet1->getCellByColumnAndRow(1, 14)->getValue();

    $objPHPexcel->setActiveSheetIndex(0);
    $objWorksheet1 = $objPHPexcel->getActiveSheet();
    $objWorksheet1->getCellByColumnAndRow(1, 9)->setValue($startTime);

    // Forma F1.1 dhe F1.2
    $v_rowf11 = 8;
    $v_rowf21 = 8;
    $v_rowf12 = 8;
    $v_rowf22 = 8;

    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfoM_sql = " select monedha, ((boaorder - 1) * 4) as col
                          from monedha
                         where monedha <> 'LEK'
                      order by boaorder ";
    $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
    $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    $v_perioddate = " and ek.date_trans = '" . $RepDate . "' ";
    while ($row_RepInfoM) {

      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Kursi i Kembimit me EUR
      $query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                                    from kursi_detaje, monedha
                                   where kursi_detaje.master_id  = " . $kursi_id . "
                                     and kursi_detaje.monedha_id = monedha.id
                                     and monedha.monedha        = '" . $row_RepInfoM['monedha'] . "' ";
      $monkurs_info = mysqli_query($MySQL, $query_monkurs_info) or die(mysqli_error($MySQL));
      $row_monkurs_info = $monkurs_info->fetch_assoc();
      while ($row_monkurs_info) {

        $v_kursimon       = $row_monkurs_info['kursiblerje'];
        $row_monkurs_info = $monkurs_info->fetch_assoc();
      };
      mysqli_free_result($monkurs_info);
      if ($v_kursimon <= 0) $v_kursimon = 1;
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      $objPHPexcel->setActiveSheetIndex(4);
      $objWorksheet2 = $objPHPexcel->getActiveSheet();
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Blerje Individe mbi 50 000 EUR
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.id             <> 1
                              and m1.monedha        = 'LEK'
                              and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                           having sum(ed.vleftadebituar) >= (50000 * " . $v_kursieur . " / " . $v_kursimon . ") ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      //--
      while ($row_RepInfo) {

        $v_rowf11++;
        $objWorksheet2->getCellByColumnAndRow(0, $v_rowf11)->setValue($v_1); // "Individ�". html_entity_decode('�')); // �
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("Individual");
        } else {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet2->getCellByColumnAndRow(2, $v_rowf11)->setValue("NJE INDIVID");
        } else {
          $objWorksheet2->getCellByColumnAndRow(2, $v_rowf11)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet2->getCellByColumnAndRow(3, $v_rowf11)->setValue($row_RepInfoM['monedha']);
        $objWorksheet2->getCellByColumnAndRow(4, $v_rowf11)->setValue($row_RepInfo['vleftadebituar']);
        $objWorksheet2->getCellByColumnAndRow(5, $v_rowf11)->setValue($row_RepInfo['vleftapaguar'] / $row_RepInfo['vleftadebituar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Blerje Individe nen 50 000 EUR ose Individe te Rastesishem
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                           having sum(ed.vleftadebituar) < (50000 * " . $v_kursieur . " / " . $v_kursimon . ") or k.id = 1 ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      $row_vler1 = 0;
      $row_vler2 = 0;
      $row_kurs = 0;
      $row_num = 0;
      while ($row_RepInfo) {

        $row_num  += (int)$row_RepInfo['nr'];
        $row_vler1 += (int)$row_RepInfo['vleftadebituar'];
        $row_vler2 += (int)$row_RepInfo['vleftapaguar'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      if ($row_vler1 > 0) {
        $v_rowf11++;
        $row_kurs  = $row_vler2 / $row_vler1;
        $objWorksheet2->getCellByColumnAndRow(0, $v_rowf11)->setValue($v_1);
        if ($row_num == 1) {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("Individual");
        } else {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("I agreguar");
        }
        if ($row_num == 1) {
          $objWorksheet2->getCellByColumnAndRow(2, $v_rowf11)->setValue("NJE INDIVID");
        } else {
          $objWorksheet2->getCellByColumnAndRow(2, $v_rowf11)->setValue("TOTAL SHUMA ME TE VOGLA SE 50,000 EUR");
        }
        $objWorksheet2->getCellByColumnAndRow(3, $v_rowf11)->setValue($row_RepInfoM['monedha']);
        $objWorksheet2->getCellByColumnAndRow(4, $v_rowf11)->setValue($row_vler1);
        $objWorksheet2->getCellByColumnAndRow(5, $v_rowf11)->setValue($row_kurs);
      }
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Blerje Jo Individe
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.gender in ('C', 'B', 'Z')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_rowf11++;
        if ($row_RepInfo['gender'] == "C") {
          $objWorksheet2->getCellByColumnAndRow(0, $v_rowf11)->setValue($v_2);
        }
        if ($row_RepInfo['gender'] == "B") {
          $objWorksheet2->getCellByColumnAndRow(0, $v_rowf11)->setValue($v_4);
        }
        if ($row_RepInfo['gender'] == "Z") {
          $objWorksheet2->getCellByColumnAndRow(0, $v_rowf11)->setValue($v_5);
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("Individual");
        } else {
          $objWorksheet2->getCellByColumnAndRow(1, $v_rowf11)->setValue("I agreguar");
        }
        $objWorksheet2->getCellByColumnAndRow(2, $v_rowf11)->setValue($row_RepInfo['emriplote']);
        $objWorksheet2->getCellByColumnAndRow(3, $v_rowf11)->setValue($row_RepInfoM['monedha']);
        $objWorksheet2->getCellByColumnAndRow(4, $v_rowf11)->setValue($row_RepInfo['vleftadebituar']);
        $objWorksheet2->getCellByColumnAndRow(5, $v_rowf11)->setValue($row_RepInfo['vleftapaguar'] / $row_RepInfo['vleftadebituar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      $objPHPexcel->setActiveSheetIndex(5);
      $objWorksheet3 = $objPHPexcel->getActiveSheet();
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Shitje Individe mbi 50 000 EUR
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.id             <> 1
                              and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and m2.monedha        = 'LEK'
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                           having sum(ek.vleftapaguar) >= (50000 * " . $v_kursimon . " / " . $v_kursieur . ") ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      //--
      while ($row_RepInfo) {

        $v_rowf12++;
        $objWorksheet3->getCellByColumnAndRow(0, $v_rowf12)->setValue($v_1);
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("Individual");
        } else {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet3->getCellByColumnAndRow(2, $v_rowf12)->setValue("NJE INDIVID");
        } else {
          $objWorksheet3->getCellByColumnAndRow(2, $v_rowf12)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet3->getCellByColumnAndRow(3, $v_rowf12)->setValue($row_RepInfoM['monedha']);
        $objWorksheet3->getCellByColumnAndRow(4, $v_rowf12)->setValue($row_RepInfo['vleftapaguar']);
        $objWorksheet3->getCellByColumnAndRow(5, $v_rowf12)->setValue($row_RepInfo['vleftadebituar'] / $row_RepInfo['vleftapaguar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Shitje Individe nen 50 000 EUR ose Individe te Rastesishem
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and m2.monedha        = 'LEK'
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m2.monedha, m2.monedha
                           having sum(ek.vleftapaguar) < (50000 * " . $v_kursimon . " / " . $v_kursieur . ") or k.id = 1 ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      $row_vler1 = 0;
      $row_vler2 = 0;
      $row_kurs = 0;
      $row_num = "";
      while ($row_RepInfo) {

        $row_num  += $row_RepInfo['nr'];
        $row_vler1 += $row_RepInfo['vleftadebituar'];
        $row_vler2 += $row_RepInfo['vleftapaguar'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      if ($row_vler2 > 0) {

        $v_rowf12++;
        $row_kurs  = $row_vler1 / $row_vler2;
        $objWorksheet3->getCellByColumnAndRow(0, $v_rowf12)->setValue($v_1);
        if ($row_num == 1) {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("Individual");
        } else {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("I agreguar");
        }
        if ($row_num == 1) {
          $objWorksheet3->getCellByColumnAndRow(2, $v_rowf12)->setValue("NJE INDIVID");
        } else {
          $objWorksheet3->getCellByColumnAndRow(2, $v_rowf12)->setValue("TOTAL SHUMA ME TE VOGLA SE 50,000 EUR");
        }
        $objWorksheet3->getCellByColumnAndRow(3, $v_rowf12)->setValue($row_RepInfoM['monedha']);
        $objWorksheet3->getCellByColumnAndRow(4, $v_rowf12)->setValue($row_vler2);
        $objWorksheet3->getCellByColumnAndRow(5, $v_rowf12)->setValue($row_kurs);
      }
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Leke Shitje Jo Individe
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and m2.monedha        = 'LEK'
                              and k.gender in ('C', 'B', 'Z')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_rowf12++;
        if ($row_RepInfo['gender'] == "C") {
          $objWorksheet3->getCellByColumnAndRow(0, $v_rowf12)->setValue($v_2);
        }
        if ($row_RepInfo['gender'] == "B") {
          $objWorksheet3->getCellByColumnAndRow(0, $v_rowf12)->setValue($v_4);
        }
        if ($row_RepInfo['gender'] == "Z") {
          $objWorksheet3->getCellByColumnAndRow(0, $v_rowf12)->setValue($v_5);
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("Individual");
        } else {
          $objWorksheet3->getCellByColumnAndRow(1, $v_rowf12)->setValue("I agreguar");
        }
        $objWorksheet3->getCellByColumnAndRow(2, $v_rowf12)->setValue($row_RepInfo['emriplote']);
        $objWorksheet3->getCellByColumnAndRow(3, $v_rowf12)->setValue($row_RepInfoM['monedha']);
        $objWorksheet3->getCellByColumnAndRow(4, $v_rowf12)->setValue($row_RepInfo['vleftapaguar']);
        $objWorksheet3->getCellByColumnAndRow(5, $v_rowf12)->setValue($row_RepInfo['vleftadebituar'] / $row_RepInfo['vleftapaguar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      $objPHPexcel->setActiveSheetIndex(6);
      $objWorksheet4 = $objPHPexcel->getActiveSheet();
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Blerje Individe mbi 50 000 EUR
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.id             <> 1
                              and m1.monedha       <> 'LEK'
                              and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                           having sum(ed.vleftadebituar) >= (50000 * " . $v_kursieur . " / " . $v_kursimon . ") ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      //--
      while ($row_RepInfo) {

        $v_rowf21++;
        $objWorksheet4->getCellByColumnAndRow(0, $v_rowf21)->setValue($v_1);
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("Individual");
        } else {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet4->getCellByColumnAndRow(2, $v_rowf21)->setValue("NJE INDIVID");
        } else {
          $objWorksheet4->getCellByColumnAndRow(2, $v_rowf21)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet4->getCellByColumnAndRow(3, $v_rowf21)->setValue($row_RepInfoM['monedha']);
        $objWorksheet4->getCellByColumnAndRow(4, $v_rowf21)->setValue($row_RepInfo['monedha']);
        $objWorksheet4->getCellByColumnAndRow(5, $v_rowf21)->setValue($row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(6, $v_rowf21)->setValue($row_RepInfo['vleftapaguar'] / $row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(7, $v_rowf21)->setValue($row_RepInfo['vleftapaguar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Blerje Individe nen 50 000 EUR ose Individe te Rastesishem
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select sum(info.nr) nr, info.monedha,
                                  sum(info.vleftadebituar) vleftadebituar,
                                  sum(info.vleftapaguar)   vleftapaguar
                             from (
                                      select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                             sum(ed.vleftadebituar) vleftadebituar,
                                             sum(ek.vleftapaguar)   vleftapaguar
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
                                         and m1.monedha       <> 'LEK'
                                         and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                         and k.gender in ('M', 'F')
                                         " . $v_perioddate . "
                                    group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                                      having sum(ed.vleftadebituar) < (50000 * " . $v_kursieur . " / " . $v_kursimon . ") or k.id = 1
                                   ) info
                          group by info.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_rowf21++;
        $objWorksheet4->getCellByColumnAndRow(0, $v_rowf21)->setValue($v_1);
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("Individual");
        } else {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet4->getCellByColumnAndRow(2, $v_rowf21)->setValue("NJE INDIVID");
        } else {
          $objWorksheet4->getCellByColumnAndRow(2, $v_rowf21)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet4->getCellByColumnAndRow(3, $v_rowf21)->setValue($row_RepInfoM['monedha']);
        $objWorksheet4->getCellByColumnAndRow(4, $v_rowf21)->setValue($row_RepInfo['monedha']);
        $objWorksheet4->getCellByColumnAndRow(5, $v_rowf21)->setValue($row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(6, $v_rowf21)->setValue($row_RepInfo['vleftapaguar'] / $row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(7, $v_rowf21)->setValue($row_RepInfo['vleftapaguar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Blerje Jo Individe
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m1.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and m1.monedha       <> 'LEK'
                              and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and k.gender in ('C', 'B', 'Z')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_rowf21++;
        if ($row_RepInfo['gender'] == "C") {
          $objWorksheet4->getCellByColumnAndRow(0, $v_rowf21)->setValue($v_2);
        }
        if ($row_RepInfo['gender'] == "B") {
          $objWorksheet4->getCellByColumnAndRow(0, $v_rowf21)->setValue($v_4);
        }
        if ($row_RepInfo['gender'] == "Z") {
          $objWorksheet4->getCellByColumnAndRow(0, $v_rowf21)->setValue($v_5);
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("Individual");
        } else {
          $objWorksheet4->getCellByColumnAndRow(1, $v_rowf21)->setValue("I agreguar");
        }
        $objWorksheet4->getCellByColumnAndRow(2, $v_rowf21)->setValue($row_RepInfo['emriplote']);
        $objWorksheet4->getCellByColumnAndRow(3, $v_rowf21)->setValue($row_RepInfoM['monedha']);
        $objWorksheet4->getCellByColumnAndRow(4, $v_rowf21)->setValue($row_RepInfo['monedha']);
        $objWorksheet4->getCellByColumnAndRow(5, $v_rowf21)->setValue($row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(6, $v_rowf21)->setValue($row_RepInfo['vleftapaguar'] / $row_RepInfo['vleftadebituar']);
        $objWorksheet4->getCellByColumnAndRow(7, $v_rowf21)->setValue($row_RepInfo['vleftapaguar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      $objPHPexcel->setActiveSheetIndex(7);
      $objWorksheet5 = $objPHPexcel->getActiveSheet();
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Shitje Individe mbi 50 000 EUR
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and k.id             <> 1
                              and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and m2.monedha       <> 'LEK'
                              and k.gender in ('M', 'F')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                           having sum(ek.vleftapaguar) >= (50000 * " . $v_kursimon . " / " . $v_kursieur . ") ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      //--
      while ($row_RepInfo) {

        $v_rowf22++;
        $objWorksheet5->getCellByColumnAndRow(0, $v_rowf22)->setValue($v_1);
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("Individual");
        } else {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet5->getCellByColumnAndRow(2, $v_rowf22)->setValue("NJE INDIVID");
        } else {
          $objWorksheet5->getCellByColumnAndRow(2, $v_rowf22)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet5->getCellByColumnAndRow(3, $v_rowf22)->setValue($row_RepInfoM['monedha']);
        $objWorksheet5->getCellByColumnAndRow(4, $v_rowf22)->setValue($row_RepInfo['monedha']);
        $objWorksheet5->getCellByColumnAndRow(5, $v_rowf22)->setValue($row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(6, $v_rowf22)->setValue($row_RepInfo['vleftadebituar'] / $row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(7, $v_rowf22)->setValue($row_RepInfo['vleftadebituar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Shitje Individe nen 50 000 EUR ose Individe te Rastesishem
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select sum(info.nr) nr, info.monedha,
                                  sum(info.vleftadebituar) vleftadebituar,
                                  sum(info.vleftapaguar)   vleftapaguar
                             from (
                                      select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                             sum(ed.vleftadebituar) vleftadebituar,
                                             sum(ek.vleftapaguar)   vleftapaguar
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
                                         and m2.monedha       <> 'LEK'
                                         and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                         and k.gender in ('M', 'F')
                                         " . $v_perioddate . "
                                    group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha
                                      having sum(ek.vleftapaguar) < (50000 * " . $v_kursimon . " / " . $v_kursieur . ") or k.id = 1
                                   ) info
                          group by info.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      $row_vler1 = 0;
      $row_vler2 = 0;
      $row_kurs = 0;
      $row_num = "";
      while ($row_RepInfo) {

        $v_rowf22++;
        $objWorksheet5->getCellByColumnAndRow(0, $v_rowf22)->setValue($v_1);
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("Individual");
        } else {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("I agreguar");
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet5->getCellByColumnAndRow(2, $v_rowf22)->setValue("NJE INDIVID");
        } else {
          $objWorksheet5->getCellByColumnAndRow(2, $v_rowf22)->setValue("TOTAL SHUMA ME TE MEDHA SE 50,000 EUR");
        }

        $objWorksheet5->getCellByColumnAndRow(3, $v_rowf22)->setValue($row_RepInfoM['monedha']);
        $objWorksheet5->getCellByColumnAndRow(4, $v_rowf22)->setValue($row_RepInfo['monedha']);
        $objWorksheet5->getCellByColumnAndRow(5, $v_rowf22)->setValue($row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(6, $v_rowf22)->setValue($row_RepInfo['vleftadebituar'] / $row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(7, $v_rowf22)->setValue($row_RepInfo['vleftadebituar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      // Valute Shitje Jo Individe
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfo_sql = " select count(k.id) nr, k.id, k.emriplote, k.gender, m2.monedha,
                                  sum(ed.vleftadebituar) vleftadebituar,
                                  sum(ek.vleftapaguar)   vleftapaguar
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
                              and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                              and m2.monedha       <> 'LEK'
                              and k.gender in ('C', 'B', 'Z')
                              " . $v_perioddate . "
                         group by k.id, k.emriplote, k.gender, m1.monedha, m2.monedha ";
      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_rowf22++;
        if ($row_RepInfo['gender'] == "C") {
          $objWorksheet5->getCellByColumnAndRow(0, $v_rowf22)->setValue($v_2);
        }
        if ($row_RepInfo['gender'] == "B") {
          $objWorksheet5->getCellByColumnAndRow(0, $v_rowf22)->setValue($v_4);
        }
        if ($row_RepInfo['gender'] == "Z") {
          $objWorksheet5->getCellByColumnAndRow(0, $v_rowf22)->setValue($v_5);
        }
        if ($row_RepInfo['nr'] == 1) {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("Individual");
        } else {
          $objWorksheet5->getCellByColumnAndRow(1, $v_rowf22)->setValue("I agreguar");
        }
        $objWorksheet5->getCellByColumnAndRow(2, $v_rowf22)->setValue($row_RepInfo['emriplote']);
        $objWorksheet5->getCellByColumnAndRow(3, $v_rowf22)->setValue($row_RepInfoM['monedha']);
        $objWorksheet5->getCellByColumnAndRow(4, $v_rowf22)->setValue($row_RepInfo['monedha']);
        $objWorksheet5->getCellByColumnAndRow(5, $v_rowf22)->setValue($row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(6, $v_rowf22)->setValue($row_RepInfo['vleftadebituar'] / $row_RepInfo['vleftapaguar']);
        $objWorksheet5->getCellByColumnAndRow(7, $v_rowf22)->setValue($row_RepInfo['vleftadebituar']);

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);
      // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------












      $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoMRS);





    // End BoA Excel
    $objPHPexcel->setActiveSheetIndex(0);
    $objWorksheet1 = $objPHPexcel->getActiveSheet();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel2007');
    $objWriter->save($v_file);

    header(sprintf("Location: %s", $v_file));
  }
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  //   Raporti I Vjeter ne Excel
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  if ((isset($_POST['rep_type'])) && ($_POST['rep_type'] == "excel")) {

    $v_file = "rep/Boa_Raport_" . date("Y") . date("m") . date("d") . ".xlsx";

    require_once   'Classes/PHPExcel.php';
    $objPHPexcel   = PHPExcel_IOFactory::load('doc/BOARaport.xlsx');


    // Veprimet ditore
    $objWorksheet1 = $objPHPexcel->getActiveSheet();

    $startTime = strtotime("" . substr($_POST['p_date1'], 6, 4) . "-" . substr($_POST['p_date1'], 3, 2) . "-" . substr($_POST['p_date1'], 0, 2) . " 12:00");
    $endTime   = strtotime("" . substr($_POST['p_date2'], 6, 4) . "-" . substr($_POST['p_date2'], 3, 2) . "-" . substr($_POST['p_date2'], 0, 2) . " 12:00");
    $v_row     = 5;
    for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {

      $v_row++;
      $thisDate     = date('Y-m-d', $i);
      $objWorksheet1->getCellByColumnAndRow(0, $v_row)->setValue($thisDate);

      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfoM_sql = " select monedha, ((boaorder - 1) * 4) as col
                              from monedha
                             where monedha <> 'LEK'
                               and boaorder > 0
                          order by boaorder ";
      $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
      $row_RepInfoM = $RepInfoMRS->fetch_assoc();

      $v_col        = 0;
      $v_perioddate = " and ek.date_trans = '" . $thisDate . "' ";

      while ($row_RepInfoM) {

        $v_mon_b  = 0;
        $v_mon_s  = 0;
        $v_mon_rb = 0;
        $v_mon_rs = 0;

        // Blerje
        //mysql_select_db($database_MySQL, $MySQL);
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
        //mysql_select_db($database_MySQL, $MySQL);
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

        $pozicioni += ((($v_mon_b * $v_mon_rb) - ($v_mon_s * $v_mon_rs)));

        $v_col = $row_RepInfoM['col'];

        $objWorksheet1->getCellByColumnAndRow($v_col + 1, $v_row)->setValue($v_mon_b);
        $objWorksheet1->getCellByColumnAndRow($v_col + 2, $v_row)->setValue($v_mon_rb);

        $objWorksheet1->getCellByColumnAndRow($v_col + 3, $v_row)->setValue($v_mon_s);
        $objWorksheet1->getCellByColumnAndRow($v_col + 4, $v_row)->setValue($v_mon_rs);

        $row_RepInfoM = $RepInfoMRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoMRS);
    }


    // Shumat > 50 000 EUR
    $objPHPexcel->setActiveSheetIndex(1);
    $objWorksheet2 = $objPHPexcel->getActiveSheet();
    $v_row         = 1;
    for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {

      $reportamount = array(50000, 61300, 57500, 43900, 76450, 77500, 482000, 497300, 371000, 6590000);

      $thisDate     = date('Y-m-d', $i);
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfoM_sql = " select monedha, (boaorder - 1) as col
                              from monedha
                             where monedha <> 'LEK'
                               and boaorder > 0
                          order by boaorder ";
      $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
      $row_RepInfoM = $RepInfoMRS->fetch_assoc();

      $v_perioddate = " and ek.date_trans = '" . $thisDate . "' ";

      while ($row_RepInfoM) {

        $v_mon_vb = 0;
        $v_mon_vs = 0;

        // Blerje
        //mysql_select_db($database_MySQL, $MySQL);
        $RepInfo_sql = " select k.emriplote, m1.monedha,
                                      (sum(ek.vleftapaguar) / sum(ed.vleftadebituar)) kursi1,
                                      sum(ed.vleftadebituar) vleftadebituar,
                                      sum(ek.vleftapaguar)   vleftapaguar,
                                      (sum(ed.vleftadebituar) / sum(ek.vleftapaguar)) kursi2
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
                                  and m1.monedha       <> m2.monedha
                                  and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                  " . $v_perioddate . "
                             group by k.emriplote, m1.monedha, m2.monedha
                               having sum(ed.vleftadebituar) >= (" . $reportamount[$row_RepInfoM['col']] . ") ";

        $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
        $row_RepInfo = $RepInfoRS->fetch_assoc();

        while ($row_RepInfo) {

          $v_mon_kb = $row_RepInfo['emriplote'];
          $v_mon_cd = $row_RepInfo['monedha'];
          $v_mon_vb = $row_RepInfo['vleftadebituar'];
          $v_mon_rb = $row_RepInfo['kursi1'];
          $v_mon_ks = $row_RepInfo['emriplote'];
          $v_mon_vs = $row_RepInfo['vleftapaguar'];
          $v_mon_rs = $row_RepInfo['kursi2'];

          $row_RepInfo = $RepInfoRS->fetch_assoc();
        };
        mysqli_free_result($RepInfoRS);

        if (($v_mon_vb > 0) || ($v_mon_vs > 0)) {

          $v_row++;
          $objWorksheet2->getCellByColumnAndRow(0, $v_row)->setValue($thisDate);
          $objWorksheet2->getCellByColumnAndRow(1, $v_row)->setValue($v_mon_kb);
          $objWorksheet2->getCellByColumnAndRow(2, $v_row)->setValue($row_RepInfoM['monedha']);
          $objWorksheet2->getCellByColumnAndRow(3, $v_row)->setValue($v_mon_vb);
          $objWorksheet2->getCellByColumnAndRow(4, $v_row)->setValue($v_mon_rb);
          $objWorksheet2->getCellByColumnAndRow(5, $v_row)->setValue($v_mon_cd);
          $objWorksheet2->getCellByColumnAndRow(6, $v_row)->setValue($v_mon_vs);
          $objWorksheet2->getCellByColumnAndRow(7, $v_row)->setValue($v_mon_rs);
        }

        // Shitje
        //mysql_select_db($database_MySQL, $MySQL);
        $RepInfo_sql = " select k.emriplote, m1.monedha as monedha1, m2.monedha as monedha2,
                                      (sum(ek.vleftapaguar) / sum(ed.vleftadebituar)) kursi1,
                                      sum(ed.vleftadebituar) vleftadebituar,
                                      sum(ek.vleftapaguar)   vleftapaguar,
                                      (sum(ed.vleftadebituar) / sum(ek.vleftapaguar)) kursi2
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
                                  and m1.monedha       <> m2.monedha
                                  and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                  " . $v_perioddate . "
                             group by k.emriplote, m1.monedha, m2.monedha
                               having sum(ek.vleftapaguar) >= (" . $reportamount[$row_RepInfoM['col']] . ") ";

        $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
        $row_RepInfo = $RepInfoRS->fetch_assoc();

        while ($row_RepInfo) {

          $v_mon_kb = $row_RepInfo['emriplote'];
          $v_mon_cd = $row_RepInfo['monedha2'];
          $v_mon_vb = $row_RepInfo['vleftadebituar'];
          $v_mon_rb = $row_RepInfo['kursi1'];
          $v_mon_ks = $row_RepInfo['emriplote'];
          $v_mon_vs = $row_RepInfo['vleftapaguar'];
          $v_mon_rs = $row_RepInfo['kursi2'];

          $row_RepInfo = $RepInfoRS->fetch_assoc();
        };
        mysqli_free_result($RepInfoRS);

        if (($v_mon_vb > 0) || ($v_mon_vs > 0)) {

          $v_row++;
          $objWorksheet2->getCellByColumnAndRow(0, $v_row)->setValue($thisDate);
          $objWorksheet2->getCellByColumnAndRow(1, $v_row)->setValue($v_mon_kb);
          $objWorksheet2->getCellByColumnAndRow(5, $v_row)->setValue($row_RepInfoM['monedha']);
          $objWorksheet2->getCellByColumnAndRow(3, $v_row)->setValue($v_mon_vb);
          $objWorksheet2->getCellByColumnAndRow(4, $v_row)->setValue($v_mon_rb);
          $objWorksheet2->getCellByColumnAndRow(2, $v_row)->setValue($v_mon_cd);
          $objWorksheet2->getCellByColumnAndRow(6, $v_row)->setValue($v_mon_vs);
          $objWorksheet2->getCellByColumnAndRow(7, $v_row)->setValue($v_mon_rs);
        }

        $row_RepInfoM = $RepInfoMRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoMRS);
    }


    // Valute me Valute
    $objPHPexcel->setActiveSheetIndex(2);
    $objWorksheet3 = $objPHPexcel->getActiveSheet();
    $v_row         = 1;
    for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {

      $thisDate     = date('Y-m-d', $i);
      //mysql_select_db($database_MySQL, $MySQL);
      $RepInfoM_sql = " select monedha
                              from monedha
                             where monedha <> 'LEK'
                               and boaorder > 0
                          order by boaorder ";
      $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
      $row_RepInfoM = $RepInfoMRS->fetch_assoc();

      $v_perioddate = " and ek.date_trans = '" . $thisDate . "' ";

      while ($row_RepInfoM) {

        $v_mon_vb = 0;
        $v_mon_vs = 0;

        // Blerje
        //mysql_select_db($database_MySQL, $MySQL);
        $RepInfo_sql = " select k.emriplote, m1.monedha,
                                      (sum(ek.vleftapaguar) / sum(ed.vleftadebituar)) kursi1,
                                      sum(ed.vleftadebituar) vleftadebituar,
                                      sum(ek.vleftapaguar)   vleftapaguar,
                                      (sum(ed.vleftadebituar) / sum(ek.vleftapaguar)) kursi2
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
                                  and m1.monedha       <> 'LEK'
                                  and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                                  " . $v_perioddate . "
                             group by k.emriplote, m1.monedha, m2.monedha ";

        $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
        $row_RepInfo = $RepInfoRS->fetch_assoc();

        while ($row_RepInfo) {

          $v_mon_kb = $row_RepInfo['emriplote'];
          $v_mon_cd = $row_RepInfo['monedha'];
          $v_mon_vb = $row_RepInfo['vleftadebituar'];
          $v_mon_rb = $row_RepInfo['kursi1'];
          $v_mon_ks = $row_RepInfo['emriplote'];
          $v_mon_vs = $row_RepInfo['vleftapaguar'];
          $v_mon_rs = $row_RepInfo['kursi2'];

          $row_RepInfo = $RepInfoRS->fetch_assoc();
        };
        mysqli_free_result($RepInfoRS);

        if (($v_mon_vb > 0) || ($v_mon_vs > 0)) {

          $v_row++;
          $objWorksheet3->getCellByColumnAndRow(0, $v_row)->setValue($thisDate);
          $objWorksheet3->getCellByColumnAndRow(1, $v_row)->setValue($v_mon_kb);
          $objWorksheet3->getCellByColumnAndRow(2, $v_row)->setValue($row_RepInfoM['monedha']);
          $objWorksheet3->getCellByColumnAndRow(3, $v_row)->setValue($v_mon_vb);
          $objWorksheet3->getCellByColumnAndRow(4, $v_row)->setValue($v_mon_rb);
          $objWorksheet3->getCellByColumnAndRow(5, $v_row)->setValue($v_mon_cd);
          $objWorksheet3->getCellByColumnAndRow(6, $v_row)->setValue($v_mon_vs);
          $objWorksheet3->getCellByColumnAndRow(7, $v_row)->setValue($v_mon_rs);
        }

        $row_RepInfoM = $RepInfoMRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoMRS);
    }

    // End BoA Excel
    $objPHPexcel->setActiveSheetIndex(0);
    $objWorksheet1 = $objPHPexcel->getActiveSheet();
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel2007');
    $objWriter->save($v_file);

    header(sprintf("Location: %s", $v_file));
  }
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  //   Raporti Ditor
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  if ((isset($_POST['rep_type'])) && ($_POST['rep_type'] == "ditor")) {

    //-----------------------------------------------------------------------------------------------------------
    // require_once "docgen/cl_xml2driver.php";
    //-----------------------------------------------------------------------------------------------------------
    $date_print = new DateTime();
    $file_pdf = "rep/BOA_ditor_" . $date_print->format('Y-m-d_H:i:s') . ".rtf";
    //-----------------------------------------------------------------------------------------------------------
    // $xml_template  =  '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
    // $xml_template .= '<DOC config_file="doc_config.inc" title="Raport per BOA" company="' . $_SESSION['CNAME'] . '">';
    $xml_template = '<header>';
    $xml_template .= '</header>';
    $xml_template .= '<footer>';
    $xml_template .= '</footer>';

    $xml_template .= '<table width="100%" align="left" border="1">';
    $xml_template .= '<tr>';
    $xml_template .= '<td width="15%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="17%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="17%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="17%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="17%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="17%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="6">&nbsp;<font size="16"><b>RAPORT DITOR I VEPRIMTARISE SE KEMBIMIT VALUTOR</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="6">&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="4">&nbsp;<font size="14"><b>ZYRA E KEMBIMIT VALUTOR : &nbsp; ' . $_SESSION['CNAME'] . ' </b></font></td>';
    $xml_template .= '<td colspan="2">&nbsp;<font size="14"><b>&nbsp;PERIUDHA: ' . $_POST['p_date1'] . '&nbsp;- ' . $_POST['p_date2'] . '</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="6">&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center" rowspan="3"><b>MONEDHA</b></td>';
    $xml_template .= '<td colspan="5" border="1" align="center">&nbsp;<b>KEMBIMI VALUTOR KUNDREJT LEKUT</b></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center">[1]</td>';
    $xml_template .= '<td border="1" align="center">[2]</td>';
    $xml_template .= '<td border="1" align="center">[3]</td>';
    $xml_template .= '<td border="1" align="center">[4]</td>';
    $xml_template .= '<td border="1" align="center">[5]</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center"><b>BLERJE</b></td>';
    $xml_template .= '<td border="1" align="center"><b>KURSI MESATAR</b></td>';
    $xml_template .= '<td border="1" align="center"><b>SHITJE</b></td>';
    $xml_template .= '<td border="1" align="center"><b>KURSI MESATAR</b></td>';
    $xml_template .= '<td border="1" align="center"><b>POZICIONI (ne leke)</b></td>';
    $xml_template .= '</tr>';

    $pozicioni = 0;
    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfoM_sql = " select monedha
                        from monedha
                       where monedha <> 'LEK' ";
    $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
    $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    while ($row_RepInfoM) {

      $v_mon_b = 0;
      $v_mon_rb = 0;
      $v_mon_s = 0;
      $v_mon_rs = 0;

      // Blerje
      //mysql_select_db($database_MySQL, $MySQL);
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
      //mysql_select_db($database_MySQL, $MySQL);
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

      $pozicioni += ((($v_mon_b * $v_mon_rb) - ($v_mon_s * $v_mon_rs)));

      $xml_template .= '<tr>';
      $xml_template .= '<td border="1" align="center">' . $row_RepInfoM['monedha'] . '</td>';
      $xml_template .= '<td border="1" align="center">' . number_format($v_mon_b, 2, '.', ',')  . '</td>';
      $xml_template .= '<td border="1" align="center">' . number_format($v_mon_rb, 4, '.', ',') . '</td>';
      $xml_template .= '<td border="1" align="center">' . number_format($v_mon_s, 2, '.', ',')  . '</td>';
      $xml_template .= '<td border="1" align="center">' . number_format($v_mon_rs, 4, '.', ',') . '</td>';
      $xml_template .= '<td border="1" align="center">' . number_format(((($v_mon_b * $v_mon_rb) - ($v_mon_s * $v_mon_rs))), 2, '.', ',') . '</td>';
      $xml_template .= '</tr>';


      $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoMRS);


    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" colspan="5">&nbsp;</td>';
    $xml_template .= '<td border="1" align="center"><b>TOTALI I POZICIONIT&nbsp;(ne leke; shuma e kolones [5])</b></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" colspan="5">&nbsp;</td>';
    $xml_template .= '<td border="1" align="center"><b>' . number_format($pozicioni, 2, '.', ',') . '</b></td>';
    $xml_template .= '</tr>';

    $xml_template .= '<tr>';
    $xml_template .= '<td border="1"><b>&nbsp;Detajim i detyrueshem</b></td>';
    $xml_template .= '<td border="1"><b>&nbsp;Veprime me</b></td>';
    $xml_template .= '<td border="1" align="center"><b>&nbsp;Blerje Valute (ne leke)</b></td>';
    $xml_template .= '<td border="1" align="center"><b>&nbsp;Shitje Valute (ne leke)</b></td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';

    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfo_sql = " select k.gender, sum(ek.vleftapaguar) vleftapaguar, m1.monedha as mon1, avg(ed.kursi) kursi, avg(ed.kursi1) kursi1, sum(ed.vleftadebituar) vleftadebituar, m2.monedha as mon2
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
                        and (m1.monedha = 'LEK' or m2.monedha = 'LEK')
                       " . $v_perioddate . "
                   group by m1.monedha, m2.monedha, k.gender ";
    $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
    $row_RepInfo = $RepInfoRS->fetch_assoc();

    $v_lek_ib  = 0;
    $v_lek_bb  = 0;
    $v_lek_is  = 0;
    $v_lek_bs  = 0;
    $v_lek_cb  = 0;
    $v_lek_zb  = 0;
    $v_lek_cs  = 0;
    $v_lek_zs  = 0;

    while ($row_RepInfo) {

      if (($row_RepInfo['mon2'] == "LEK") && (($row_RepInfo['gender'] == "M") ||
        ($row_RepInfo['gender'] == "F") ||
        ($row_RepInfo['gender'] == "T"))) {
        $v_lek_ib += $row_RepInfo['vleftadebituar'];
      }
      if (($row_RepInfo['mon2'] == "LEK") && ($row_RepInfo['gender'] == "B")) {
        $v_lek_bb += $row_RepInfo['vleftadebituar'];
      }
      if (($row_RepInfo['mon2'] == "LEK") && ($row_RepInfo['gender'] == "C")) {
        $v_lek_cb += $row_RepInfo['vleftadebituar'];
      }
      if (($row_RepInfo['mon2'] == "LEK") && ($row_RepInfo['gender'] == "Z")) {
        $v_lek_zb += $row_RepInfo['vleftadebituar'];
      }

      if (($row_RepInfo['mon1'] == "LEK") && (($row_RepInfo['gender'] == "M") ||
        ($row_RepInfo['gender'] == "F") ||
        ($row_RepInfo['gender'] == "T"))) {
        $v_lek_is += $row_RepInfo['vleftapaguar'];
      }
      if (($row_RepInfo['mon1'] == "LEK") && ($row_RepInfo['gender'] == "B")) {
        $v_lek_bs += $row_RepInfo['vleftapaguar'];
      }
      if (($row_RepInfo['mon1'] == "LEK") && ($row_RepInfo['gender'] == "C")) {
        $v_lek_cs += $row_RepInfo['vleftapaguar'];
      }
      if (($row_RepInfo['mon1'] == "LEK") && ($row_RepInfo['gender'] == "Z")) {
        $v_lek_zs += $row_RepInfo['vleftapaguar'];
      }

      $row_RepInfo = $RepInfoRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoRS);

    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td border="1"><b>&nbsp;Banka</b></td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_bs, 2, '.', ',') . '</td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_bb, 2, '.', ',') . '</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td border="1"><b>&nbsp;Z.K.Valutor</b></td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_zs, 2, '.', ',') . '</td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_zb, 2, '.', ',') . '</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td border="1"><b>&nbsp;Biznese</b></td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_cs, 2, '.', ',') . '</td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_cb, 2, '.', ',') . '</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td border="1"><b>&nbsp;Individe</b></td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_is, 2, '.', ',') . '</td>';
    $xml_template .= '<td border="1" align="center">' . number_format($v_lek_ib, 2, '.', ',') . '</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="6">&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="3" border="1" align="center"><b>KEMBIM I VALUTAVE KUNDREJT VALUTAVE</b></td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center"><b>MONEDHA</b></td>';
    $xml_template .= '<td border="1" align="center"><b>BLERJE</b></td>';
    $xml_template .= '<td border="1" align="center"><b>SHITJE</b></td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';


    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfoM_sql = " select monedha
                        from monedha
                       where monedha <> 'LEK' ";
    $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
    $row_RepInfoM = $RepInfoMRS->fetch_assoc();

    while ($row_RepInfoM) {

      $v_mon_vb = 0;
      $v_mon_vs = 0;

      // Blerje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            and m1.monedha       <> 'LEK'
                            and m2.monedha        = '" . $row_RepInfoM['monedha'] . "'
                            " . $v_perioddate . "
                       group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_mon_vb  = $row_RepInfo['vleftadebituar'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      // Shitje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            and m2.monedha       <> 'LEK'
                            and m1.monedha        = '" . $row_RepInfoM['monedha'] . "'
                            " . $v_perioddate . "
                       group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_mon_vs  = $row_RepInfo['vleftapaguar'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);


      if (($v_mon_vb > 0) || ($v_mon_vs > 0)) {

        $xml_template .= '<tr>';
        $xml_template .= '<td border="1" align="center">' . $row_RepInfoM['monedha'] . '</td>';
        $xml_template .= '<td border="1" align="center">&nbsp;' . number_format($v_mon_vb, 2, '.', ',') . '</td>';
        $xml_template .= '<td border="1" align="center">&nbsp;' . number_format($v_mon_vs, 2, '.', ',') . '</td>';
        $xml_template .= '<td>&nbsp;</td>';
        $xml_template .= '<td>&nbsp;</td>';
        $xml_template .= '<td>&nbsp;</td>';
        $xml_template .= '</tr>';
      }

      $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoMRS);


    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td align="center"><b>ADMINISTRATORI</b></td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td align="center"><b>&nbsp;' . $_SESSION['CADMI'] . '&nbsp;</b></td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '</table>';

    $xml_template .= '</DOC>';

    // $xml = new nDOCGEN($xml_template, "RTF");
    // $fp = fopen($file_pdf, "w");
    // fwrite($fp, $xml->get_result_file());
    // @fclose($fp);
    $fileName = "BOA_ditor_". $date_print->format('Y-m-d_H:i:s').".doc";
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $xml_template;

    // header(sprintf("Location: %s", $file_pdf));
  }

  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  //   Raporti Javor
  // -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  if ((isset($_POST['rep_type'])) && ($_POST['rep_type'] == "javor")) {

    //-----------------------------------------------------------------------------------------------------------
    // require_once "docgen/cl_xml2driver.php";
    require_once "docgen/num_to_words.php";
    //-----------------------------------------------------------------------------------------------------------
    $date_print1 = new DateTime();
    $file_pdf = "rep/BOA_javor_" . $date_print1->format('Y-m-d H:i:s') . ".rtf";
    //-----------------------------------------------------------------------------------------------------------
    // $xml_template  =  '<' . '?xml version="1.0" encoding="ISO-8859-1" ?' . '>';
    // $xml_template .= '<DOC config_file="doc_config.inc" title="Raport per BOA" company="' . $_SESSION['CNAME'] . '">';
    $xml_template = '<header>';
    $xml_template .= '</header>';
    $xml_template .= '<footer>';
    $xml_template .= '</footer>';


    $pozicioni_a = 0;

    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfoM_sql = " select monedha
                        from monedha
                       where monedha <> 'LEK' ";
    $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
    $row_RepInfoM = $RepInfoMRS->fetch_assoc();

    while ($row_RepInfoM) {

      $v_mon_a_b = 0;
      $v_mon_a_rb = 0;
      $v_mon_a_s = 0;
      $v_mon_a_rs = 0;

      // Blerje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            " . $v_perioddate_a . "
                         group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_mon_a_b  = $row_RepInfo['vleftadebituar'];
        $v_mon_a_rb = $row_RepInfo['kursi'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      // Shitje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            " . $v_perioddate_a . "
                       group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_mon_a_s  = $row_RepInfo['vleftapaguar'];
        $v_mon_a_rs = $row_RepInfo['kursi'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      $pozicioni_a += ((($v_mon_a_b * $v_mon_a_rb) - ($v_mon_a_s * $v_mon_a_rs)));

      $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoMRS);


    $pozicioni_b = 0;

    //mysql_select_db($database_MySQL, $MySQL);
    $RepInfoM_sql = " select monedha
                        from monedha
                       where monedha <> 'LEK' ";
    $RepInfoMRS   = mysqli_query($MySQL, $RepInfoM_sql) or die(mysqli_error($MySQL));
    $row_RepInfoM = $RepInfoMRS->fetch_assoc();

    while ($row_RepInfoM) {

      $v_mon_b_b = 0;
      $v_mon_b_rb = 0;
      $v_mon_b_s = 0;
      $v_mon_b_rs = 0;

      // Blerje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            " . $v_perioddate_b . "
                       group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();

      while ($row_RepInfo) {

        $v_mon_b_b  = $row_RepInfo['vleftadebituar'];
        $v_mon_b_rb = $row_RepInfo['kursi'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      // Shitje
      //mysql_select_db($database_MySQL, $MySQL);
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
                            " . $v_perioddate_b . "
                       group by m1.monedha, m2.monedha ";

      $RepInfoRS   = mysqli_query($MySQL, $RepInfo_sql) or die(mysqli_error($MySQL));
      $row_RepInfo = $RepInfoRS->fetch_assoc();
      while ($row_RepInfo) {

        $v_mon_b_s  = $row_RepInfo['vleftapaguar'];
        $v_mon_b_rs = $row_RepInfo['kursi'];

        $row_RepInfo = $RepInfoRS->fetch_assoc();
      };
      mysqli_free_result($RepInfoRS);

      $pozicioni_b += ((($v_mon_b_b * $v_mon_b_rb) - ($v_mon_b_s * $v_mon_b_rs)));

      $row_RepInfoM = $RepInfoMRS->fetch_assoc();
    };
    mysqli_free_result($RepInfoMRS);

    $xml_template .= '<table width="100%" align="left" border="0">';
    $xml_template .= '<tr>';
    $xml_template .= '<td width="50%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '<td width="50%" align="center"><font size="3">&nbsp;</font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<font size="16"><b>RAPORTIMI JAVOR I VEPRIMTARISE SE KEMMBIMIT VALUTOR</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<br /></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<br /></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;<font size="14"><b>ZYRA E KEMBIMIT VALUTOR : &nbsp; ' . $_SESSION['CNAME'] . ' </b></font></td>';
    $xml_template .= '<td>&nbsp;<font size="14"><b>&nbsp;PERIUDHA: ' . $_POST['p_date1'] . ' - ' . $_POST['p_date2'] . '&nbsp;</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<br /></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<br /></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center"><b>POZICIONI I KUMULIMIT (ne lek nga fillimi i vitit)</b></td>';
    $xml_template .= '<td border="1" align="center"><b>SHUMA E PAGESAVE (ne lek) E TOTALIT TE POZICIONIT DITOR</b></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td border="1" align="center">&nbsp;<b>' . number_format(($pozicioni_a), 2, '.', ',') . ' ( ' . num_to_words(number_format(($pozicioni_a), 2, '.', ''), '', 2, '') . ' )</b></td>';
    $xml_template .= '<td border="1" align="center">&nbsp;<b>' . number_format(($pozicioni_b), 2, '.', ',') . ' ( ' . num_to_words(number_format(($pozicioni_b), 2, '.', ''), '', 2, '') . ' )</b></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td align="right"><font size="11"><b>DATE: ' . strftime('%d.%m.%Y') . '</b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;<br /></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td align="center"><font size="13"><b>ADMINISTRATOR</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td colspan="2">&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td align="center"><font size="13"><b>(&nbsp;' . $_SESSION['CADMI'] . '&nbsp;)</b></font></td>';
    $xml_template .= '</tr>';
    $xml_template .= '<tr>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '<td>&nbsp;</td>';
    $xml_template .= '</tr>';
    $xml_template .= '</table>';

    $xml_template .= '</DOC>';

    // $xml = new nDOCGEN($xml_template, "RTF");
    // $fp = fopen($file_pdf, "w");
    // fwrite($fp, $xml->get_result_file());
    // @fclose($fp);

    // header(sprintf("Location: %s", $file_pdf));
    $date_print1 = new DateTime();
    $fileName = "BOA_javor_". $date_print1->format('Y-m-d_H:i:s').".doc";
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $xml_template;
  }
}

?>
