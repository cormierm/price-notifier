<div x-data="{ open: false, darkMode: localStorage.getItem('theme') || 'system' }"
     x-init="if (darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
              } else {
                document.documentElement.classList.remove('dark');
              }"
     @theme-change.window="if (darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
              } else {
                document.documentElement.classList.remove('dark');
              }">
    <button @click="open = ! open" class="dark:bg-gray-800 dark:text-gray-300 dark:hover:text-gray-400 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
        Theme: <span x-text="darkMode === 'system' ? 'System' : (darkMode === 'dark' ? 'Dark' : 'Light')"></span>
    </button>

    <div x-show="open" @click.outside="open = false" class="absolute z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1">
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300"
            href="#"
            @click.prevent="darkMode = 'light'; localStorage.setItem('theme', 'light'); $dispatch('theme-change'); open = false;"
        >
            Light
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300"
            href="#"
            @click.prevent="darkMode = 'dark'; localStorage.setItem('theme', 'dark'); $dispatch('theme-change'); open = false;"
        >
            Dark
        </a>
        <a
            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300"
            href="#"
            @click.prevent="darkMode = 'system'; localStorage.setItem('theme', 'system'); $dispatch('theme-change'); open = false;"
        >
            System
        </a>
    </div>
</div>
