<x-guest-layout>
        <div class="flex items-center justify-center bg-gray-100">
        <div class="p-8 space-y-6 bg-white rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold text-center text-gray-700">Página do Convidado</h2>
            <p class="text-center text-gray-600">Bem-vindo à página exclusiva para convidados!</p>
            <!-- Adicione o conteúdo específico para o convidado aqui -->

            <!-- Botão de Voltar -->
            <div class="text-center mt-4">
                <form action="{{ route('select-user') }}" method="GET">
                    <button type="submit" class="w-full px-4 py-2 text-white bg-gray-600 rounded-lg hover:bg-gray-500 focus:ring focus:ring-gray-300 focus:outline-none">
                        Voltar para Seleção de Usuário
                    </button>
                </form>
            </div>
        </div>
    </div>
    <p class="mt-4 text-sm text-center text-gray-600">
         Não tem uma conta?
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registre-se</a>
    </p>
</x-guest-layout>