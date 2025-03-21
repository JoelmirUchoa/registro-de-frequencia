<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Presenças</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>
        Relatório de Presenças - 
        @if($userType === 'brother')
            Irmão do Quadro
        @elseif($userType === 'visitor')
            Irmão Visitante
        @else
            Geral
        @endif
    </h1>
    <table>
        <thead>
            <tr>
                <th>CIM</th>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Tipo</th>
                <th>Loja</th>
                <th>Número da Loja</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $data)
                <tr>
                    <td>{{ $data['sim'] }}</td>
                    <td>{{ $data['name'] }}</td>
                    <td>{{ $data['position'] }}</td>
                    <td>{{ $data['user_type'] }}</td>
                    <td>{{ $data['loja'] }}</td>
                    <td>{{ $data['numero_da_loja'] }}</td>
                    <td>{{ $data['date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
