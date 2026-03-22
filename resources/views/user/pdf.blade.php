<!DOCTYPE html>
<html>
<head>
    <title>Data User</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; color: #666; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>DATA USER</h2>
    <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="30px" class="text-center">No</th>
                <th>Satker</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $user->satker->nama_satker ?? 'Global / Admin' }}</td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
