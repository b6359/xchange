<?php
//initialize the session
require_once('ConMySQL.php');
session_start();
date_default_timezone_set('Europe/Tirane');
//------------------------------------------------------------------------------------------------
//                                                                                       //
//------------------------------------------------------------------------------------------------
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
//------------------------------------------------------------------------------------------------

if (isset($_SESSION['uid'])) {

  $v_wheresql = "";
  $v_llog = 0;

  if ($_SESSION['Usertype'] == 2)  $v_llog = $_SESSION['Userfilial'];
  if ($_SESSION['Usertype'] == 3)  $v_llog = $_SESSION['Userfilial'];
  if ($_SESSION['Usertype'] == 2)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";
  if ($_SESSION['Usertype'] == 3)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";
  if ($_SESSION['Usertype'] == 2)  $v_wheresqls = " and id_llogfilial = " . $_SESSION['Userfilial'] . " ";
  if ($_SESSION['Usertype'] == 3)  $v_wheresqls = " and id_llogfilial = " . $_SESSION['Userfilial'] . " ";

  if ((isset($_POST['id_llogfilial'])) && ($_POST['id_llogfilial'] != "")) {
    $v_wheresqls = " and id_llogfilial = " . $_POST['id_llogfilial'] . " ";
    $v_llog      = $_POST['id_llogfilial'];
  }

  $query_filiali_info = "select * from filiali " . $v_wheresql   . " order by filiali asc";
  $filiali_info = $MySQL->query($query_filiali_info) or die(mysqli_error($MySQL));
  $row_filiali_info =  $filiali_info->fetch_assoc();

  //----------------------------------------------------------------------------------------------------------------------------------------------
  if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {

    $v_insert = 1;
    //----------------------------------------------------------------------------------------------------------------------------------------------
    $mon_sql_info = "select monedha.id, monedha.monedha from monedha where mon_vendi = 'J'";
    $mon_data = $MySQL->query($mon_sql_info) or die(mysqli_error($MySQL));
    $row_mon_data = $mon_data->fetch_assoc();

    while ($row_mon_data) {
      /*
                if (($_POST[$row_mon_data['monedha'].'kursiblerje'] == "0") ||
                    ($_POST[$row_mon_data['monedha'].'kursiblerje'] == "0.0") ||
                    ($_POST[$row_mon_data['monedha'].'kursiblerje'] == "")       ) $v_insert = 0;

                if (($_POST[$row_mon_data['monedha'].'kursishitje'] == "0") ||
                    ($_POST[$row_mon_data['monedha'].'kursishitje'] == "0.0") ||
                    ($_POST[$row_mon_data['monedha'].'kursishitje'] == "")       ) $v_insert = 0;
*/
      $row_mon_data = $mon_data->fetch_assoc();
    };
    mysqli_free_result($mon_data);
    //----------------------------------------------------------------------------------------------------------------------------------------------

    if ($v_insert == 1) {

      //----------------------------------------------------------------------------------------------------------------------------------------------
      $sql_info = sprintf("select max(fraksion) frak_nr from kursi_koka where date = %s", GetSQLValueString($_POST['date'], "date"));
      $id_fraksion = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
      $row_id_fraksion = $id_fraksion->fetch_assoc();
      $frak_no = $row_id_fraksion['frak_nr'] + 1;
      mysqli_free_result($id_fraksion);
      //----------------------------------------------------------------------------------------------------------------------------------------------
      $user_info =  $_SESSION['Username'] ?? addslashes($_SESSION['Username']);
      //----------------------------------------------------------------------------------------------------------------------------------------------
      $insertSQL = sprintf(
        "INSERT INTO kursi_koka (date, id_llogfilial, fraksion, perdoruesi) VALUES (%s, %s, %s, %s)",
        GetSQLValueString($_POST['date'], "date"),
        GetSQLValueString($_POST['id_llogfilial'], "int"),
        GetSQLValueString($frak_no, "int"),
        GetSQLValueString($user_info, "text")
      );

      //            $Result1 = mysql_query($insertSQL, $MySQL) or die(mysql_error());
      if (mysqli_query($MySQL, $insertSQL)) {
        $last_id = mysqli_insert_id($MySQL);
        $Result1 = mysqli_query($MySQL, "SELECT * FROM kursi_koka WHERE id = $last_id");
      }
      //----------------------------------------------------------------------------------------------------------------------------------------------
      $sql_info = "select max(id) id_trans from kursi_koka";
      $id_trans = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
      $row_id_trans = $id_trans->fetch_assoc();
      $trans_no = $row_id_trans['id_trans'];
      mysqli_free_result($id_trans);
      //----------------------------------------------------------------------------------------------------------------------------------------------
      $mon_sql_info = "select monedha.id, monedha.monedha from monedha where mon_vendi = 'J'";
      $mon_data = $MySQL->query($mon_sql_info) or die(mysqli_error($MySQL));
      $row_mon_data = $mon_data->fetch_assoc();

      while ($row_mon_data) {

        //----------------------------------------------------------------------------------------------------------------------------------------------
        $kursiblerje = isset($_POST[$row_mon_data['monedha'] . 'kursiblerje']) ? (float) $_POST[$row_mon_data['monedha'] . 'kursiblerje'] : 0;
        $kursishitje = isset($_POST[$row_mon_data['monedha'] . 'kursishitje']) ? (float) $_POST[$row_mon_data['monedha'] . 'kursishitje'] : 0;

        $kursimesatar = ($kursiblerje + $kursishitje) / 2;

        $insertSQL = sprintf(
          "INSERT INTO kursi_detaje (master_id, monedha_id, kursiblerje, kursimesatar, kursishitje) VALUES (%s, %s, %s, %s, %s)",
          GetSQLValueString($trans_no, "int"),
          GetSQLValueString($row_mon_data['id'], "int"),
          GetSQLValueString($kursiblerje, "double"),
          GetSQLValueString($kursimesatar, "double"),
          GetSQLValueString($kursishitje, "double")
        );

        if (mysqli_query($MySQL, $insertSQL)) {
          $last_id = mysqli_insert_id($MySQL);
          $Result1 = mysqli_query($MySQL, "SELECT * FROM kursi_detaje WHERE id = $last_id");
        }
        //----------------------------------------------------------------------------------------------------------------------------------------------

        $row_mon_data = $mon_data->fetch_assoc();
      };
      mysqli_free_result($mon_data);
      //----------------------------------------------------------------------------------------------------------------------------------------------
      $mon_sql_info = "select monedha.id, monedha.monedha from monedha where mon_vendi = 'J' and monedha like 'EUR%'";
      $mon_data = $MySQL->query($mon_sql_info) or die(mysqli_error($MySQL));
      $row_mon_data = $mon_data->fetch_assoc();

      while ($row_mon_data) {

        //----------------------------------------------------------------------------------------------------------------------------------------------
        $kursiblerje = isset($_POST['e' . $row_mon_data['monedha'] . 'kursiblerje']) ? (float) $_POST['e' . $row_mon_data['monedha'] . 'kursiblerje'] : 0;
        $kursimesatar = isset($_POST['e' . $row_mon_data['monedha'] . 'kursimesatar']) ? (float) $_POST['e' . $row_mon_data['monedha'] . 'kursimesatar'] : 0;
        $kursishitje = isset($_POST['e' . $row_mon_data['monedha'] . 'kursishitje']) ? (float) $_POST['e' . $row_mon_data['monedha'] . 'kursishitje'] : 0;

        $insertSQL = sprintf(
          "INSERT INTO kursi_eurusd (master_id, monedha_id, kursiblerje, kursimesatar, kursishitje) VALUES (%s, %s, %s, %s, %s)",
          GetSQLValueString($trans_no, "int"),
          GetSQLValueString($row_mon_data['id'], "int"),
          GetSQLValueString($kursiblerje, "double"),
          GetSQLValueString($kursimesatar, "double"),
          GetSQLValueString($kursishitje, "double")
        );

        // $Result1 = mysql_query($insertSQL, $MySQL) or die(mysql_error());
        //----------------------------------------------------------------------------------------------------------------------------------------------

        $row_mon_data =  $mon_data->fetch_assoc();
      };
      mysqli_free_result($mon_data);
      //----------------------------------------------------------------------------------------------------------------------------------------------
    }
    //----------------------------------------------------------------------------------------------------------------------------------------------
    $updateGoTo = "exchange_rate.php";
    //if (isset($_SERVER['QUERY_STRING'])) {
    //$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //$updateGoTo .= $_SERVER['QUERY_STRING'];
    //}
    header(sprintf("Location: %s", $updateGoTo));
    //----------------------------------------------------------------------------------------------------------------------------------------------
  }
  //----------------------------------------------------------------------------------------------------------------------------------------------


?>


  <form id="exchangeForm" enctype="multipart/form-data" action="ins_rate_data.php" method="post" name="formmenu">
    <input id="formAction" name="form_action" type="hidden" value="src">

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex align-items-center gap-3">
                <div class="form-group">
                    <label for="date" class="form-label">Datë:</label>
                    <div class="input-group input-group-sm">
                        <input name="date" 
                               type="text" 
                               value="<?php echo strftime('%Y-%m-%d'); ?>" 
                               id="date" 
                               class="form-control" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_llogfilial" class="form-label">Filiali:</label>
                    <div class="input-group input-group-sm">
                        <select name="id_llogfilial" 
                                id="id_llogfilial" 
                                class="form-select">
                            <?php while ($row_filiali_info) { ?>
                                <option value="<?php echo $row_filiali_info['id']; ?>" 
                                        <?php if ($row_filiali_info['id'] == $v_llog) echo "selected"; ?>>
                                    <?php echo $row_filiali_info['filiali']; ?>
                                </option>
                            <?php 
                                $row_filiali_info = $filiali_info->fetch_assoc();
                            }
                            mysqli_free_result($filiali_info);
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group align-self-end">
                    <button type="submit" 
                            name="repdata" 
                            class="btn btn-primary btn-sm">
                        Shfaq kursin...
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Monedha</th>
                        <th class="text-center">Blej<br>Kundrejt LEK</th>
                        <th class="text-center">Shes<br>Kundrejt LEK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $temp_v_wheresqls = isset($v_wheresqls) ? $v_wheresqls : "";
                    $sql_info = "select * from kursi_koka where id = (select max(id) from kursi_koka where 1=1 " . $temp_v_wheresqls . ") " . $temp_v_wheresqls;
                    $h_menu = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
                    $row_h_menu = $h_menu->fetch_assoc();

                    $data_sql_info = "select monedha.monedha from monedha where mon_vendi = 'J' order by taborder";
                    $h_data = $MySQL->query($data_sql_info) or die(mysqli_error($MySQL));
                    $row_h_data = $h_data->fetch_assoc();

                    while ($row_h_data) {
                        $data2_sql_info = "select kursi_detaje.*, monedha.monedha from kursi_detaje, monedha where master_id = " . $row_h_menu['id'] . " and kursi_detaje.monedha_id = monedha.id and monedha.monedha = '" . $row_h_data['monedha'] . "' ";
                        $h_data2 = $MySQL->query($data2_sql_info) or die(mysqli_error($MySQL));
                        $row_h_data2 = $h_data2->fetch_assoc();
                    ?>
                        <tr>
                            <td class="text-center"><strong><?php echo $row_h_data['monedha']; ?></strong></td>
                            <td class="text-center">
                                <input name="<?php echo $row_h_data['monedha']; ?>kursiblerje" 
                                       type="text" 
                                       class="form-control form-control-sm" 
                                       id="<?php echo $row_h_data['monedha']; ?>kursiblerje" 
                                       value="<?php echo isset($row_h_data2['kursiblerje']) ? number_format($row_h_data2['kursiblerje'], 2, '.', ',') : ''; ?>">
                            </td>
                            <td class="text-center">
                                <input name="<?php echo $row_h_data['monedha']; ?>kursishitje" 
                                       type="text" 
                                       class="form-control form-control-sm" 
                                       id="<?php echo $row_h_data['monedha']; ?>kursishitje" 
                                       value="<?php echo isset($row_h_data2['kursishitje']) ? number_format($row_h_data2['kursishitje'], 2, '.', ',') : ''; ?>">
                            </td>
                        </tr>
                    <?php 
                        $row_h_data = $h_data->fetch_assoc();
                    }
                    mysqli_free_result($h_data);
                    ?>
                </tbody>
            </table>
        </div>

        <div id="datetrans" class="d-none position-absolute bg-white"></div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <button type="button" class="btn btn-success" 
                        onclick="submitForm();">
                    Ruaj kursin e këmbimit
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function submitForm() {
    const form = document.getElementById('exchangeForm');
    const formAction = document.getElementById('formAction');
    if (form && formAction) {
        formAction.value = 'ins';
        form.submit();
    }
}
</script>

<?php
}
?>