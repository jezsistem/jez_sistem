<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ArticleStockExport implements FromView
{
    protected $store, $brand, $article_id, $startDate, $endDate;

    public function __construct($store, $brand, $article_id, $startDate, $endDate)
    {
        $this->store = $store;
        $this->brand = $brand;
        $this->article_id = $article_id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        $data = DB::select("CALL sumary_stocks(?, ?, ?, ?, ?)", [
            $this->store,
            $this->brand,
            $this->article_id,
            $this->startDate,
            $this->endDate,
        ]);

        $collection = array_slice($data, 0, -1);
//        .stock_card
        return view('app.stock_card.article_stock_export', [
            'data' => $collection
        ]);
    }
}
