<?php
session_start();
?>
<link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.ico">
<script src="./assets/libs/jquery/dist/jquery.min.js"></script>
<link href="./dist/css/style.css" rel="stylesheet">
<script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<title>Tabela Live</title>
<div class="container-fluid text-center">
<img
    src="<?php if (isset($_SESSION['logo_image']) && !empty($_SESSION['logo_image'])) { echo $_SESSION['logo_image']; } else { echo './assets/images/Logo.png'; } ?>"
    alt=""
    class="img-fluid" />
</div>
<div id="data-container">

</div>
<script>
    function fetchData() {
        $.ajax({
            url: 'exchange_tabel_live_fetch.php', // Create this file to return the updated data
            method: 'GET',
            success: function(data) {
                // Update the relevant part of the page with the new data
                $('#data-container').html(data);
            },
            error: function() {
                console.error('Failed to fetch data');
            }
        });
    }

    setInterval(fetchData, 1000);
</script>