<?php

session_start();
date_default_timezone_set('Europe/Tirane');
// Comment out the Excel library import
// require_once 'Spreadsheet/Excel/Writer.php';
?>
<?php require_once('ConMySQL.php'); ?>
<?php

if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  mysqli_select_db($MySQL, $database_MySQL);

  $sql_exchange_info = "select * from exchange_koke where id = '" . $user_info . 'CHN' . $_GET['hid'] . "'";
  $exchange_info = mysqli_query($MySQL, $sql_exchange_info) or die(mysqli_error($MySQL));
  $row_exchange_info = mysqli_fetch_assoc($exchange_info);

  $sql_exchange_det_info = "select * from exchange_detaje where id_exchangekoke = '" . $row_exchange_info['id'] . "' ";
  $exchange_det_info = mysqli_query($MySQL, $sql_exchange_det_info) or die(mysqli_error($MySQL));
  $row_exchange_det_info = mysqli_fetch_assoc($exchange_det_info);
  $time = date("H:i");

  // Get currency information
  $sql_input_currency = "select * from monedha where id = " . $row_exchange_det_info['id_mondebituar'];
  $rs_input_currency = mysqli_query($MySQL, $sql_input_currency) or die(mysqli_error($MySQL));
  $row_input_currency = mysqli_fetch_assoc($rs_input_currency);
  $input_currency_code = strtoupper(trim($row_input_currency['monedha']));
  mysqli_free_result($rs_input_currency);

  $sql_output_currency = "select * from monedha where id = " . $row_exchange_info['id_monkreditim'];
  $rs_output_currency = mysqli_query($MySQL, $sql_output_currency) or die(mysqli_error($MySQL));
  $row_output_currency = mysqli_fetch_assoc($rs_output_currency);
  $output_currency_code = strtoupper(trim($row_output_currency['monedha']));
  mysqli_free_result($rs_output_currency);

  $input_currency_code = ($input_currency_code === 'LEK') ? 'ALL' : $input_currency_code;
  $output_currency_code = ($output_currency_code === 'LEK') ? 'ALL' : $output_currency_code;

  // Check if JSON format is requested
  if (isset($_GET['download_type']) && $_GET['download_type'] == 'json') {
    // Create base exchange data structure
    $exchange_data = [
      "invoiceType" => "EXCHANGE",
      "inputCurrency" => [
        "code" => $input_currency_code,
        "buyRate" => floatval($row_exchange_det_info['kursi']),
        "quantity" => floatval($row_exchange_det_info['vleftadebituar'])
      ],
      "outputCurrency" => [
        "code" => $output_currency_code,
        "buyRate" => floatval($row_exchange_det_info['kursi']),
        "sellRate" => floatval($row_exchange_det_info['kursi1'])
      ],
      "commission" => [
        "percent" => floatval($row_exchange_info['perqindjekomisioni']),
        "amount" => floatval($row_exchange_info['vleftakomisionit'])
      ]
    ];

    // Handle ALL (LEK) to other currency
    if ($input_currency_code === 'ALL') {
      $exchange_data['inputCurrency']['sellRate'] = floatval($row_exchange_det_info['kursi']);
      $exchange_data['outputCurrency']['buyRate'] = floatval($row_exchange_det_info['kursi1']);
    } else if ($output_currency_code === 'ALL') {
      $exchange_data['inputCurrency']['buyRate'] = floatval($row_exchange_det_info['kursi1']);
      $exchange_data['outputCurrency']['sellRate'] = floatval($row_exchange_det_info['kursi']);
    } else if ($input_currency_code === 'USD') {
      $exchange_data['inputCurrency']['sellRate'] = floatval($row_exchange_det_info['kursi1']);
    }

    array_walk_recursive($exchange_data, function(&$value) {
      if (is_float($value)) {
        $value = round($value, 4);
      }
    });

    $json_file_name = "rep/exchange_" . strftime('%Y%m%d%H%M%S') . ".json";
    $json_data = json_encode($exchange_data, JSON_PRETTY_PRINT);
    file_put_contents($json_file_name, $json_data);

    header("Content-Type: application/json");
    header("Content-Disposition: attachment; filename=\"" . basename($json_file_name) . "\"");
    header("Content-Length: " . filesize($json_file_name));
    readfile($json_file_name);
    exit;
  } else {
    // Comment out all Excel generation code
    /*
    $v_file = "rep/Mandat_këmbimi_valutor_" . strftime('%Y%m%d%H%M%S') . ".xls";
    $workbook = new Spreadsheet_Excel_Writer($v_file);

    // Create worksheet
    $worksheet = $workbook->addWorksheet('Mandat këmbimi valutor');

    // Define formats
    $format_bold = $workbook->addFormat();
    $format_bold->setBold();
    $format_bold->setSize(14);

    $format_right = $workbook->addFormat();
    $format_right->setAlign('right');

    $format_bold_right = $workbook->addFormat();
    $format_bold_right->setAlign('right');
    $format_bold_right->setBold();

    $format_bold_center = $workbook->addFormat();
    $format_bold_center->setBold();
    $format_bold_center->setAlign('center');
    $format_bold_center->setSize(12);

    // Set column width
    $worksheet->setColumn(0, 0, 15);
    $worksheet->setColumn(1, 1, 25);
    $worksheet->setColumn(2, 2, 20);

    // Write data
    $worksheet->mergeCells(0, 1, 0, 2);
    $worksheet->write(0, 1, "Mandat këmbimi valutor", $format_bold_center);

    $worksheet->write(1, 0, "Nr: " . $row_exchange_info['unique_id']);
    $dateCell = substr($row_exchange_info['datarregjistrimit'], 8, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 5, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 0, 4) . "";
    $worksheet->write(1, 2, "Date");
    $worksheet->write(1, 3, $dateCell);


    $sql_subinfo = "select * from klienti where id = " . $row_exchange_info['id_klienti'];
    $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
    $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
    $info = $row_rs_subinfo['emriplote'];
    mysqli_free_result($rs_subinfo);
    $worksheet->write(2, 0, "Client");
    $worksheet->write(2, 2, $info);

    $sql_subinfo = "select * from monedha where id = " . $row_exchange_det_info['id_mondebituar'];
    $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
    $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
    $info = $row_rs_subinfo['monedha'];
    mysqli_free_result($rs_subinfo);
    $worksheet->write(3, 0, "Balance");
    $worksheet->write(3, 2, number_format($row_exchange_det_info['vleftadebituar'], 2, '.', ','));

    $sql_subinfo = "select * from monedha where id = " . $row_exchange_info['id_monkreditim'];
    $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
    $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
    $info = $row_rs_subinfo['monedha'];
    mysqli_free_result($rs_subinfo); 
    $worksheet->write(4, 0, "Kursi ".  $row_exchange_det_info['kursi_txt'] ."=");
    $worksheet->write(4, 2,   $row_exchange_det_info['kursi'] );

    $worksheet->write(5, 0, "Total");
    $worksheet->write(5, 2, number_format($row_exchange_det_info['vleftakredituar'], 2, '.', ',')." ".$info);

    $worksheet->write(7, 0, "P&euml;r t'u paguar");
    $worksheet->write(7, 2, number_format($row_exchange_info['vleftapaguar'], 2, '.', ',')." ".$info);

    $worksheet->write(9, 0, "Ju Faleminderit! - Thank you!");

    // Close workbook
    $workbook->close();

    header(sprintf("Location: %s", $v_file));
    */
  }
}

?>

  <html>

  <head>
    <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System</title>
    <style type="text/css">
      body,
      td,
      th {
        font-size: 10px;
      }
    </style>
  </head>

  <body leftmargin=0 topmargin=0 marginheight="0" marginwidth="5" bgcolor=#FFFFFF vlink="#0000ff" link="#0000ff">
    <table cellSpacing=0 cellPadding=0 width="300" border=0>
      <tbody>
        <tr>
          <td align="center">
            <img src="<?php if (isset($_SESSION['logo_image']) && !empty($_SESSION['logo_image'])) {
                        echo $_SESSION['logo_image'];
                      } else {
                        echo './assets/images/Logo.png';
                      } ?>" title="GlobalTech.al" alt="GlobalTech.al" border="0" width="100">
          </td>
        </tr>
      </tbody>
    </table>
    <table cellSpacing=0 cellPadding=0 width="300" border=0>
      <tbody style="font-weight: bolder;">
        <tr>
          <td height="15" colSpan=3 align="left" valign="middle">
            <div class=ctxheadingP>
              <b>
                <font size="2">
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CNIPT']; ?><br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CADDR']; ?></strong>
                </font>
              </b>

            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <table cellSpacing=0 cellPadding=0 width="300" border=0>
      <tbody style="font-weight: bolder;">
        <td height="15" colspan="3" align="left" valign="middle" style="
            font-size: 20px;
            text-align: center;
            text-transform: capitalize;">Mandat k&euml;mbimi valutor</td>
        <tr style="font-weight: bolder;">
          <td align="center" colSpan=3>
            <div class=ctxheadingP>

              <table width="300" border="0" cellpadding="0" cellspacing="0" style="font-weight:bolder">
                <tr>
                  <td colspan="7" height="5"></td>
                </tr>
                <tr valign="middle">
                  <td></td>
                  <td width="*" colspan="5">Nr:&nbsp;<b><?php echo
                                                        $row_exchange_info['unique_id']; ?>
                    </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data:&nbsp;<b><?php echo substr($row_exchange_info['datarregjistrimit'], 8, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 5, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 0, 4) . ""; ?></b></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="5"></td>
                </tr>
                <tr>
                  <td></td>
                  <?php
                  $sql_subinfo = "select * from klienti where id = " . $row_exchange_info['id_klienti'];
                  $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                  $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                  $info = $row_rs_subinfo['emriplote'];
                  mysqli_free_result($rs_subinfo);
                  ?>
                  <td width="*" colspan="5">Klienti:&nbsp;<b><?php echo $info; ?></b></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <?php
                  $sql_subinfo = "select * from monedha where id = " . $row_exchange_det_info['id_mondebituar'];
                  $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                  $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                  $info = $row_rs_subinfo['monedha'];
                  mysqli_free_result($rs_subinfo);
                  ?>
                  <td>Bler&euml;:</td>
                  <td colspan="4" align="right"><b>
                      <font size="2"><?php echo number_format($row_exchange_det_info['vleftadebituar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?>
                    </b></font>
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <?php
                $sql_subinfo = "select * from monedha where id = " . $row_exchange_info['id_monkreditim'];
                $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                $info = $row_rs_subinfo['monedha'];
                mysqli_free_result($rs_subinfo);
                ?>
                <tr>
                  <td></td>
                  <td width="*" rowspan="2" valign="middle">Kursi</td>
                  <td width="*" valign="middle"></td>
                  <?php if ($row_exchange_det_info['kursi'] > $row_exchange_det_info['kursi1']) {  ?>
                    <td width="*" colspan="4"><?php echo $row_exchange_det_info['kursi_txt']; ?>&nbsp;=&nbsp;1&nbsp;/&nbsp;<font size="2"><?php echo $row_exchange_det_info['kursi']; ?></font>
                    </td>
                  <?php  } else {  ?>
                    <td width="*" colspan="4"><?php echo $row_exchange_det_info['kursi1_txt']; ?>&nbsp;=&nbsp;1&nbsp;/&nbsp;<font size="2"><?php echo $row_exchange_det_info['kursi1']; ?></font>
                    </td>
                  <?php  }  ?>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="1"></td>
                </tr>
                <tr>
                  <td></td>
                  <td>Total:</td>
                  <td colspan="4" align="right"><b>
                      <font size="1.7"><?php echo number_format($row_exchange_det_info['vleftakredituar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?>
                    </b></font>
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <!--
        <td colspan="3">Komisioni:</td>
        <td colspan="2" align="right">&nbsp;<b><?php echo $row_exchange_info['P&euml;rqindjekomisioni']; ?></b>&nbsp;%&nbsp;&nbsp;=&nbsp;<b><?php echo $row_exchange_info['vleftakomisionit']; ?></b></td>
      -->
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="1"></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="3">P&euml;r t'u paguar:</td>
                  <td colspan="2" align="right"><b>
                      <font size="2.5"><?php echo number_format($row_exchange_info['vleftapaguar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?></font>
                    </b></td>
                  <td></td>
                </tr>
                <tr>
                  <td width="5" height="1"></td>
                  <td width="10"></td>
                  <td width="5"></td>
                  <td width="30"></td>
                  <td width="70"></td>
                  <td width="55"></td>
                  <td width="5"></td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <table cellSpacing=0 cellPadding=0 width="300" border=0>
      <tbody style="font-weight: bolder;">
        <tr>
          <td height="15" colSpan=3 align="left" valign="middle">
            <div class=ctxheadingP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ju Faleminderit! - Thank you!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="Kthehu pas..." class="link4" href="exchange.php"><img src="images/down_arrow.gif" border="0"></a></div>
          </td>
        </tr>
      </tbody>
    </table>
  </body>

  </html>
<?php
// }

?>