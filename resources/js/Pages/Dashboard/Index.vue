<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({
    layout: DashboardLayout,
});

const page = usePage();

const user = computed(() => page.props.auth?.user);
</script>

<template>
    <div>
        <div class="mb-5">
            <h1 class="text-base font-medium text-gray-900">
                Selamat datang, {{ user?.name }}
            </h1>
            <p class="mt-0.5 text-sm text-gray-400">
                Kelola kantor dan review kamu di sini
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <Link
                href="/dashboard/kantor"
                class="rounded-xl border border-blue-100 bg-white p-4 transition hover:border-blue-200"
            >
                <div class="mb-2 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <svg
                            class="h-4 w-4 text-blue-600"
                            fill="none"
                            viewBox="0 0 16 16"
                        >
                            <rect
                                x="2"
                                y="5"
                                width="12"
                                height="9"
                                rx="1"
                                stroke="currentColor"
                                stroke-width="1.2"
                            />
                            <path
                                d="M6 14v-4h4v4"
                                stroke="currentColor"
                                stroke-width="1.2"
                            />
                            <path
                                d="M2 5l6-3 6 3"
                                stroke="currentColor"
                                stroke-width="1.2"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <span class="text-sm font-medium text-gray-900">
                        Kantor saya
                    </span>
                </div>

                <p class="text-xs text-gray-400">
                    Lihat dan kelola kantor yang kamu ajukan
                </p>
            </Link>

            <Link
                href="/dashboard/interaksi"
                class="rounded-xl border border-blue-100 bg-white p-4 transition hover:border-blue-200"
            >
                <div class="mb-2 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <svg
                            class="h-4 w-4 text-blue-600"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                d="M1 3h12M1 7h8M1 11h5"
                                stroke="currentColor"
                                stroke-width="1.2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <span class="text-sm font-medium text-gray-900">
                        Review saya
                    </span>
                </div>

                <p class="text-xs text-gray-400">
                    Lihat semua review yang pernah kamu tulis
                </p>
            </Link>
        </div>
    </div>
</template>
