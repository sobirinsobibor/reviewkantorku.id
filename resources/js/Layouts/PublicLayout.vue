<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const loginModal = ref(false);
const userDropdown = ref(false);
const user = page.props.auth?.user ?? null;
const mobileMenuOpen = ref(false);

const navLinks = [
    { label: 'Home', href: '/' },
    { label: 'Kantor', href: '/kantor' },
]

onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    if (params.get('login') === '1') {
        loginModal.value = true
    }
})
</script>

<template>
    <div class="flex min-h-screen flex-col bg-blue-50/30 text-gray-800">
        <nav class="sticky top-0 z-50 border-b border-blue-100 bg-white">
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6"
            >
                <Link href="/" class="flex shrink-0 items-center gap-2.5">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 shadow-sm"
                    >
                        <img
                            :src="'/storage/images/icon.jpeg'"
                            alt="icon"
                            class="h-8 w-auto"
                        />
                    </div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-sm font-semibold text-gray-900"
                            >ReviewKantorku</span
                        >
                        <span class="hidden text-[11px] text-gray-400 sm:block">
                            Review kantor di Indonesia
                        </span>
                    </div>
                </Link>

                <div class="flex items-center gap-2">
                    <!-- ================= DESKTOP ================= -->
                    <div class="hidden items-center gap-2 sm:flex">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                            :class="{
                                'bg-blue-50 text-blue-600':
                                    $page.url === link.href ||
                                    (link.href !== '/' &&
                                        $page.url.startsWith(link.href)),
                            }"
                        >
                            {{ link.label }}
                        </Link>

                        <div v-if="user" class="relative user-dropdown-wrapper">
                            <button
                                type="button"
                                @click="userDropdown = !userDropdown"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-blue-50"
                            >
                                <span>{{ user.name }}</span>

                                <svg
                                    class="h-3 w-3 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 10 10"
                                >
                                    <path
                                        d="M2 3.5l3 3 3-3"
                                        stroke="currentColor"
                                        stroke-width="1.2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div
                                v-if="userDropdown"
                                class="absolute right-0 z-50 mt-2 w-52 rounded-xl border border-blue-100 bg-white py-1 shadow-lg"
                            >
                                <!-- User -->
                                <div class="border-b border-blue-50 px-3 py-2">
                                    <p
                                        class="truncate text-xs font-medium text-gray-900"
                                    >
                                        {{ user.name }}
                                    </p>

                                    <p
                                        class="truncate text-[11px] text-gray-400"
                                    >
                                        {{ user.email }}
                                    </p>
                                </div>

                                <!-- Admin -->
                                <a
                                    v-if="user.is_admin == 1"
                                    href="/admin"
                                    class="block px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
                                >
                                    Halaman Admin
                                </a>

                                <!-- Menu -->
                                <Link
                                    href="/dashboard"
                                    class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50"
                                >
                                    Dashboard saya
                                </Link>

                                <Link
                                    href="/my-profile"
                                    class="block px-3 py-2 text-sm text-gray-600 hover:bg-blue-50"
                                >
                                    Profil saya
                                </Link>

                                <div class="my-1 border-t border-gray-50" />

                                <!-- Logout -->
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="block w-full px-3 py-2 text-left text-sm text-red-500 hover:bg-red-50"
                                >
                                    Keluar
                                </Link>
                                <!-- <button 
                                    @click="logout"
                                    class="block w-full px-3 py-2 text-left text-sm text-red-500 hover:bg-red-50"
                                >
                                    Keluar
                                </button> -->

                                <!-- Close -->
                                <div class="border-t border-blue-50 px-3 py-2">
                                    <button
                                        type="button"
                                        @click="userDropdown = false"
                                        class="w-full rounded-lg border border-gray-200 py-1.5 text-xs text-gray-500 hover:bg-gray-50"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Login -->
                        <button
                            v-else
                            type="button"
                            @click="loginModal = true"
                            class="rounded-lg px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50"
                        >
                            Masuk
                        </button>
                    </div>

                    <!-- ================= MOBILE ================= -->
                    <!-- ================= MOBILE ================= -->
                    <div class="sm:hidden flex items-center gap-2">
                        <!-- Belum login -->
                        <button
                            v-if="!user"
                            type="button"
                            @click="loginModal = true"
                            class="rounded-lg px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50"
                        >
                            Masuk
                        </button>

                        <!-- Sudah login: hamburger -->
                        <button
                            v-else
                            type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50"
                        >
                            <svg
                                v-if="!mobileMenuOpen"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M4 7h16M4 12h16M4 17h16"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                            <svg
                                v-else
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M6 6l12 12M18 6L6 18"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- ================= MOBILE SHEET ================= -->
                    <Teleport to="body">
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="translate-y-full opacity-0"
                            enter-to-class="translate-y-0 opacity-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="translate-y-0 opacity-100"
                            leave-to-class="translate-y-full opacity-0"
                        >
                            <div
                                v-if="mobileMenuOpen && user"
                                class="fixed inset-0 z-[999] sm:hidden"
                            >
                                <!-- Backdrop -->
                                <div
                                    class="absolute inset-0 bg-black/30 backdrop-blur-[1px]"
                                    @click="mobileMenuOpen = false"
                                />

                                <!-- Sheet -->
                                <div
                                    class="absolute bottom-0 left-0 right-0 rounded-t-2xl border-t border-blue-100 bg-white"
                                >
                                    <!-- Handle -->
                                    <div class="flex justify-center pb-1 pt-2">
                                        <div
                                            class="h-1 w-10 rounded-full bg-gray-200"
                                        />
                                    </div>

                                    <!-- User -->
                                    <div
                                        class="border-b border-blue-50 px-4 py-3"
                                    >
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ user.name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ user.email }}
                                        </p>
                                    </div>

                                    <!-- Nav Links -->
                                    <div class="border-b border-blue-50 py-1">
                                        <a
                                            v-for="link in navLinks"
                                            :key="link.href"
                                            :href="link.href"
                                            class="block px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600"
                                            :class="{
                                                'text-blue-600 bg-blue-50':
                                                    $page.url === link.href ||
                                                    (link.href !== '/' &&
                                                        $page.url.startsWith(
                                                            link.href,
                                                        )),
                                            }"
                                            @click="mobileMenuOpen = false"
                                        >
                                            {{ link.label }}
                                        </a>
                                    </div>

                                    <!-- Admin -->
                                    <a
                                        v-if="user.is_admin == 1"
                                        href="/admin"
                                        class="block px-4 py-3 text-sm font-medium text-blue-600 hover:bg-blue-50"
                                        @click="mobileMenuOpen = false"
                                    >
                                        Halaman Admin
                                    </a>

                                    <!-- Menu Akun -->
                                    <Link
                                        href="/dashboard"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50"
                                        @click="mobileMenuOpen = false"
                                    >
                                        Dashboard saya
                                    </Link>
                                    <Link
                                        href="/my-profile"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50"
                                        @click="mobileMenuOpen = false"
                                    >
                                        Profil saya
                                    </Link>

                                    <div
                                        class="my-1 border-t border-gray-100"
                                    />

                                    <!-- Logout -->
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="block w-full px-4 py-3 text-left text-sm text-red-500 hover:bg-red-50"
                                        @click="mobileMenuOpen = false"
                                    >
                                        Keluar
                                    </Link>

                                    <!-- Close -->
                                    <div class="px-4 pb-6 pt-2">
                                        <button
                                            type="button"
                                            @click="mobileMenuOpen = false"
                                            class="w-full rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50"
                                        >
                                            Tutup menu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </Teleport>
                </div>
            </div>
        </nav>

        <main class="w-full flex-1">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8">
                <slot />
            </div>
        </main>

        <footer class="mt-auto border-t border-blue-100 bg-white">
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-4 sm:flex-row sm:px-6"
            >
                <span class="text-sm text-gray-400"
                    >© {{ new Date().getFullYear() }} ReviewKantorku</span
                >

                <div class="flex gap-4 sm:gap-6">
                    <Link
                        href="#"
                        class="text-sm text-gray-400 hover:text-blue-600"
                        >Tentang</Link
                    >
                    <Link
                        href="#"
                        class="text-sm text-gray-400 hover:text-blue-600"
                        >Kebijakan Privasi</Link
                    >
                    <Link
                        href="#"
                        class="text-sm text-gray-400 hover:text-blue-600"
                        >Kontak</Link
                    >
                </div>
            </div>
        </footer>

        <div
            v-if="loginModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="loginModal = false"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6">
                <div class="mb-5">
                    <div
                        class="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100"
                    >
                        <!-- logo login -->
                        <img
                            :src="'/storage/images/icon.jpeg'"
                            alt="icon"
                            class="h-8 w-auto"
                        />
                    </div>

                    <h2 class="text-base font-semibold text-gray-900">
                        Masuk ke ReviewKantorku
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-400">
                        Masuk untuk berbagi pengalaman di dunia kerja.
                    </p>
                </div>

                <a
                    href="/auth-google-redirect"
                    class="block w-full rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-700"
                >
                    Masuk dengan Google
                </a>

                <button
                    type="button"
                    @click="loginModal = false"
                    class="mt-3 w-full rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50"
                >
                    Kembali
                </button>
            </div>
        </div>
    </div>
</template>
