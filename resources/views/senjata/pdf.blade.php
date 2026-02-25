<!DOCTYPE html>
<html>

<head>
    <title>Laporan Inventaris Senjata</title>
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
    <h2>LAPORAN INVENTARIS SENJATA {{ $satker ? '- ' . strtoupper($satker->nama_satker) : '' }}</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @if(!$satker)
                    <th>Satker</th>
                @endif
                <th>Jenis Senpi</th>
                <th>NUP</th>
                <th>No. Senpi</th>
                <th>Kondisi</th>
                <th>Penanggung Jawab</th>
                <th>Pangkat/NRP</th>
                <th>Status</th>
                <th>Masa Berlaku SIMSA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($senjatas as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @if(!$satker)
                        <td>{{ $item->satker->nama_satker ?? '-' }}</td>
                    @endif
                    <td>{{ $item->jenis_senpi }}</td>
                    <td>{{ $item->nup ?? '-' }}</td>
                    <td>{{ $item->no_senpi ?? '-' }}</td>
                    <td>{{ $item->kondisi }}</td>
                    <td>{{ $item->penanggung_jawab ?? '-' }}</td>
                    <td>{{ $item->nrp ?? '-' }}</td>
                    <td>{{ $item->status_penyimpanan ?? '-' }}</td>
                    <td>{{ $item->masa_berlaku_simsa ? \Carbon\Carbon::parse($item->masa_berlaku_simsa)->format('d/m/Y') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>