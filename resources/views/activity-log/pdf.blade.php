<!DOCTYPE html>
<html>
<head>
    <title>Log Aktivitas User</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; color: #666; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>LOG AKTIVITAS USER</h2>
    <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th width="80px">Waktu</th>
                <th width="100px">User</th>
                <th width="80px">Aktivitas</th>
                <th>Detail</th>
                <th width="60px" class="text-center">Modul</th>
                <th width="120px">Info Perangkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $log->user->name ?? 'System' }}</strong><br>
                        <small>{{ $log->user->role ?? '-' }}</small>
                    </td>
                    <td>{{ $log->activity }}</td>
                    <td>{{ $log->description }}</td>
                    <td class="text-center">{{ $log->module ?? '-' }}</td>
                    <td>
                        IP: {{ $log->ip_address }}<br>
                        <small>{{ Str::limit($log->user_agent, 50) }}</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
