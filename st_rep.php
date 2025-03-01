<?php include 'header.php'; ?>
<div class="page-wrapper">
  <div class="container-fluid">
    <script language="JavaScript" src="calendar_eu.js"></script>
    <link rel="stylesheet" href="calendar.css">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Raporti përmbledhës</h4>
            <form action="str_view.php" method="POST" name="formmenu" target="_blank">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="id_llogfilial"><b>Llogaria:</b></label>
                    <div class="input-group">
                      <select name="id_llogfilial" id="id_llogfilial" class="form-control">
                        <?php
                        //mysql_select_db($database_MySQL, $MySQL);
                        $query_filiali_info = "select * from filiali order by filiali asc";
                        $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
                        $row_filiali_info = $filiali_info->fetch_assoc();

                        while ($row_filiali_info) {
                        ?>
                          <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) {
                                                                                    echo "selected";
                                                                                  } ?>><?php echo $row_filiali_info['filiali']; ?></option>
                        <?php
                          $row_filiali_info = $filiali_info->fetch_assoc();
                        }
                        mysqli_free_result($filiali_info);
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <label><b>Data:</b></label>
                    <div class="row">
                      <div class="col-md-6">
                        <label>Nga:</label>
                        <input name="p_date1" type="text" id="p_date1" value="<?php echo strftime('%d.%m.%Y'); ?>" class="form-control" maxlength="10">
                        <small class="form-text text-muted">(dd.mm.yyyy)</small>
                        <script language="JavaScript">
                          var o_cal = new tcal({
                            'formname': 'formmenu',
                            'controlname': 'p_date1'
                          });
                          o_cal.a_tpl.yearscroll = true;
                          o_cal.a_tpl.weekstart = 1;
                        </script>
                      </div>
                      <div class="col-md-6">
                        <label>Deri:</label>
                        <input name="p_date2" type="text" id="p_date2" value="<?php echo strftime('%d.%m.%Y'); ?>" class="form-control" maxlength="10">
                        <small class="form-text text-muted">(dd.mm.yyyy)</small>
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
              </div>

              <div class="row mt-3">
                <div class="col-12 text-left">
                  <button type="submit" name="repdata" class="btn btn-info">Shfaq raportin...</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php include 'footer.php'; ?>