<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useSweetAlert } from '@/Composables/UseSweetAlert'

const { toast } = useSweetAlert()

defineOptions({
    layout: DashboardLayout,
});

defineProps({
    interactions: Object,
    stats: Object,
});

const selectedInteraction = ref(null);

const openModal = (interaction) => {
    selectedInteraction.value = interaction;
};

const closeModal = () => {
    selectedInteraction.value = null;
};

const updateVisibility = (interaction) => {
    router.put(
        `/dashboard/interaksi/${interaction.ulid}`,
        {
            is_hidden: !interaction.is_hidden,
            ulid: interaction.ulid,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedInteraction.value = {
                    ...interaction,
                    is_hidden: !interaction.is_hidden,
                },
                toast({ icon: 'success', title: 'Data berhasil diubah!' })
            },
            onError: () => {
                toast({ icon: 'error', title: 'Terjadi kesalahan!' })
            },
        },
    );
};

const updateAnonymous = (interaction) => {
    router.put(
        `/dashboard/interaksi/${interaction.ulid}`,
        {
            is_anonymous: !interaction.is_anonymous,
            ulid: interaction.ulid,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedInteraction.value = {
                    ...interaction,
                    is_anonymous: !interaction.is_anonymous,
                },
                toast({ icon: 'success', title: 'Status anonimitas berhasil diubah!' })
            },
            onError: () => {
                toast({ icon: 'error', title: 'Terjadi kesalahan!' })
            },
        },
    );
};

const typeLabel = (type) => {
    return (
        {
            review: "Review",
            cerita_magang: "Cerita Magang",
            menfess: "Menfess",
            qna: "QnA",
        }[type] || type
    );
};

const getOtherAnswer = (field) =>
    getUserData(field)
        .find((i) => i.startsWith("other:"))
        ?.replace("other:", "") ?? null;

const isSelectedOption = (field, option) =>
    getUserData(field).includes(String(option.value));

const isDisplayableField = (field) =>
    field.name && !["header", "paragraph"].includes(field.type);

const getUserData = (field) => {
    if (!field?.userData) return [];
    return Array.isArray(field.userData)
        ? field.userData.map(String)
        : [String(field.userData)];
};
</script>

<template>
    <div>
        <div class="mb-5">
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

            <h1 class="text-base font-semibold text-gray-900">Review saya</h1>
            <p class="mt-0.5 text-sm text-gray-400">
                Semua review, cerita, menfess, dan QnA yang pernah kamu tulis.
            </p>
        </div>

        <!-- STATS -->
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Total</p>
                <p class="text-xl font-semibold">{{ stats.total }}</p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Review</p>
                <p class="text-xl font-semibold text-blue-600">
                    {{ stats.review }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Cerita Magang</p>
                <p class="text-xl font-semibold text-purple-600">
                    {{ stats.cerita_magang }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">Menfess</p>
                <p class="text-xl font-semibold text-amber-500">
                    {{ stats.menfess }}
                </p>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-3">
                <p class="text-xs text-gray-400">QnA</p>
                <p class="text-xl font-semibold text-green-600">
                    {{ stats.qna }}
                </p>
            </div>
        </div>

        <div
            v-if="!interactions.data.length"
            class="rounded-xl border border-blue-100 bg-white p-8 text-center"
        >
            <p class="text-sm text-gray-400">
                Kamu belum menulis interaksi apapun.
            </p>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="interaction in interactions.data"
                :key="interaction.id"
                class="relative rounded-xl border border-gray-100 bg-white p-5 transition hover:border-gray-200 group"
            >
                <!-- Blue left accent on hover -->
                <span
                    class="absolute left-0 top-4 bottom-4 w-0.5 rounded-r bg-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"
                ></span>

                <!-- Header -->
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-100 bg-blue-50 text-xs font-medium text-blue-700"
                        >
                            {{ interaction.user?.initials ?? 'U' }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ interaction.user?.name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ interaction.office?.name }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-[11px] px-2 py-0.5 rounded-full font-medium"
                        :class="{
                            'bg-blue-50 text-blue-600': interaction.type === 'review',
                            'bg-purple-50 text-purple-600': interaction.type === 'cerita_magang',
                            'bg-yellow-50 text-yellow-600': interaction.type === 'menfess',
                            'bg-green-50 text-green-600': interaction.type === 'qna',
                            'bg-gray-50 text-gray-600': interaction.type === 'reply',
                        }"
                    >
                        {{ typeLabel(interaction.type) }}
                    </span>
                </div>

                <!-- Divider -->
                <div class="mb-3 h-px bg-gray-100"></div>

                <!-- Content -->
                <div class="space-y-1.5 mb-3">
                    <template
                        v-for="(item, key) in interaction.main_contents"
                        :key="key"
                    >
                        <p
                            v-if="item.value"
                            class="text-sm leading-relaxed text-gray-600"
                        >
                            <span class="font-medium text-gray-700">{{ item.label }}:</span>
                            {{ item.value }}
                        </p>
                    </template>
                </div>

                <!-- Photo badge -->
                <div v-if="interaction.files?.length" class="mb-3">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-100 bg-gray-50 px-2.5 py-1 text-xs text-gray-400"
                    >
                        <svg
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 16 16"
                        >
                            <rect
                                x="1"
                                y="3"
                                width="14"
                                height="11"
                                rx="1.5"
                                stroke="currentColor"
                                stroke-width="1.2"
                            />
                            <circle
                                cx="5.5"
                                cy="7.5"
                                r="1.5"
                                stroke="currentColor"
                                stroke-width="1.2"
                            />
                            <path
                                d="M1 12l3.5-3.5 2.5 2.5 2.5-2.5 4.5 4"
                                stroke="currentColor"
                                stroke-width="1.2"
                            />
                        </svg>
                        {{ interaction.files.length }} foto
                    </span>
                </div>

                <!-- Status & Action -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                    <span
                        class="inline-flex items-center gap-1.5 text-[11px] px-2 py-0.5 rounded-full font-medium"
                        :class="
                            interaction.is_hidden
                                ? 'bg-red-50 text-red-600'
                                : 'bg-green-50 text-green-600'
                        "
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full"
                            :class="
                                interaction.is_hidden ? 'bg-red-500' : 'bg-green-500'
                            "
                        ></span>
                        {{ interaction.is_hidden ? "Disembunyikan" : "Tampil" }}
                    </span>

                    <button
                        @click="openModal(interaction)"
                        class="text-xs px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                    >
                        Lihat detail
                    </button>
                </div>
            </div>
        </div>

        <!-- PAGINATION -->
        <div class="mt-4 flex justify-center gap-2">
            <template v-for="link in interactions.links" :key="link.label">
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

        <div
            v-if="selectedInteraction"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="closeModal"
        >
            <div
                class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white"
            >
                <div
                    class="flex items-start justify-between border-b border-blue-50 px-5 py-4"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Detail Interaksi
                        </h2>

                        <p class="mt-0.5 text-sm text-gray-400">
                            {{ selectedInteraction.office.name }}
                        </p>

                        <!-- TAMBAHAN TANGGAL -->
                        <p class="text-[11px] text-gray-300 mt-0.5">
                            {{ selectedInteraction.created_at }}
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-4 px-5 py-4">
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
                        >
                            {{ typeLabel(selectedInteraction.type) }}
                        </span>

                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="
                                selectedInteraction.is_hidden
                                    ? 'bg-red-50 text-red-600'
                                    : 'bg-green-50 text-green-600'
                            "
                        >
                            {{
                                selectedInteraction.is_hidden
                                    ? "Disembunyikan"
                                    : "Tampil publik"
                            }}
                        </span>
                    </div>

                    <div>
                        <p
                            class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Pengalaman
                        </p>
                        <p
                            class="rounded-xl bg-blue-50/50 px-3 py-2 text-sm leading-relaxed text-gray-700"
                        >
                            {{ selectedInteraction.experience }}
                        </p>
                    </div>

                    <div v-if="selectedInteraction.positive_notes">
                        <p
                            class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-400"
                        >
                            Hal positif
                        </p>
                        <p
                            class="rounded-xl bg-blue-50/50 px-3 py-2 text-sm leading-relaxed text-blue-700"
                        >
                            {{ selectedInteraction.positive_notes }}
                        </p>
                    </div>

                    <!-- Toggles Row -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Anonymous Toggle -->
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Anonimkan nama pengguna
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        {{
                                            selectedInteraction.is_anonymous
                                                ? 'Nama pengguna disembunyikan.'
                                                : 'Nyalakan untuk menyembunyikan nama pengguna.'
                                        }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="updateAnonymous(selectedInteraction)"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                    :class="
                                        selectedInteraction.is_anonymous
                                            ? 'bg-gray-300'
                                            : 'bg-blue-600'
                                    "
                                >
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                                        :class="
                                            selectedInteraction.is_anonymous
                                                ? 'translate-x-0'
                                                : 'translate-x-5'
                                        "
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Visibility Toggle -->
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Sembunyikan interaksi
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        {{
                                            selectedInteraction.is_hidden
                                                ? 'Interaksi ini hanya dapat dilihat oleh Anda.'
                                                : 'Nyalakan agar interaksi ini tidak terlihat oleh pengguna lain.'
                                        }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="updateVisibility(selectedInteraction)"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                                    :class="
                                        selectedInteraction.is_hidden
                                            ? 'bg-red-500'
                                            : 'bg-green-500'
                                    "
                                >
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                                        :class="
                                            selectedInteraction.is_hidden
                                                ? 'translate-x-0'
                                                : 'translate-x-5'
                                        "
                                    />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Attributes -->
                    <div
                        v-if="
                            selectedInteraction.attributes?.filter(
                                isDisplayableField,
                            ).length
                        "
                    >
                        <p
                            class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400"
                        >
                            Detail
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div
                                v-for="field in selectedInteraction.attributes.filter(
                                    isDisplayableField,
                                )"
                                :key="field.name"
                                class="rounded-2xl border border-blue-100 bg-blue-50/40 p-3"
                            >
                                <p
                                    class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-400"
                                >
                                    {{ field.label }}
                                </p>
                                <p
                                    v-if="field.description"
                                    class="mb-2 text-[11px] text-gray-400"
                                >
                                    {{ field.description }}
                                </p>

                                <!-- Options (radio/checkbox) -->
                                <div
                                    v-if="field.values?.length"
                                    class="flex flex-wrap gap-1.5"
                                >
                                    <span
                                        v-for="option in field.values"
                                        :key="option.value"
                                        class="rounded-full border px-2.5 py-1 text-[11px] font-medium"
                                        :class="
                                            isSelectedOption(field, option)
                                                ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                                : 'border-white bg-white text-gray-400'
                                        "
                                    >
                                        {{ option.label }}
                                    </span>
                                    <span
                                        v-if="getOtherAnswer(field)"
                                        class="rounded-full border border-blue-600 bg-blue-600 px-2.5 py-1 text-[11px] font-medium text-white shadow-sm"
                                    >
                                        Lainnya: {{ getOtherAnswer(field) }}
                                    </span>
                                    <span
                                        v-else-if="field.other"
                                        class="rounded-full border border-white bg-white px-2.5 py-1 text-[11px] font-medium text-gray-300"
                                    >
                                        Lainnya
                                    </span>
                                </div>

                                <!-- Text / free answer -->
                                <div
                                    v-else
                                    class="rounded-xl bg-white px-3 py-2 text-sm text-gray-700"
                                >
                                    <span
                                        v-if="
                                            getUserData(field).length &&
                                            getUserData(field)[0]
                                        "
                                    >
                                        {{ getUserData(field).join(", ") }}
                                    </span>
                                    <span v-else class="text-gray-300"
                                        >Tidak diisi</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-2 border-t border-blue-50 px-5 py-4"
                >
                    <button
                        type="button"
                        @click="closeModal"
                        class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
