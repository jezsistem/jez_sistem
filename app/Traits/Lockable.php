<?php
// app/Traits/Lockable.php
namespace App\Traits;
use App\Models\ModalLock;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Lockable
{
    // Metode ini mendefinisikan hubungan ke tabel `modal_locks`
    public function locks(): MorphMany
    {
        return $this->morphMany(ModalLock::class, 'lockable');
    }
}
