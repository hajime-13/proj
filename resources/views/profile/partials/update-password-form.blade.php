<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
        <input type="password" name="current_password"
               class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
               autocomplete="current-password">
        @if($errors->updatePassword->has('current_password'))
            <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
        <input type="password" name="password"
               class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
               autocomplete="new-password">
        @if($errors->updatePassword->has('password'))
            <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
        @endif
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation"
               class="form-control"
               autocomplete="new-password">
    </div>

    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-primary">Update Password</button>
        @if (session('status') === 'password-updated')
            <span class="text-success small">✔ Password updated.</span>
        @endif
    </div>
</form>
