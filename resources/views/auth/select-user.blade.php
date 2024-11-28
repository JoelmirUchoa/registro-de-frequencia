<x-guest-layout>
        <div class="flex items-center justify-center bg-gray-100">
        <div class="p-8 space-y-6 bg-white rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold text-center text-gray-700">Escolha o Tipo de Usuário</h2>
            <div class="space-y-4">
                <form method="GET" action="{{ route('dashboard') }}">
                    <button type="submit"
                        class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none">
                        Usuário Principal
                    </button>
                </form>
                <form method="GET" action="{{ route('guest.create') }}">
                    <button type="submit"
                        class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-500 focus:ring focus:ring-green-300 focus:outline-none">
                        Convidado
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>