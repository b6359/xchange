<?php
require_once('ConMySQL.php');
// Handle currency deletion (commented out for safety)
if (isset($_GET['action']) && $_GET['action'] === "del") {
  // Modern way to handle deletion with prepared statement
  /*
        $sql_info = "DELETE FROM monedha WHERE id = ?";
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
        */
}
?>


<script LANGUAGE="JavaScript">
  function do_deleteCurrency(value) {
    var flag = false;

    flag = confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!. ');

    if (flag == true) {
      window.location = 'exchange_currency.php?action=del&hid=' + value;
    }
  }
  function openAddCurrencyModal(action, id = null) {
    let url = 'insupd_currency_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#AddCurrencyModal .modal-body').html(data);
      $('#AddCurrencyModal').modal('show');
    });
  }
</script>
<div class="responsive-table">
  <table class="table table-bordered">
    <tr>
      <th> Monedha </th>
      <th> P&euml;rshkrimi </th>
      <th> M. Lokale </th>
      <th> Simboli </th>
      <th> Veprimi</th>
    </tr>
    <?php
    //mysql_select_db($database_MySQL, $MySQL);
    $sql_info = "select * from monedha order by mon_vendi desc, id";
    $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
    $row_h_menu = $h_menu->fetch_assoc();
    $totalRows_h_menu = $h_menu->num_rows;

    while ($row_h_menu) { ?>
      <tr>
        <td><?php echo $row_h_menu['monedha']; ?></td>
        <td><?php echo $row_h_menu['pershkrimi']; ?></td>
        <td align="center"><?php echo $row_h_menu['mon_vendi']; ?></td>
        <td align="center"><?php echo $row_h_menu['simboli']; ?></td>
        <td class="d-flex justify-content-evenly">
            <div class="cursor-pointer" onclick="openAddCurrencyModal('upd', <?php echo $row_h_menu['id']; ?>)">
              <i class="fa fa-pen-square"></i>
            </div>
            <div class="cursor-pointer" onClick="JavaScript: do_deleteCurrency(<?php echo $row_h_menu['id']; ?>);">
              <i class="fa fa-trash text-danger"></i>
            </div>
          </td>
      </tr>
    <?php $row_h_menu = $h_menu->fetch_assoc();
    };
    mysqli_free_result($h_menu);
    ?>
  </table>
</div>
<div class="modal fade" id="AddCurrencyModal" tabindex="-1" role="dialog" aria-labelledby="AddCurrencyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AddCurrencyModalLabel">Administrimi i monedhave</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>