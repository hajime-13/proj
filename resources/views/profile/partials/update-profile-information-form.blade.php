<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    {{-- Profile picture preview --}}
    <div class="text-center mb-4">
        @if (!empty($user->profile_picture_path))
            <img id="avatarPreview"
                 src="{{ Storage::url($user->profile_picture_path) }}"
                 alt="Profile Picture"
                 class="rounded-circle border"
                 style="width:100px;height:100px;object-fit:cover;">
        @else
            <div id="avatarPreview"
                 class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white fw-bold"
                 style="width:100px;height:100px;font-size:2rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}" required autofocus>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 text-warning small">
                Your email is unverified.
                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0">Re-send verification email</button>
                </form>
            </div>
        @endif
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Profile Picture</label>
        <input type="file" name="profile_picture" id="profile_picture_input"
               class="form-control @error('profile_picture') is-invalid @enderror"
               accept="image/jpeg,image/png,image/webp">
        <div class="form-text">JPG, PNG or WebP — max 2 MB</div>
        @error('profile_picture')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-primary">Save Changes</button>
        @if (session('status') === 'profile-updated')
            <span class="text-success small">✔ Saved successfully.</span>
        @endif
    </div>
</form>

<script>
    document.getElementById('profile_picture_input')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace the div with an img
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.className = 'rounded-circle border';
                img.style = 'width:100px;height:100px;object-fit:cover;';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    });
</script>
