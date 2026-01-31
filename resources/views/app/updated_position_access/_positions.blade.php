<label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
    Position <span class="text-red-500">*</span>
</label>
<select class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="position_id" name="position_id" required>
    <option value="">- Pilih -</option>
    @foreach ($position as $pos)
        <option value="{{ $pos->id }}">{{ $pos->up_code }}</option>
    @endforeach
</select>
