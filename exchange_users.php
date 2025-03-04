<?php include 'header.php'; ?>

<?php

  $user_id = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

  if ("1" != $_SESSION['Usertype']) {

    $logoutGoTo = "info.php";
    header("Location: $logoutGoTo");
    exit;
  }

?>

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between" >
                <h4 class="card-title">
                    <b>Lista e përdoruesve</b>
                </h4>
                <div>
                    <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="printForm()">
                        <i class="fas fa-print cursor-pointer"></i> Printo
                    </a>
                    <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="openAddUserModal('ins')">
                        <i class="fas fa-plus cursor-pointer"></i>Shto përdorues të ri
                    </a>
                </div>
            </div>
            <div class="table-responsive" id="printable-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <td scope="col">P&euml;rdoruesi</td>
                            <td scope="col">Emri i plot&euml;</td>
                            <td scope="col"></td>
                            <td scope="col">Llogaria</td>
                            <td scope="col">Tipi</td>
                            <td scope="col"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $data_sql_info = "select app_user.*, filiali.filiali, usertype.description from app_user, filiali, usertype where app_user.id_filiali = filiali.id and app_user.id_usertype = usertype.id";
                            $h_data = $MySQL->query($data_sql_info) or die(mysqli_error($MySQL));
                            $row_h_data = $h_data->fetch_assoc();

                            while ($row_h_data) { ?>
                                <tr>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['username']; ?>
                                        </b>
                                    </td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['full_name']; ?>
                                        </b>
                                    </td>
                                    <td></td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['filiali']; ?>
                                        </b>
                                    </td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['description']; ?>
                                        </b>
                                    </td>
                                    <td align="center">
                                        <div class="cursor-pointer" onClick="openAddUserModal('upd', <?php echo $row_h_data['id']; ?>)">
                                            <i class="fa fa-pen-square"></i>
                                        </div>
                                    </td>
                                </tr>
                        <?php $row_h_data = $h_data->fetch_assoc();
                        };
                        mysqli_free_result($h_data);
                        ?>                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="AddNewUserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddNewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="AddNewUserModalLabel">Filiali</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                  
                </div>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
<script>
    function printForm() {
      var printContents = document.getElementById('printable-table').innerHTML;
      var originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  function openAddUserModal(action, id = null) {
    let url = 'insupd_user_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#AddNewUserModal .modal-body').html(data);
      $('#AddNewUserModal').modal('show');
    });
  }
</script>