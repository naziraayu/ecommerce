@component('mail::message')
# Konfirmasi Perubahan Email

Silakan klik tombol di bawah untuk mengonfirmasi perubahan email Anda:

@component('mail::button', ['url' => $verificationUrl])
Konfirmasi Perubahan Email
@endcomponent

Jika Anda tidak meminta perubahan ini, abaikan email ini.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
