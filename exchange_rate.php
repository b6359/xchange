<?php
include 'header.php';
$user_info = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

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
$row_filiali_info = $filiali_info->fetch_assoc();
?>

<div class="page-wrapper">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body d-flex align-items-center justify-content-between">
        <h4 class="card-title">
          <b>Kursi i këmbimit</b>
        </h4>
        <div>
          <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="printForm()">
            <i class="fas fa-print cursor-pointer"></i> Printo
          </a>
          <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="openAddExchangeRateModal('ins')">
            <i class="fas fa-plus cursor-pointer"></i>Shto kurs këmbimi
          </a>
        </div>
      </div>
      <div class="table-responsive" id="printable-table">
        <table class="table table-bordered">
          <form action="exchange_rate.php" method="POST" name="formmenu" target="_self">
            <tr>
              <td colspan="4">
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label">Filiali: </label>
                    <select name="id_llogfilial" class="form-select" id="id_llogfilial">
                      <?php
                      while ($row_filiali_info) {
                      ?>
                        <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $v_llog) {
                                                                                  echo "selected";
                                                                                } ?>><?php echo $row_filiali_info['filiali']; ?></option>
                      <?php
                        $row_filiali_info =  $filiali_info->fetch_assoc();
                      }
                      mysqli_free_result($filiali_info);
                      ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <input name="repdata" class="mt-4 btn btn-outline-primary" type="submit" value="Shfaq kursin...">
                  </div>
                </div>
              </td>
            </tr>
          </form>
          <?php
          $temp_v_wheresqls = isset($v_wheresqls) ? $v_wheresqls : "";
          $sql_info = "select k.*, (select f.filiali from filiali as f where f.id = k. id_llogfilial) as filiali from kursi_koka as k where id = (select max(id) from kursi_koka where 1=1 " . $temp_v_wheresqls . ") " . $temp_v_wheresqls;
          $h_menu = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
          $row_h_menu = $h_menu->fetch_assoc();
          $totalRows_h_menu = $h_menu->num_rows;

          if ($row_h_menu) { ?>
            <tr>
              <td colspan="4"> 
                <label>Dat&euml;: <b><?php echo substr($row_h_menu['date'], 8, 2) . "." . substr($row_h_menu['date'], 5, 2) . "." . substr($row_h_menu['date'], 0, 4); ?></b>
                  &nbsp; Fraksion kursi: <b><?php echo $row_h_menu['fraksion']; ?></b>
                  &nbsp; Llogari: <b><?php echo $row_h_menu['filiali']; ?></b></label>
              </td>
            </tr>
            <tr>
              <th> Monedha</th>
              <th align="center">Blej<br>Kundrejt LEK</th>
              <th align="center">Mesatar<br>Kundrejt LEK</th>
              <th align="center">Shes<br>Kundrejt LEK</th>
            </tr>
            <?php
            //mysql_select_db($database_MySQL, $MySQL);
            $data_sql_info = "select kursi_detaje.*, monedha.monedha from kursi_detaje, monedha where master_id = " . $row_h_menu['id'] . " and kursi_detaje.monedha_id = monedha.id order by monedha.taborder ";
            $h_data = $MySQL->query($data_sql_info) or die(mysqli_error($MySQL));
            $row_h_data = $h_data->fetch_assoc();

            while ($row_h_data) { ?>
              <tr>
                <td><b><?php echo $row_h_data['monedha']; ?></b></td>
                <td align="center"><b><?php echo number_format($row_h_data['kursiblerje'], 2, '.', ','); ?></b></td>
                <td align="center"><b><?php echo number_format($row_h_data['kursimesatar'], 2, '.', ','); ?></b></td>
                <td align="center"><b><?php echo number_format($row_h_data['kursishitje'], 2, '.', ','); ?></b></td>
              </tr>
          <?php $row_h_data = $h_data->fetch_assoc();
            };
          };
          mysqli_free_result($h_menu);
          ?>
        </table>
      </div>
      <div id="AddNewExchangeRateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddNewExchangeRateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="AddNewExchangeRateModalLabel">Kursi i këmbimit</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">

            </div>
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

        function openAddExchangeRateModal(action, id = null) {
          let url = 'ins_rate_data.php?action=' + action;
          if (id) {
            url += '&hid=' + id;
          }

          // Load content into modal
          $.get(url, function(data) {
            $('#AddNewExchangeRateModal .modal-body').html(data);
            $('#AddNewExchangeRateModal').modal('show');
          });
        }
      </script>