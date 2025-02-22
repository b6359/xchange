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
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Raporti i veprimeve për klient</h4>
            <form action="cli_view.php" method="POST" name="formmenu" target="_blank">
              <div class="form-body">
                <label class="form-label">Klienti </label>
                <div class="row">
                  <div class="col-md-5">
                    <div class="form-group mb-3">
                      <select name="id_klienti" id="id_klienti" class="form-select mr-sm-2">
                        <?php
                        while ($row_klienti_info) {
                        ?>
                          <option value="<?php echo htmlspecialchars($row_klienti_info['id']); ?>"><?php echo htmlspecialchars($row_klienti_info['emriplote']); ?></option>
                        <?php
                          $row_klienti_info = $klienti_info->fetch_assoc();
                        }
                        mysqli_free_result($klienti_info);
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-1">
                    <a class="btn btn-outline-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#myModal">
                      <i class="fas fa-user cursor-pointer" ></i>
                    </a>
                  </div>
                </div>
                <label class="form-label">Shfaq </label>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <select name="reptype" id="reptype" class="form-select mr-sm-2">
                        <option value="kembim">Kembimet valutore</option>
                        <option value="hyrdal">Hyrje / Dalje</option>
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
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Klienti</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
            aria-hidden="true"></button>
        </div>
        <div class="modal-body">
          <?php
          $rec_limit = 10;

          if (isset($_GET['page'])) {
            $page = $_GET['page'] + 1;
            $offset = $rec_limit * $page;
          } else {
            $page = 0;
            $offset = 0;
          }

          $left_rec = $rec_limit;

          $query = "SELECT * FROM klienti WHERE (id > 100 or id = 1) LIMIT $offset, $rec_limit";
          $result = mysqli_query($MySQL, $query) or die(mysqli_error($MySQL));

          echo "<div class='client-list'>";

          while ($row = mysqli_fetch_array($result)) {
            echo "<div class='client-item p-2 border-bottom'>";
            echo "<a href='javascript:void(0)' class='text-decoration-none text-dark' 
                       onclick='updateSelectedClient(" . $row['id'] . ", \"" .
              htmlspecialchars($row['emriplote'], ENT_QUOTES) . "\")'>" .
              htmlspecialchars($row['emriplote']) . "</a>";
            echo "</div>";
          }

          echo "</div>";

          // Pagination links
          echo "<div class='pagination-links mt-3 text-center'>";
          if ($page > 0) {
            $last = $page - 2;
            echo "<a href='javascript:void(0)' class='btn btn-sm btn-outline-secondary me-2' onclick='loadPage($last)'>« Previous</a>";
            echo "<a href='javascript:void(0)' class='btn btn-sm btn-outline-secondary' onclick='loadPage($page)'>Next »</a>";
          } else if ($page == 0) {
            echo "<a href='javascript:void(0)' class='btn btn-sm btn-outline-secondary' onclick='loadPage($page)'>Next »</a>";
          } else if ($left_rec < $rec_limit) {
            $last = $page - 2;
            echo "<a href='javascript:void(0)' class='btn btn-sm btn-outline-secondary' onclick='loadPage($last)'>« Previous</a>";
          }
          echo "</div>";
          ?>

          <style>
            .client-list {
              max-height: 400px;
              overflow-y: auto;
            }

            .client-item {
              transition: background-color 0.2s;
            }

            .client-item:hover {
              background-color: #f8f9fa;
              cursor: pointer;
            }
          </style>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function updateSelectedClient(clientId, clientName) {
      // Update the select dropdown
      const select = document.getElementById('id_klienti');
      const options = select.options;

      for (let i = 0; i < options.length; i++) {
        if (options[i].value == clientId) {
          select.selectedIndex = i;
          break;
        }
      }

      // Close the modal
      var myModal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
      myModal.hide();
    }

    function loadPage(page) {
      // Load new page content using AJAX
      fetch('cli_rep.php?page=' + page)
        .then(response => response.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const modalBody = doc.querySelector('.modal-body');
          document.querySelector('.modal-body').innerHTML = modalBody.innerHTML;
        })
        .catch(error => console.error('Error:', error));
    }
  </script>

  <?php include 'footer.php'; ?>