@extends('emails.layout')

@section('content')
<h1>Atur Ulang Password</h1>
<p>Halo {{ $notifiable->name }},</p>
<p>Kami menerima permintaan untuk mengatur ulang password akun Operra Anda. Klik tombol di bawah ini untuk membuat password baru.</p>

<div class="body-action">
    <a href="{{ $url }}" class="button button--blue" target="_blank">Reset Password</a>
</div>

<p>Tautan reset password ini akan kadaluarsa dalam {{ $count }} menit.</p>
<p>Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan.</p>

<div class="attributes">
    <div class="attributes_content">
        <p class="sub">Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:</p>
        <p class="sub"><a href="{{ $url }}">{{ $url }}</a></p>
    </div>
</div>
@endsection
