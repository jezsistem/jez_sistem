// Fungsi utama untuk mencoba membuka modal dan mendapatkan lock
async function openEditModal(type, id, identifier) {
    console.log(`Mencoba mengunci ${type} #${id} untuk sesi '${identifier}'...`);
    try {
        const response = await fetch('/lock/acquire', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                lockable_type: type,
                lockable_id: id,
                identifier: identifier
            })
        });

        const data = await response.json();

        if (response.ok) {
            // alert(data.message);
            // showYourModal(data);
        } else if (response.status === 423) {
            swal('Gagal', `Data sedang diakses oleh ${data.locked_by}`, 'error');
            return false;
        } else {
            alert(`Terjadi kesalahan: ${data.message}`);
        }
    } catch (error) {
        console.error('Tidak bisa terhubung ke server:', error);
        alert('Tidak bisa terhubung ke server.');
    }
    return true;
}

// Fungsi untuk melepas lock saat modal ditutup
async function closeEditModal(type, id, identifier  ) {
    console.log(`Melepaskan kunci untuk ${type} dengan ID ${id}...`);
    await fetch('/lock/release', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify({
            lockable_type: type,
            lockable_id: id,
            identifier: identifier
        })
    });
    // alert("Lock telah dilepaskan.");
}

// Fungsi extendLock
async function extendLock(type, id, identifier) {
    try {
        await fetch('/lock/extend', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                lockable_type: type,
                lockable_id: id,
                identifier: identifier
            })
        });
        // Tidak perlu alert, cukup silent
    } catch (error) {
        // Bisa tambahkan log jika perlu
        // console.error('Gagal extend lock:', error);
    }
}
