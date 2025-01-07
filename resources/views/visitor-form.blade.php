<x-guest-layout>
    <!-- Container Principal para Visitante -->
    <div class="flex items-center justify-center bg-gray-100">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM do Visitante</h3>
            <div id="visitorMessageContainer" class="hidden bg-blue-100 text-blue-800 px-4 py-2 rounded"></div>
            <input id="visitor-sim" type="text" placeholder="Número SIM" autocomplete="off"
                class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
                onblur="verifyVisitor()">
            <div id="visitorInfo" class="mt-4 hidden">
                <p id="visitorName" class="text-gray-700 font-semibold"></p>
            </div>
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="window.location.href='{{ route('select-user') }}'"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerVisitorPresence()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
                <button onclick="openPopup('register-visitor')"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500">
                    Registrar Novo Visitante
                </button>
            </div>
        </div>
    </div>

    <!-- Pop-up para Registrar Novo Visitante -->
    <div id="register-visitor-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Registrar Novo Visitante</h3>
            <form id="registerForm" onsubmit="registerVisitor(event)" novalidate>
                <input type="text" name="name" placeholder="Nome do Visitante" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" name="position" placeholder="Cargo do Visitante" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" id="generated-sim" name="sim" readonly
                    class="w-full mt-4 px-4 py-2 border border-gray-300 bg-gray-100 text-gray-600 focus:outline-none">
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="button" onclick="closePopup('register-visitor')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Função para exibir mensagens
        function showMessage(message, type = 'info') {
            const messageContainer = document.getElementById('visitorMessageContainer');
            messageContainer.textContent = message;
            messageContainer.className = `block px-4 py-2 rounded ${
                type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            }`;
            setTimeout(() => messageContainer.classList.add('hidden'), 5000);
        }

        // Função para abrir o pop-up
        // function openPopup(type) {
        //     const popup = document.getElementById(`${type}-popup`);
        //     popup.classList.remove('hidden');
        //     if (type === 'register-visitor') {
        //         document.getElementById('generated-sim').value = `SIM-${Date.now().toString().slice(-6)}`;
        //     }
        // }
        function openPopup(type) {
            const popup = document.getElementById(`${type}-popup`);
            popup.classList.remove('hidden');
            if (type === 'register-visitor') {
                // Gera um número SIM apenas numérico
                document.getElementById('generated-sim').value = Math.floor(100000 + Math.random() * 900000);
            }
        }

        // Função para fechar o pop-up
        function closePopup(type) {
            const popup = document.getElementById(`${type}-popup`);
            popup.classList.add('hidden');
        }

        // Requisição POST
        async function sendPostRequest(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });
                return response.json();
            } catch (error) {
                showMessage('Erro na comunicação com o servidor.', 'error');
            }
        }

        // Registrar presença visitante
        async function registerVisitorPresence() {
            const sim = document.getElementById('visitor-sim').value;
            const result = await sendPostRequest('/visitor/register-presence', { sim });
            if (result?.success) {
                showMessage(result.message, 'success');
            } else {
                showMessage(result?.message || 'Erro ao registrar presença.', 'error');
            }
        }

        // Verificar visitante
        async function verifyVisitor() {
            const sim = document.getElementById('visitor-sim').value;
            if (!sim) {
                showMessage('Por favor, insira o número SIM.', 'error');
                return;
            }
            const result = await sendPostRequest('/verify-visitor', { sim });
            if (result?.success) {
                document.getElementById('visitorName').textContent = `Nome: ${result.data.name}`;
                document.getElementById('visitorInfo').classList.remove('hidden');
            } else {
                showMessage(result?.message || 'Visitante não encontrado.', 'error');
            }
        }

        // Registrar novo visitante
        async function registerVisitor(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('registerForm'));
            const data = Object.fromEntries(formData.entries());
            const result = await sendPostRequest('/register-visitor', data);
            if (result?.success) {
                showMessage(result.message, 'success');
                closePopup('register-visitor');
            } else {
                showMessage(result?.message || 'Erro ao cadastrar visitante.', 'error');
            }
        }
    </script>
</x-guest-layout>
