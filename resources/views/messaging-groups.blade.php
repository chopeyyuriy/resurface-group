@forelse($groups as $group)
<li>
    <a href="#" class="chat-group-item" data-id="{{ $group->id }}" data-in-group="{{ $group->in_group }}"
        data-readonly="{{ $group->readonly }}" 
        data-participants="{{ $group->participants }}">
        <div class="media align-items-center">
            <div class="avatar-xs me-3">
                <span class="avatar-title rounded-circle bg-primary bg-soft text-primary">
                    {{ Str::upper($group->name[0]) }}
                </span>
            </div>
            <div class="media-body">
                <h5 class="font-size-14 mb-0">
                    <span class="badge rounded-pill bg-danger" style="display: none;">0</span>
                    <span class="span_name {{ $group->in_group ? '' : 'text-muted' }}">{{ $group->name }}</span>
                </h5>
            </div>
        </div>
    </a>
</li>
@empty
<li></li>
@endforelse