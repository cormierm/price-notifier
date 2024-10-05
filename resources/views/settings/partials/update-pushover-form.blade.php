<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Pushover Configuration') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your pushover user key and api key for your pushover credentials. Pushover is used to send notifications on price and stock changes.") }}
        </p>
    </header>

    <form method="post" action="{{ route('settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="pushover_user_key" :value="__('Pushover User Key')" />
            <x-text-input id="pushover_user_key" name="pushover_user_key" type="text" class="mt-1 block w-full" :value="old('pushover_user_key', $user->pushover_user_key)" />
            <x-input-error class="mt-2" :messages="$errors->get('pushover_user_key')" />
        </div>

        <div>
            <x-input-label for="pushover_api_token" :value="__('Pushover Api Token')" />
            <x-text-input id="pushover_api_token" name="pushover_api_token" type="text" class="mt-1 block w-full" :value="old('pushover_api_token', $user->pushover_api_token)" />
            <x-input-error class="mt-2" :messages="$errors->get('pushover_api_token')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'pushover-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
