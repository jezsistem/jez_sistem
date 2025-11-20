<script>
    function loadStatus() {
        $.get("{{ url('/wa/status') }}", function(res) {

            if (res.ready === true) {
                $("#wa-status-section").hide();
                $("#wa-qr-section").hide();
                $("#wa-connected-section").show();
            } else {
                $("#wa-connected-section").hide();
                loadQR(); // tampilkan qr
            }
        });
    }

    function loadQR() {
        $.get("{{ url('/wa/qr') }}", function(res) {

            if (res.status === true) {
                $("#wa-qr-image").attr("src", res.qr);
                $("#wa-qr-section").show();
            } else {
                $("#wa-qr-section").hide();
            }

            $("#wa-status-section").hide();
        });
    }

    setInterval(loadStatus, 5000);

    loadStatus();
</script>
