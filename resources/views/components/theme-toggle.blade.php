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
        <i class="fa-solid fa-circle-half-stroke h-4 w-4"></i>
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
            <div class="flex gap-2 items-center"><i class="fa-solid fa-sun h-3 w-3"></i>Light</div>
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900"
            @click.prevent="darkMode = 'dark'; localStorage.setItem('theme', 'dark'); $dispatch('theme-change'); open = false;"
        >
            <div class="flex gap-2 items-center"><i class="fa-solid fa-moon h-3 w-3"></i>Dark</div>
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900"
            @click.prevent="darkMode = 'system'; localStorage.setItem('theme', 'system'); $dispatch('theme-change'); open = false;"
        >
            <div class="flex gap-2 items-center"><i class="fa-solid fa-desktop h-3 w-3"></i>System</div>
        </a>
    </div>
</div>
