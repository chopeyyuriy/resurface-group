<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
use App\Http\Requests\NoteUpdateRequest;
use App\Models\Note;
use App\Models\Client;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index($client_id)
    {
        return Note::where('client_id', $client_id)->get();
    }

    public function save(NoteRequest $request, $id)
    {
        $clinicianID = (int)$request->input('clinician_id');
        $clientID = (int)$request->input('client_id');

        Note::updateOrCreate(
            ['id' => $id],
            [
                'clinician_id' => $clinicianID,
                'client_id' => $clientID,
                'text' => htmlspecialchars($request->input('text'))
            ]
        );

        if(empty($id)) {
            $status = 'Note created';
        } else {
            $status = 'Note updated';
        }
        
        return view('client-notes', [
            'client' => Client::find($clientID),
        ]);

        //return redirect(route('client.view', ['id' => $clientID]))->with('status', $status);
    }

    public function update(NoteUpdateRequest $request, $id)
    {
        $note = Note::find($id);
        $note->text = htmlspecialchars($request->text);
        $note->save();
        
        return view('client-notes', [
            'client' => Client::find($note->client_id),
        ]);
        
        /*$note = Note::where('id', $id)->update([
            'text' => $request->input('text')
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $note
        ]); */
    }

    public function delete($id)
    {
        $note = Note::find($id);

        if(!empty($note)) {
            $clientID = data_get($note, 'client.id');
            $client = Client::find($clientID);
            $note->delete();
            
            return view('client-notes', [
                'client' => $client,
            ]);
        }
        
        abort(404);

        //return redirect(route('client.view', ['id' => $clientID]))->with('status', 'Note deleted');
    }
}
