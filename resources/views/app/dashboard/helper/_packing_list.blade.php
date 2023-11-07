@if (!empty($data['packing_list']))
    <div class="card-body table-responsive">
        <table class="table table-hover table-checkable">
            <thead class="btn btn-inventory text-light">
                <tr>
                    <th class="text-dark">No</th>
                    <th class="text-dark">Artikel</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($data['packing_list'] as $row)
                <tr>
                    <td>{{ $i }}</td>
                    <td>@if ($row->plst_status == 'WAITING FOR PACKING')
                        <a class="btn btn-sm btn-info">{{ ($row->plst_status) }}</a>
                        @elseif ($row->plst_status == 'DONE')
                        <a class="btn btn-sm btn-success">{{ ($row->plst_status) }}</a>
                        @elseif ($row->plst_status == 'REJECT')
                        <a class="btn btn-sm btn-danger">{{ ($row->plst_status) }}</a>
                        @elseif ($row->plst_status == 'WAITING')
                        <a class="btn btn-sm btn-warning">{{ ($row->plst_status) }}</a>
                        @elseif ($row->plst_status == 'INSTOCK')
                        <a class="btn btn-sm btn-primary">{{ ($row->plst_status) }}</a>
                        @endif 
                        <br/> {{ $row->p_name }} {{ $row->p_color }} [{{ $row->sz_name }}]
                        <br/> <a class="btn btn-sm btn-primary">Jml : {{ ($row->plst_qty) }}</a>
                        <a class="btn btn-sm btn-success" data-name="{{ $row->p_name }} {{ $row->p_color }} ({{ $row->sz_name }})" data-plst_id="{{ $row->plst_id }}" id="done_btn">
                            Done
                        </a>
                    </td>
                </tr>
                @php $i ++; @endphp
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <center><h4>Invoice tidak ditemukan</h4></center>
@endif