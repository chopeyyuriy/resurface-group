<?php

namespace App\Http\Controllers;

use App\Helpers\DateFormat;
use App\Models\Directories;
use App\Models\Document;
use App\Models\Family;
use App\Http\Requests\FamilyRequest;
use Illuminate\Http\Request;
use Auth;
use Storage;
use DB;

class FamilyController extends Controller
{
    public function getList(Request $request)
    {
        $title = $request->input('title');
        $location = $request->input('location');
        $status = $request->input('status');
        $admission = $request->input('admission');

        $family = Family::query();
        $familyAll = $family->get();

        $family->when($title, function($query, $title) {
            return $query->where('title', 'like', '%' . $title . '%');
        });

        $family->when($location, function($query, $location) {
            return $query->where('location', $location);
        });

        $family->when($status, function($query, $status) {
            return $query->where('status', $status);
        });

        $family->when($admission, function($query, $admission) {
            $admission = DateFormat::setter($admission);
            return $query->where('admission', $admission);
        });
        
        $family->when(!Auth::user()->hasRole('admin'), function ($query) {
            $clinicianId = Auth::user()->userable->id;
            $query->whereRaw("exists (select *
                                        from clinician_client cc, clients c
                                       where cc.clinician_id = $clinicianId
                                         and cc.client_id = c.id
                                         and c.family_id = families.id)");
        });

        $family->orderBy('title');
        $family = $family->get();

        return view('erm', compact('family', 'familyAll'));
    }

    public function jsonData(Request $request, $id)
    {
        return response()->json([
            'result' => 'success',
            'data' => Family::find($id)
        ]);
    }

    public function create(FamilyRequest $request)
    {
        $family = Family::create([
            'title' => $request->input('title'),
            'location' => (int) $request->input('location'),
            'status' => 1, //(int) $request->input('status'),
            'admission' => date('Y-m-d', strtotime($request->input('admission')))
        ]);

        if ($family){
            $data = Directories::where('parent_id', $request->input('location'))->where('title', 'Clients')->first();
            $clients_id = $data->id;

            $directory = new Directories();
            $directory->parent_id = $clients_id;
            $directory->title = $family->title;
            $directory->user_id = Auth::id();
            $directory->type = 'families';
            $directory->family_id = $family->id;

            if ($directory->save()){
                $directoryName = Document::directoryName($family->title, '');
                Storage::disk('families')->makeDirectory($directoryName);
                $family->directory = $directoryName;
                $family->update();
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'result' => 'success',
                'data' => $family
            ]);
        } else {
            return redirect()->back()->with('status', 'Family created');
        }

    }

    public function update(FamilyRequest $request)
    {
        $id = (int) $request->id;
        
        $family = Family::find($id);
        
        DB::transaction(function () use ($family, $request) {
            if ($family->location != $request->location) { // Location has been changed
                Directories::moveFamily($family->id, $request->location);
            }

            $family->update([
                'title' => $request->title,
                'location' => (int) $request->location,
                'status' => (int) $request->status,
                'admission' => date('Y-m-d', strtotime($request->admission))
            ]);            
        });

        return response()->json([
            'result' => 'success',
            'data' => $family
        ]);
    }

    public function delete($id = 0)
    {
        $family = Family::find($id);

        if (!empty($family)) {
            $family->clients()->delete();
            $family->delete();

            Directories::whereFamilyId($id)->delete();
        }

        Storage::disk('families')->deleteDirectory($family->directory);

        return redirect(route('erm'))->with('status', 'Family deleted');
    }
}
