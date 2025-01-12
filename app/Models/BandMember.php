<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BandMember extends Model
{
    use HasFactory;

    protected $fillable = ['uuid'];

    protected static function boot()
    {
        parent::boot();

        // Automatyczne generowanie UUID przy tworzeniu rekordu
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function proposedNames()
    {
        return $this->hasMany(Name::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

}
