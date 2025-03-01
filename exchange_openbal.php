<?php


if ((isset($_SESSION['uid'])) && ($_SESSION['Usertype'] != 3)) {
  $user_info = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

  $v_dt = strftime('%d.%m.%Y');
  if ((isset($_POST['date_trans'])) && ($_POST['date_trans'] != "")) {
    $v_dt = $_POST['date_trans'];
  }
  if ((isset($_GET['dt'])) && ($_GET['dt'] != "")) {
    $v_dt = $_GET['dt'];
  }

?>
  <?php
  if (isset($_GET['action']) && ($_GET['action'] == "del")) {
    $sql_info = "DELETE FROM openbalance WHERE id = " . $_GET['hid'];
    $result = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
  }
  ?>

  <script language="JavaScript" src="calendar_eu.js"></script>
  <link rel="stylesheet" href="calendar.css">

  <form enctype="multipart/form-data" ACTION="exchange_openbal.php" METHOD="POST" name="formmenu" id="formmenu">
    <div class="col-md-6 ps-7 mb-3">
      <div class="d-flex align-items-center">
        <label for="date_trans">Dat&euml;:</label>
        <input name="date_trans" class="form-control" type="text" value="<?php echo $v_dt; ?>" id="date_trans" size="10" reandonly>&nbsp;
        <script language="JavaScript">
          var o_cal = new tcal({
            'formname': 'formmenu',
            'controlname': 'date_trans'
          });
          o_cal.a_tpl.yearscroll = true;
          o_cal.a_tpl.weekstart = 1;
        </script>
        <input name="insupd" class="btn btn-outline-primary ms-3" type="button" value=" Shfaq informacionin " onClick="JavaScript: document.formmenu.submit(); ">
      </div>
    </div>
    <div class="responsive-table m-3">
      <table class="table table-bordered">
        <tr>
          <td><b>Llogaria</b></td>
          <td><b>Monedha</b></td>
          <td align="right"><b>Vlera</b>&nbsp; &nbsp; &nbsp; </td>
          <th class="OraColumnHeader"> </th>
        </tr>
        <?php

        set_time_limit(0);

        $v_wheresql = "";
        //if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and openbalance.id_llogfilial = ". $_SESSION['Userfilial'] ." ";
        if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and openbalance.perdoruesi    = '" . $_SESSION['Username'] . "' ";

        //mysql_select_db($database_MySQL, $MySQL);
        $query_gjendje_info = " SELECT openbalance.id, filiali.filiali, monedha.monedha, openbalance.vleftakredituar
                  FROM openbalance, monedha, filiali
                 WHERE openbalance.monedha_id    = monedha.id
                   AND openbalance.id_llogfilial = filiali.id
                   and openbalance.id            > (select max(id_opb) from systembalance)
                   AND openbalance.date_trans    = '" . substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2) . "'
                    " . $v_wheresql . "
              ORDER BY filiali.filiali, openbalance.monedha_id ";
        $gjendje_info     = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
        $row_gjendje_info = mysqli_fetch_assoc($gjendje_info);

        while ($row_gjendje_info) {
        ?>
          <tr>
            <td><?php echo $row_gjendje_info['filiali']; ?></td>
            <td><?php echo $row_gjendje_info['monedha']; ?></td>
            <td align="right"><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?>&nbsp; &nbsp;</td>
            <td width="20">
              <a title="Modifiko Informacionin" href="JavaScript: openOpenBalanceModal('upd', <?php echo $row_gjendje_info['id']; ?>); "><img src="images/edit.gif" border="0"></a>
              <a title="Fshij Informacionin" href="JavaScript: do_deleteOpenBalance(<?php echo $row_gjendje_info['id']; ?>); "><img src="images/del.gif" border="0"></a>
            </td>
          </tr>
        <?php $row_gjendje_info = mysqli_fetch_assoc($gjendje_info);
        }
        mysqli_free_result($gjendje_info);
        // ---------------------------------------------------------------------------------
        ?>
      </table>
    </div>
  </form>
  <div class="modal fade" id="openBalanceModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="openBalanceModalLabel">Hapja e balancave</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
          <!-- Content will be loaded here -->
        </div>
      </div>
    </div>
  </div>
<?php
} else {
  header("Location: exchange_account.php");
}
?>

<script>
  function printOpenBalance() {
    var printContents = document.getElementById('formmenu').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }

  function openOpenBalanceModal(action, id = null) {
    let url = 'insupd_openbal_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#openBalanceModal .modal-body').html(data);
      $('#openBalanceModal').modal('show');
    });
  }

  function do_deleteOpenBalance(val1, val2) {
    var flag = false;
    flag = confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!. ');
    if (flag == true) {
      window.location = 'exchange_openbal.php?action=del&hid=' + val1 + '&dt=' + val2;
    }
  }
</script>