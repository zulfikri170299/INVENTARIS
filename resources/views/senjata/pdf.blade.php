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
                <th style="width: 30px; text-align: center;">NO</th>
                @if(!$satker)
                    <th>SATKER</th>
                @endif
                <th>JENIS SENJATA</th>
                <th style="width: 80px;">LARAS</th>
                @if(($context ?? 'Gudang') === 'Personel')
                    <th>NUP</th>
                    <th>NO. SENPI</th>
                    <th>KONDISI</th>
                    <th>JUMLAH AMUNISI</th>
                    <th>NAMA PENGGUNA</th>
                    <th>PANGKAT/NRP</th>
                    <th>MASA SIMSA</th>
                @else
                    <th>NUP</th>
                    <th>NO. SENPI</th>
                    <th>KONDISI</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($senjatas as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    @if(!$satker)
                        <td>{{ $item->satker->nama_satker ?? '-' }}</td>
                    @endif
                    <td style="font-weight: bold;">{{ $item->jenis_senpi }}</td>
                    <td>{{ $item->laras }}</td>
                    @if(($context ?? 'Gudang') === 'Personel')
                        <td>{{ $item->nup ?? '-' }}</td>
                        <td>{{ $item->no_senpi ?? '-' }}</td>
                        <td>{{ $item->kondisi }}</td>
                        <td style="text-align: center;">{{ $item->jumlah_amunisi_dibawa ?? 0 }}</td>
                        <td>{{ $item->penanggung_jawab ?? '-' }}</td>
                        <td>{{ $item->nrp ?? '-' }}</td>
                        <td>{{ $item->masa_berlaku_simsa ? \Carbon\Carbon::parse($item->masa_berlaku_simsa)->format('d/m/Y') : '-' }}</td>
                    @else
                        <td>{{ $item->nup ?? '-' }}</td>
                        <td>{{ $item->no_senpi ?? '-' }}</td>
                        <td>{{ $item->kondisi }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>