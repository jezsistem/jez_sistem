<script>
    $(document).ready(function() {
        var table = $('#StaffInformationtb').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('staff-information.datatables') }}',
                data: function (d) {
                    d.position = $('#position_filter').val();
                    d.division = $('#division_filter').val();
                    d.search = $('#staff_search').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'u_name',
                    name: 'u_name'
                },
                {
                    data: 'u_ktp',
                    name: 'u_ktp'
                },

                {
                    data: 'u_npwp',
                    name: 'u_npwp'
                },

                {
                    data: 'u_birthday',
                    name: 'u_birthday',
                },
                {
                    data: 'u_address',
                    name: 'u_address'
                },
                {
                    data: 'u_bpjs_kes_number',
                    name: 'u_bpjs_kes_number'
                },

                {
                    data: 'u_bpjs_tk_number',
                    name: 'u_bpjs_tk_number'
                },

                {
                    data: 'u_bank_name',
                    name: 'u_bank_name'
                },
                {
                    data: 'u_bank_account_number',
                    name: 'u_bank_account_number'
                },
                {
                    data: 'u_bank_account_holder',
                    name: 'u_bank_account_holder'
                },
                {
                    data: 'up_name',
                    name: 'up_name'
                },
                {
                    data: 'ud_name',
                    name: 'ud_name'
                },
                {
                    data: 'contract_number',
                    name: 'contract_number'
                },
                {
                    data: 'u_active',
                    name: 'u_active'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'asc']
            ],
            scrollX: true,
            responsive: false,
            searching: false,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
        });

        $('#position_filter, #division_filter').change(function() {
            table.ajax.reload();
        });
        $('#staff_search').on('keyup', function() {
            table.ajax.reload();
        });

        $('#export_btn').click(function() {
            var position = $('#position_filter').val();
            var division = $('#division_filter').val();
            var search = $('#staff_search').val();
            var queryParams = $.param({
                position: position,
                division: division,
                search: search
            });
            var url = '{{ route('staff-information.export') }}' + '?' + queryParams;
            window.location.href = url;
        });
    });
</script>
