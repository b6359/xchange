<?php include 'header.php'; ?>
<script>
  $(document).ready(function() {
    setInterval(function() {
      $("#autodata").load("tabela.php");
    }, 1000);
  });
</script>
<div class="page-wrapper">
  <div class="container-fluid">
  <div class="card p-3">
    <div id="autodata"></div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>