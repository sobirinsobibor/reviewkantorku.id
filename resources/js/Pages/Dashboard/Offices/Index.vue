<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/PublicLayout.vue";

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    offices: Object,
    stats: Object,
    filters: Object,
});

const form = useForm({
    search: props.filters.search || "",
    status: props.filters.status || "",
});

const submit = () => {
    form.get("/dashboard/kantor", {
        preserveState: true,
        replace: true,
    });
};

const canModify = (office) => {
    return ["pending", "rejected"].includes(office.status);
};

const canView = (office) => {
    return office.status === "approved";
};

const destroyForm = useForm({});

const deleteOffice = (office) => {
    if (!confirm("Hapus kantor ini?")) return;

    destroyForm.delete(`/dashboard/kantor/${office.id}`, {
        preserveScroll: true,
    });
};
</script>

<style scoped>
@media (max-width: 640px) {
    .desktop-table { display: none; }
    .mobile-cards  { display: block; }
}
@media (min-width: 641px) {
    .desktop-table { display: block; }
    .mobile-cards  { display: none; }
}
</style>

<template>
    <div>
        <!-- HEADER -->
        <div class="mb-5">
            <!-- Back Button -->
            <Link
                href="/dashboard"
                class="mb-3 inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-blue-600"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 14 14">
                    <path
                        d="M9 11L5 7l4-4"
                        stroke="currentColor"
                        stroke-width="1.2"
                        stroke-linecap="round"
                    />
                </svg>
                Kembali
            </Link>

            <div class="flex items-center justify-between">
                <!-- Text Content -->
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">
                        Kantor Saya
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ offices.total }} kantor diajukan
                    </p>
                </div>

                <Link
                    href="/dashboard/kantor/create"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
                >
                    + Tambah kantor
                </Link>
            </div>
        </div>

        <!-- STATS -->
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Total kantor</p>
                <p class="text-xl font-semibold">{{ stats.offices }}</p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Approved</p>
                <p class="text-xl font-semibold text-green-600">
                    {{ stats.approved }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Pending</p>
                <p class="text-xl font-semibold text-amber-500">
                    {{ stats.pending }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Rejected</p>
                <p class="text-xl font-semibold text-rose-500">
                    {{ stats.rejected }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Ulasan</p>
                <p class="text-xl font-semibold">{{ stats.reviews }}</p>
            </div>
        </div>

        <!-- FILTER -->
        <form @submit.prevent="submit" class="mb-4 flex gap-2">
            <input
                v-model="form.search"
                type="text"
                placeholder="Cari kantor..."
                class="flex-1 rounded-lg border border-blue-200 px-3 py-1.5 text-sm"
            />

            <select
                v-model="form.status"
                class="rounded-lg border border-blue-200 px-3 py-1.5 text-sm"
            >
                <option value="">Semua</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Terverifikasi</option>
                <option value="rejected">Ditolak</option>
            </select>

            <button
                class="rounded-lg border border-blue-200 px-3 py-1.5 text-sm"
            >
                Filter
            </button>
        </form>

        <!-- TABLE -->
        <!-- TABLE (desktop) -->
        <div class="desktop-table overflow-hidden rounded-xl border border-blue-100 bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs text-gray-400">Nama</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-400">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-400">Industri</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-400">Ulasan</th>
                        <th class="px-4 py-3 text-right text-xs text-gray-400"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="office in offices.data"
                        :key="office.id"
                        class="border border-blue-100 hover:bg-blue-50/30"
                    >
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ office.name }}</p>
                            <p class="text-xs text-gray-400">{{ office.regency }}, {{ office.province }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ office.address }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="i in office.industries"
                                    :key="i.id"
                                    class="rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700"
                                >{{ i.name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="rounded px-2 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-green-50 text-green-700':  office.status === 'approved',
                                    'bg-gray-100 text-gray-600':   office.status === 'pending',
                                'bg-red-50 text-red-700':      office.status === 'rejected',
                                }"
                            >
                                {{ office.status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ office.reviews_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <Link v-if="canView(office)" :href="`/dashboard/kantor/${office.slug}`"
                                    class="rounded-lg px-2 py-1 text-xs text-blue-600 hover:bg-blue-50">View</Link>
                                <Link v-if="canModify(office)" :href="`/dashboard/kantor/${office.slug}/edit`"
                                    class="rounded-lg px-2 py-1 text-xs text-gray-600 hover:bg-gray-100">Edit</Link>
                                <button v-if="canModify(office)" type="button" @click="deleteOffice(office)"
                                    class="rounded-lg px-2 py-1 text-xs text-red-500 hover:bg-red-50">Delete</button>
                                <span v-if="!canView(office) && !canModify(office)" class="text-xs text-gray-300">-</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!offices.data.length">
                        <td colspan="6" class="py-10 text-center text-sm text-gray-400">Belum ada kantor</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- CARDS (mobile) -->
        <div class="mobile-cards space-y-3">
            <div
                v-for="office in offices.data"
                :key="office.id"
                class="rounded-xl border border-blue-100 bg-white p-4"
            >
                <!-- Nama & Status -->
                <div class="mb-2 flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-gray-900">{{ office.name }}</p>
                        <p class="text-xs text-gray-400">{{ office.regency }}, {{ office.province }}</p>
                    </div>
                    <span class="shrink-0 rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700">
                        {{ office.status_label }}
                    </span>
                </div>

                <!-- Alamat -->
                <p class="mb-2 text-xs text-gray-500">{{ office.address }}</p>

                <!-- Industri -->
                <div class="mb-3 flex flex-wrap gap-1">
                    <span
                        v-for="i in office.industries"
                        :key="i.id"
                        class="rounded bg-blue-50 px-2 py-0.5 text-xs text-blue-700"
                    >{{ i.name }}</span>
                </div>

                <!-- Footer: Ulasan + Actions -->
                <div class="flex items-center justify-between border-t border-blue-50 pt-2">
                    <p class="text-xs text-gray-400">{{ office.reviews_count }} ulasan</p>
                    <div class="flex gap-1">
                        <Link v-if="canView(office)" :href="`/dashboard/kantor/${office.slug}`"
                            class="rounded-lg px-2 py-1 text-xs text-blue-600 hover:bg-blue-50">View</Link>
                        <Link v-if="canModify(office)" :href="`/dashboard/kantor/${office.slug}/edit`"
                            class="rounded-lg px-2 py-1 text-xs text-gray-600 hover:bg-gray-100">Edit</Link>
                        <button v-if="canModify(office)" type="button" @click="deleteOffice(office)"
                            class="rounded-lg px-2 py-1 text-xs text-red-500 hover:bg-red-50">Delete</button>
                    </div>
                </div>
            </div>

            <div v-if="!offices.data.length" class="rounded-xl border border-blue-100 bg-white py-10 text-center text-sm text-gray-400">
                Belum ada kantor
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4 flex justify-center gap-2">
            <template v-for="link in offices.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded border px-3 py-1 text-sm transition"
                    :class="link.active 
                        ? 'bg-blue-500 text-white border-blue-500' 
                        : 'text-gray-600 hover:bg-gray-100'"
                >
                    <span v-if="link.label.includes('previous') || link.label === '&laquo; Previous'">«</span>
                    <span v-else-if="link.label.includes('next') || link.label === 'Next &raquo;'">»</span>
                    <span v-else v-html="link.label" />
                </Link>
                <span
                    v-else
                    class="rounded border px-3 py-1 text-sm text-gray-300 cursor-not-allowed"
                >
                    <span v-if="link.label.includes('previous')">«</span>
                    <span v-else-if="link.label.includes('next')">»</span>
                    <span v-else v-html="link.label" />
                </span>
            </template>
        </div>
    </div>
</template>
