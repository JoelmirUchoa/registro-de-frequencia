<x-guest-layout>
    <!-- Container Principal para Visitante -->
    <div class="flex items-center justify-center bg-gray-100">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número CIM do Visitante</h3>
            <div id="visitorMessageContainer" class="hidden bg-blue-100 text-blue-800 px-4 py-2 rounded"></div>
                <div class="flex items-center mt-4">
                    <input id="visitor-sim" type="text" placeholder="Número CIM" autocomplete="off"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
                        onkeydown="handleEnterKey(event)" onblur="verifyVisitor()">
                    <button class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Pesquisar</button>
                </div>
            <div id="visitorInfo" class="mt-4 hidden">
                <p id="visitorName" class="text-gray-700 font-semibold"></p>
            </div>
            <div class="mt-2 flex justify-center space-x-4">
                <button id="register-new-visitor" onclick="openPopup('register-visitor')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-500 hidden">
                    Registrar Novo Visitante
                </button>
            </div>
            <div class="mt-2 flex justify-end space-x-4">
                <button onclick="window.location.href='{{ route('select-user') }}'"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerVisitorPresence()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>

            </div>
        </div>
    </div>

    <!-- Pop-up para Registrar Novo Visitante -->
    <div id="register-visitor-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Registrar Novo Visitante</h3>
            <div id="error-message" class="hidden text-red-500 text-sm mb-4"></div>
            <form id="registerForm" onsubmit="registerVisitor(event)" novalidate>
                <input type="text" name="name" placeholder="Nome do Visitante" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" name="position" placeholder="Cargo do Visitante" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" name="sim" placeholder="Número CIM" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" name="loja" placeholder="Loja" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <input type="text" name="numero_da_loja" placeholder="Número da Loja" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
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
        function openPopup(type) {
            const popup = document.getElementById(`${type}-popup`);
            popup.classList.remove('hidden');
            if (type === 'register-visitor') {
                // Gera um número CIM apenas numérico
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
                setTimeout(() => {
                    window.location.href = '{{ route("select-user") }}'; // Redireciona para a tela principal após 2 segundos
                }, 2000);
            } else {
                showMessage(result?.message || 'Erro ao registrar presença.', 'error');
            }
        }

        // Verificar visitante
        async function verifyVisitor() {
            const sim = document.getElementById('visitor-sim').value.trim();
            const visitorInfo = document.getElementById('visitorInfo');
            const visitorName = document.getElementById('visitorName');
            const registerNewVisitor = document.getElementById('register-new-visitor');

            // Oculta informações e botão inicialmente
            visitorInfo.classList.add('hidden');
            visitorName.textContent = '';
            registerNewVisitor.classList.add('hidden');

            if (!sim) {
                showMessage('Por favor, insira o número CIM.', 'error');
                return;
            }

            const result = await sendPostRequest('/verify-visitor', { sim });

            if (result?.success) {
                visitorName.innerHTML = `
                    Nome: ${result.data.name} <br>
                    Cargo: ${result.data.position} <br>
                    Loja: ${result.data.loja} <br>
                    Número da Loja: ${result.data.numero_da_loja}
                `;
                visitorInfo.classList.remove('hidden');
            } else if (result?.message === 'O número CIM informado já está cadastrado como irmão.') {
                showMessage(result.message, 'error');
            } else {
                showMessage(result?.message || 'Visitante não encontrado.', 'error');
                registerNewVisitor.classList.remove('hidden');
            }
        }

        // Registrar novo visitante
        async function registerVisitor(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('registerForm'));
            const data = Object.fromEntries(formData.entries());

            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.add('hidden'); // Esconde mensagens de erro anteriores
            errorMessage.textContent = ''; // Limpa mensagens anteriores

            try {
                const response = await fetch('/register-visitor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result?.success) {
                        // Fecha o pop-up e limpa o formulário
                        closePopup('register-visitor');
                        document.getElementById('registerForm').reset();
                    }
                } else if (response.status === 422) {
                    const result = await response.json();
                    if (result.message) {
                        errorMessage.textContent = result.message; // Exibe a mensagem de erro
                        errorMessage.classList.remove('hidden'); // Torna o erro visível
                    }
                } else {
                    errorMessage.textContent = 'Erro desconhecido ao registrar visitante.';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                errorMessage.textContent = 'Erro ao conectar ao servidor.';
                errorMessage.classList.remove('hidden');
            }
        }

        function handleEnterKey(event) {
            if (event.key === "Enter") {
                verifyVisitor(); // Chama a função de verificação ao pressionar Enter
                event.preventDefault(); // Evita comportamentos padrões, como o envio de formulários
            }
        }
    </script>
</x-guest-layout>
