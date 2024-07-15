<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use Exception;
use Illuminate\Support\Str;

class VoucherService
{
    public int $voucherCodeLength = 5;
    public int $voucherCodeLimit = 10;

    public function createVoucherForUser(User $user)
    {
        throw_if($user->vouchers->count() >= $this->voucherCodeLimit, new Exception("User Voucher Limit Reached.", 500));

        return $user->vouchers()->create([
            'code' => $this->generateVoucherCode()
        ]);
    }

    public function generateVoucherCode()
    {
        do {
            $code = Str::random($this->voucherCodeLength);
        } while (Voucher::whereCode($code)->first());

        return $code;
    }
}
