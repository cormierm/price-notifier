<div
    x-data="{ open: false, darkMode: localStorage.getItem('theme') || 'system' }"
    x-init="if (darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
              } else {
                document.documentElement.classList.remove('dark');
              }"
    @theme-change.window="if (darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
              } else {
                document.documentElement.classList.remove('dark');
              }"
>
    <button
        class="hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-900 px-3 py-2 rounded-full transition ease-in-out duration-150"
        @click="open = ! open"
    >
        <svg class="w-4 h-4 fill-gray-700 dark:fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path d="M448 256c0-106-86-192-192-192l0 384c106 0 192-86 192-192zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z"/>
        </svg>
    </button>

    <div
        class="absolute z-50 mt-2 w-36 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1"
        x-show="open"
        @click.outside="open = false"
    >
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900"
            @click.prevent="darkMode = 'light'; localStorage.setItem('theme', 'light'); $dispatch('theme-change'); open = false;"
        >
            Light
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900"
            @click.prevent="darkMode = 'dark'; localStorage.setItem('theme', 'dark'); $dispatch('theme-change'); open = false;"
        >
            Dark
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900"
            @click.prevent="darkMode = 'system'; localStorage.setItem('theme', 'system'); $dispatch('theme-change'); open = false;"
        >
            System
        </a>
    </div>
</div>
