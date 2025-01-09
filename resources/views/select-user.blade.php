<x-guest-layout>
    <!-- Container Principal -->
    <div class="flex items-center justify-center bg-gray-100">
        <div class="p-8 space-y-6 bg-white rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold text-center text-gray-700">Escolha o Tipo de Usuário</h2>
            <div class="space-y-4">
                <!-- Botão Irmão do Quadro -->
                <button onclick="openPopup('brother')"
                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none">
                    Irmão do Quadro
                </button>
                
                <!-- Botão Visitante -->
                <button onclick="window.location.href='{{ route('visitor-page') }}'"
                    class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-500 focus:ring focus:ring-green-300 focus:outline-none">
                    Visitante
                </button>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-center">
        <a href="{{ route('presence.report')}}" class="mt-4">Acesso do Secretário</a>
    </div>

    <!-- Pop-up para Irmão do Quadro -->
    <div id="brother-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM</h3>
            <div id="messageContainer" class="hidden"></div>
            <input id="brother-sim" type="text" placeholder="Número SIM" autocomplete="off"
                class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
                onblur="fetchBrotherData()">
            <div id="brotherInfo" class="mt-4">
                <p id="brotherName" class="text-gray-700 font-semibold"></p>
                <p id="brotherPosition" class="text-gray-500"></p>
                <p id="brotherLoja" class="text-gray-500"></p>
                <p id="brotherNumeroLoja" class="text-gray-500"></p>
            </div>
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="window.location.href='{{ route('select-user') }}'"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerBrotherPresence('')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
        </div>
    </div>

    <script>
    // Função para exibir mensagens
    function showMessage(message) {
        const messageContainer = document.getElementById('messageContainer');
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
    // function closePopup(type) {
    //     clearFields(type); // Limpa os campos ao fechar o pop-up
    //     document.getElementById(`${type}-popup`).classList.add('hidden');
    // }
    function closePopup(type) {
    const popup = document.getElementById(`${type}-popup`);
    if (popup) {
        popup.classList.add('hidden');
    }
    clearFields(type);
}

    // Limpa os campos de entrada e dados exibidos
    function clearFields(type) {
        document.getElementById(`${type}SimInput`).value = ""; // Limpa o campo do número SIM

        if (type === "brother") {
            document.getElementById("brotherName").textContent = "";
            document.getElementById("brotherPosition").textContent = "";
        }
    }

    // Limpa as mensagens exibidas
    function clearMessages(type) {
        if (type === "brother") {
            document.getElementById('brotherName').textContent = "";
            document.getElementById('brotherPosition').textContent = "";
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

     // Função para exibir mensagens de confirmação
     function showConfirmationMessage(message) {
        const confirmationContainer = document.createElement('div');
        confirmationContainer.textContent = message;
        confirmationContainer.style.position = 'fixed';
        confirmationContainer.style.top = '20px';
        confirmationContainer.style.right = '20px';
        confirmationContainer.style.backgroundColor = '#28a745';
        confirmationContainer.style.color = 'white';
        confirmationContainer.style.padding = '10px 20px';
        confirmationContainer.style.borderRadius = '5px';
        confirmationContainer.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.3)';
        confirmationContainer.style.zIndex = '1000';

        document.body.appendChild(confirmationContainer);

        // Remove a mensagem após 3 segundos
        setTimeout(() => {
            document.body.removeChild(confirmationContainer);
        }, 3000);
    }

    // Registrar presença (para irmãos)
    function registerBrotherPresence() {
        const sim = document.querySelector('#brother-sim').value; // Campo de SIM do irmão

        fetch('/brother/register-presence', {
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
                // Exibe a mensagem de confirmação
                showConfirmationMessage(data.message);

                // Fecha o pop-up
                closePopup('brother');
            } else {
                showMessage('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            showMessage('Erro na comunicação com o servidor.');
        });
    }

    // Função para fechar o pop-up
    function closePopup(type) {
        const popup = document.getElementById(`${type}-popup`);
        if (popup) {
            popup.classList.add('hidden');
        }
        clearFields(type);
    }

    // Limpa os campos de entrada e dados exibidos
    function clearFields(type) {
        document.getElementById(`${type}SimInput`).value = ""; // Limpa o campo do número SIM

        if (type === "brother") {
            document.getElementById("brotherName").textContent = "";
            document.getElementById("brotherPosition").textContent = "";
        }
    }

    // Função para exibir mensagens de erro ou informações
    function showMessage(message) {
        const messageContainer = document.getElementById('messageContainer');
        messageContainer.textContent = message;
        messageContainer.classList.remove('hidden');
        
        // Esconde a mensagem após 5 segundos
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }


    // Buscar dados do irmão do quadro
    async function fetchBrotherData() {
        const sim = document.getElementById('brother-sim').value;

        if (!sim) {
            showMessage('Por favor, insira o número SIM.');
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
                showMessage(`Aviso: Este SIM já está cadastrado como visitante. Nome: ${result.data.name}`);
            } else {
                showMessage(result.message);
            }
        }
    }

    //<visitante>
    // Verificar se o visitante já está registrado
    async function verifyVisitor() {
        const sim = document.getElementById('visitorSimInput').value;

        if (!sim) {
            showMessage('Por favor, insira o número SIM.');
            return;
        }

        const result = await sendPostRequest('/verify-visitor', { sim });

        if (result.success) {
            showMessage('Visitante encontrado! Exibindo dados...');
            closePopup('visitor');
        } else {
            showMessage('Visitante não cadastrado. Use a opção "Cadastrar Visitante".');
        }
    }

    // Cadastrar novo visitante
    async function registerVisitor(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById('registerForm'));
        const name = formData.get('name');
        const sim = formData.get('sim');

        if (!name || !sim) {
            showMessage('Preencha todos os campos.');
            return;
        }

        const result = await sendPostRequest('/register-visitor', { name, sim });

        if (result.success) {
            showMessage(`Visitante ${name} com SIM ${sim} cadastrado com sucesso!`);
            closePopup('register');
        } else {
            showMessage('Erro ao cadastrar visitante.');
        }
    }

    // Buscar dados ao pressionar Enter
    document.getElementById('brother-sim').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            fetchBrotherData();
        }
    });
    //</visitante>
</script>

</x-guest-layout>