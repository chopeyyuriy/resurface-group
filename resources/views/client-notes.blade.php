@forelse($client->notes()->orderBy('created_at', 'desc')->get() as $note)
    <li class="event-list">
        <div class="event-timeline-dot">
            <i class="bx bx-right-arrow-circle font-size-18"></i>
        </div>
        <div class="media">
            <img src="{{ data_get($note, 'clinician.photo') ? '/avatars/crop-32/clinician/' . data_get($note, 'clinician.photo') : asset('/assets/images/default-user.jpg') }}"
                 alt="" class="rounded-circle avatar-xs me-3">
            <div class="media-body js-note-wrap">
                <div class="d-flex align-items-center float-end mb-2">
                    <small class="text-muted fw-normal ps-sm-2 ml-auto">Last
                        edit: {{ data_get($note, 'updated_at') }}</small>
                    <div class="contact-links ms-2 ms-sm-3 d-flex">
                        <a href="#" title="Edite" class="flex-shrink-0 note_edit_btn"
                           id="clinicalNoteEdit-{{ data_get($note, 'id') }}" data-id="{{ data_get($note, 'id') }}"><i
                                    class="mdi mdi-circle-edit-outline font-size-20"></i></a>
                        <a href="#" title="Delete" class="flex-shrink-0 ms-2 note_delete_btn" data-id="{{ data_get($note, 'id') }}">
                            <i class="mdi mdi-close-circle-outline font-size-20"></i>
                        </a>
                    </div>
                </div>
                <h5 class="font-size-14 mb-0">{{ data_get($note, 'clinician.name') }}</h5>
                <div class="text-muted mb-2">{{ data_get($note, 'clinician.locationData.state_id') }} {{ data_get($note, 'clinician.locationData.city') }}</div>
                <div class="js-note note_editor" id="clinicalNote-{{ data_get($note, 'id') }}" data-id="{{ data_get($note, 'id') }}"
                     data-type="textarea" data-placeholder="Your note here...">
                    {!! nl2br(data_get($note, 'text')) !!}
                </div>
            </div>
        </div>
    </li>
@empty
    You have no messages yet
@endforelse