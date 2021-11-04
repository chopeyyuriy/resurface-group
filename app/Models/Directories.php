<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directories extends Model
{
    protected $table = 'directories';

    protected $fillable = [
        'parent_id',
        'title',
        'user_id',
        'client_id',
        'family_id',
        'clinician_id',
        'type'
    ];

    public function childrens()
    {
        return $this->hasMany(Directories::class,   'parent_id','id');
    }
    
    /**
     * This is a method returns root list entries.
     * Used to display a list of locations.
     * 
     * @return type
     */
    public static function rootList()
    {
        return Directories::whereNull('parent_id')
                ->orderBy('title')
                ->get();
    }
    
    /**
     * This is a method of moving the family folder to a new location.
     * 
     * @param type $id
     * @param type $newLocation
     * @return boolean
     */
    public static function moveFamily($id, $newLocation)
    {
        $family = Directories::whereFamilyId($id)->whereType('families')->first();
        $clients = Directories::whereParentId($newLocation)->first();
        if ($family && $clients) {
            $family->parent_id = $clients->id;
            $family->save();
            
            return true;
        }
        return false;
    }
}
