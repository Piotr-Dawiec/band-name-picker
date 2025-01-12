<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'band_member_id'];

    public function bandMember()
    {
        return $this->belongsTo(BandMember::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

}
