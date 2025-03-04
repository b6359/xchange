  <?php
  include 'ConMySQL.php';
  if (isset($_GET['action']) && ($_GET['action'] == "del")) {
    $sql_info = "DELETE FROM llogarite 
      WHERE id = " . $_GET['hid'] . "
      AND (SELECT COUNT(*) FROM exchange_koke WHERE exchange_koke.id_llogfilial = llogarite.id) = 0";
    $result = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
    echo "<script>window.location.href = 'exchange_account.php';</script>";
  }
  ?>
  <!-- //insupd_llogari_data.php?action=ins   Shto Llogari-->
  <div class="responsive-table">
    <table class="table table-bordered">
      <tr>
        <th class="OraColumnHeader"> Kodi </th>
        <th class="OraColumnHeader"> Llogaria </th>
        <th class="OraColumnHeader"> Aktive/Pasive </th>
        <th class="OraColumnHeader"> Veprimi (D/C) </th>
        <th class="OraColumnHeader"> </th>
      </tr>
      <?php

      $sql_info = "select * from llogarite order by kodi asc";
      $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
      $row_h_menu = $h_menu->fetch_assoc();
      $totalRows_h_menu = $h_menu->num_rows;

      while ($row_h_menu) { ?>
        <tr>
          <td align="center"><?php echo $row_h_menu['kodi']; ?></td>
          <td align="center"><?php echo $row_h_menu['llogaria']; ?></td>
          <td align="center"><?php echo $row_h_menu['tipi']; ?></td>
          <td align="center"><?php echo $row_h_menu['veprimi']; ?></td>
          <td class="d-flex justify-content-between">
            <div class="cursor-pointer" onclick="openAccountsModal('upd', <?php echo $row_h_menu['id']; ?>)">
              <i class="fa fa-pen-square"></i>
            </div>
            <div class="cursor-pointer" onClick="JavaScript: do_deleteAccount(<?php echo $row_h_menu['id']; ?>);">
              <i class="fa fa-trash text-danger"></i>
            </div>
          </td>
        </tr>
      <?php $row_h_menu = $h_menu->fetch_assoc();;
      };
      mysqli_free_result($h_menu);
      ?>
    </table>
  </div>
  <div class="modal fade" id="accountsModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accountsModalLabel">Administrimi i llogarive</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
  function openAccountsModal(action, id = null) {
    let url = 'insupd_llogari_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#accountsModal .modal-body').html(data);
      $('#accountsModal').modal('show');
    });
  }

  function do_deleteAccount(value) {
    var flag = false;
    flag = confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!. ');
    if (flag == true) {
      window.location = 'exchange_llogari.php?action=del&hid=' + value;
    }
  }
</script>