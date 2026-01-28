@extends('emails.layout')

@section('content')
<h1>Verifikasi Email Anda</h1>
<p>Halo {{ $notifiable->name }},</p>
<p>Terima kasih telah mendaftar di Operra. Mohon klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun Anda.</p>

<div class="body-action">
    <a href="{{ $url }}" class="button button--blue" target="_blank">Verifikasi Email</a>
</div>

<p>Jika Anda tidak merasa mendaftar di Operra, Anda bisa mengabaikan email ini.</p>

<div class="attributes">
    <div class="attributes_content">
        <p class="sub">Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:</p>
        <p class="sub"><a href="{{ $url }}">{{ $url }}</a></p>
    </div>
</div>
@endsection
