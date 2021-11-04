<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Document extends Model
{
    protected $fillable = [
        'path',
        'name',
        'hash_name',
        'size',
        'type'
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function getCreatedAtAttribute($value)
    {
        return date('m/d/Y h:i A', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('m/d/Y h:i A', strtotime($value));
    }

    public static function directoryName($firstName, $lastName)
    {
        return strtolower(str_replace(' ', '_', $firstName . '_' . $lastName . '_' . time()));
    }

    public function documentName()
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->name);
    }

    public static function getDocuments($keyword, $limit, $cliniciansId = null)
    {
        $query = Document::query();

        $query->select(
            'id',
            DB::raw('name AS text'),
            DB::raw('CONCAT(\'/document/download/client/\', "", id) AS url')
        )->where('name', 'like', "%$keyword%")
            ->take($limit);

        if ($cliniciansId){
            $query->where('documentable_id', $cliniciansId);
        }

        $documents = $query->get()->toArray();

        return $documents;
    }


}
