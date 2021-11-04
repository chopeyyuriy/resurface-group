<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', 'AuthController@login')->name('login');
Route::any('/logout', 'AuthController@logout')->name('logout');
Route::post('/auth', 'AuthController@auth')->name('auth');

Route::get('/registration', 'RegistrationController@view')->name('registration.view');
Route::post('/registration/save', 'RegistrationController@save')->name('registration.save');

Route::get('/forgot-password', 'ResetPasswordController@forgotPassword')->name('forgot');
Route::post('/forgot-password/reset', 'ResetPasswordController@resetPassword')->name('forgot.reset');
Route::get('/forgot-password/{token}', 'ResetPasswordController@getToken')->name('password.reset');
Route::post('/password-update', 'ResetPasswordController@passwordUpdate')->name('password.update');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'DashboardController@index')->name('root');
    Route::get('/root/dash-json/{now}', 'DashboardController@dashJson')->name('root.dash_json');
    Route::get('/root/event-json/{id}', 'DashboardController@eventJson')->name('root.event_json');

    Route::any('/phone-delete/{id}', function ($id) {
        $status = \App\Helpers\PhoneHelper::delete($id);
        $statusText = ($status ? 'success' : 'fail');
        return response()->json(['status' => $statusText]);
    });

    Route::get('/clinician-directory', 'ClinicianController@index')->name('clinician_directory');
    Route::get('/clinician-directory/table', 'ClinicianController@table')->name('clinician.table');
    Route::get('/clinician-directory/form/{id}', 'ClinicianController@form')->name('clinician.form');
    Route::post('/clinician-directory/save/{id}', 'ClinicianController@save')->name('clinician.save');
    Route::get('/clinician-directory/delete/{id}', 'ClinicianController@delete')->name('clinician.delete');
    Route::get('/clinician-directory/detach/client/{client_id}/{clinician_id}', 'ClinicianController@detachClient')->name('clinician.detach.client');

    Route::any('/erm', 'FamilyController@getList')->name('erm');
    Route::post('/erm/json-data/{id}', 'FamilyController@jsonData')->name('erm.json.family');
    Route::post('/erm/create', 'FamilyController@create')->name('erm.add.family');
    Route::post('/erm/update', 'FamilyController@update')->name('erm.update.family');
    Route::get('/erm/delete/{id}', 'FamilyController@delete')->name('erm.delete.family');

    Route::get('/client/{id}', 'ClientController@view')->name('client.view');
    Route::post('/client/create', 'ClientController@create')->name('client.add');
    Route::post('/client/update/{id}', 'ClientController@update')->name('client.update');
    Route::post('/client/upload/file', 'ClientController@clientUploadsFile')->name('client.upload.file');
    Route::get('/client/detach/clinician/{client_id}/{clinician_id}', 'ClientController@detachClinician')->name('client.detach.clinician');
    Route::get('/client/delete/{id}', 'ClientController@delete')->name('client.delete');

    Route::get('/note/{client_id}', 'NoteController@index')->name('note');
    Route::post('/note/save/{id}', 'NoteController@save')->name('note.save');
    Route::any('/note/update/{id}', 'NoteController@update')->name('note.update');
    Route::get('/note/delete/{id}', 'NoteController@delete')->name('note.delete');

    Route::get('/documents', 'DocumentController@view')->name('documents');
    Route::any('/document/table/clients', 'DocumentController@clientsTable')->name('document.clients');
    Route::any('/documents/delete/file/{id}', 'DocumentController@documentDeleteFile')->name('documents.client.delete');
    Route::any('/document/folder/new', 'DocumentController@documentNewFolder')->name('document.new.folder');
    Route::get('/documents/location-folder/{id}', 'DocumentController@getLocationFolder')->name('get_location_folder');

    Route::get('/search', 'SearchController@search')->name('search');

    Route::any('/document/table/client', 'DocumentController@clientTable')->name('document.client');
    Route::post('/document/upload/client', 'DocumentController@clientUploads')->name('document.client.upload');
    Route::any('/document/download/client/{id}', 'DocumentController@clientDownload')->name('document.client.download');
    Route::any('/document/delete/client/{id}', 'DocumentController@clientDelete')->name('document.client.delete');
    Route::post('/document/rename/client/{id}', 'DocumentController@clientRename')->name('document.client.rename');

    Route::get('/calendar/event-modal', 'CalendarController@eventModal')->name('calendar.event_modal');
    Route::post('/calendar/create', 'CalendarController@create')->name('calendar.add');
    Route::post( '/calendar/remove/{event_id}', 'CalendarController@remove')->name('calendar.event.remove');
    Route::get('/calendar/is-admin', 'CalendarController@isAdmin')->name('calendar.is_admin');

    Route::any('/calendar/{user_id?}', 'CalendarController@index')->name('calendar');
    Route::get('/calendar/view-events/{user_id}', 'CalendarController@viewEvents')->name('calendar.view');

    Route::post('/new-time-entry', 'TimeEntriesController@create')->name('new_time_entry');
    Route::post('/edit-time-entry/json/{id}', 'TimeEntriesController@jsonData')->name('edit_time_entry.json');
    Route::post('/edit-time-entry', 'TimeEntriesController@update')->name('edit_time_entry');
    Route::get('/del-time-entry/{id}', 'TimeEntriesController@delete')->name('del_time_entry');

    Route::get('/time-reporting', 'TimeReportingController@index')->name('time_reporting');
    Route::get('/time-reporting/data', 'TimeReportingController@data')->name('time_reporting.data');

    Route::get('/messaging', 'MessagingController@index')->name('messaging');
    Route::get('/messaging/load/chats', 'MessagingController@chatList')->name('messaging.load.chats');
    Route::get('/messaging/load/groups', 'MessagingController@groupList')->name('messaging.load.groups');
    Route::get('/messaging/data/{lastId}/{groupId?}', 'MessagingController@data')->name('messaging.data');
    Route::post('/messaging/send', 'MessagingController@send')->name('messaging.send');
    Route::get('/messaging/check-new-messages', 'MessagingController@checkNewMessages')->name('messaging.check_new_messages');
    Route::get('/messaging/set-as-seen-message/{groupId}/{id}', 'MessagingController@setAsSeenMessage')->name('messaging.set_as_seen_message');
    Route::post('/messaging/group/add', 'MessagingController@addGroup')->name('messaging.group.add');
    Route::get('/messaging/group/del/{id}', 'MessagingController@delGroup')->name('messaging.group.del');
    Route::get('/messaging/group/clear/{id}', 'MessagingController@clearGroup')->name('messaging.group.clear');
    Route::get('/messaging/group/add-user/{groupId}/{id}', 'MessagingController@addUserToGroup')->name('messaging.group.add_user');
    Route::get('/messaging/group/del-user/{groupId}/{id}', 'MessagingController@delUserFromGroup')->name('messaging.group.del_user');
    Route::get('/messaging/search', 'MessagingController@search')->name('messaging.search');

    Route::post( '/calendar/remove/{event_id}', 'CalendarController@remove')->name('calendar.event.remove');
    
    Route::get('/faq', 'FaqPagesController@index')->name('faq');
    Route::get('/faq/list', 'FaqPagesController@data')->name('faq.list');
    Route::get('/faq/page/{id}', 'FaqPagesController@showPage')->name('faq.page');
    Route::get('/faq/page-edit/{id?}', 'FaqPagesController@showEditor')->name('faq.page_edit');
    Route::post('/faq/page-edit/{id?}', 'FaqPagesController@postEditor')->name('faq.page_edit');
    Route::get('/faq/page-delete/{id}', 'FaqPagesController@deletePage')->name('faq.page_delete');
    Route::post('/faq/page-upload-file', 'FaqPagesController@pageUploadFile')->name('faq.page_upload_file');
    Route::get('/faq/page-image/{img}', 'FaqPagesController@pageImage')->name('faq.page_image');
    
    Route::post('/root/onesignal', 'AuthController@setOnesignalPlayerId')->name('onesignal.player_id');

    /*Route::get('/user-profile', function () {
        return view('user-profile');
    })->name('user_profile');*/

    /*Route::get('/edit-page', function () {
        return view('edit-page');
    })->name('edit_page'); */

    /*Route::get('/messaging', function () {
        return view('messaging');
    })->name('messaging'); */

    /*Route::get('/time-reporting', function () {
        return view('time-reporting');
    })->name('time_reporting');*/

    /*Route::get('/contacts-profile', function () {
        return view('contacts-profile');
    })->name('contacts_profile');*/

    /*Route::get('/editable-pages', function () {
        return view('editable-pages');
    })->name('editable_pages');*/

    /*Route::post('/new-time-entry', function () {
        return \Request::all();
    })->name('new_time_entry');*/

});
