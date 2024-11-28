<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-700">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="block w-full px-4 py-2 mt-2 text-gray-700 bg-gray-100 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none">
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input id="password" type="password" name="password" required
                        class="block w-full px-4 py-2 mt-2 text-gray-700 bg-gray-100 border rounded-md focus:ring focus:ring-blue-300 focus:outline-none">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full px-4 py-2 tracking-wide text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none">
                        Entrar
                    </button>
                </div>
            </form>

            <p class="mt-4 text-sm text-center text-gray-600">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registre-se</a>
            </p>
        </div>
    </div>
</x-guest-layout>