

<div class="table-responsive">
    <table class="table table-hover table-checkable" id="ProductLocationtb">
        <thead class="bg-light text-dark">
            <tr>
                <th class="text-dark">No</th>
                <th style="white-space: nowrap;" class="text-light">Artikel</th>
                <th style="white-space: nowrap;" class="text-light">Warna</th>
                <th style="white-space: nowrap;" class="text-light">Size [Jumlah] [Barcode]</th>
                <th style="white-space: nowrap;" class="text-light"></th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($data['product']))
            @foreach($data['product'] as $row)
            @php 
            $i=1; 
            $exp = explode('|', $row->p_image);
            @endphp

            <tr>
                <td style="white-space: nowrap;">{{ $i }}</td>
                <td style="white-space: nowrap;">{{ $row->p_name }}</td>
                <td style="white-space: nowrap;">({{ $row->mc_name }}) {{ $row->p_color }}</td>
                <td style="white-space: nowrap;">
                    <a class="btn btn-sm btn-primary">{{ $row->sz_name }}</a> <a class="btn btn-sm btn-primary">{{ $row->pls_qty }}</a> <a class="btn btn-sm btn-primary">{{ $row->ps_barcode }}</a>
                </td>
                <td style="white-space: nowrap;">
                    <input type="checkbox" id="check_item_{{ $row->pls_id }}" onclick="return checkItem( {{ $row->pls_id }} )"/> 
                    <span id="article_controller_{{ $row->pls_id }}" style="display:none;">
                    <input type="number" style="width:50px;" placeholder="Qty"/>
                    <select style="width:120px;" id="pl_id" name="pl_id" required>
                        <option value="">- BIN Tujuan -</option>
                        @foreach ($data['pl_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <a onclick="return mutationArticle()" class="btn btn-sm btn-primary">Mutasi</a>
                    <span>
                </td>
            </tr>
            @php $i++; @endphp
            @endforeach
            @endif
        </tbody>
    </table>
</div>