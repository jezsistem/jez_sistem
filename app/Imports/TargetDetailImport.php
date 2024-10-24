<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TargetDetailImport implements ToCollection, WithStartRow
{
    private $rows = 0;
    private $tr_id;
    private $st_id;
    private $stt_id;

    function __construct($tr_id, $st_id, $stt_id)
    {
        $this->tr_id = $tr_id;
        $this->st_id = $st_id;
        $this->stt_id = $stt_id;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $str_id = DB::table('sub_targets')->insertGetId([
            'tr_id' => $this->tr_id,
            'st_id' => $this->st_id,
            'stt_id' => $this->stt_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $insert = array();
        foreach ($collection as $r) {
            if ($r[0] == null) {
                return null;
            }
            $date = date('Y-m-d', strtotime($r[0]));
            $qty = (int)$r[1];
            $insert[] = array(
                'str_id' => $str_id,
                'sstr_date' => $date,
                'sstr_amount' => ltrim($qty),
                'created_at' => date('Y-m-d H:i:s')
            );
        }

        $save = DB::table('sub_sub_targets')->insert($insert);

        return $save;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
