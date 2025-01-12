<?php

namespace Database\Factories;

use App\Models\BandMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BandMemberFactory extends Factory
{
    protected $model = BandMember::class;

    public function definition()
    {
        return [
            'uuid' => (string) Str::uuid(),
        ];
    }
}
