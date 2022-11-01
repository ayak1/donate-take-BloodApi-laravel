<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodTypes extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'amount'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function donate()
    {
        return $this->hasMany(donate_schedule::class);
    }







    
    public function taken()
    {
        return $this->hasMany(taken_request::class);
    }
}
