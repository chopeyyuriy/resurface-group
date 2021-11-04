<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\DB;

class FaqPage extends Model
{
    const STATUSES = [
        1 => 'Published',
        2 => 'Draft',
        3 => 'Archived'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public static function findForEditWithAuth(int $id)
    {
        $item = FaqPage::findOrFail($id);
        if (!Auth::user()->hasRole('admin') && $item->user_id != Auth::user()->id) {
            abort(404);
        }
        return $item;
    }
    
    public static function findForViewWithAuth(int $id)
    {
        $item = FaqPage::findOrFail($id);
        if (!Auth::user()->hasRole('admin') && $item->user_id != Auth::user()->id) {
            if ($item->status != 1) {
                abort(404);
            }
        }
        
        return $item;
    }
    
    public function status()
    {
        return self::STATUSES[$this->status];
    }


    public static function getFaqs($keyword, $limit = null)
    {
        $faqs = FaqPage::select(
            'id',
            DB::raw('title AS text'),
            DB::raw('CONCAT(\'/faq\') AS url')
        )->where('title', 'like', "%$keyword%")
            ->take($limit)
            ->get()->toArray();

        return $faqs;
    }
}
