@extends('emails.layout')

@section('content')
<h1>Permintaan Custom CRM Baru</h1>
<p>Halo Admin,</p>
<p>Anda telah menerima permintaan Custom CRM baru dari website Operra. Berikut adalah detailnya:</p>

<div class="attributes">
    <div class="attributes_content">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="30%"><strong>Nama</strong></td>
                <td>: {{ $formData['name'] }}</td>
            </tr>
            <tr>
                <td width="30%"><strong>Perusahaan</strong></td>
                <td>: {{ $formData['company_name'] }}</td>
            </tr>
            <tr>
                <td width="30%"><strong>Email</strong></td>
                <td>: <a href="mailto:{{ $formData['email'] }}">{{ $formData['email'] }}</a></td>
            </tr>
            <tr>
                <td width="30%"><strong>Telepon</strong></td>
                <td>: {{ $formData['phone'] }}</td>
            </tr>
            <tr>
                <td width="30%"><strong>Bisnis</strong></td>
                <td>: {{ $formData['business_type'] }}</td>
            </tr>
        </table>
    </div>
</div>

<h2>Pesan Tambahan</h2>
<p style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #3869D4; font-style: italic;">
    "{{ $formData['message'] }}"
</p>

<div class="body-action">
    <a href="mailto:{{ $formData['email'] }}" class="button button--blue">Balas Email</a>
</div>
@endsection
