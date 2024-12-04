<x-guest-layout>
    <div class="flex items-center justify-center bg-gray-100">
        <div class="p-8 space-y-6 bg-white rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold text-center text-gray-700">Escolha o Tipo de Usuário</h2>
            <div class="space-y-4">
                <!-- Botão Irmão do Quadro -->
                <button onclick="openPopup('brother')"
                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring focus:ring-blue-300 focus:outline-none">
                    Irmão do Quadro
                </button>

                <!-- Botão para Irmão Visitante -->
                <button onclick="openPopup('visitor')"
                    class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-500 focus:ring focus:ring-green-300 focus:outline-none">
                    Irmão Visitante
                </button>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-center">
    <a href="http://" class="mt-4">Acesso do Secretário</a>
    </div>

    <!-- Pop-up padrão (Irmão do Quadro e Visitante) -->
    <!-- <div id="brother-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM</h3>
            <input id="brotherSimInput" type="text" placeholder="Número SIM"
                class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="closePopup('brother')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerPresence('brother')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
        </div>
    </div> -->

    <!-- campos para exibir os dados Irmão do Quadro-->
    <div id="brother-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM</h3>
            <input id="brotherSimInput" type="text" placeholder="Número SIM" autocomplete="off"
                class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none"
                onblur="fetchBrotherData()">
            <div id="brotherInfo" class="mt-4">
                <p id="brotherName"></p>
                <p id="brotherPosition"></p>
            </div>
            <div class="mt-4 flex justify-end space-x-4">
                <button onclick="closePopup('brother')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="registerPresence('brother')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
        </div>
    </div>

    <!-- campos para exibir os dados Visitante -->
    <div id="visitor-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold text-gray-700">Digite o número SIM</h3>
            <input id="visitorSimInput" type="text" placeholder="Número SIM"
                class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
            <div class="mt-4 flex justify-center space-x-4">
                <button onclick="closePopup('visitor')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </button>
                <button onclick="verifyVisitor()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">
                    Registrar Presença
                </button>
            </div>
            <hr class="my-4">
            <button onclick="openPopup('register')"
                class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-400">
                Cadastrar Visitante
            </button>
        </div>
    </div>

    <!-- Pop-up Cadastro Visitante -->
    <div id="register-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-lg font-bold text-gray-700">Cadastrar Visitante</h3>
            <form id="registerForm">
                <input type="text" name="name" placeholder="Nome Completo"
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
                <input type="text" name="sim" placeholder="Número SIM"
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 focus:outline-none">
                <div class="mt-4 flex justify-end space-x-4">
                    <button onclick="closePopup('register')" type="button"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button onclick="registerVisitor(event)" type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openPopup(type) {
            document.getElementById(`${type}-popup`).classList.remove('hidden');
        }

        function closePopup(type) {
            clearFields(type); // Limpa os campos ao fechar o pop-up
            document.getElementById(`${type}-popup`).classList.add('hidden');
        }

        // Limpa o campo do número SIM e os dados exibidos do irmão. 
        function clearFields(type) {
            // Limpa o campo de entrada do número SIM
            document.getElementById(`${type}SimInput`).value = "";

            // Limpa os textos dos dados do irmão, caso existam
            if (type === "brother") {
                document.getElementById("brotherName").textContent = "";
                document.getElementById("brotherPosition").textContent = "";
            }
        }

        // Registrar presença (usando apenas uma função para irmãos e visitantes)
        async function registerPresence(type) {
            const simInput = document.getElementById(`${type}SimInput`).value;

            if (!simInput) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            try {
                const response = await fetch('/register-presence', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sim: simInput, type: type })  // Adiciona o tipo para diferenciar irmão do quadro e visitante
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message); // Mensagem de sucesso
                    closePopup(type); // Fechar o pop-up
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Erro ao registrar presença:', error);
                alert('Erro ao registrar presença.');
            }
        }

        // Buscar dados do irmão do quadro
        async function fetchBrotherData() {
            const sim = document.getElementById('brotherSimInput').value;

            if (!sim) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            try {
                const response = await fetch('/brother-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sim })
                });

                const result = await response.json();

                if (result.success) {
                    const brotherData = result.data;
                    document.getElementById('brotherName').textContent = `Nome: ${brotherData.name}`;
                    document.getElementById('brotherPosition').textContent = `Cargo: ${brotherData.position}`;
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Erro ao buscar os dados:', error);
                alert('Erro ao buscar os dados.');
            }
            
        }

        // Verificar e cadastrar visitante
        async function verifyVisitor() {
            const sim = document.getElementById('visitorSimInput').value;

            if (!sim) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            try {
                const response = await fetch('/verify-visitor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sim })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Visitante encontrado! Exibindo dados...');
                    closePopup('visitor');
                } else {
                    alert('Visitante não cadastrado. Use a opção "Cadastrar Visitante".');
                }
            } catch (error) {
                console.error('Erro ao verificar visitante:', error);
                alert('Erro ao verificar visitante.');
            }
        }

        // Cadastrar visitante
        async function registerVisitor(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('registerForm'));
            const name = formData.get('name');
            const sim = formData.get('sim');

            if (!name || !sim) {
                alert('Preencha todos os campos.');
                return;
            }

            try {
                const response = await fetch('/register-visitor', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name, sim })
                });

                const result = await response.json();

                if (result.success) {
                    alert(`Visitante ${name} com SIM ${sim} cadastrado com sucesso!`);
                    closePopup('register');
                } else {
                    alert('Erro ao cadastrar visitante.');
                }
            } catch (error) {
                console.error('Erro ao cadastrar visitante:', error);
                alert('Erro ao cadastrar visitante.');
            }
        }
            //informações apareçam apenas ao pressionar Enter
            document.getElementById('brotherSimInput').addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    fetchBrotherData();
                }
            });
    </script>


    <!-- <script>
        function openPopup(type) {
            document.getElementById(`${type}-popup`).classList.remove('hidden');
        }

        function closePopup(type) {
            document.getElementById(`${type}-popup`).classList.add('hidden');
        }

        function registerPresence(type) {
            const simInput = document.getElementById(`${type}SimInput`).value;
            if (simInput) {
                alert(`SIM registrado para ${type === 'brother' ? 'Irmão do Quadro' : 'Visitante'}: ${simInput}`);
                closePopup(type);
            } else {
                alert('Por favor, insira o número SIM.');
            }
        }

        function verifyVisitor() {
            const sim = document.getElementById('visitorSimInput').value;
            if (!sim) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            // Simulação de verificação no banco
            if (sim === '123456') {
                alert('Visitante encontrado! Exibindo dados...');
                closePopup('visitor');
            } else {
                alert('Visitante não cadastrado. Use a opção "Cadastrar Visitante".');
            }
        }

        function registerVisitor(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('registerForm'));
            const name = formData.get('name');
            const sim = formData.get('sim');

            if (!name || !sim) {
                alert('Preencha todos os campos.');
                return;
            }

            alert(`Visitante ${name} com SIM ${sim} cadastrado com sucesso!`);
            closePopup('register');
        }

        //adicione um evento para buscar os dados ao digitar o número SIM
        async function fetchBrotherData() {
            const sim = document.getElementById('brotherSimInput').value;

            if (!sim) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            try {
                const response = await fetch('/brother-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sim })
                });

                const result = await response.json();

                if (result.success) {
                    // Exiba os dados do irmão no pop-up
                    const brotherData = result.data;
                    document.getElementById('brotherName').textContent = `Nome: ${brotherData.name}`;
                    document.getElementById('brotherPosition').textContent = `Cargo: ${brotherData.position}`;
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Erro ao buscar os dados:', error);
                alert('Erro ao buscar os dados.');
            }
        }
        // registrar presença
        async function registerPresence() {
            const sim = document.getElementById('brotherSimInput').value;

            if (!sim) {
                alert('Por favor, insira o número SIM.');
                return;
            }

            try {
                const response = await fetch('/register-presence', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sim })
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    closePopup('brother');
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Erro ao registrar presença:', error);
                alert('Erro ao registrar presença.');
            }
        }

    </script> -->
</x-guest-layout>
