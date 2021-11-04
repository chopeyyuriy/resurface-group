<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Clinician;
use App\Models\Document;
use App\Models\Event;
use App\Models\Faq;
use App\Models\FaqPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $limit = 30;
        $keyword = $request->keyword;

        $isAdmin = Auth::user()->hasRole('admin');

        if ($isAdmin) {

            $clinicians = Clinician::getClinician($keyword, $limit);
            $clients = Client::getClients($keyword, $limit);
            $documents = Document::getDocuments($keyword, $limit);
            $events = Event::getEvent($keyword, $limit);
            $faqs = FaqPage::getFaqs($keyword, $limit);

            $results = array_merge($clinicians, $clients, $documents, $events, $faqs);

        } else {

            $cliniciansId = Auth::id();
            $clients = Client::getClients($keyword, $limit);
            $documents = Document::getDocuments($keyword, $limit, $cliniciansId);
            $events = Event::getEvent($keyword, $limit, $cliniciansId);
            $faqs = FaqPage::getFaqs($keyword, $limit);
            $results = array_merge($clients, $documents, $events, $faqs);
        }

        return response()->json(['results' => $results]);
    }
}
