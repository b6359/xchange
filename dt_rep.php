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
                    <a class="btn btn-outline-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#myModal">
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
<div id="myModal" class="modal fade" tabindex="-1" role="dialog"
  aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Llogaria</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"
          aria-hidden="true"></button>
      </div>
      <div class="modal-body">

        <?php

        require_once('ConMySQL.php');

        $v_wheresql = " ";
        if ($_SESSION['Usertype'] == 3)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";

        $query_filiali_info = "select * from filiali " . $v_wheresql . " order by filiali asc";
        $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
        $row_filiali_info = mysqli_fetch_assoc($filiali_info);

        ?>
        <table class="table table-bordered" width="300" height="100%" border="0">
          <tr>
            <td height="43" colspan="3">
              <DIV class=ctxheading>Perzgjidh nga lista</DIV>
            </td>
          </tr>
          <tr valign="top">
            <td width="80%" align="center">

              <table width="300" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="1" width="10" align="center" class="titull"></td>
                  <td height="1" width="280" align="center" class="titull"></td>
                  <td height="1" width="10" align="center" class="titull"></td>
                </tr>
                <?php while ($row_filiali_info) {  ?>
                  <tr bgcolor="#080570">
                    <td height="1" colspan="3" align="center" class="titull"></td>
                  </tr>
                  <tr bgcolor="#99FFCC">
                    <td class="titull"></td>
                    <td height="16">
                        <a href="javascript:void(0)" 
                           onclick="document.getElementById('id_llogfilial').value='<?php echo $row_filiali_info['id']; ?>'; 
                                   document.querySelector('[data-bs-dismiss=modal]').click();" 
                           class="link4">
                            <b><?php echo $row_filiali_info['filiali']; ?></b>
                        </a>
                    </td>
                    <td class="titull"></td>
                  </tr>
                <?php $row_filiali_info = mysqli_fetch_assoc($filiali_info);
                }
                mysqli_free_result($filiali_info);
                ?>
                <tr bgcolor="#080570">
                  <td height="1" colspan="3" align="center" class="titull"></td>
                </tr>
                <tr>
                  <td height="5" colspan="3" align="center" class="titull"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>