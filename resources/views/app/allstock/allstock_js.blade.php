<script>
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const formattedDate = now.toLocaleDateString('en-US', options);
        document.getElementById('realtime-date').textContent = formattedDate;
    }

    setInterval(updateDateTime, 1000); // Memperbarui setiap detik
    updateDateTime(); // Panggilan awal untuk menampilkan waktu saat halaman dimuat
</script>
