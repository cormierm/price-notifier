<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Watcher Configuration') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update the user agent that is used when fetching content from websites.") }}
        </p>
    </header>

    <form method="post" action="{{ route('settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="user_agent" :value="__('User Agent')" />
            <div class="mt-1 w-full flex">
                <x-text-input
                    class="grow"
                    id="user_agent"
                    name="user_agent"
                    type="text"
                    :value="old('user_agent', $user->user_agent)"
                />
                <x-primary-button
                    x-on:click.prevent="userAgent = navigator.userAgent"
                >{{ __('Fetch User Agent') }}</x-primary-button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('user_agent')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'watcher-updated')
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
