<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $letter->letterType->name }} - {{ $letter->request_number }}</title>
<style>
    @page {
        size: A4;
        margin: 2.5cm 2.5cm 2cm 3cm;
    }
    body {
        font-family: "Times New Roman", serif;
        font-size: 12pt;
        line-height: 1.5;
        color: #000;
        margin: 0;
    }
    .header {
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 12px;
        margin-bottom: 25px;
    }
    .header h2 {
        font-size: 16pt;
        margin: 0;
        font-weight: bold;
    }
    .header p {
        font-size: 11pt;
        margin: 4px 0;
    }
    .letter-title {
        text-align: center;
        margin: 20px 0;
    }
    .letter-title h3 {
        font-size: 14pt;
        font-weight: bold;
        text-decoration: underline;
        margin: 0;
        text-transform: uppercase;
    }
    .letter-title p {
        margin: 5px 0;
    }
    .content {
        text-align: justify;
        white-space: pre-line;
    }
    .signature {
        width: 250px;
        margin-left: auto;
        margin-top: 35px;
        text-align: center;
        page-break-inside: avoid;
    }
    .signature-space {
        height: 75px;
    }
    .footer {
        margin-top: 35px;
        font-size: 9pt;
        text-align: center;
    }
</style>
</head>
<body>

<div class="header">
    <h2>RUKUN WARGA 05</h2>
    <p>
        Alamanda Regency, Blok I, Tambun Utara,
        Kabupaten Bekasi
    </p>
</div>

<div class="letter-title">
    <h3>{{ strtoupper($letter->letterType->name) }}</h3>
    <p>Nomor: {{ $letter->output->document_number }}</p>
</div>

<div class="content">
    {!! $renderedHtml !!}
</div>

<div class="signature">
    <p>
        Tambun Utara,
        {{ \Carbon\Carbon::parse(
            $letter->completed_at ?? $letter->output->generated_at
        )->locale('id')->translatedFormat('d F Y') }}
    </p>

    <p><strong>Ketua Rukun Warga 05</strong></p>

    <div class="signature-space"></div>

    <p><strong>H. AHMAD SURYANA</strong></p>
</div>

</body>
</html>
