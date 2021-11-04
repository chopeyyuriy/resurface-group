<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Clinician;
use App\Models\Directories;
use App\Models\Document;
use App\Models\Family;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function view()
    {
        $clients = Client::all();
        $families = Family::whereNotNull('directory')->get();
        $clinicians = Clinician::all();
        $directories = Directories::whereNull('parent_id')->with('childrens')->get();

        return view('apps-filemanager', compact('clients', 'families', 'clinicians', 'directories'));
    }

    public function clientTable(Request $request)
    {
        $clientID = (int)$request->input('id');
        $postName = $request->input('name');
        $postType = $request->input('type');

        $client = Client::find($clientID);
        $documents = $client->documents();

        $documents->when($postName, function ($query, $postName) {
            $query->where('name', 'LIKE', '%' . $postName . '%');
        });

        if ($postType !== 'application') {
            $documents->when($postType, function ($query, $postType) {
                $query->where('type', $postType);
            });
        }

        return datatables()->of($documents)
            ->editColumn('name', function ($document) {
                return '<i class="mdi mdi-file-document font-size-16 align-middle text-primary me-2"></i>' . $document->name;
            })
            ->addColumn('action', function ($document) {
                return '<div class="dropdown">
                    <a class="font-size-16 text-muted dropdown-toggle" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true">
                        <i class="mdi mdi-dots-horizontal"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="' . route('document.client.download', ['id' => $document]) . '">Open</a></a>
                        <a class="dropdown-item rename-file" href="#" data-bs-toggle="modal" data-bs-target=".js-rename-file-modal" data-document_id="' . $document->id . '" data-name="' . $document->documentName() . '">Rename</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="' . route('document.client.delete', ['id' => $document]) . '" onclick="return confirm(\'ARE YOU SURE?\')">Remove</a>
                    </div>
                </div>';
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }

    public function clientsTable(Request $request)
    {
        $clientID = (int)$request->input('id');
        $postName = $request->input('name');
        $postType = $request->input('type');

        $documents = Document::query();

        if (isset($clientID) && $clientID !== 0) {
            $documents->where('documentable_id', $clientID);
        }

        if (isset($postName)) {
            $documents->where('name', 'LIKE', "%$postName%");
        }

        if ($postType !== 'application') {
            if (isset($postType)) {
                $documents->where('type', $postType);
            }
        }

        if (!$clientID && !$postName && !$postType) {
            $documents = [];
        }

        return datatables()->of($documents)
            ->editColumn('name', function ($document) {
                return '<i class="mdi mdi-file-document font-size-16 align-middle text-primary me-2"></i>' . $document->name;
            })
            ->addColumn('action', function ($document) {
                return '<div class="dropdown">
                    <a class="font-size-16 text-muted dropdown-toggle" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true">
                        <i class="mdi mdi-dots-horizontal"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="' . route('document.client.download', ['id' => $document]) . '">Open</a></a>
                        <a class="dropdown-item rename-file" href="#" data-bs-toggle="modal" data-bs-target=".js-rename-file-modal" data-document_id="' . $document->id . '" data-name="' . $document->documentName() . '">Rename</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="' . route('documents.client.delete', ['id' => $document]) . '" onclick="return confirm(\'ARE YOU SURE?\')">Remove</a>
                    </div>
                </div>';
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }

    public function clientUploads(Request $request)
    {
        $rules = [
            'location_id' => 'required|numeric',
            'children_id' => 'required|numeric',
            'family_clinician' => 'required|numeric',
            'document' => 'file'
        ];

        $validator = Validator::make($request->all(), $rules);

        if (count($validator->messages()) > 0) {
            return redirect()->back()->with('error', 'Location not selected');
        }

        $location_id = (int)$request->input('location_id');
        $children_id = (int)$request->input('children_id');
        $family_clinician = (int)$request->input('family_clinician');
        $clientID = (int)$request->input('user_id');

        $directory = Directories::find($clientID);
        if (!$directory) {
            $directory = Directories::where('client_id', $clientID)->first();
        }

        if (!$directory) {
            $clinician = Directories::find($family_clinician);
            $directory = Directories::where('clinician_id', $clinician->clinician_id)->first();
        }

        if ($children_id){
            $children = Directories::find($children_id);
        }

        if (isset($directory->client_id)){
            $path = Client::find($directory->client_id);
        } else {
            $clinician = Directories::find($family_clinician);
            $path = Clinician::find($clinician->clinician_id);
        }

        if ($path && $directory) {
            $basePath = 'documents/' . $directory->type . '/' . $path->directory;
            $uploadFile = $request->file('document');

            if (empty($uploadFile)) {
                return redirect(route('client.view', ['id' => $clientID]))->with('status', 'File not found');
            }

            $fileName = $uploadFile->getClientOriginalName();
            $fileHashName = $uploadFile->hashName();
            $fileType = $uploadFile->getClientMimeType();
            $fileSize = number_format(($uploadFile->getSize() / 1024), 2, '.', ' ') . ' KB';
            $uploadFile->store($basePath);


            if ($children->type == 'clients'){
                $client = Client::find($directory->client_id);

                $client->documents()->create([
                    'path' => $basePath,
                    'name' => $fileName,
                    'hash_name' => $fileHashName,
                    'size' => $fileSize,
                    'type' => $fileType
                ]);

            } else {

                $document = new Document();
                $document->path = $basePath;
                $document->name = $fileName;
                $document->hash_name = $fileHashName;
                $document->size = $fileSize;
                $document->type = $fileType;
                $document->documentable_type = 'App\Models\Client';
                $document->documentable_id = $family_clinician;
                $document->save();
            }

        }

        if ($children->type == 'clients'){
            $url = "/documents?location=$location_id&children=$children_id&user=$family_clinician&client=$directory->client_id";
        } else {
            $url = "/documents?location=$location_id&children=$children_id&user=$family_clinician&client=$family_clinician";
        }

        return redirect($url)->with('status', 'File uploaded');

    }

    public function clientDownload($id)
    {
        $document = Document::find($id);
        $basePath = $document->path . '/';

        if (empty($document)) {
            return redirect(route('client.view', ['id' => data_get($document, 'client.id')]))->with('status', 'File not found');
        }

        return Storage::download($basePath . $document->hash_name, $document->name);
    }

    public function clientDelete($id)
    {
        $document = Document::find($id);
        $basePath = $document->path . '/';

        if (empty($document)) {
            return redirect(route('client.view', ['id' => data_get($document, 'client.id')]))->with('status', 'File not found');
        }

        Storage::delete($basePath . data_get($document, 'hash_name'));
        $document->delete();

        return redirect(route('client.view', ['id' => $document->documentable->id]))->with('status', 'File deleted');
    }

    public function clientRename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $newFileName = $request->input('name');
        $document = Document::find($id);

        if (empty($document)) {
            return redirect(route('client.view', ['id' => data_get($document, 'client.id')]))->with('status', 'File not found');
        }

        $fileType = strrchr($document->name, '.');
        $document->name = $newFileName . $fileType;
        $document->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return redirect(route('client.view', ['id' => $document->documentable->id]))->with('status', 'File renamed');
        }
    }

    public function documentDeleteFile($id)
    {
        $document = Document::find($id);
        $basePath = $document->path . '/';

        if (empty($document)) {
            return redirect(route('client.view', ['id' => data_get($document, 'client.id')]))->with('status', 'File not found');
        }

        Storage::delete($basePath . data_get($document, 'hash_name'));
        $document->delete();

        return redirect(route('documents'))->with('status', 'File deleted');
    }

    public function documentNewFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required',
        ]);

        $folder = $request->folder_id;
        $client_folder_id = $request->client_folder_id;

        if ($folder) {
            $folder_id = $folder;
        } else {
            $folder_id = $client_folder_id;
        }

        if ($folder_id == 'None') {
            return redirect()->back();
        }

        $folder_name = $request->folder_name;

        $der = Directories::find($folder_id);
        $directoryName = Document::directoryName($folder_name, '');

        if ($der->type == 'clients') {

            $family = Family::create([
                'title' => $folder_name,
                'location' => $der->parent_id,
                'status' => 0,
                'admission' => date('Y-m-d'),
                'directory' => $directoryName
            ]);

            $type = 'families';


        } elseif ($der->type == 'clinicians') {

            $clinicians = Clinician::create([
                'type' => 1,
                'location' => $der->parent_id,
                'photo' => '',
                'first_name' => $folder_name,
                'last_name' => $folder_name,
                'middle_name' => ' ',

            ]);

            $user = User::create([
                'name' => $folder_name,
                'email' => "$directoryName@gmail.com",
                'password' => Hash::make('password'),
                'status' => 1,
                'userable_type' => 'App\Models\Clinician',
                'userable_id' => $clinicians->id,

            ]);

            $type = 'clinicians';


        } else {

            $client = Client::create([
                'family_id' => $der->family_id,
                'status' => 2,
                'photo' => '',
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
                'relationship_status' => 3,
                'date_birth' => date('Y-m-d', strtotime("-20 year")),
                'admission_date' => date('Y-m-d'),
                'gender' => '',
                'race' => 0,
                'address' => '',
                'city' => '',
                'zipcode' => '',
            ]);

            $type = 'clients';

        }

        if ($folder_id && $folder_name) {

            $directory = new Directories();
            $directory->parent_id = $folder_id;
            $directory->title = $folder_name;
            $directory->user_id = Auth::id();
            $directory->type = $type;

            if (isset($family)) {
                $directory->family_id = $family->id;
            }

            if (isset($client)) {
                $directory->client_id = $client->id;
            }

            if (isset($clinicians)) {
                $directory->clinician_id = $clinicians->id;
            }

            if ($directory->save()) {
                Storage::disk($type)->makeDirectory($directoryName);
            }
        }

        return redirect()->back()->with('status', 'Create folder');
    }

    public function getLocationFolder($id)
    {
        if ($id) {

            $type = Directories::find($id);
            $folders = DB::table('directories')
                ->select('id', 'title')
                ->where('parent_id', $id)
                ->get();

        } else {
            $folders = [];
        }

        return response()->json(['folders' => $folders, 'type' => isset($type) ? $type->type : []]);
    }


}
