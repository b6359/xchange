

<?php include 'header.php'; ?>
<?php

if (isset($_GET['hid']) && ($_GET['action'] == "del")) {
  $sql_info = "DELETE FROM filiali WHERE id = " . $_GET['hid'];
  $result = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
}
?>
<div class="page-wrapper">
  <div class="container-fluid">

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a href="#Filialet" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
          <i class="mdi mdi-home-variant d-lg-none d-block me-1"></i>
          <span class="d-none d-lg-block">Filialet</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#Hapjebalance" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
          <i class="mdi mdi-home-variant d-lg-none d-block me-1"></i>
          <span class="d-none d-lg-block">Hapje balance</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#Llogaritë" data-bs-toggle="tab" aria-expanded="false"
          class="nav-link">
          <i class="mdi mdi-account-circle d-lg-none d-block me-1"></i>
          <span class="d-none d-lg-block">Llogaritë</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#Klientët" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
          <i class="mdi mdi-settings-outline d-lg-none d-block me-1"></i>
          <span class="d-none d-lg-block">Klientët</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#Monedhat" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
          <i class="mdi mdi-settings-outline d-lg-none d-block me-1"></i>
          <span class="d-none d-lg-block">Monedhat</span>
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane show active" id="Filialet">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <h4 class="card-title">
              <b>Administrimi i filialeve</b>
            </h4>
            <button class="btn btn-outline-primary" onclick="openModal('ins')">
              <i class="fas fa-plus cursor-pointer"></i> Shto Filial
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">Filiali</th>
                  <th scope="col">Kod. Llogarie</th>
                  <th scope="col">Menaxher</th>
                  <th scope="col">Veprimi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql_info = "select * from filiali order by ID ASC";
                $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
                $row_h_menu = $h_menu->fetch_assoc();

                while ($row_h_menu) { ?>
                  <tr>
                    <td>
                      <b><?php echo $row_h_menu['filiali']; ?></b>
                    </td>
                    <td>
                      <b><?php echo $row_h_menu['kodllogari']; ?></b>
                    </td>
                    <td>
                      <b><?php echo $row_h_menu['administrator']; ?></b>
                    </td>
                    <td align="center" class="d-flex  justify-content-evenly">
                      <div class="cursor-pointer" onclick="openModal('upd', <?php echo $row_h_menu['id']; ?>)">
                        <i class="fa fa-pen-square"></i>
                      </div>
                      <div class="cursor-pointer" onClick="JavaScript: do_delete(<?php echo $row_h_menu['id']; ?>);">
                        <i class="fa fa-trash text-danger"></i>
                      </div>
                    </td>
                  </tr>
                <?php $row_h_menu = $h_menu->fetch_assoc();
                };
                mysqli_free_result($h_menu);
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="tab-pane" id="Hapjebalance">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <h4 class="card-title">Hapje balance</h4>
            <div class="d-flex align-items-center">
              <button class="btn btn-outline-primary" onclick="printOpenBalance()">
                <i class="fas fa-print cursor-pointer"></i> Printo
              </button>
              <button class="btn btn-outline-primary ms-3" onclick="openOpenBalanceModal('ins')">
                <i class="fas fa-plus cursor-pointer"></i> Shto Balance
              </button>
            </div>
          </div>
          <?php include 'exchange_openbal.php'; ?>
        </div>
      </div>
      <div class="tab-pane" id="Llogaritë">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <h4 class="card-title">Administrimi i llogarive</h4>
            <button class="btn btn-outline-primary" onclick="openAccountsModal('ins')">
              <i class="fas fa-plus cursor-pointer"></i> Shto Llogari
            </button>
          </div>
          <?php include 'exchange_llogari.php'; ?>
        </div>
      </div>
      <div class="tab-pane" id="Klientët">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <h4 class="card-title">
              <b>Administrimi i klientëve</b>
            </h4>
            <button class="btn btn-outline-primary" onclick="openClientModal('ins')">
              <i class="fas fa-plus cursor-pointer"></i> Shto Klient
            </button>
          </div>
          <?php include 'exchange_client.php'; ?>
        </div>
      </div>
      <div class="tab-pane" id="Monedhat">
        <div class="card">
          <div class="card-body d-flex align-items-center justify-content-between">
            <h4 class="card-title">Administrimi i monedhave</h4>
            <button class="btn btn-outline-primary" onclick="openAddCurrencyModal('ins')">
              <i class="fas fa-plus cursor-pointer"></i> Shto Monedhat
            </button>
          </div>
          <?php include 'exchange_currency.php'; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Modal at the end of the page before closing body tag -->
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="accountModalLabel">Administrimi i filialeve</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
  function openModal(action, id = null) {
    let url = 'insupd_account_data.php?action=' + action;
    if (id) {
      url += '&hid=' + id;
    }

    // Load content into modal
    $.get(url, function(data) {
      $('#accountModal .modal-body').html(data);
      $('#accountModal').modal('show');
    });
  }

  function do_delete(value) {
    if (confirm('Jeni i sigurte per fshirjen e ketij rekordi ?!')) {
      window.location = 'exchange_account.php?action=del&hid=' + value;
    }
  }
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Get last active tab from localStorage
    let activeTab = window.localStorage.getItem("activeTab");
    // If there's an active tab stored, activate it
    if (activeTab) {
      let tabElement = document.querySelector(`a[href="${activeTab}"]`);
      if (tabElement) {
        new bootstrap.Tab(tabElement).show();
      }
    }

    // Store the active tab on click
    document.querySelectorAll(".nav-link").forEach(tab => {
      tab.addEventListener("click", function () {
        let tabId = this.getAttribute("href");
        window.localStorage.setItem("activeTab", tabId);
      });
    });
  });
</script>