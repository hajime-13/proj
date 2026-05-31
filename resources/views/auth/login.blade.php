<x-guest-layout>
    <h4 class="fw-bold mb-4 text-center">Sign In</h4>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Remember me</label>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Log In</button>
        </div>

        <div class="d-flex justify-content-between small">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="text-muted">Create account</a>
            @endif
        </div>
    </form>
</x-guest-layout>
