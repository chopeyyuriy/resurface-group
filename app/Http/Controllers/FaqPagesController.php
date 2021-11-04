<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqPage;
use Storage;
use Auth;

class FaqPagesController extends Controller
{
    const UPLOAD_IMAGE_PATH = 'faq/';
    
    /**
     * This route to displaing index view.
     * 
     * @return type
     */
    public function index()
    {
        return view('editable-pages');
    }
    
    /**
     * This is the route to request json data for the index page.
     * 
     * @param Request $request
     */
    public function data(Request $request)
    {
        $user_id = Auth::user()->id;
        $isAdmin = Auth::user()->hasRole('admin');
        
        $pages = FaqPage::with('user')
            ->when(!$isAdmin, function ($query) use ($user_id) {
                $query->where(function ($q) use ($user_id) {
                    $q->whereUserId($user_id)->orWhere('status', 1);
                });
            })
            ->when($request->search_text, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%'.$request->search_text.'%')
                        ->orWhereHas('user', function ($q2) use ($request) {
                            $q2->where('name', 'like', '%'.$request->search_text.'%');
                        });
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereStatus($request->status);
            })
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('updated_at', [$start, $end]);
            })
            ->get();
        
        return datatables()->of($pages)
            ->editColumn('title', function ($entry) {
                return '<h5 class="font-size-14 mb-1"><a href="'.route('faq.page', $entry->id).'" class="text-dark">'.$entry->title.'</a></h5>';
            })
            ->editColumn('user', function ($entry) {
                return $entry->user->name;
            })
            ->editColumn('status', function ($entry) {
                return $entry->status();
            })
            ->addColumn('action', function ($entry) use ($user_id, $isAdmin) {
                if ($entry->user_id == $user_id || $isAdmin) {
                    return '<ul class="list-inline font-size-20 contact-links mb-0">
                        <li class="list-inline-item pe-2"><a href="'.route('faq.page_edit', $entry->id).'" title="Edit"><i class="mdi mdi-circle-edit-outline"></i></a></li>
                        <li class="list-inline-item px-2"><a href="#" data-id="'.$entry->id.'" title="Delete" class="faq-page-del"><i class="mdi mdi-close-circle-outline"></i></a></li>
                    </ul>';
                } else {
                    return '';
                }
            })
            ->rawColumns(['title', 'action'])
            ->make(true);
    }
    
    /**
     * This is the route to display faq page by id.
     * 
     * @param int $id
     */
    public function showPage(int $id)
    {
        $item = FaqPage::findForViewWithAuth($id);
        $isAuthor = Auth::user()->hasRole('admin') || $item->user_id == Auth::user()->id;
        
        return view('editable-page-view', [
            'item' => $item,
            'isAuthor' => $isAuthor,
        ]);
    }
    
    /**
     * This is the route to display the faq page editor by id.
     * 
     * @param int $id
     * @return type
     */
    public function showEditor(int $id = null)
    {
        if ($id) {
            $item = FaqPage::findForEditWithAuth($id);
        } else { // Using the model as a repository
            $item = new FaqPage();
            $item->id = -1;
            $item->title = 'This Page Title';
            $item->status = 2;
            $item->data = '';
        }
        
        return view('edit-page', [
            'item' => $item,
        ]);
    }
    
    /**
     * This is the route to create or update an faq page by id.
     * 
     * @param int $id
     * @return type
     */
    public function postEditor(Request $request, int $id = null)
    {
        if ($id > 0) {
            $item = FaqPage::findForEditWithAuth($id);
        } else { // Using the model as a repository
            $item = new FaqPage();
            $item->user_id = Auth::user()->id;
        }
        
        $item->title = $request->title;
        $item->status = $request->status;
        $item->data = $request->data ?? '';
        $item->save();

        return redirect(route('faq'));
    }
    
    /**
     * This is the route to delete the faq page by id.
     * 
     * @param int $id
     * @return string
     */
    public function deletePage(int $id)
    {
        $item = FaqPage::findForEditWithAuth($id);
        $item->delete();
        return 'OK';
    }
    
    /**
     * This is the route to upload faq image or file.
     * 
     * @param Request $request
     * @return type
     */
    public function pageUploadFile(Request $request)
    {
        if (!$request->file('file')) {
            abort(422);
        }
        
        $file = $request->file('file');
        $file->storePublicly(self::UPLOAD_IMAGE_PATH);
        $location = route('faq.page_image', $file->hashName());
        
        return response()->json([
            'location' => $location,
        ]);
    }
    
    /**
     * This is the route to download faq inage or file.
     * 
     * @param type $img
     * @return type
     */
    public function pageImage($img)
    {
        return Storage::response(self::UPLOAD_IMAGE_PATH.$img);
    }
}
