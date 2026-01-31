<?php

namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserInformationExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->users;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Name',
            'KTP Number',
            'KTP Image',
            'NPWP Number',
            'NPWP Image',
            'Birthday',
            'Address',
            'Photo',
            'BPJS Kesehatan Number',
            'BPJS Kesehatan Image',
            'BPJS Ketenagakerjaan Number',
            'BPJS Ketenagakerjaan Image',
            'Bank Name',
            'Bank Account Number',
            'Bank Account Holder',
            'Position',
            'Division'
        ];
    }

    /**
    * @param mixed $user
    * @return array
    */
    public function map($user): array
    {
        return [
            $user->u_name,
            $user->u_ktp,
            $user->u_ktp_image ? Storage::disk('s3')->url($user->u_ktp_image) : '',
            $user->u_npwp,
            $user->u_npwp_image ? Storage::disk('s3')->url($user->u_npwp_image) : '',
            $user->u_birthday,
            $user->u_address,
            $user->u_photo ? Storage::disk('s3')->url($user->u_photo) : '',
            $user->u_bpjs_kes_number,
            $user->u_bpjs_kes_image ? Storage::disk('s3')->url($user->u_bpjs_kes_image) : '',
            $user->u_bpjs_tk_number,
            $user->u_bpjs_tk_image ? Storage::disk('s3')->url($user->u_bpjs_tk_image) : '',
            $user->u_bank_name,
            $user->u_bank_account_number,
            $user->u_bank_account_holder,
            $user->up_name,
            $user->ud_name
        ];
    }
}
