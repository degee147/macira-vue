<x-mail::message>

<h2 style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
    Messaging Failure Info
</h2>

<p style="font-family: Poppins, sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
    {{ $message }}
</p>

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>
