<x-guest-layout>
    <h4 class="fw-bold mb-3 text-center">Forgot Password</h4>
    <p class="text-muted small mb-4">Enter your email and we'll send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>

        <div class="text-center small">
            <a href="{{ route('login') }}" class="text-muted">Back to login</a>
        </div>
    </form>
</x-guest-layout>
