<link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.ico">
<script src="./assets/libs/jquery/dist/jquery.min.js"></script>
<link href="./dist/css/style.css" rel="stylesheet">
<script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<title>Tabela</title>
<script>
  $(document).ready(function() {
    setInterval(function() {
      $("#autodata").load("tabela.php");
    }, 1000);
  });
</script>
<div class="container-fluid text-center">
<img
    src="./assets/images/Logo.png"
    alt=""
    class="img-fluid" />
</div>
<div id="autodata"></div>