<?php

namespace App\Http\Controllers;

use App\Helpers\DateFormat;
use App\Helpers\PhoneHelper;
use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Models\Clinician;
use App\Models\Directories;
use App\Models\Document;
use App\Models\Family;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Str;

class ClientController extends Controller
{
    public function view($id)
    {
        $client = Client::findOrFail($id);
        $families = Family::orderBy('title')->get();
        $clinicians = Clinician::all();
        $phones = $client->phones()->get();
        $mainPatient = $client->mainPatient();
        $numFamilyMembers = $client->numFamilyMembers();
        $familyMembers = $client->familyMembers();

        return view('client-details', compact('client', 'families', 'phones', 'clinicians', 'mainPatient', 'numFamilyMembers', 'familyMembers'));
    }

    public function create(ClientCreateRequest $request)
    {
        $client = Client::create([
            'family_id' => (int)$request->input('family_id'),
            'status' => 2,
            'photo' => '',
            'first_name' => '',
            'last_name' => '',
            'middle_name' => '',
            'relationship_status' => (int)$request->input('relationship_status'),
            'date_birth' => date('Y-m-d', strtotime("-20 year")),
            'admission_date' => date('Y-m-d'),
            'gender' => '',
            'race' => 0,
            'address' => '',
            'city' => '',
            'zipcode' => '',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'result' => 'success',
                'data' => $client
            ]);
        } else {
            return redirect(route('client.view', ['id' => $client]))->with('status', 'Profile created');
        }
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $clinician = $request->input('clinician');

            $dateBirth = $request->input('date_birth');
            $dateBirth = DateFormat::setter($dateBirth);

            $admissionDate = $request->input('admission_date');
            $admissionDate = DateFormat::setter($admissionDate);

            $client = Client::find($id);
            
            if ($request->avatar_delete == 'delete') {
                Storage::delete('avatars/client/'.data_get($client, 'photo'));
                $fileHashName = '';
            } else
            if ($request->photo) {
                $img_data = explode(";base64,", $request->photo);
                Storage::delete('avatars/client/'.data_get($client, 'photo'));
                $ext = explode('.', $request->photo_name);
                $fileHashName = Str::random(40).'.'.$ext[count($ext) - 1];
                Storage::put('avatars/client/'.$fileHashName, base64_decode($img_data[1]));
            } else {
                $fileHashName = data_get($client, 'photo');
            }

            Client::where('id', $id)->update([
                'family_id' => (int)$request->input('family_id'),
                'status' => (int)$request->input('status'),
                'marital_status' => (int)$request->input('marital_status'),
                'photo' => $fileHashName,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'middle_name' => $request->input('middle_name') ?? '',
                'relationship_status' => (int)$request->input('relationship_status'),
                'date_birth' => $dateBirth,
                'admission_date' => $admissionDate,
                'gender' => $request->input('gender'),
                'race' => (int)$request->input('race'),
                'referred_name' => $request->input('referred_name'),
                'referred_company' => $request->input('referred_company'),
                'referred_phone' => $request->input('referred_phone'),
                'referred_email' => $request->input('referred_email'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'zipcode' => $request->input('zipcode'),
                'email' => $request->input('email'),
            ]);

            if (is_null($client->directory) && $request->first_name && $request->last_name) {
                $client->directory = Document::directoryName($request->first_name, $request->last_name);

                if ($client->update()) {
                    $familyDir = Directories::whereFamilyId($client->family_id)->first();
                    
                    $directory = new Directories();
                    $directory->parent_id = $familyDir->id;
                    $directory->title = $request->first_name.' '.$request->last_name;
                    $directory->user_id = Auth::id();
                    $directory->client_id = $client->id;
                    $directory->type = 'clients';
                    $directory->save();
                }
            }


            $client->clinician()->sync($clinician);

            PhoneHelper::save($request, $client);
        });

        return redirect(route('client.view', ['id' => $id]))->with('status', 'Profile updated');
    }

    public function detachClinician($client_id, $clinician_id)
    {
        $client = Client::find($client_id);

        if (!empty($client)) {
            $client->clinician()->detach($clinician_id);
        }

        return redirect(route('client.view', ['id' => $client_id]))->with('status', 'Clinician detached');
    }

    public function delete($id)
    {
        $id = (int) $id;

        $mainClientID = DB::transaction(function() use ($id) {
            $client = Client::findOrFail($id);
            $mainClientID = $client->mainPatient('id');
            $directories = Directories::whereClientId($client->id)->delete();
            $client->delete();

            return $mainClientID;
        });


        if (!empty($mainClientID) && $mainClientID != $id) {
            return redirect(route('client.view', ['id' => $mainClientID]))->with('status', 'Profile deleted');
        } else {
            return redirect(route('erm'))->with('status', 'Profile deleted');
        }
    }

    public function clientUploadsFile(Request $request)
    {
        $clientID = (int)$request->input('id');

        $directory = Directories::find($clientID);
        if (!$directory) {
            $directory = Directories::where('client_id', $clientID)->first();
        }

        $client = Client::find($directory->client_id);

        if ($client && $directory) {
            $basePath = 'documents/' . $directory->type . '/' . $client->directory;
            $uploadFile = $request->file('document');

            if (empty($uploadFile)) {
                return redirect(route('client.view', ['id' => $clientID]))->with('status', 'File not found');
            }

            $fileName = $uploadFile->getClientOriginalName();
            $fileHashName = $uploadFile->hashName();
            $fileType = $uploadFile->getClientMimeType();
            $fileSize = number_format(($uploadFile->getSize() / 1024), 2, '.', ' ') . ' KB';
            $uploadFile->store($basePath);

            $client = Client::find($directory->client_id);
            $client->documents()->create([
                'path' => $basePath,
                'name' => $fileName,
                'hash_name' => $fileHashName,
                'size' => $fileSize,
                'type' => $fileType
            ]);
        }

        return redirect()->back()->with('status', 'File uploaded');
    }
}
