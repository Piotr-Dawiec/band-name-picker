<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ['band_member_id', 'name_id', 'vote'];

    public function bandMember()
    {
        return $this->belongsTo(BandMember::class);
    }

    public function name()
    {
        return $this->belongsTo(Name::class);
    }
}
