<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Uncial+Antiqua&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">



    <title>Imprimir Presença</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .card {
            width: 8.5cm; /* Largura padrão para caber na A4 */
            height: 5.5cm; /* Altura padrão para caber na A4 */
            background-color: #ffffff;
            border: 1px solid #000;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .card p {
            margin: 3px 0;
            font-size: 14px;
        }

        .card strong {
            font-family: 'Cinzel Decorative', cursive;
            font-size: 14px;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: -1px;
        }

        .signatures p {
            font-family: 'MedievalSharp', cursive;
            font-size: 14px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            h1 {
                display: none;
            }

            .cards-container {
                gap: 10px;
                justify-content: flex-start;
            }

            .card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <h1>Presença dos Irmãos</h1>
    <div class="cards-container">
        @foreach($presences as $presence)
            <div class="card">
                <p><strong>Certificamos que o Am∴ Ir∴</strong> {{ $presence->name }}</p>
                <p><strong>CIM nº:</strong> {{ $presence->sim }}</p>
                <p><strong>Esteve presente em sessão de:</strong> {{ $presence->position }}</p>
                <p><strong>Obreiro da Loja:</strong> {{ $presence->loja }}</p>
                <p><strong>Número da Loja:</strong> {{ $presence->numero_da_loja }}</p>
                <p><strong>Data:</strong> {{ formatarData($presence->created_at) }}</p>
                <div class="signatures">
                    <p><strong>Chanc∴</strong>
                    <p><strong>Ven∴ Mestre:</strong>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

<?php
function formatarData($data) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];

    $dia = $data->format('d');
    $mes = $meses[(int)$data->format('m')];
    $ano = $data->format('Y');

    return "$dia de $mes de $ano";
}
?>
