<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const previewImage = ref(null);

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    office: Object,
});
</script>

<template>
    <div>
        <!-- HEADER -->
        <div class="mb-5">
            <Link
                href="/dashboard/kantor"
                class="mb-2 inline-flex items-center gap-1 text-sm text-gray-400 hover:text-blue-600"
            >
                ← Kembali
            </Link>

            <h1 class="text-base font-semibold text-gray-900">
                {{ office.name }}
            </h1>

            <p class="mt-0.5 text-sm text-gray-400">
                Detail kantor (hanya lihat)
            </p>
        </div>

        <!-- STATUS REVIEW -->
        <div
            class="mb-4 rounded-xl border p-4"
            :class="{
                'border-yellow-200 bg-yellow-50': office.status === 'pending',
                'border-green-200 bg-green-50':  office.status === 'approved',
                'border-red-200 bg-red-50':      office.status === 'rejected',
            }"
        >
            <!-- Baris atas: status badge + tanggal -->
            <div class="flex items-center justify-between">
                <span
                    class="rounded-md px-2 py-0.5 text-xs font-medium"
                    :class="{
                        'bg-yellow-100 text-yellow-700': office.status === 'pending',
                        'bg-green-100 text-green-700':  office.status === 'approved',
                        'bg-red-100 text-red-700':      office.status === 'rejected',
                    }"
                >
                    {{
                        office.status == 'pending'  ? 'Menunggu Review' :
                        office.status == 'approved' ? 'Terverifikasi' : 'Ditolak'
                    }}
                </span>

                <span class="text-xs text-gray-400">
                    Diajukan {{ office.created_at }}
                </span>
            </div>

            <!-- Direview oleh -->
            <div v-if="office.reviewed_by" class="mt-3 flex items-center gap-2">
                <span class="text-xs text-gray-400">Direview oleh</span>
                <span class="text-xs font-medium text-gray-700">
                    {{ office.reviewed_by }}
                </span>
                <span v-if="office.reviewed_at" class="text-xs text-gray-400">
                    · {{ office.reviewed_at }}
                </span>
            </div>

            <!-- Alasan penolakan -->
            <div
                v-if="office.status === 'rejected' && office.rejection_reason"
                class="mt-3 border-t border-red-200 pt-3"
            >
                <p class="mb-1 text-xs font-medium text-red-600">Alasan penolakan</p>
                <p class="text-sm text-red-700">{{ office.rejection_reason }}</p>
            </div>

            <!-- Pending info -->
            <div v-if="office.status === 'pending'" class="mt-3">
                <p class="text-xs text-yellow-600">
                    Kantor kamu sedang dalam antrian review oleh tim kami.
                </p>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="space-y-4 rounded-xl border border-blue-100 bg-white p-4">
            <!-- INDUSTRI -->
            <div>
                <p class="mb-1 text-xs text-gray-400">Industri</p>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="industry in office.industries"
                        :key="industry.id"
                        class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs text-blue-700"
                    >
                        {{ industry.name }}
                    </span>
                </div>
            </div>

            <!-- LOKASI -->
            <div>
                <p class="mb-1 text-xs text-gray-400">Lokasi</p>
                <p class="text-sm text-gray-700">
                    {{ office.regency }}, {{ office.province }}
                </p>
            </div>

            <!-- ALAMAT -->
            <div>
                <p class="mb-1 text-xs text-gray-400">Alamat</p>
                <p class="text-sm text-gray-700">{{ office.address }}</p>
            </div>

            <!-- FOTO -->
            <div v-if="office.photos.length">
                <p class="mb-2 text-xs text-gray-400">Foto kantor</p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <img
                        v-for="photo in office.photos"
                        :key="photo.id"
                        :src="photo.url"
                        class="aspect-[4/3] w-full cursor-zoom-in rounded-lg border object-cover"
                        @click="previewImage = photo.url"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Preview foto fullscreen -->
    <div
        v-if="previewImage"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70 p-4"
        @click.self="previewImage = null"
    >
        <div class="relative w-full max-w-3xl">
            <button
                @click="previewImage = null"
                class="absolute -top-8 right-0 text-xl text-white"
            >
                ✕
            </button>
            <img :src="previewImage" class="w-full rounded-xl shadow-lg" />
        </div>
    </div>
</template>