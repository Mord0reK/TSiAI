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
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
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
                            window.location.href = 'panel/zmien_haslo.php';
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
    </script>

</body>
</html>
