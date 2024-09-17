<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#btnPria").click(function () {
            updateCount("male");
        });

        $("#btnWanita").click(function () {
            updateCount("female");
        });

        $("#btnAnak").click(function () {
            updateCount("child");
        });

        function reloadPage() {
            location.reload();
        }

        function updateCount(gender) {
            $.ajax({
                type: "POST",
                url: "/update_traffic_customer", // Replace with the actual server endpoint for updating counts
                data: { gender: gender },
                success: function (response) {

                    reloadPage();
                },
                error: function (error) {
                    console.error("Error updating count:", error);
                }
            });
        }
    });
</script><?php /**PATH D:\88 Other\Dev\jez_sistem\resources\views/app/customer/customer_traffic_js.blade.php ENDPATH**/ ?>