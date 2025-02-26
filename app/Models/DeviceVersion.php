<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceVersion extends Model
{
    use HasFactory;
    protected $fillable = ['device_id', 'version'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}

