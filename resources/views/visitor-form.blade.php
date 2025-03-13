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
                <button class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500" onclick="verifyVisitor()">Pesquisar</button>
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
                <input type="text" name="name" placeholder="Nome do Visitante" autocomplete="off" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <p id="error-name" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>

                <input type="text" name="sim" placeholder="Número CIM" autocomplete="off" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <p id="error-sim" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>

                <select name="position" id="position" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                    <option value="">Selecione um Cargo</option>
                </select>
                <p id="error-position" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>

                <select name="loja" id="loja" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                    <option value="">Selecione uma Loja</option>
                </select>
                <p id="error-loja" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>

                <input type="text" name="numero_da_loja" placeholder="Número da Loja" autocomplete="off" required
                    class="w-full mt-4 px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-purple-300 focus:outline-none">
                <p id="error-numero_da_loja" class="text-red-500 text-sm mt-1 hidden">Campo obrigatório.</p>

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
            if (type === "register-visitor") {
                document.getElementById('registerForm').reset();
                document.getElementById('error-message').classList.add('hidden');
                document.getElementById('error-message').textContent = '';
                const inputs = document.querySelectorAll('#registerForm input, #registerForm select');
                inputs.forEach(input => {
                    input.classList.remove('border-red-500');
                });
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

        async function registerVisitorPresence() {
            const sim = document.getElementById('visitor-sim').value;
            const result = await sendPostRequest('/visitor/register-presence', { sim });
            if (result.success) {
                showModalMessage("Sucesso!", result.message);
            } else {
                showModalMessage("Erro", result.message, false);
            }
        }

        async function verifyVisitor() {
            const sim = document.getElementById('visitor-sim').value.trim();
            const visitorInfo = document.getElementById('visitorInfo');
            const visitorName = document.getElementById('visitorName');
            const registerNewVisitor = document.getElementById('register-new-visitor');

            visitorInfo.classList.add('hidden');
            visitorName.textContent = '';
            registerNewVisitor.classList.add('hidden');

            if (!sim) {
                showModalMessage("Erro", "Por favor, insira o número CIM.", false);
                return;
            }

            const result = await sendPostRequest('/verify-visitor', { sim });

            if (result.success) {
                visitorName.innerHTML = `
                    Nome: ${result.data.name} <br>
                    Cargo: ${result.data.position} <br>
                    Loja: ${result.data.loja} <br>
                    Número da Loja: ${result.data.numero_da_loja}
                `;
                visitorInfo.classList.remove('hidden');
            } else {
                showModalMessage("Erro", result.message || 'Visitante não encontrado.', false);
                registerNewVisitor.classList.remove('hidden');
            }
        }

        async function fetchVisitorData() {
            const sim = document.querySelector('input[name="sim"]').value.trim();
            if (!sim) {
                return;
            }

            const result = await sendPostRequest('/fetch-visitor-data', { sim });

            if (result.success) {
                document.querySelector('input[name="position"]').value = result.data.position;
                document.querySelector('input[name="loja"]').value = result.data.loja;
                document.querySelector('input[name="numero_da_loja"]').value = result.data.numero_da_loja;
            } else {
                showModalMessage("Erro", result.message, false);
            }
        }

        function validateFields(formId) {
            let isValid = true;
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll("input[required], select[required]");

            inputs.forEach(input => {
                const errorElement = document.getElementById(`error-${input.name}`);
                
                if (!input.value.trim()) {
                    input.classList.add("border-red-500");
                    if (errorElement) {
                        errorElement.classList.remove("hidden");
                    }
                    isValid = false;
                } else {
                    input.classList.remove("border-red-500");
                    if (errorElement) {
                        errorElement.classList.add("hidden");
                    }
                }
            });

            return isValid;
        }

        async function registerVisitor(event) {
            event.preventDefault();

            if (!validateFields('registerForm')) {
                return;
            }

            const form = document.getElementById('registerForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

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

                const result = await response.json();

                if (response.ok) {
                    closePopup('register-visitor');
                    form.reset();
                    showModalMessage("Sucesso!", "Visitante registrado com sucesso.");
                } else if (response.status === 422) {
                    showModalMessage("Erro", result.message || "Erro ao registrar visitante.", false);
                } else {
                    showModalMessage("Erro", "Erro desconhecido ao registrar visitante.", false);
                }
            } catch (error) {
                showModalMessage("Erro", "Erro ao conectar ao servidor.", false);
            }
        }

        function handleEnterKey(event) {
            if (event.key === "Enter") {
                verifyVisitor();
                event.preventDefault();
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            fetch('/api/existing-data')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const positionSelect = document.getElementById("position");
                        const lojaSelect = document.getElementById("loja");

                        // Adiciona opções de cargos
                        data.positions.forEach(position => {
                            let option = document.createElement("option");
                            option.value = position;
                            option.textContent = position;
                            positionSelect.appendChild(option);
                        });

                        // Adiciona opções de lojas
                        data.lojas.forEach(loja => {
                            let option = document.createElement("option");
                            option.value = loja;
                            option.textContent = loja;
                            lojaSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error("Erro ao carregar dados:", error));
        });

        // Remove borda vermelha ao digitar
        document.addEventListener("input", function (event) {
            if (event.target.matches("input, select")) {
                event.target.classList.remove("border-red-500");
                const errorElement = document.getElementById(`error-${event.target.name}`);
                if (errorElement) {
                    errorElement.classList.add("hidden");
                }
            }
        });
    </script>
</x-guest-layout>
