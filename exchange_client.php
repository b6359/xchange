<?php

// Handle client deletion with prepared statement
if (isset($_GET['action']) && $_GET['action'] === "del") {
  $sql_info = "DELETE FROM klienti 
                     WHERE id = ? 
                     AND (SELECT COUNT(*) 
                         FROM exchange_koke 
                         WHERE exchange_koke.id_klienti = klienti.id) = 0";

  $stmt = mysqli_prepare($MySQL, $sql_info);
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $_GET['hid']);

    if (!mysqli_stmt_execute($stmt)) {
      die('Error executing query: ' . mysqli_error($MySQL));
    }
    mysqli_stmt_close($stmt);
  } else {
    die('Error preparing statement: ' . mysqli_error($MySQL));
  }
}
?>

<script>
  function openClientModal(action, id = null) {
    let url = 'insupd_client_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#clientModal .modal-body').html(data);
      $('#clientModal').modal('show');
    });
  }

  function do_deleteClient(value) {
    var flag = false;
    flag = confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!. ');
    if (flag == true) {
      window.location = 'exchange_client.php?action=del&hid=' + value;
    }
  }
</script>

<form ACTION="exchange_client.php" METHOD="POST" name="formmenu">
  <div class="col-md-3 ps-7 mb-3">
    <input name="emri" type="text" id="emri" class="form-control" placeholder="Kerko" value="" onChange="JanaScript: document.formmenu.submit();" size="35">
  </div>
  <div class="responsive-table">
    <table class="table table-bordered">
      <tr>
        <th> Emri </th>
        <th> Mbiemri </th>
        <th> Kompani</th>
        <th> Telefon </th>
        <th> Nr. Dokumenti </th>
        <th> Dokumenti </th>
        <th>Veprimi</th>
      </tr>
      <?php

      $where = " WHERE TRUE ";
      if ((isset($_POST["emri"])) && ($_POST["emri"] != "")) {
        $where = " WHERE emri like '%" . $_POST["emri"] . "%' or mbiemri like '%" . $_POST["emri"] . "%' ";
      }
      $rec_limit = 50;
      $sql = "SELECT count(id) FROM klienti " . $where;
      $retval = mysqli_query($MySQL, $sql);
      if (! $retval) {
        die('Could not get data: ' . mysqli_error($MySQL));
      }
      $row = mysqli_fetch_array($retval, MYSQLI_NUM);
      $rec_count = $row[0];

      if (isset($_GET['page'])) {
        $page = $_GET['page'] + 1;
        $offset = $rec_limit * $page;
      } else {
        $page = 0;
        $offset = 0;
      }
      $left_rec = $rec_count - ($page * $rec_limit);

      $sql_info = "SELECT id, emri, mbiemri, emrikompanise, telefon, nrpashaporte, docname " .
        "FROM klienti " .
        $where .
        " order by emri, mbiemri " .
        "LIMIT $offset, $rec_limit";
      $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
      $row_h_menu = $h_menu->fetch_assoc();
      $totalRows_h_menu = $h_menu->num_rows;

      while ($row_h_menu) { ?>
        <tr>
          <td><?php echo $row_h_menu['emri']; ?></td>
          <td><?php echo $row_h_menu['mbiemri']; ?></td>
          <td><?php echo $row_h_menu['emrikompanise']; ?></td>
          <td><?php echo $row_h_menu['telefon']; ?></td>
          <td><?php echo $row_h_menu['nrpashaporte']; ?></td>
          <td><a href="doc/<?php if ($row_h_menu['docname'] == "") {
                              echo "bosh.png";
                            } else {
                              echo $row_h_menu['docname'];
                            } ?>" target="_blank"><img src="doc/<?php if ($row_h_menu['docname'] == "") {
                                                                  echo "bosh.png";
                                                                } else {
                                                                  echo $row_h_menu['docname'];
                                                                } ?>" border="0" width="25px"></a></td>
          <td class="d-flex justify-content-between">
            <div class="cursor-pointer" onclick="openClientModal('upd', <?php echo $row_h_menu['id']; ?>)">
              <i class="fa fa-pen-square"></i>
            </div>
            <div class="cursor-pointer" onClick="JavaScript: do_deleteClient(<?php echo $row_h_menu['id']; ?>);">
              <i class="fa fa-trash text-danger"></i>
            </div>
          </td>
        </tr>
      <?php $row_h_menu = $h_menu->fetch_assoc();
      };
      mysqli_free_result($h_menu);
      ?>
      <tr>
        <td height="1" colspan="8">
          <?php
          if ($page > 0) {
            $last = $page - 2;
            echo "<a href=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "?page=$last\">Last 50 Records</a> |";
            //   echo "<a href=\"$_PHP_SELF?page=$page\">Next 50 Records</a>";
            echo "<a href=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "?page=$page\">Next 50 Records</a>";
          } else if ($page == 0) {
            // echo "<a href=\"$_PHP_SELF?page=$page\">Next 50 Records</a>";
            echo "<a href=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "?page=$page\">Next 50 Records</a>";
          } else if ($left_rec < $rec_limit) {
            $last = $page - 2;
            //echo "<a href=\"$_PHP_SELF?page=$last\">Last 50 Records</a>";
            echo "<a href=\"" . htmlspecialchars($_SERVER['PHP_SELF']) . "?page=$last\">Last 50 Records</a>";
          }
          ?>
        </td>
      </tr>
    </table>
  </div>
</form>

<div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientModalLabel">Administrimi i klientëve</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>