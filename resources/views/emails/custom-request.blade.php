<!DOCTYPE html>
<html>
<head>
    <title>Permintaan Custom CRM Baru</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #000; color: #fff; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .field { font-weight: bold; margin-bottom: 5px; }
        .value { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Operra CRM</h1>
        </div>
        <div class="content">
            <h2>Permintaan Custom CRM Baru</h2>
            <p>Anda menerima permintaan kustom baru dari landing page:</p>
            
            <div class="field">Nama:</div>
            <div class="value">{{ $formData['name'] }}</div>

            <div class="field">Nama Perusahaan:</div>
            <div class="value">{{ $formData['company_name'] }}</div>

            <div class="field">Email:</div>
            <div class="value">{{ $formData['email'] }}</div>

            <div class="field">Nomor WA:</div>
            <div class="value">{{ $formData['phone'] }}</div>

            <div class="field">Tipe Bisnis:</div>
            <div class="value">{{ $formData['business_type'] }}</div>

            <div class="field">Pesan/Kebutuhan:</div>
            <div class="value">{{ $formData['message'] }}</div>
        </div>
    </div>
</body>
</html>

