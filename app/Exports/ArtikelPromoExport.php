<?php

namespace App\Exports;

use App\Models\ArtikelPromo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArtikelPromoExport implements FromCollection, WithHeadings
{
    private $search;
    private $dateRange;
    private $storeId;

    public function __construct($search = null, $dateRange = null, $storeId = null)
    {
        $this->search = $search;
        $this->dateRange = $dateRange;
        $this->storeId = $storeId;
    }

    public function collection()
    {
        $query = ArtikelPromo::select(
            'article_id',
            'p_name',
            'stores.st_code as st_code',
            'promo_name',
            'date_start',
            'date_end',
            'promo_disc',
            'promo_note'
        )
        ->join('stores', 'stores.id', '=', 'articles_promo.st_id')
        ->join('products', 'products.id', '=', 'articles_promo.p_id');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->orWhere('p_id', 'LIKE', "%{$this->search}%")
                    ->orWhere('article_id', 'LIKE', "%{$this->search}%")
                    ->orWhere('p_name', 'LIKE', "%{$this->search}%")
                    ->orWhere('st_code', 'LIKE', "%{$this->search}%")
                    ->orWhere('promo_name', 'LIKE', "%{$this->search}%")
                    ->orWhere('date_start', 'LIKE', "%{$this->search}%")
                    ->orWhere('date_end', 'LIKE', "%{$this->search}%")
                    ->orWhere('promo_disc', 'LIKE', "%{$this->search}%")
                    ->orWhere('promo_note', 'LIKE', "%{$this->search}%");
            });
        }

        if (!empty($this->dateRange)) {
            $dates = explode('|', $this->dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('date_start', [$dates[0], $dates[1]]);
            } else {
                $query->whereDate('date_start', $dates[0]);
            }
        }

        if (!empty($this->storeId)) {
            $query->where('st_id', $this->storeId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Article ID',
            'Product Name',
            'Store Code',
            'Promo Name',
            'Start Date',
            'End Date',
            'Discount',
            'Notes'
        ];
    }
}
