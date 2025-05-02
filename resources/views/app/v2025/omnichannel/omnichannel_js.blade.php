<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('#stockTabs .nav-link');
    tabs.forEach(tab => {
      tab.addEventListener('click', function (e) {
        e.preventDefault();
        const target = this.getAttribute('data-bs-target');
        const allTabs = document.querySelectorAll('.tab-pane');
        allTabs.forEach(t => t.classList.remove('show', 'active'));
        document.querySelector(target).classList.add('show', 'active');
      });
    });

    // Chart.js: Cancel vs Success by platform
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Cancel', 'Success'],
        datasets: [
          {
            label: 'Shopee',
            data: [12, 45],
            backgroundColor: 'rgba(227, 6, 19, 0.6)'
          },
          {
            label: 'Tokopedia',
            data: [8, 32],
            backgroundColor: 'rgba(76, 175, 80, 0.6)'
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: { stacked: false },
          y: {
            beginAtZero: true,
            ticks: { stepSize: 10 }
          }
        }
      }
    });
  });

  // Dummy function for loading invoice details
  function loadInvoiceDetails(invoiceId) {
    // Contoh data dummy untuk modal
    const invoiceDetails = {
      1: {
        items: [
          { name: 'Adidas Ultraboost', qty: 2, price: 'Rp 300.000' },
          { name: 'Nike Air Max', qty: 3, price: 'Rp 450.000' }
        ],
        totalItems: 5,
        totalPrice: 'Rp 750.000',
        status: 'Success'
      },
      2: {
        items: [
          { name: 'Puma RS-X', qty: 1, price: 'Rp 200.000' },
          { name: 'Adidas NMD', qty: 4, price: 'Rp 600.000' }
        ],
        totalItems: 5,
        totalPrice: 'Rp 800.000',
        status: 'Cancel'
      }
      // Tambahkan data lainnya jika diperlukan
    };

    // Ambil detail invoice dari data dummy
    const details = invoiceDetails[invoiceId];

    if (details) {
      // Update konten modal dengan data invoice
      document.getElementById('invoiceModalTitle').innerText = `Invoice #${invoiceId}`;
      document.getElementById('invoiceModalItems').innerHTML = details.items
        .map(
          item =>
            `<tr>
            <td>${item.name}</td>
            <td>${item.qty}</td>
            <td>${item.price}</td>
          </tr>`
        )
        .join('');
      document.getElementById('invoiceModalTotalItems').innerText = details.totalItems;
      document.getElementById('invoiceModalTotalPrice').innerText = details.totalPrice;
      document.getElementById('invoiceModalStatus').innerText = details.status;

      // Tampilkan modal
      const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
      invoiceModal.show();
    } else {
      alert('Invoice details not found!');
    }
  }
</script>