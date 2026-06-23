<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin login | Oakter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/favicon.png') }}" />
    @vite(['resources/css/app.css'])
  </head>
  <body class="admin-body admin-auth">
    <div class="admin-auth-card">
      <img src="{{ asset('assets/oakter-logo-280.png') }}" alt="Oakter" />
      <h1>Admin sign in</h1>
      <p>Manage orders and store settings.</p>

      <form class="admin-form" method="post" action="{{ route('admin.login.store') }}">
        @csrf

        <div class="admin-field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus />
          @error('email')<p class="admin-error">{{ $message }}</p>@enderror
        </div>

        <div class="admin-field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" required />
          @error('password')<p class="admin-error">{{ $message }}</p>@enderror
        </div>

        <label class="admin-checkbox admin-remember-me">
          <input type="checkbox" name="remember" value="1" @checked(old('remember')) />
          <span>Remember me</span>
        </label>

        <button class="admin-button" type="submit">Sign in</button>
      </form>
    </div>
  </body>
</html>
