@forelse($events as $row)
@if($loop->index >= 5) @break @endif
<tr>
    <td>{{ $loop->index + 1 }}</td>
    <td>
        {{ \Carbon\Carbon::parse($row->date)->format('m/d/Y') }}
        {{ \Carbon\Carbon::parse($row->from)->format('g:i A') }}
    </td>
    <td>{{ $row->subject }}</td>
    <td class="td-fit">
        <button class="btn btn-primary btn-sm btn-rounded waves-effect waves-light show_event" data-id="{{ $row->id }}" type="button">View</button>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" style="text-align: center;">No data available in table</td>
</tr>
@endforelse