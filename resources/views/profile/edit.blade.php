<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-5 mb-0">👤 My Profile</h2>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Profile Info --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Profile Information</div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Change Password</div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card border-0 shadow-sm border-danger mb-4">
                <div class="card-header bg-white fw-semibold text-danger">Danger Zone</div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
