<select class="ml-3" style="width: 100%; font-size: 1.5rem;" id="storage_area" multiple>
    <option value="">--- Pilih Lokasi Penyimpanan ---</option>
    @forelse ($storageAreas as $storage_area)
        <option value="{{ $storage_area->id }}">
            {{ $storage_area->name }}</option>
    @empty
        
    @endforelse
</select>

<script>
    $('#storage_area').select2({
        width: "100%",
        dropdownParent: $('#storage_area').parent()
    });
    // Change Select2 rendered text size
    $('#storage_area').on('select2:open', function () {
        $('.select2-results__option').css('font-size', '1.5rem');
    });
    $('.select2-selection__rendered').css('font-size', '1.5rem');
    $('#storage_area').on('select2:select', function () {
        $('.select2-selection__choice').css('font-size', '1.5rem');
    });
</script>