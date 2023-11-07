<select class="form-control" id="transfer_invoice" name="transfer_invoice">
    <option value="">PILIH INVOICE</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>

<script>
    $('#transfer_invoice').select2({
        width: "100%",
        dropdownParent: $('#TransferModal')
    });
</script>