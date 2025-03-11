<x-guest-layout>
    <!-- Container Principal -->
    <div class="flex items-center justify-center">
        <div class="p-8 space-y-6 bg-gray-700 bg-opacity-50 rounded-lg shadow-md w-full sm:w-96">
            <h2 class="text-2xl font-bold text-center text-gray-100">Registrar Presença</h2>
            <div class="space-y-4">
                <button onclick="openPopup('brother')"
                    class="w-full px-8 py-6 text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none mb-6 text-xl font-bold">
                    Irmão do Quadro
                </button>
                
                <button onclick="window.location.href='{{ route('visitor-page') }}'"
                    class="w-full px-8 py-6 text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none text-xl font-bold">
                    Irmão Visitante
                </button>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center">
        <a href="#" onclick="document.getElementById('login-popup').classList.remove('hidden');" 
           class="font-bold mt-4 text-white bg-gray-800 bg-opacity-50">Acesso do Chanceler</a>

    </div>
    <!-- Pop-up de Login -->
    <div id="login-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Login</h3>
            <form method="POST" action="{{ route('login.chancellor') }}">
                @csrf
                <div class="mt-4">
                    <input type="text" name="username" placeholder="Nome" autocomplete="off" required 
                        class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
                </div>
                <div class="mt-4">
                    <input type="password" name="password" placeholder="Senha" required
                        class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
                </div>
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="button" onclick="document.getElementById('login-popup').classList.add('hidden');"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
    

    <!-- Pop-up para Registrar Presença -->
    <div id="brother-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número CIM</h3>
            <div class="flex items-center mt-4">
                <input id="brother-sim" type="text" placeholder="Número CIM" autocomplete="off"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
                    onblur="fetchBrotherData()">
                <button class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Pesquisar</button>
            </div>
            <div id="brotherInfo" class="mt-4 text-gray-700">
                <p id="brotherName"></p>
                <p id="brotherPosition"></p>
                <p id="brotherLoja"></p>
                <p id="brotherNumeroLoja"></p>
            </div>
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="closePopup('brother')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerBrotherPresence()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Mensagem -->
    <div id="message-modal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h3 id="modal-title" class="text-2xl font-extrabold uppercase"></h3>
            <p id="modal-message" class="mt-2 text-xl font-bold"></p>
        </div>
    </div>

    <script>
    function openPopup(type) {
        document.getElementById(`${type}-popup`).classList.remove('hidden');
    }

    function closePopup(type) {
        document.getElementById(`${type}-popup`).classList.add('hidden');
        clearFields(type);
    }

    function clearFields(type) {
        if (type === "brother") {
            document.getElementById('brother-sim').value = "";
            document.getElementById('brotherName').textContent = "";
            document.getElementById('brotherPosition').textContent = "";
            document.getElementById('brotherLoja').textContent = "";
            document.getElementById('brotherNumeroLoja').textContent = "";
        }
    }

    async function sendPostRequest(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Erro na comunicação com o servidor.' };
        }
    }

    function showModalMessage(title, message, success = true) {
        const modal = document.getElementById('message-modal');
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-message').textContent = message;
        modal.classList.remove('hidden');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (success) {
                window.location.href = "{{ route('select-user') }}";
            }
        }, 3000);
    }

    function registerBrotherPresence() {
        const sim = document.getElementById('brother-sim').value;

        if (!sim) {
            showModalMessage("Erro", "Por favor, insira o número CIM.", false);
            return;
        }

        fetch('/brother/register-presence', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ sim })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModalMessage("Sucesso!", "Presença registrada com sucesso.");
            } else {
                showModalMessage("Erro", data.message, false);
            }
        })
        .catch(() => {
            showModalMessage("Erro", "Erro na comunicação com o servidor.", false);
        });
    }

    async function fetchBrotherData() {
        const sim = document.getElementById('brother-sim').value;

        if (!sim) {
            showModalMessage("Aviso", "Por favor, insira o número CIM.", false);
            return;
        }

        const result = await sendPostRequest('/brother-data', { sim });

        if (result.success) {
            if (result.type === 'brother') {
                const brotherData = result.data;
                document.getElementById('brotherName').textContent = `Nome: ${brotherData.name}`;
                document.getElementById('brotherPosition').textContent = `Cargo: ${brotherData.position}`;
                document.getElementById('brotherLoja').textContent = `Loja: ${brotherData.loja}`;
                document.getElementById('brotherNumeroLoja').textContent = `Número da Loja: ${brotherData.numero_da_loja}`;
            }
        } else {
            if (result.type === 'visitor') {
                showModalMessage("Aviso", `Este CIM já está cadastrado como visitante.`, false);
            } else {
                showModalMessage("Erro", result.message, false);
            }
        }
    }
    </script>
</x-guest-layout>
