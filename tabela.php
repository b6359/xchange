<?php

session_start();
require_once('ConMySQL.php');

// Global variables replaced with $_SESSION
$_SESSION['CNAME'] = "EXCHANGE";
$_SESSION['CADDR'] = "Tiranë";
$_SESSION['CNIPT'] = "A12345678B";
$_SESSION['CADMI'] = "Administrator";
$_SESSION['CMOBI'] = "+355 69 123 4567";
$_SESSION['DPPPP'] = "1000000";

$v_wheresql = "";
$v_wheresqls = "";
$v_llog = 0;

if (isset($_SESSION['Usertype'])) {
  if ($_SESSION['Usertype'] == 2 || $_SESSION['Usertype'] == 3) {
    $v_llog = $_SESSION['Userfilial'];
    $v_wheresql = " WHERE id = " . $_SESSION['Userfilial'];
    $v_wheresqls = " AND id_llogfilial = " . $_SESSION['Userfilial'];
  }
}
?>

<script>
 document.getElementById("tableContainer").addEventListener("click", function() {
    var el = document.documentElement;
    var rfs = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen;
    if (rfs) {
      rfs.call(el);
    }
  });
</script>

<div class="container" id="tableContainer">
  <div class="row">
    <div class="col-12">
      <!-- Date and Time -->
      <div class="row mb-3">
        <div class="col-6 text-center">
          <i class="fa fa-calendar"></i> <b><?php echo date('d.m.Y'); ?></b>
        </div>
        <div class="col-6 text-center">
          <i class="fa fa-watch"></i> <b><?php echo date('H:i:s'); ?></b>
        </div>
      </div>

      <?php
      $sql_info = "SELECT k.* FROM kursi_koka AS k 
                   WHERE id = (SELECT MAX(id) FROM kursi_koka WHERE 1=1 $v_wheresqls) $v_wheresqls";

      $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
      $row_h_menu = mysqli_fetch_assoc($h_menu);

      if ($row_h_menu) { ?>
        <!-- Currency Exchange Table -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="bg-primary text-white">
              <tr>
                <th class="text-center" width="25%">MONEDHA</th>
                <th class="text-center" width="25%">CODE</th>
                <th class="text-center" width="25%">BLIHET</th>
                <th class="text-center" width="25%">SHITET</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $data_sql_info = "SELECT kursi_detaje.*, monedha.monedha 
                            FROM kursi_detaje, monedha 
                            WHERE master_id = {$row_h_menu['id']} 
                            AND kursi_detaje.monedha_id = monedha.id 
                            AND kursimesatar > 0 
                            ORDER BY kursi_detaje.monedha_id";

              $h_data = mysqli_query($MySQL, $data_sql_info) or die(mysqli_error($MySQL));
              
              while ($row_h_data = mysqli_fetch_assoc($h_data)) { ?>
                <tr>
                  <td class="text-center"><img src="images/flag/<?php echo $row_h_data['monedha']; ?>.png" width="50"></td>
                  <td><b><?php echo $row_h_data['monedha']; ?></b></td>
                  <td class="text-center"><b><?php echo number_format($row_h_data['kursiblerje'], 2, '.', ','); ?></b></td>
                  <td class="text-center"><b><?php echo number_format($row_h_data['kursishitje'], 2, '.', ','); ?></b></td>
                </tr>
              <?php }
              mysqli_free_result($h_data);
              ?>
            </tbody>
          </table>
        </div>
      <?php }
      mysqli_free_result($h_menu);
      ?>

      <!-- Welcome Message -->
      <div class="text-center mt-3">
        <b>Mirë se vini! | No Commission</b>
      </div>
    </div>
  </div>
</div>
