<!DOCTYPE html>
<html>
<head>
    <title>Data Satuan Kerja</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; color: #666; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>DATA SATUAN KERJA</h2>
    <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="30px" class="text-center">No</th>
                <th>Nama Satuan Kerja</th>
                <th class="text-right">Total Inventaris</th>
            </tr>
        </thead>
        <tbody>
            @foreach($satkers as $index => $satker)
                @php
                    $total = ($satker->senjatas_count ?? 0) +
                        ($satker->kendaraans_count ?? 0) +
                        ($satker->alsuses_count ?? 0) +
                        ($satker->alsintors_count ?? 0) +
                        ($satker->amunisis_count ?? 0);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $satker->nama_satker }}</strong></td>
                    <td class="text-right">{{ number_format($total, 0, ',', '.') }} Item</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
