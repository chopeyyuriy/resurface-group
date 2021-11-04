<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'clinician_id',
        'client_id',
        'text'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function clinician()
    {
        return $this->belongsTo(Clinician::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date('m/d/Y, h:i A', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('m/d/Y, h:i A', strtotime($value));
    }
}
