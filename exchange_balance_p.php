<?php

include 'header.php';
?>

<div class="page-wrapper">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h4 class="card-title">
            <b>Bilanci sipas Llogarive</b>
          </h4>
          <button class="btn btn-outline-primary" onclick="printForm()">
            <i class="fas fa-print cursor-pointer"></i> Printo
          </button>
        </div>
        <div class="table-responsive" id="printable-table">
          <table class="table border table-striped table-bordered text-nowrap" style="width:100%">
            <thead>
              <tr>
                <th>Llogaria</th>
                <th>Monedha</th>
                <th class="text-end">Debitim</th>
                <th class="text-end">Kreditim</th>
              </tr>
            </thead>
            <tbody>
              <?php
              set_time_limit(0);

              $v_wheresql = "";
              if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and ek.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
              if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and ek.perdoruesi    = '" . $_SESSION['Username'] . "' ";

              $query_gjendje_info = " select tab_info.llogaria, tab_info.monedha, sum(tab_info.vleftakredituar) vleftakredituar, sum(tab_info.vleftadebituar) vleftadebituar
                                      from (
                                             select ek.id_llogkomision llogaria, m1.monedha, sum(ek.vleftakomisionit) vleftakredituar, sum(0) vleftadebituar
                                               from exchange_koke ek, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                           group by ek.id_llogkomision, m1.monedha
                                             having (sum(ek.vleftakomisionit) <> 0)
                                          union all
                                             select filiali.filiali llogaria, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                               from exchange_koke ek, filiali, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.id_llogfilial  = filiali.id
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                           group by filiali.filiali, m1.monedha
                                          union all
                                             select filiali.filiali llogaria, m1.monedha, sum(0) vleftakredituar, sum( ed.vleftadebituar ) vleftadebituar
                                               from exchange_koke ek, exchange_detaje ed, filiali, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.id             = ed.id_exchangekoke
                                                and ek.id_llogfilial  = filiali.id
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ed.id_mondebituar = m1.id " . $v_wheresql . "
                                           group by filiali.filiali, m1.monedha
                                       ) tab_info
                               group by tab_info.llogaria, tab_info.monedha
                               order by tab_info.llogaria, tab_info.monedha";
              $gjendje_info = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              
              while ($row_gjendje_info = mysqli_fetch_assoc($gjendje_info)) { ?>
                <tr>
                  <td><?php echo $row_gjendje_info['llogaria']; ?></td>
                  <td><?php echo $row_gjendje_info['monedha']; ?></td>
                  <td class="text-end"><?php echo number_format($row_gjendje_info['vleftadebituar'], 2, '.', ','); ?></td>
                  <td class="text-end"><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?></td>
                </tr>
              <?php }
              mysqli_free_result($gjendje_info);
              ?>
            </tbody>
            <tfoot>
              <?php
              $query_gjendje_info = " select tab_info.monedha, sum(tab_info.vleftakredituar) vleftakredituar, sum(tab_info.vleftadebituar) vleftadebituar
                                      from  (

                                             select ek.id_llogkomision llogaria, m1.monedha, sum(ek.vleftakomisionit) vleftakredituar, sum(0) vleftadebituar
                                               from exchange_koke ek, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                           group by ek.id_llogkomision, m1.monedha
                                             having (sum(ek.vleftakomisionit) <> 0)
                                          union all
                                             select filiali.filiali llogaria, m1.monedha, sum(ek.vleftapaguar) vleftakredituar, sum(0) vleftadebituar
                                               from exchange_koke ek, filiali, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.id_llogfilial  = filiali.id
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ek.id_monkreditim = m1.id " . $v_wheresql . "
                                           group by filiali.filiali, m1.monedha
                                          union all
                                             select filiali.filiali llogaria, m1.monedha, sum(0) vleftakredituar, sum( ed.vleftadebituar ) vleftadebituar
                                               from exchange_koke ek, exchange_detaje ed, filiali, monedha m1
                                              where ek.chstatus       = 'T'
                                                and ek.id             = ed.id_exchangekoke
                                                and ek.id_llogfilial  = filiali.id
                                                and ek.date_trans >= '" . gmstrftime("%Y-", time()) . "01-01'
                                                and ek.date_trans <= '" . gmstrftime("%Y-", time()) . "12-31'
                                                and ed.id_mondebituar = m1.id " . $v_wheresql . "
                                           group by filiali.filiali, m1.monedha
                                      ) tab_info
                             group by tab_info.monedha
                             order by tab_info.monedha ";
              $gjendje_info = mysqli_query($MySQL, $query_gjendje_info) or die(mysqli_error($MySQL));
              
              while ($row_gjendje_info = mysqli_fetch_assoc($gjendje_info)) { ?>
                <tr>
                  <td></td>
                  <td><strong><?php echo $row_gjendje_info['monedha']; ?></strong></td>
                  <td class="text-end"><strong><?php echo number_format($row_gjendje_info['vleftadebituar'], 2, '.', ','); ?></strong></td>
                  <td class="text-end"><strong><?php echo number_format($row_gjendje_info['vleftakredituar'], 2, '.', ','); ?></strong></td>
                </tr>
              <?php }
              mysqli_free_result($gjendje_info);
              ?>
            </tfoot>
          </table>
        </div>
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
  </script>