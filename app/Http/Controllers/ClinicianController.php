<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneHelper;
use App\Helpers\UserHelper;
use App\Http\Requests\ClinicianRequest;
use App\Models\Clinician;
use App\Models\Directories;
use App\Models\Document;
use App\Models\FaqPage;
use Illuminate\Http\Request;
use App\Models\ClinicianToLocation;
use App\Models\Note;
use Auth;
use DB;
use Storage;
use Str;

class ClinicianController extends Controller
{
    public function index()
    {
        return view('clinician-directory');
    }

    public function table(Request $request)
    {
        $postName = $request->input('name');
        $postType = $request->input('type');
        $postLocation = $request->input('name_location');
        $postStatus = $request->input('status');

        $clinician = Clinician::join('users', function ($join) {
            $join->on('users.userable_id', 'clinicians.id')
                ->where('users.userable_type', Clinician::class);
        })
        ->select([
            'clinicians.*',
            'users.name',
            'users.email',
            'users.status',
        ]);

        $clinician->when($postName, function ($query, $postName){
            return $query->where(function ($q) use ($postName) {
                $q->where('first_name', 'LIKE', '%' . $postName . '%');
                $q->orWhere('last_name', 'LIKE', '%' . $postName . '%');
                $q->orWhere('middle_name', 'LIKE', '%' . $postName . '%');
                $q->orWhere('email', 'LIKE', '%' . $postName . '%');
            });
        });

        $clinician->when($postType, function ($query, $postType){
            $query->where('type', $postType);
        });

        $clinician->when($postLocation, function ($query, $postLocation){
            $query->whereRaw("exists (select * 
                                        from clinician_to_location 
                                       where clinician_to_location.clinician_id = clinicians.id
                                         and clinician_to_location.location_id = $postLocation)");
        });

        $clinician->when($postStatus, function ($query, $postStatus){
            $query->where('status', $postStatus);
        });

        $table = datatables()->of($clinician)
            ->addColumn('avatar', function ($item) {
                $photo = data_get($item, 'photo') ;

                if(!empty($photo)) {
                    return '<img src="/avatars/crop-32/clinician/'.$photo.'" class="avatar-xs rounded-circle">';
                } else {
                    $firstLetter = ucfirst(substr(data_get($item, 'first_name'), 0, 1));
                    return '<div class="avatar-xs"><span class="avatar-title rounded-circle">'.$firstLetter.'</span></div>';
                }
            })
            ->editColumn('name', function ($item) {
                return '<h5 class="font-size-14 mb-1">
                            <a href="'.route('clinician.form', $item->id).'" class="text-dark">'.data_get($item, 'name').'</a>
                        </h5>
                        <p class="text-muted mb-0">'.($item->locationNames()).'</p>';
            })
            ->editColumn('type', function ($item) {
                return config('clinician.types.'.$item->type);
            })
            ->editColumn('status', function ($item) {
                return config('client.status.' . data_get($item, 'status', 0), '');
            });

            if (Auth::user()->hasRole('admin')) {
                $table->addColumn('action', function ($item) {
                    return '<ul class="list-inline font-size-20 contact-links mb-0">
                        <li class="list-inline-item pe-2"><a href="'.route('clinician.form', ['id' => $item->id]).'" title="Edit"><i class="mdi mdi-circle-edit-outline"></i></a></li>
                        <li class="list-inline-item px-2"><a href="'.route('clinician.delete', ['id' => $item->id]).'" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="mdi mdi-close-circle-outline"></i></a></li>
                    </ul>';
                })
                ->rawColumns(['avatar', 'name', 'action']);
            } else {
                $table->rawColumns(['avatar', 'name']);
            }

        return $table->make(true);
    }

    public function form(int $id)
    {
        $clinician = Clinician::find($id);
        $phones = data_get($clinician, 'phones', []);
        $clients = data_get($clinician, 'clients', []);
        $locations = $clinician ? $clinician->locationIDs() : [];
        $user_id = $clinician ? $clinician->user->id : Auth::id();
        return view('user-profile', compact('clinician', 'phones', 'clients', 'locations', 'user_id'));
    }

    public function save(ClinicianRequest $request, int $id)
    {
        $clinician = DB::transaction(function() use ($request, $id) {
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $middleName = $request->input('middle_name');

            if (Auth::user()->hasRole('admin')) {
                $clinician = Clinician::updateOrCreate(
                    ['id' => $id],
                    [
                        'status' => (int)$request->input('status'),
                        'type' => (int)$request->input('type'),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'middle_name' => $middleName ?? '',
                    ]
                );
            } else {
                $clinician = Clinician::updateOrCreate(
                    ['id' => $id],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'middle_name' => $middleName ?? '',
                    ]
                );
            }

            if ($request->avatar_delete == 'delete') {
                Storage::delete('avatars/clinician/' . data_get($clinician, 'photo'));
                $clinician->photo = null;
            } else
                if ($request->photo) {
                    $img_data = explode(";base64,", $request->photo);
                    Storage::delete('avatars/clinician/' . data_get($clinician, 'photo'));
                    $ext = explode('.', $request->photo_name);
                    $fileHashName = Str::random(40) . '.' . $ext[count($ext) - 1];
                    Storage::put('avatars/clinician/' . $fileHashName, base64_decode($img_data[1]));
                    $clinician->photo = $fileHashName;
                }
            $clinician->save();
            
            // locations
            if (Auth::user()->hasRole('admin')) {
                ClinicianToLocation::syncClinicianLocations($clinician->id, $request->location);
            }
            // --------

            if ($clinician) {

                $isSaveDirectory = Directories::where('clinician_id', $clinician->id)->first();

                if (!$isSaveDirectory) {

                    $data = Directories::where('parent_id', $request->input('location'))->where('title', 'Clinicians')->first();
                    $clients_id = $data->id;

                    $directory = new Directories();
                    $directory->parent_id = $clients_id;
                    $directory->title = $firstName . ' ' . $lastName;
                    $directory->user_id = Auth::id();
                    $directory->clinician_id = $clinician->id;
                    $directory->type = 'clinicians';
                    $directory->save();
                }


                $directoryName = Document::directoryName($firstName, $lastName);
                Storage::disk('clinicians')->makeDirectory($directoryName);

                $clinician->directory = $directoryName;
                $clinician->update();
            }

            UserHelper::save($request, $clinician, 'clinician');
            PhoneHelper::save($request, $clinician);

            switch ($request->type) {
                case 1:
                    $clinician->user->syncRoles('clinician');
                    break;
                case 2:
                    $clinician->user->syncRoles('admin');
                    break;
            }

            return $clinician;
        });

        if($id > 0) {
            $status = 'Profile updated';
        } else {
            $status = 'Profile created';
        }

        return redirect(route('clinician_directory'))->with('status', $status);
    }

    public function delete(int $id)
    {
        DB::transaction(function() use ($id) {
            $clinician = Clinician::find($id);
            FaqPage::whereUserId($clinician->user->id)->delete();
            $clinician->user()->delete();
            $clinician->phones()->delete();
            ClinicianToLocation::whereClinicianId($clinician->id)->delete();
            Note::whereClinicianId($clinician->id)->delete();
            Directories::whereClinicianId($clinician->id)->delete();
            $clinician->delete();

            if ($clinician->directory) {
                Storage::disk('clinicians')->deleteDirectory($clinician->directory);
            }
        });

        return redirect(route('clinician_directory'))->with('status', 'Profile deleted');
    }

    public function detachClient($client_id, $clinician_id)
    {
        $clinician = Clinician::find($clinician_id);

        if(!empty($clinician)) {
            $clinician->clients()->detach($client_id);
        }

        return redirect(route('clinician.form', ['id' => $clinician_id]))->with('status', 'Client detached');
    }
}
