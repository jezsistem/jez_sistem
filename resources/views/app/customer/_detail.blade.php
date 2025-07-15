<form>
    <div class="form-group">
        <label>Store</label>
        <input type="text" class="form-control" value="{{ $customer->st_name }}" disabled>
    </div>
    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" value="{{ $customer->cust_name }}" disabled>
    </div>
    <div class="form-group">
        <label>Telepon</label>
        <input type="text" class="form-control" value="{{ $customer->cust_phone }}" disabled>
    </div>
</form>