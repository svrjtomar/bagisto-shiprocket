<x-admin::layouts>
    <x-slot:title>
        Shiprocket Integration
    </x-slot:title>

    <h1 class="text-2xl font-bold mb-6">🚚 Shiprocket API Integration</h1>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-100 text-green-800 border border-green-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- ================= CREDENTIALS ================= -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">API Credentials</h2>

        <form method="POST" action="{{ route('admin.shiprocket.save') }}">
            @csrf

            <div class="space-y-4 max-w-xl">
                <div>
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ core()->getConfigData('shiprocket.email') }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-medium mb-1">Password</label>
                    <input type="password"
                           name="password"
                           placeholder="Enter Shiprocket API password"
                           class="w-full border rounded px-3 py-2">
                </div>

                <button class="bg-blue-600 text-white px-6 py-2 rounded">
                    Save Credentials
                </button>
            </div>
        </form>
    </div>

    <!-- ================= TEST ================= -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">API Test</h2>

        <button onclick="testShiprocketApi()"
                class="bg-blue-600 text-white px-6 py-2 rounded">
            Test API
        </button>

        <p id="testResult" class="mt-4 font-semibold"></p>
    </div>

    <!-- ================= SAVED CREDENTIALS ================= -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Saved Credentials</h2>

        <button onclick="showCredentials()"
                class="bg-gray-700 text-white px-6 py-2 rounded">
            Show Saved Credentials
        </button>

        <div id="credentialsBox"
             class="hidden mt-4 p-4 border rounded bg-gray-50 max-w-xl">
            <p class="mb-2">
                <strong>Email:</strong>
                <span id="savedEmail"></span>
            </p>

            <p class="mb-2">
                <strong>Password:</strong>
                <span id="savedPassword">********</span>
                <button onclick="togglePassword()"
                        class="ml-2 text-sm text-blue-600 underline">
                    Reveal
                </button>
            </p>
        </div>
    </div>

    <script>
        function testShiprocketApi() {
            fetch("{{ route('admin.shiprocket.test.api') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
            })
            .catch(() => {
                alert('Server error while testing Shiprocket API');
            });
        }

        let realPassword = null;
        let passwordVisible = false;

        function showCredentials() {
            fetch('{{ url("admin/shiprocket/fetch") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('savedEmail').innerText =
                        data.email ?? 'Not set';

                    realPassword = data.password ?? null;
                    document.getElementById('savedPassword').innerText =
                        realPassword ? '********' : 'Not set';

                    document.getElementById('credentialsBox')
                        .classList.remove('hidden');
                })
                .catch(() => {
                    alert('Failed to fetch saved credentials');
                });
        }

        function togglePassword() {
            if (!realPassword) return;

            passwordVisible = !passwordVisible;
            document.getElementById('savedPassword').innerText =
                passwordVisible ? realPassword : '********';
        }
    </script>
</x-admin::layouts>
