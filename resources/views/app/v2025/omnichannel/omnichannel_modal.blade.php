<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="invoiceModalTitle">Invoice Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Total Items:</strong> <span id="invoiceModalTotalItems"></span></p>
        <p><strong>Total Price:</strong> <span id="invoiceModalTotalPrice"></span></p>
        <p><strong>Status:</strong> <span id="invoiceModalStatus"></span></p>
        <table class="table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Qty</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody id="invoiceModalItems">
            <!-- Konten akan diisi secara dinamis -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>