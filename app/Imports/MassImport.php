<?php

namespace App\Imports;

use App\Exports\EqualExport;
use App\Models\ProductLocationSetup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;

class MassImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */

    private $rows = 0;
    private $ma_id_throw = null;
    private $ma_code_throw = null;
    protected $st_id;
    protected $psc_id;
    protected $br_id;
    protected $pl_id;
    protected $qty_filter;
    protected $note;
    public $invalidPlsIds = [];
    function __construct($st_id, $psc_id, $br_id, $pl_id, $qty_filter, $note, $tipe)
    {
        $this->st_id = $st_id;
        $this->psc_id = $psc_id;
        $this->br_id = $br_id;
        $this->pl_id = $pl_id;
        $this->qty_filter = $qty_filter;
        $this->note = $note;
        $this->tipe = $tipe;
    }

    public function startRow(): int
    {
        return 2;
    }


    public function getInvalidPlsIds()
    {
        return $this->invalidPlsIds;
    }


    public function collection(Collection $collection)
    {
        ++$this->rows;
        $ma_code = 'MADJ'.date('YmdHis');
        $this->ma_code_throw = $ma_code;
        $st_id = $this->st_id;
        $ma_id = null;
        $detail = array();

        foreach ($collection as $r) {
            if ($r[0] == null) {
                continue; // skip baris kosong
            }

            $pls_id = $r[0];
            $qty_export = (int)$r[9];

            // Ambil qty dari database
            $productLocation = ProductLocationSetup::find($pls_id);
            if (!$productLocation) {
                $this->invalidPlsIds[] = $pls_id;
                continue;
            }
            $qty_pls = $productLocation->pls_qty;

            if ($qty_export != $qty_pls) {
                $this->invalidPlsIds[] = [
                    'sku' => $r[2],
                    'qty_export' => $qty_export,
                    'qty_system' => $qty_pls
                ];
            }
        }

//        dd($this->invalid_skus);

        if (!empty($this->invalidPlsIds)) {
            $this->isValid = false;
            return;
        }

        foreach ($collection as $r) {
            if ($r[0] == null) {
                continue;
            }

            $pls_id = $r[0];
            $qty_export = (int)$r[9];
            $qty_so = (int)$r[10];

            if ($qty_export > $qty_so) {
                $type = '-';
                $diff = $qty_export - $qty_so;
            } elseif ($qty_export < $qty_so) {
                $type = '+';
                $diff = $qty_so - $qty_export;
            } else {
                $type = '=';
                $diff = 0;
            }

            if (empty($ma_id)) {
                $ma_id = DB::table('mass_adjustments')->insertGetId([
                    'st_id' => $st_id,
                    'u_id' => Auth::user()->id,
                    'ma_code' => $ma_code,
                    'ma_approve' => null,
                    'ma_editor' => null,
                    'ma_executor' => null,
                    'ma_status' => '0',
                    'note_adjustment' => $this->note,
                    'tipe_adjustment' => $this->tipe,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->ma_id_throw = $ma_id;
            }

            if ($type != '=') {
                $detail[] = [
                    'ma_id' => $ma_id,
                    'pls_id' => $pls_id,
                    'qty_export' => $qty_export,
                    'qty_so' => $qty_so,
                    'mad_type' => $type,
                    'mad_diff' => $diff,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        if (!empty($detail)) {
            DB::table('mass_adjustment_details')->insert($detail);
        }
    }


    public function getRowCount(): array
    {
        $data = [
            'ma_id' => $this->ma_id_throw,
            'ma_code' => $this->ma_code_throw,
        ];
        return $data;
    }
}

