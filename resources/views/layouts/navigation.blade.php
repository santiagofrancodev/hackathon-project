<nav x-data="{ open: false }" class="bg-sidebar-dark border-b border-sidebar-dark">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('diagnostic.index')" :active="request()->routeIs('diagnostic*')">
                        {{ __('Autodiagnóstico') }}
                    </x-nav-link>
                    <x-nav-link :href="route('company.index')" :active="request()->routeIs('company*')">
                        {{ __('Empresas') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white/80 bg-sidebar-light hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <x-icons.chevron-down class="h-4 w-4" />
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-white/70 hover:text-white hover:bg-sidebar-light/50 focus:outline-none transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white/60 hover:text-white hover:bg-sidebar-light focus:outline-none focus:bg-sidebar-light focus:text-white transition duration-150 ease-in-out">
                    <x-icons.menu x-show="!open" class="h-6 w-6" />
                    <x-icons.x x-show="open" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-sidebar-dark">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('diagnostic.index')" :active="request()->routeIs('diagnostic*')">
                {{ __('Autodiagnóstico') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('company.index')" :active="request()->routeIs('company*')">
                {{ __('Empresas') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-sidebar-light">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-white/60">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-white hover:bg-sidebar-light/50 hover:border-white/30 focus:outline-none focus:text-white focus:bg-sidebar-light/50 focus:border-white/30 transition duration-150 ease-in-out">
                            {{ __('Log Out') }}
                        </button>
                    </form>
            </div>
        </div>
    </div>
</nav>
