<x-guest-layout>
    <!-- Container Principal para Visitante -->
    <div class="flex items-center justify-center bg-gray-100">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM do Visitante</h3>
            <div id="visitorMessageContainer" class="hidden"></div>
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
                <button onclick="registerVisitorPresence('')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
        </div>
    </div>

    <script>
    // Função para exibir mensagens
    function showMessage(message) {
        const messageContainer = document.getElementById('visitorMessageContainer');
        messageContainer.textContent = message;
        messageContainer.classList.remove('hidden');
        
        // Esconde a mensagem após 5 segundos
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }

    // Função para abrir o pop-up
    function openPopup(type) {
        document.getElementById(`${type}-popup`).classList.remove('hidden');
        clearMessages(type); // Limpa as mensagens ao abrir o pop-up
    }

    // Função para fechar o pop-up
    function closePopup(type) {
        clearFields(type); // Limpa os campos ao fechar o pop-up
        document.getElementById(`${type}-popup`).classList.add('hidden');
    }

    // Limpa os campos de entrada e dados exibidos
    function clearFields(type) {
        document.getElementById(`${type}SimInput`).value = ""; // Limpa o campo do número SIM

        if (type === "visitor") {
            document.getElementById("visitorName").textContent = "";
        }
    }

    // Limpa as mensagens exibidas
    function clearMessages(type) {
        if (type === "visitor") {
            document.getElementById('visitorName').textContent = "";
        }
    }

    // Enviar requisição POST com dados
    async function sendPostRequest(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            if (error instanceof TypeError) {
                showMessage('Erro de rede. Verifique sua conexão.');
            } else {
                showMessage('Ocorreu um erro inesperado.');
            }
        }
    }

    // Registrar presença (para irmãos e visitantes)
    function registerVisitorPresence() {
    const sim = document.querySelector('#visitor-sim').value; // Campo de SIM do visitante

    fetch('/visitor/register-presence', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ sim: sim })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro na comunicação com o servidor.');
    });
}

    // Verificar se o visitante já está registrado
    async function verifyVisitor() {
        const sim = document.getElementById('visitor-sim').value;

        if (!sim) {
            showMessage('Por favor, insira o número SIM.');
            return;
        }

        const result = await sendPostRequest('/verify-visitor', { sim });

        if (result.success) {
            const visitorData = result.data;
            document.getElementById('visitorName').textContent = `Nome: ${visitorData.name}`;
            document.getElementById('visitorInfo').classList.remove('hidden');
        } else {
            showMessage('Visitante não encontrado.');
        }
    }
    </script>
</x-guest-layout>
