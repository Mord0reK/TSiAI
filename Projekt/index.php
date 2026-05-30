<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteka - Logowanie</title>
    <script src="../cdn/tailwind.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-6">Biblioteka</h1>

        <!-- Formularz logowania -->
        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Login</label>
                <input type="text" name="login" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Hasło</label>
                <input type="password" name="haslo" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div id="errorMsg" class="text-red-600 text-sm hidden"></div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Zaloguj się
            </button>
        </form>

        <!-- Formularz zmiany hasła (ukryty domyślnie) -->
        <form id="zmienHasloForm" class="space-y-4 hidden mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 text-center">Musisz zmienić hasło przed kontynuacją.</p>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nowe hasło</label>
                <input type="password" name="nowe_haslo" required minlength="6"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Powtórz hasło</label>
                <input type="password" name="powtorz_haslo" required minlength="6"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div id="zmienHasloError" class="text-red-600 text-sm hidden"></div>
            <div id="zmienHasloSuccess" class="text-green-600 text-sm hidden"></div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                Zmień hasło
            </button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const zmienHasloForm = document.getElementById('zmienHasloForm');

        // --- Logowanie ---
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = new FormData(this);
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.classList.add('hidden');

            try {
                const res = await fetch('api/auth/login.php', {
                    method: 'POST',
                    body: form
                });
                const data = await res.json();

                if (data.status) {
                    if (data.typ === 'admin') {
                        window.location.href = 'panel/admin.php';
                    } else if (data.typ === 'czytelnik') {
                        if (data.zmien_haslo) {
                            // Pokaż formularz zmiany hasła
                            loginForm.classList.add('hidden');
                            zmienHasloForm.classList.remove('hidden');
                        } else {
                            window.location.href = 'panel/czytelnik.php';
                        }
                    }
                } else {
                    errorMsg.textContent = data.komunikat || 'Błąd logowania';
                    errorMsg.classList.remove('hidden');
                }
            } catch (err) {
                errorMsg.textContent = 'Błąd połączenia z serwerem';
                errorMsg.classList.remove('hidden');
            }
        });

        // --- Zmiana hasła ---
        zmienHasloForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const noweHaslo = this.nowe_haslo.value;
            const powtorzHaslo = this.powtorz_haslo.value;
            const errorEl = document.getElementById('zmienHasloError');
            const successEl = document.getElementById('zmienHasloSuccess');

            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            // Sprawdź zgodność haseł
            if (noweHaslo !== powtorzHaslo) {
                errorEl.textContent = 'Hasła nie są identyczne';
                errorEl.classList.remove('hidden');
                return;
            }

            if (noweHaslo.length < 6) {
                errorEl.textContent = 'Hasło musi mieć co najmniej 6 znaków';
                errorEl.classList.remove('hidden');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('nowe_haslo', noweHaslo);

                const res = await fetch('api/auth/zmien_haslo.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    successEl.textContent = 'Hasło zmienione. Przekierowanie...';
                    successEl.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = 'panel/czytelnik.php';
                    }, 1000);
                } else {
                    errorEl.textContent = data.komunikat || 'Błąd zmiany hasła';
                    errorEl.classList.remove('hidden');
                }
            } catch (err) {
                errorEl.textContent = 'Błąd połączenia z serwerem';
                errorEl.classList.remove('hidden');
            }
        });
    </script>

</body>
</html>
