<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniquePhoneNumber implements Rule
{
    protected $phoneNumber;

    public function __construct($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function passes($attribute, $value)
    {
        $exists = DB::table('customers')
            ->where('phone', $this->phoneNumber)
            ->orWhere('phone2', $this->phoneNumber)
            ->exists();

        return !$exists;
    }

    public function message()
    {
        return 'The phone number already exists!.';
    }
}
