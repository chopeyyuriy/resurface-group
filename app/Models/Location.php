<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function getFullNameAttribute()
    {
        return $this->state_id . ' ' . $this->city;
    }
}
