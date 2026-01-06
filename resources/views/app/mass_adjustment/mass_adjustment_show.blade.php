@extends('app.structure')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid">

        {{-- Subheader --}}
        <div class="subheader py-2 py-lg-6 subheader-solid">
            <div class="container-fluid">
                <h5 class="text-dark font-weight-bold my-1">
                    {{ $data['subtitle'] }} - {{ $data['ma']->ma_code }}
                </h5>
            </div>
        </div>

        <div class="container">

            {{-- Info Card --}}
            <div class="card card-custom gutter-b">
                <div class="card-body row">


                    <div class="col-md-3">
                        <strong>Store</strong>
                        <div>{{ $data['ma']->st_name }}</div>
                    </div>

                    <div class="col-md-3">
                        <strong>Dibuat Oleh</strong>
                        <div>{{ $data['ma']->u_name }}</div>
                    </div>

                    <div class="col-md-3">
                        <strong>Approval</strong>
                        <div>{{ $data['ma']->approve_name }}</div>
                    </div>

                    <div class="col-md-3">
                        <strong>Status</strong>
                        <div>
                            @if ($data['ma']->ma_status == 0)
                                <span class="badge badge-warning">Menunggu Eksekusi</span>
                            @elseif ($data['ma']->ma_status == 2)
                                <span class="badge badge-danger">Cancel</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-custom gutter-b">
                <div class="card-body d-flex justify-content-between align-items-center">

                    {{-- BACK --}}
                    <a href="{{ url('mass_adjustment') }}" class="btn btn-light">
                        ← Kembali
                    </a>

                    @if ($data['ma']->ma_status == 0)
                        <div class="d-flex align-items-center gap-3">

                            <input type="text"
                                   class="form-control mr-2"
                                   id="approval_label"
                                   placeholder="Menunggu Approval"
                                   readonly
                                   style="width:200px">
                            @if($data['ma']->ma_approve == null)
                                {{-- APPROVAL --}}
                                <div class="d-flex align-items-center">

                                    <a class="btn btn-success mr-3"
                                       id="approval_btn">
                                        Approve
                                    </a>
                                </div>

                            @else
                                {{-- ACTION --}}
                                <a class="btn btn-primary mr-3"
                                   id="execution_btn">
                                    Eksekusi
                                </a>
                            @endif
                            <a class="btn btn-danger"
                               id="cancel_btn">
                                Batalkan
                            </a>

                        </div>
                    @endif

                    {{-- <a class="btn btn-secondary" id="export_btn">Export</a> --}}

                </div>
            </div>


            <input type="hidden" id="ma_id"  value="{{ $data['ma']->id }}">


            <div class="card card-custom gutter-b">
                <div class="card-body table-responsive">

                    <input type="search"
                           class="form-control col-4 mb-4"
                           id="detail_search"
                           placeholder="Cari artikel / SKU">

                    <table class="table table-hover table-checkable"
                           id="MassAdjustmentDetailtb">
                        <thead class="bg-light text-dark">
                        <tr>
                            <th>No</th>
                            <th>BIN</th>
                            <th>BRAND</th>
                            <th>SKU</th>
                            <th>ARTIKEL</th>
                            <th>WARNA</th>
                            <th>SIZE</th>
                            <th>Sub Kategori</th>
                            <th>HB</th>
                            <th>HJ</th>
                            <th>Qty System</th>
                            <th>Qty SO</th>
                            <th>Type</th>
                            <th>Diff</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>

        </div>

        <script>
            let ma_id = "{{ $data['ma']->id }}";

            {{--let detailTable = $('#MassAdjustmentDetailtb').DataTable({--}}
            {{--    processing: true,--}}
            {{--    serverSide: true,--}}
            {{--    ajax: {--}}
            {{--        url: "{{ url('mass-adjustment/detail-datatables') }}",--}}
            {{--        data: {--}}
            {{--            ma_id: ma_id--}}
            {{--        }--}}
            {{--    },--}}
            {{--    columns: [--}}
            {{--        { data: 'DT_RowIndex' },--}}
            {{--        { data: 'bin' },--}}
            {{--        { data: 'brand' },--}}
            {{--        { data: 'sku' },--}}
            {{--        { data: 'artikel' },--}}
            {{--        { data: 'warna' },--}}
            {{--        { data: 'size' },--}}
            {{--        { data: 'sub_kategori' },--}}
            {{--        { data: 'hb' },--}}
            {{--        { data: 'hj' },--}}
            {{--        { data: 'qty_system' },--}}
            {{--        { data: 'qty_so' },--}}
            {{--        { data: 'type' },--}}
            {{--        { data: 'diff' },--}}
            {{--    ]--}}
            {{--});--}}

            $('#detail_search').on('keyup', function () {
                detailTable.search(this.value).draw();
            });


        </script>

    @include('app._partials.js')
    @include('app.mass_adjustment.mass_adjustment_js')
@endsection
