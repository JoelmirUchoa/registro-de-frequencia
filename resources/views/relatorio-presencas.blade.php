<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Presenças</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- Cabeçalho -->
    <header class="bg-gray-800 text-gray-100 py-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center px-4">
            <h1 class="text-xl font-bold">Relatório de Presenças</h1>
            <nav>
                <button onclick="window.location.href='{{ route('select-user') }}'" class="px-4 py-2 bg-blue-700 rounded-lg hover:bg-blue-800">
                    Voltar
                </button>
            </nav>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container mx-auto py-8 px-4">
        <!-- Formulário de Filtros -->
        <section class="mb-6 bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Filtrar Relatório</h2>
            <form method="GET" action="{{ route('presence.report') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <input type="text" name="name" placeholder="Nome" value="{{ request('name') }}" class="border p-2 rounded bg-gray-700 text-gray-100">
                <input type="text" name="sim" placeholder="SIM" value="{{ request('sim') }}" class="border p-2 rounded bg-gray-700 text-gray-100">
                <input type="text" name="position" placeholder="Cargo" value="{{ request('position') }}" class="border p-2 rounded bg-gray-700 text-gray-100">
                <select name="user_type" class="border p-2 rounded bg-gray-700 text-gray-100">
                    <option value="">Todos</option>
                    <option value="brother" {{ request('user_type') == 'brother' ? 'selected' : '' }}>Irmão</option>
                    <option value="visitor" {{ request('user_type') == 'visitor' ? 'selected' : '' }}>Visitante</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="border p-2 rounded bg-gray-700 text-gray-100">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border p-2 rounded bg-gray-700 text-gray-100">
                <div class="sm:col-span-2 lg:col-span-3 text-right">
                    <!-- Botão de filtro -->
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filtrar
                    </button>
                    <!-- Botão de limpar filtro -->
                    <button type="reset" class="px-4 py-2 ml-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700" onclick="window.location='{{ route('presence.report') }}'">
                        Limpar Filtros
                    </button>
                </div>
            </form>
        </section>

        <!-- Botão de Exportação -->
        <section class="mb-6">
            <form method="GET" action="{{ route('presence.report.pdf') }}" class="flex justify-between items-center bg-gray-800 shadow-md rounded-lg p-4">
                <div>
                    <input type="hidden" name="name" value="{{ request('name') }}">
                    <input type="hidden" name="sim" value="{{ request('sim') }}">
                    <input type="hidden" name="position" value="{{ request('position') }}">
                    <input type="hidden" name="user_type" value="{{ request('user_type') }}">
                    <input type="hidden" name="date" value="{{ request('date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Exportar PDF
                </button>
            </form>
        </section>

        <!-- Tabela de Relatório -->
        <section class="bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Resultados</h2>
            <table class="w-full border-collapse border border-gray-700 text-gray-100">
                <thead>
                    <tr class="bg-gray-700">
                        <th class="border p-2">SIM</th>
                        <th class="border p-2">Nome</th>
                        <th class="border p-2">Cargo</th>
                        <th class="border p-2">Tipo de Usuário</th>
                        <th class="border p-2">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reportData as $data)
                        <tr class="text-center">
                            <td class="border p-2">{{ $data['sim'] }}</td>
                            <td class="border p-2">{{ $data['name'] }}</td>
                            <td class="border p-2">{{ $data['position'] }}</td>
                            <td class="border p-2">{{ $data['user_type'] }}</td>
                            <td class="border p-2">{{ $data['date'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border p-4 text-center text-gray-500">
                                Nenhum dado encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Paginação -->
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <form id="filterForm" method="GET" action="{{ route('presence.report') }}">
                        <!-- Manter os filtros atuais -->
                        @foreach (request()->except('per_page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <label for="perPage" class="text-sm">Registros por página:</label>
                        <select id="perPage" name="per_page" onchange="document.getElementById('filterForm').submit()" class="bg-gray-700 text-gray-100 rounded p-2">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        </select>
                    </form>
                </div>
                <div>
                    {{ $reportData->links() }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>