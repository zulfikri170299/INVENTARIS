<!DOCTYPE html>
<html>

<head>
    <title>Laporan Inventaris Amunisi</title>
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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
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

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2>LAPORAN INVENTARIS AMUNISI {{ $satker ? '- ' . strtoupper($satker->nama_satker) : '' }}</h2>
    <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="30px" class="text-center">No</th>
                @if(!$satker)
                    <th>Satker</th>
                @endif
                <th>Jenis Amunisi</th>
                <th class="text-right">Jumlah di Gudang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($amunisis as $index => $item)
                @php $total += $item->jumlah; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    @if(!$satker)
                        <td>{{ $item->satker->nama_satker ?? '-' }}</td>
                    @endif
                    <td>{{ $item->jenis_amunisi }}</td>
                    <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        @if(count($amunisis) > 0)
            <tfoot>
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td colspan="{{ !$satker ? 3 : 2 }}" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>

</html>