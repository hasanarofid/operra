<x-mail::message>
# New Reply on Ticket #{{ $ticket->id }}

**Subject:** {{ $ticket->subject }}

**Reply:**
{{ $replyMessage }}

<x-mail::button :url="route('bot_antam.support.show', $ticket->id)">
View Ticket
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
