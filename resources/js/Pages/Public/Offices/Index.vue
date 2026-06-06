<script setup>
import { Link } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";

defineOptions({
    layout: PublicLayout,
});

defineProps({
    offices: Object,
});
</script>

<template>
    <div>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Daftar kantor</h2>
                <p class="mt-0.5 text-sm text-gray-400">
                    {{ offices.total }} kantor tersedia
                </p>
            </div>

            <select
                class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 focus:outline-none"
            >
                <option>Semua kota</option>
            </select>
        </div>

        <!-- Grid 2 kolom, tiap card horizontal -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <Link
                v-for="office in offices.data"
                :key="office.id"
                :href="`/kantor/${office.slug}`"
                class="group block"
            >
                <div class="flex overflow-hidden rounded-xl border border-gray-100 bg-white transition-colors hover:border-gray-300">
                    <!-- Gambar kiri -->
                    <div class="w-32 shrink-0 sm:w-36">
                        <div v-if="office.photo_url" class="h-full overflow-hidden">
                            <img
                                :src="office.photo_url"
                                :alt="office.name"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                            />
                        </div>

                        <div
                            v-else
                            class="flex h-full min-h-[108px] flex-col items-center justify-center gap-1 bg-gray-50"
                        >
                            <svg class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24">
                                <rect x="3" y="7" width="18" height="15" rx="1.5" stroke="currentColor" stroke-width="1.2" />
                                <path d="M8 22V17h8v5M7 7V5a5 5 0 0110 0v2" stroke="currentColor" stroke-width="1.2" />
                            </svg>
                            <span class="text-[10px] text-gray-400">Belum ada foto</span>
                        </div>
                    </div>

                    <!-- Info kanan -->
                    <div class="flex min-w-0 flex-1 flex-col justify-between p-3">
                        <div>
                            <!-- Lokasi -->
                            <div class="mb-1 flex items-center gap-1 text-[11px] text-gray-400">
                                <svg class="h-2.5 w-2.5 shrink-0" fill="none" viewBox="0 0 12 12">
                                    <circle cx="6" cy="5" r="2" stroke="currentColor" stroke-width="1.1" />
                                    <path d="M6 11C6 11 2 7.5 2 5a4 4 0 018 0c0 2.5-4 6-4 6z" stroke="currentColor" stroke-width="1.1" />
                                </svg>
                                <span class="truncate">{{ office.regency }}, {{ office.province }}</span>
                            </div>

                            <!-- Nama kantor -->
                            <h3 class="mb-2 line-clamp-2 text-[13px] font-medium leading-snug text-gray-900">
                                {{ office.name }}
                            </h3>

                            <!-- Industri -->
                            <div v-if="office.industries.length" class="flex flex-wrap gap-1">
                                <span
                                    v-for="industry in office.industries.slice(0, 2)"
                                    :key="industry.id"
                                    class="rounded-[5px] bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-800"
                                >
                                    {{ industry.name }}
                                </span>
                                <span
                                    v-if="office.industries.length > 2"
                                    class="rounded-[5px] bg-gray-50 px-2 py-0.5 text-[11px] text-gray-400"
                                >
                                    +{{ office.industries.length - 2 }}
                                </span>
                            </div>
                        </div>

                        <!-- Status + suka -->
                        <div class="mt-2.5 flex items-center justify-between border-t border-gray-50 pt-2">
                            <span class="inline-flex items-center gap-1.5 rounded-[5px] bg-green-50 px-2 py-0.5 text-[11px] font-medium text-green-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                {{ office.status_label }}
                            </span>
                            <span class="text-[11px] text-gray-400">{{ office.likes_count }} suka</span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="offices.total > offices.per_page" class="mt-6 flex justify-center gap-2">
            <Link
                v-for="link in offices.links"
                :key="link.label"
                :href="link.url || '#'"
                v-html="link.label"
                class="rounded-lg border px-3 py-1.5 text-sm"
                :class="{
                    'border-blue-600 bg-blue-600 text-white': link.active,
                    'pointer-events-none text-gray-300': !link.url,
                    'text-gray-600 hover:bg-blue-50': link.url && !link.active,
                }"
            />
        </div>
    </div>
</template>