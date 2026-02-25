<!DOCTYPE html>
<html>

<head>
    <title>Laporan Inventaris Kendaraan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin-top: 0;
            color: #666;
        }
    </style>
</head>

<body>
    <h2>LAPORAN INVENTARIS KENDARAAN {{ $satker ? '- ' . strtoupper($satker->nama_satker) : '' }}</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @if(!$satker)
                    <th>Satker</th>
                @endif
                <th>Jenis Kendaraan</th>
                <th>No. Rangka</th>
                <th>NUP</th>
                <th>Plat Nomor</th>
                <th>Roda</th>
                <th>Kondisi</th>
                <th>BBM</th>
                <th>Penanggung Jawab</th>
                <th>Pangkat/NRP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraans as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @if(!$satker)
                        <td>{{ $item->satker->nama_satker ?? '-' }}</td>
                    @endif
                    <td>{{ $item->jenis_kendaraan }}</td>
                    <td>{{ $item->no_rangka ?? '-' }}</td>
                    <td>{{ $item->nup ?? '-' }}</td>
                    <td>{{ $item->nopol ?? '-' }}</td>
                    <td>{{ $item->jenis_roda }}</td>
                    <td>{{ $item->kondisi }}</td>
                    <td>{{ $item->bahan_bakar ?? '-' }}</td>
                    <td>{{ $item->penanggung_jawab ?? '-' }}</td>
                    <td>{{ $item->nrp ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>