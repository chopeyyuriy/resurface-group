<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $locationForSelect = DB::table('locations')
            ->select(DB::Raw('id, CONCAT(state_id, " ", city) AS name'))
            ->orderBy('name')
            ->limit(10)
            ->pluck('name', 'id');

        $stateForSelect = DB::table('locations')
            ->select(DB::Raw('state_name, state_id'))
            ->orderBy('state_name')
            ->distinct()
            ->pluck('state_name', 'state_id');

        view()->share(compact('locationForSelect', 'stateForSelect'));
    }
}
