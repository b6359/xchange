<?php include 'header.php'; ?>
<?php


$query_klienti_info = "select * from klienti where (id > 100 or id = 1) order by id asc";
$klienti_info = mysqli_query($MySQL, $query_klienti_info) or die(mysqli_error($MySQL));
$row_klienti_info = $klienti_info->fetch_assoc();

?>

<div class="page-wrapper">
  <div class="container-fluid">
    <script language="JavaScript" src="calendar_eu.js"></script>
    <link rel="stylesheet" href="calendar.css">
    
    <div class="row">
      <div class="col-12">
        <form action="vle_view.php" method="POST" name="formmenu" target="_blank">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Raporti i veprimeve për vlera</h4>
              
              <div class="row mb-3">
                <label class="col-md-1 col-form-label"><b>Klienti:</b></label>
                <div class="col-md-6">
                  <div class="input-group">
                    <select name="id_klienti" id="id_klienti" class="form-select">
                      <option value="all">Të gjithë</option>
                      <?php
                      while ($row_klienti_info) {
                      ?>
                        <option value="<?php echo $row_klienti_info['id']; ?>"><?php echo $row_klienti_info['emriplote']; ?></option>
                      <?php
                        $row_klienti_info = $klienti_info->fetch_assoc();
                      }
                      mysqli_free_result($klienti_info);
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-1 col-form-label"><b>Vlera:</b></label>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text">Nga:</span>
                    <input name="p_vlera1" type="text" class="form-control" id="p_vlera1" value="0" maxlength="10">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text">Deri:</span>
                    <input name="p_vlera2" type="text" class="form-control" id="p_vlera2" value="9999999999" maxlength="10">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-1 col-form-label"><b>Data:</b></label>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text">Nga:</span>
                    <input name="p_date1" type="text" class="form-control" id="p_date1" value="<?php echo strftime('%d.%m.%Y'); ?>" maxlength="10">
                    <script language="JavaScript">
                      var o_cal = new tcal({
                        'formname': 'formmenu',
                        'controlname': 'p_date1'
                      });
                      o_cal.a_tpl.yearscroll = true;
                      o_cal.a_tpl.weekstart = 1;
                    </script>
                  </div>
                  <small class="text-muted">(dd.mm.yyyy)</small>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text">Deri:</span>
                    <input name="p_date2" type="text" class="form-control" id="p_date2" value="<?php echo strftime('%d.%m.%Y'); ?>" maxlength="10">
                    <script language="JavaScript">
                      var o_cal = new tcal({
                        'formname': 'formmenu',
                        'controlname': 'p_date2'
                      });
                      o_cal.a_tpl.yearscroll = true;
                      o_cal.a_tpl.weekstart = 1;
                    </script>
                  </div>
                  <small class="text-muted">(dd.mm.yyyy)</small>
                </div>
              </div>

              <hr>
              
              <div class="row">
                <div class="col-12 text-center">
                  <button type="submit" name="repdata" class="btn btn-primary">Shfaq raportin...</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>