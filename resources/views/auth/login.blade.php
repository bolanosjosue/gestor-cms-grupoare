<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login - Grupo Are</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

</head>

<body class="auth-page">
    <div class="card">
        <h1>Acceso Admin</h1>

        @if ($errors->has('username'))
            <div class="alert">{{ $errors->first('username') }}</div>
        @endif

        <form method="POST" action="{{ route('login.perform') }}">
            @csrf

            <div class="group">
                <label for="username">Nombre de usuario</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                    required autofocus autocomplete="username">
                @error('username')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="group">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="row group">
                <label class="check" style="font-weight:400;">
                    <input type="checkbox" name="remember" value="1" checked>
                    Recordarme
                </label>
            </div>

            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>

</html>
