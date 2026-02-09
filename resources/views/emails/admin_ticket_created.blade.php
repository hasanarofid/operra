<x-mail::message>
# New Support Ticket

**From:** {{ $ticket->user->name }} ({{ $ticket->company->name }})
**Subject:** {{ $ticket->subject }}

{{ $messageContent }}

<x-mail::button :url="route('admin.tickets.show', $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
