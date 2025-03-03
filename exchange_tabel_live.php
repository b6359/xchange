<?php
include 'header.php';
?>
<div class="page-wrapper">
<div class="container-fluid" id="data-container">

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
<?php
include 'footer.php';
?>