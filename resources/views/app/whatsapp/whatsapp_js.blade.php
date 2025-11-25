<script>
    function loadStatus() {
        $.get("{{ url('/wa/status') }}", function (res) {

            if (res.ready === true) {

                // sudah konek
                $("#wa-status-section").hide();
                $("#wa-qr-section").hide();
                $("#wa-connected-section").show();

                // tampilkan data profil WA
                loadProfile();

                // ubah tombol jadi "Logout"
                $("#wa_action_btn")
                    .removeClass("btn-secondary")
                    .addClass("btn-danger")
                    .text("Logout WA")
                    .attr("data-action", "logout");

                $("#qr_box").html(`
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fa fa-check-circle text-success" style="font-size: 80px;"></i>
                        </div>
                        <h4 class="text-success">Terkoneksi</h4>
                    </div>
                `);

            } else {

                // belum konek
                $("#wa-connected-section").hide();
                loadQR();
                // $("#wa-info-section").hide();

                // ubah tombol jadi "Reload Barcode"
                $("#wa_action_btn")
                    .removeClass("btn-danger")
                    .addClass("btn-secondary")
                    .text("Reload Barcode")
                    .attr("data-action", "reload");
            }
        });
    }

    function loadQR() {
        $.get("{{ url('/wa/qr') }}", function (res) {

            if (res.status === true && res.qr) {

                $("#qr_box").html(`
                <img id="wa-qr-image" src="${res.qr}" width="250">
            `);

            } else {

                $("#qr_box").html(`
                <span class="text-muted">${res.message ?? 'QR belum tersedia'}</span>
            `);
            }
        });
    }

    function loadProfile() {
        $.get("{{ url('/wa/profile') }}", function (res) {

            if (res.status === true) {
                $("#wa-name").val(res.name);
                $("#wa-number").val(res.number);
                $("#wa-status-text").val("Connected");

                $("#wa-info-section").show();

                // tombol = Logout
                $("#wa_logout_btn").text("Logout WA").removeClass("btn-primary").addClass("btn-danger");

            } else {
                // jika belum konek
                $("#wa-name").val("-");
                $("#wa-number").val("-");
                $("#wa-status-text").val("Not Connected");

                // tombol = Reload Barcode
                $("#wa_logout_btn").text("Reload Barcode").removeClass("btn-danger").addClass("btn-primary");
            }
        });
    }

    $("#wa_action_btn").on("click", function () {
        const action = $(this).attr("data-action");

        if (action === "reload") {
            loadQR();
            toastr.info("Meminta QR baru...");
        }

        if (action === "logout") {
            $.get("{{ url('/wa/logout') }}", function () {
                toastr.success("Berhasil logout!");
                loadStatus();
            });
        }
    });

    setInterval(() => {
        loadStatus();
        loadProfile(); // <— tambahkan
    }, 5000);


    $('#waJobTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('wa.job.datatable') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'job_name', name: 'job_name' },
            { data: 'start_at', name: 'start_at' },
            { data: 'end_at', name: 'end_at' },
            { data: 'interval_hours', name: 'interval_hours' },
            { data: 'batch_size', name: 'batch_size' },
            { data: 'status', name: 'status' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $("#addJobForm").on("submit", function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "{{ route('wa.job.store') }}",
            method: "POST",
            data: formData,
            beforeSend: function () {
                Swal.fire({
                    title: "Menyimpan...",
                    text: "Mohon tunggu",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function (res) {
                Swal.close();

                if (res.status === true) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $("#addJobModal").modal("hide");
                    $("#addJobForm")[0].reset();

                    // reload DataTable
                    $("#waJobTable").DataTable().ajax.reload();
                }
            },
            error: function (xhr) {
                Swal.close();

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: xhr.responseJSON?.message ?? "Terjadi kesalahan."
                });
            }
        });
    });
</script>
