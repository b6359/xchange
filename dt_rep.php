<?php include 'header.php'; ?>
<div class="page-wrapper">
  <div class="container-fluid">
    <script language="JavaScript" src="calendar_eu.js"></script>
    <link rel="stylesheet" href="calendar.css">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Raporti i veprimeve ditore/periodike</h4>
            <form action="dtr_view.php" method="POST" name="formmenu" target="_blank">
              <input name="view" type="hidden" value="n/e">
              <div class="form-body">
                <div class="row">
                  <label class="form-label">Llogaria</label>
                  <div class="col-md-5">
                    <div class="input-group mb-3">
                      <select name="id_llogfilial" id="id_llogfilial" class="form-select">
                        <?php
                        $query_filiali_info = "select * from filiali order by filiali asc";
                        $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
                        $row_filiali_info = $filiali_info->fetch_assoc();

                        while ($row_filiali_info) {
                        ?>
                          <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) echo "selected"; ?>>
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
                  <div class="col-md-1">
                    <a class="btn btn-outline-primary" href="JavaScript: Open_Filial_Window();">
                      <i class="fa fa-file-alt"></i>
                    </a>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <label class="form-label">Nga Data</label>
                      <input type="text" name="p_date1" id="p_date1" class="form-control" value="<?php echo strftime('%d.%m.%Y'); ?>">
                      <small class="text-muted">(dd.mm.yyyy)</small>
                      <script language="JavaScript">
                        var o_cal = new tcal({
                          'formname': 'formmenu',
                          'controlname': 'p_date1'
                        });
                        o_cal.a_tpl.yearscroll = true;
                        o_cal.a_tpl.weekstart = 1;
                      </script>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <label class="form-label">Deri Data</label>
                      <input type="text" name="p_date2" id="p_date2" class="form-control" value="<?php echo strftime('%d.%m.%Y'); ?>">
                      <small class="text-muted">(dd.mm.yyyy)</small>
                      <script language="JavaScript">
                        var o_cal = new tcal({
                          'formname': 'formmenu',
                          'controlname': 'p_date2'
                        });
                        o_cal.a_tpl.yearscroll = true;
                        o_cal.a_tpl.weekstart = 1;
                      </script>
                    </div>
                  </div>
                </div>

                <hr class="text-primary">

                <div class="form-actions">
                  <div class="text-left">
                    <button type="submit" name="repdata" class="btn btn-primary me-2">Shfaq raportin...</button>
                    <button type="button" name="showfl" class="btn btn-success"
                      onClick="JavaScript: document.formmenu.view.value = 'excel'; document.formmenu.submit();">
                      Shfaq excel
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>