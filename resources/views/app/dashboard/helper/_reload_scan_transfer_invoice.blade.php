<select class="form-control" id="scan_transfer_invoice" name="scan_transfer_invoice">
    <option value="">PILIH INVOICE</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>

<script>
    $('#scan_transfer_invoice').select2({
        width: "100%",
        dropdownParent: $('#ScanTransferModal')
    });
</script>