<?php include 'header.php'; ?>
<div class="page-wrapper">
  <div class="container-fluid">
    <script language="JavaScript" src="calendar_eu.js"></script>
    <link rel="stylesheet" href="calendar.css">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Raporti i Bankës së Shqipërisë</h4>
            <form action="boa_print.php" method="POST" name="formmenu" target="_blank">
              <div class="form-body">
                <label class="form-label">Shfaq raportin </label>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <select name="rep_type" id="rep_type" class="form-select mr-sm-2">
                        <option value="excelnew">Raporti i Ri (excel)</option>
                        <option value="excel">Raporti i Vjeter (excel)</option>
                        <option value="ditor">Ditor</option>
                        <option value="javor">Javor</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <label class="form-label">Nga Data</label>
                      <input type="text" name="p_date1" id="p_date1" class="form-control" value="<?php echo strftime('%d.%m.%Y'); ?>">
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
              </div>
              <div class="form-actions">
                <div class="text-start">
                  <button type="submit" name="repdata" class="btn btn-info">Shfaq raportin</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>