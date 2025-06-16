<select class="form-control" id="invoice" name="invoice">
    <option value="">PILIH JIKA SCANNER ERROR</option>
    @foreach ($data['invoice'] as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
</select>

<script>
    $('#invoice').select2({
        width: "100%",
        dropdownParent: $('#ScannerModal')
    });

    $('#invoice').on('change', function() {
		var invoice = $(this).val();
		$('#scanned-result').val(invoice).trigger('change');
	});
</script>