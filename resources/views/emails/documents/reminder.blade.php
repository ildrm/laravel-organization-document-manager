<x-mail::message>
# {{ __('common.document_reminder') }}

{{ __('common.reminder_body_text', ['title' => $reminder->document->title]) }}

**{{ __('common.field') }}:** {{ $reminder->field_key }}
**{{ __('common.date') }}:** {{ $reminder->reminder_at->toDateTimeString() }}

<x-mail::button :url="config('app.url') . '/app/documents/' . $reminder->document_id">
{{ __('common.view_document') }}
</x-mail::button>

{{ __('common.thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>
