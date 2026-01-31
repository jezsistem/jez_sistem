# Strategi Migrasi Layout V2 (Tailwind + Flowbite)

## 📋 Overview
Dokumen ini menjelaskan strategi untuk mengimplementasikan layout baru (app_v2) ke halaman-halaman lain tanpa menghapus halaman lama.

## 🎯 Prinsip Dasar

### 1. **Non-Destructive Migration**
- Halaman lama tetap dipertahankan dan berfungsi
- Halaman baru dibuat secara paralel
- User bisa memilih versi yang diinginkan (atau gradual migration)

### 2. **Naming Convention**

#### Folder Structure:
```
resources/views/app/
├── announcement/              # Halaman lama (tetap ada)
│   ├── index.blade.php
│   └── show.blade.php
└── updated_announcement/      # Halaman baru (layout v2)
    ├── index.blade.php
    └── show.blade.php
```

#### Controller Methods:
```php
// Method lama (tetap ada)
public function index() { ... }

// Method baru (suffix V2)
public function indexV2() { ... }
```

#### Routes:
```php
// Route lama (tetap ada)
Route::get('announcements', [AnnouncementController::class, 'index']);

// Route baru (suffix _v2 atau di folder updated_*)
Route::get('announcements_v2', [AnnouncementController::class, 'indexV2']);
// atau
Route::get('updated/announcements', [AnnouncementController::class, 'indexV2']);
```

## 📁 Struktur Folder yang Disarankan

### Option 1: Folder `updated_*` (Recommended)
```
resources/views/app/
├── announcement/              # Old layout
├── updated_announcement/      # New layout (v2)
├── user/                      # Old layout
├── updated_user/              # New layout (v2)
└── updated_dashboard/         # Already exists
```

**Pros:**
- Jelas terpisah antara old dan new
- Mudah diidentifikasi
- Bisa dihapus folder lama setelah migration selesai

### Option 2: Suffix `_v2`
```
resources/views/app/
├── announcement/
│   ├── index.blade.php        # Old
│   └── index_v2.blade.php    # New
```

**Pros:**
- Semua file dalam satu folder
- Mudah dibandingkan side-by-side

**Cons:**
- Bisa membingungkan jika banyak file

## 🔄 Migration Strategy

### Phase 1: Parallel Development
1. Buat halaman baru dengan layout `app_v2`
2. Test functionality sama dengan halaman lama
3. Deploy kedua versi secara bersamaan

### Phase 2: Gradual Migration
1. Update link di sidebar/topbar untuk point ke versi baru
2. Monitor user feedback
3. Fix bugs di versi baru

### Phase 3: Complete Migration
1. Setelah semua fitur verified, redirect route lama ke route baru
2. Atau hapus route lama jika sudah tidak diperlukan

## 📝 Checklist untuk Setiap Halaman

- [ ] Buat folder/view baru dengan layout `app_v2`
- [ ] Buat controller method baru (suffix V2)
- [ ] Buat route baru
- [ ] Test semua functionality
- [ ] Update sidebar/topbar link (optional)
- [ ] Dokumentasi perubahan

## 🎨 Template untuk Halaman Baru

### Controller Method Template:
```php
public function indexV2(Request $request)
{
    // Copy logic dari index() method
    // Pastikan return view dengan layout baru
    
    $data = [
        'title' => 'JEZ PRO - Announcements',
        'subtitle' => 'Announcements',
        'user' => Auth::user(),
        'sidebar' => $this->sidebar(),
        'segment' => 'announcements_v2'
    ];
    
    return view('app.updated_announcement.index', compact('data', ...));
}
```

### View Template:
```blade
@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Description</p>
        </div>
        <!-- Action Buttons -->
    </div>
    
    <!-- Content -->
</div>
@endsection
```

## 🔗 Link Management

### Option 1: Sidebar dengan Toggle
Tambahkan toggle di sidebar untuk switch antara old/new layout:
```blade
<a href="{{ url('/announcements') }}">Announcements (Old)</a>
<a href="{{ url('/announcements_v2') }}">Announcements (New)</a>
```

### Option 2: Query Parameter
Gunakan query parameter untuk switch:
```php
Route::get('announcements', function(Request $request) {
    if ($request->get('v2') == 'true') {
        return app(AnnouncementController::class)->indexV2($request);
    }
    return app(AnnouncementController::class)->index($request);
});
```

### Option 3: User Preference
Simpan preference user di database/session:
```php
if (Auth::user()->use_v2_layout) {
    return redirect()->route('announcements_v2.index');
}
```

## 📊 Priority Pages untuk Migration

1. ✅ **Dashboard** - Already done (`dashboard_new`)
2. 🔄 **Announcements** - High priority (frequently used)
3. 📋 **User Management** - Medium priority
4. 📦 **Product/Stock** - Medium priority
5. 💰 **POS** - Low priority (complex, keep old for now)

## ⚠️ Important Notes

1. **Jangan hapus halaman lama** sampai versi baru fully tested
2. **Keep backward compatibility** - pastikan data structure sama
3. **Test thoroughly** - semua fitur harus bekerja di versi baru
4. **Document changes** - catat perubahan yang signifikan
5. **Gradual rollout** - jangan migrate semua sekaligus

## 🚀 Quick Start

Untuk membuat halaman baru dengan layout v2:

1. Copy controller method lama → tambahkan suffix `V2`
2. Buat folder `updated_[page_name]` di views
3. Copy view lama → convert ke Tailwind + app_v2 layout
4. Tambahkan route baru
5. Test functionality

---

**Last Updated:** {{ date('Y-m-d') }}
**Maintained by:** Development Team
