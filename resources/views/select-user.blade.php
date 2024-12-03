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

    <!-- Pop-up padrão (Irmão do Quadro e Visitante) -->
    <div id="brother-popup" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
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
    </div>

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
    </script>
</x-guest-layout>
