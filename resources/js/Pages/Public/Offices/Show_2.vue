<script setup>
import { computed, ref } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";

defineOptions({
    layout: PublicLayout,
});

const props = defineProps({
    office: Object,
    reviewTemplate: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const previewImage = ref(null);

const currentPhoto = ref(0);
const reviewModal = ref(false);

const getAttributeAnswer = (review, key) => {
    const item = review.attributes?.find((attribute) => attribute.name === key);

    if (!item || !item.userData) return null;

    return Array.isArray(item.userData)
        ? item.userData.join(", ")
        : item.userData;
};

const openReviewModal = () => {
    reviewForm.reset();
    reviewForm.clearErrors();

    reviewForm.type = "review";
    reviewForm.is_anonymous = false;
    reviewForm.answers = {};
    reviewForm.files = [];
    reviewFilePreviews.value = [];

    const fields = Array.isArray(props.reviewTemplate)
        ? props.reviewTemplate
        : [];

    fields.forEach((field) => {
        if (!field.name) return;

        reviewForm.answers[field.name] =
            field.type === "checkbox-group" ? [] : "";
    });

    reviewModal.value = true;
};

const closeReviewModal = () => {
    reviewForm.reset();
    reviewForm.clearErrors();
    reviewForm.answers = {};
    reviewForm.files = [];
    reviewFilePreviews.value = [];
    reviewModal.value = false;
};

const submitReview = () => {
    reviewForm.post(`/kantor/${props.office.slug}/reviews`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeReviewModal();
        },
    });
};

const toggleCheckboxAnswer = (name, value) => {
    if (!Array.isArray(reviewForm.answers[name])) {
        reviewForm.answers[name] = [];
    }

    if (reviewForm.answers[name].includes(value)) {
        reviewForm.answers[name] = reviewForm.answers[name].filter(
            (item) => item !== value,
        );
        return;
    }

    reviewForm.answers[name].push(value);
};

// const getUserData = (field) => {
//     if (!field?.userData) return [];

//     return Array.isArray(field.userData)
//         ? field.userData.map(String)
//         : [String(field.userData)];
// };

// const isSelectedOption = (field, option) => {
//     const selected = getUserData(field);
//     return selected.includes(String(option.value));
// };

// const isDisplayableField = (field) => {
//     return field.name && !["header", "paragraph"].includes(field.type);
// };

const reviewFilePreviews = ref([]);

const reviewForm = useForm({
    type: "review",
    is_anonymous: false,
    answers: {},
    files: [],
});

const previewReviewFiles = (event) => {
    const files = Array.from(event.target.files);

    reviewForm.files = files;
    reviewFilePreviews.value = [];

    files.forEach((file) => {
        const reader = new FileReader();

        reader.onload = (e) => {
            reviewFilePreviews.value.push(e.target.result);
        };

        reader.readAsDataURL(file);
    });
};

const removeReviewFilePreview = (index) => {
    reviewFilePreviews.value.splice(index, 1);
    reviewForm.files = reviewForm.files.filter(
        (_, fileIndex) => fileIndex !== index,
    );
};

const getUserData = (field) => {
    if (!field?.userData) return [];

    return Array.isArray(field.userData)
        ? field.userData.map(String)
        : [String(field.userData)];
};

const isSelectedOption = (field, option) => {
    return getUserData(field).includes(String(option.value));
};

const getOtherAnswer = (field) => {
    const data = getUserData(field);

    return (
        data.find((item) => item.startsWith("other:"))?.replace("other:", "") ||
        null
    );
};

const isDisplayableField = (field) => {
    return field.name && !["header", "paragraph"].includes(field.type);
};
</script>

<template>
    <div>
        <Link
            href="/"
            class="mb-4 inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-blue-600"
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

        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="mb-1 text-xl font-semibold text-gray-900">
                    {{ office.name }}
                </h1>

                <div class="flex items-center gap-1 text-sm text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 12 12">
                        <circle
                            cx="6"
                            cy="5"
                            r="2.5"
                            stroke="currentColor"
                            stroke-width="1.1"
                        />
                        <path
                            d="M6 11C6 11 2 7.5 2 5a4 4 0 018 0c0 2.5-4 6-4 6z"
                            stroke="currentColor"
                            stroke-width="1.1"
                        />
                    </svg>
                    {{ office.regency }}, {{ office.province }}
                </div>
            </div>

            <span
                class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
            >
                {{ office.status_label }}
            </span>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-[1.1fr_0.9fr]">
            <div v-if="office.photos.length" class="relative">
                <div
                    class="relative aspect-[4/3] overflow-hidden rounded-xl bg-blue-50"
                >
                    <img
                        :src="office.photos[currentPhoto].url"
                        alt="Foto kantor"
                        class="h-full w-full object-cover"
                    />

                    <template v-if="office.photos.length > 1">
                        <button
                            type="button"
                            @click="
                                currentPhoto =
                                    (currentPhoto - 1 + office.photos.length) %
                                    office.photos.length
                            "
                            class="absolute left-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 hover:bg-white"
                        >
                            ‹
                        </button>

                        <button
                            type="button"
                            @click="
                                currentPhoto =
                                    (currentPhoto + 1) % office.photos.length
                            "
                            class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 hover:bg-white"
                        >
                            ›
                        </button>
                    </template>
                </div>

                <div
                    v-if="office.photos.length > 1"
                    class="mt-3 flex items-center justify-center gap-2"
                >
                    <button
                        v-for="(photo, index) in office.photos"
                        :key="photo.id"
                        type="button"
                        @click="currentPhoto = index"
                        class="rounded-full transition-all"
                        :class="
                            currentPhoto === index
                                ? 'h-1.5 w-5 bg-blue-600'
                                : 'h-1.5 w-1.5 bg-blue-200'
                        "
                    />
                </div>
            </div>

            <div
                v-else
                class="flex aspect-[4/3] flex-col items-center justify-center gap-2 rounded-xl bg-blue-50"
            >
                <svg
                    class="h-8 w-8 text-blue-200"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <rect
                        x="3"
                        y="7"
                        width="18"
                        height="15"
                        rx="1.5"
                        stroke="currentColor"
                        stroke-width="1.2"
                    />
                    <circle
                        cx="8.5"
                        cy="11.5"
                        r="1.5"
                        stroke="currentColor"
                        stroke-width="1.2"
                    />
                    <path
                        d="M3 18l4-4 3 3 3-3 5 5"
                        stroke="currentColor"
                        stroke-width="1.2"
                    />
                </svg>
                <span class="text-sm text-gray-400">Belum ada foto</span>
            </div>

            <div class="rounded-xl border border-blue-100 bg-white p-4">
                <p
                    class="mb-3 text-xs font-medium uppercase tracking-wide text-gray-400"
                >
                    Informasi kantor
                </p>

                <div class="divide-y divide-blue-50">
                    <div class="flex gap-3 py-2 text-sm">
                        <span class="w-16 shrink-0 text-gray-400">Nama</span>
                        <span class="font-medium text-gray-900">{{
                            office.name
                        }}</span>
                    </div>

                    <div class="flex gap-3 py-2 text-sm">
                        <span class="w-16 shrink-0 text-gray-400">Alamat</span>
                        <span class="font-medium text-gray-900">{{
                            office.address || "-"
                        }}</span>
                    </div>

                    <div class="flex gap-3 py-2 text-sm">
                        <span class="w-16 shrink-0 text-gray-400">Kota</span>
                        <span class="font-medium text-gray-900">
                            {{ office.regency }}, {{ office.province }}
                        </span>
                    </div>

                    <div
                        v-if="office.industries.length"
                        class="flex gap-3 py-2 text-sm"
                    >
                        <span class="w-16 shrink-0 text-gray-400"
                            >Industri</span
                        >
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="industry in office.industries"
                                :key="industry.id"
                                class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                            >
                                {{ industry.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 border-t border-blue-50 pt-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-blue-50 p-2.5">
                            <p class="mb-0.5 text-xs text-gray-400">
                                Total Ulasan
                            </p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ office.reviews.length }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-blue-50 p-2.5">
                            <p class="mb-0.5 text-xs text-gray-400">Foto</p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ office.photos.length }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">
                Review karyawan
            </h2>

            <span class="text-sm text-gray-400">
                {{ office.reviews.length }} ulasan
            </span>
        </div>

        <div class="mb-4">
            <button
                v-if="user"
                type="button"
                @click="openReviewModal"
                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
            >
                Tulis Ulasan
            </button>

            <button
                v-else
                type="button"
                class="text-sm text-blue-600 hover:underline"
            >
                Login untuk menulis review
            </button>
        </div>

        <div v-if="office.reviews.length" class="space-y-2.5">
            <div
                v-for="review in office.reviews"
                :key="review.id"
                class="rounded-xl border border-blue-100 bg-white p-4"
            >
                <div class="mb-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-medium text-blue-700"
                        >
                            {{ review.user.initials }}
                        </div>

                        <span class="text-sm font-medium text-gray-900">
                            {{ review.user.name }}
                        </span>
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ review.created_at_human }}
                    </span>
                </div>

                <p class="text-sm leading-relaxed text-gray-700">
                    {{ review.experience }}
                </p>

                <p
                    v-if="review.positive_notes"
                    class="mt-2 rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-700"
                >
                    {{ review.positive_notes }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <div
                        v-if="review.attributes?.length"
                        class="mt-4 grid gap-3 sm:grid-cols-2"
                    >
                        <div
                            v-for="field in review.attributes.filter(
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

                                <span v-else class="text-gray-300">
                                    Tidak diisi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FOTO REVIEW -->
                <div
                    v-if="review.files?.length"
                    class="mt-4 flex flex-wrap gap-2"
                >
                    <button
                        v-for="file in review.files"
                        :key="file.id"
                        type="button"
                        @click="previewImage = `/storage/${file.path}`"
                        class="group relative aspect-[4/3] w-20 overflow-hidden rounded-lg border border-blue-100"
                    >
                        <img
                            :src="`/storage/${file.path}`"
                            class="h-full w-full object-cover transition group-hover:scale-105"
                        />

                        <div
                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"
                        ></div>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="py-10 text-center text-sm text-gray-400">
            Belum ada review untuk kantor ini.
        </div>
    </div>

    <!-- modal -->
    <div
        v-if="reviewModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white"
        >
            <div class="border-b border-blue-50 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    Tulis Ulasan Kantor
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Bagikan pengalamanmu secara sopan dan bermanfaat.
                </p>
            </div>

            <!-- modal form -->
            <form class="space-y-4 px-5 py-4" @submit.prevent="submitReview">
                <template
                    v-for="field in reviewTemplate"
                    :key="field.name || field.label"
                >
                    <h1
                        v-if="field.type === 'header'"
                        class="text-base font-semibold text-gray-900"
                    >
                        {{ field.label }}
                    </h1>

                    <p
                        v-else-if="field.type === 'paragraph'"
                        class="rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-700"
                    >
                        {{ field.label }}
                    </p>

                    <div v-else>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500"
                                >*</span
                            >
                        </label>

                        <p
                            v-if="field.description"
                            class="mb-2 text-xs text-gray-400"
                        >
                            {{ field.description }}
                        </p>

                        <input
                            v-if="
                                field.type === 'text' || field.type === 'time'
                            "
                            v-model="reviewForm.answers[field.name]"
                            :type="
                                field.type === 'time'
                                    ? 'time'
                                    : field.subtype || 'text'
                            "
                            :maxlength="field.maxlength"
                            :required="field.required"
                            class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        />

                        <textarea
                            v-else-if="field.type === 'textarea'"
                            v-model="reviewForm.answers[field.name]"
                            :rows="field.rows || 3"
                            :maxlength="field.maxlength"
                            :required="field.required"
                            class="w-full resize-none rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        />

                        <div
                            v-else-if="field.type === 'radio-group'"
                            class="space-y-2"
                        >
                            <div class="flex flex-wrap gap-2">
                                <label
                                    v-for="option in field.values"
                                    :key="option.value"
                                    class="cursor-pointer"
                                >
                                    <input
                                        v-model="reviewForm.answers[field.name]"
                                        type="radio"
                                        :name="field.name"
                                        :value="option.value"
                                        :required="field.required"
                                        class="peer hidden"
                                    />

                                    <span
                                        class="inline-flex rounded-lg border border-blue-100 px-3 py-1.5 text-xs text-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white"
                                    >
                                        {{ option.label }}
                                    </span>
                                </label>

                                <label
                                    v-if="field.other"
                                    class="cursor-pointer"
                                >
                                    <input
                                        v-model="reviewForm.answers[field.name]"
                                        type="radio"
                                        :name="field.name"
                                        value="__other__"
                                        class="peer hidden"
                                    />

                                    <span
                                        class="inline-flex rounded-lg border border-blue-100 px-3 py-1.5 text-xs text-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white"
                                    >
                                        Lainnya
                                    </span>
                                </label>
                            </div>

                            <input
                                v-if="
                                    field.other &&
                                    reviewForm.answers[field.name] ===
                                        '__other__'
                                "
                                v-model="
                                    reviewForm.answers[`${field.name}_other`]
                                "
                                type="text"
                                placeholder="Tulis jawaban lainnya..."
                                class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <div
                            v-else-if="field.type === 'checkbox-group'"
                            class="space-y-2"
                        >
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="option in field.values"
                                    :key="option.value"
                                    type="button"
                                    @click="
                                        toggleCheckboxAnswer(
                                            field.name,
                                            option.value,
                                        )
                                    "
                                    class="rounded-lg border px-3 py-1.5 text-xs"
                                    :class="
                                        reviewForm.answers[
                                            field.name
                                        ]?.includes(option.value)
                                            ? 'border-blue-600 bg-blue-600 text-white'
                                            : 'border-blue-100 text-gray-600 hover:bg-blue-50'
                                    "
                                >
                                    {{ option.label }}
                                </button>

                                <button
                                    v-if="field.other"
                                    type="button"
                                    @click="
                                        toggleCheckboxAnswer(
                                            field.name,
                                            '__other__',
                                        )
                                    "
                                    class="rounded-lg border px-3 py-1.5 text-xs"
                                    :class="
                                        reviewForm.answers[
                                            field.name
                                        ]?.includes('__other__')
                                            ? 'border-blue-600 bg-blue-600 text-white'
                                            : 'border-blue-100 text-gray-600 hover:bg-blue-50'
                                    "
                                >
                                    Lainnya
                                </button>
                            </div>

                            <input
                                v-if="
                                    field.other &&
                                    reviewForm.answers[field.name]?.includes(
                                        '__other__',
                                    )
                                "
                                v-model="
                                    reviewForm.answers[`${field.name}_other`]
                                "
                                type="text"
                                placeholder="Tulis jawaban lainnya..."
                                class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            />
                        </div>

                        <p
                            v-if="reviewForm.errors[`answers.${field.name}`]"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ reviewForm.errors[`answers.${field.name}`] }}
                        </p>
                    </div>
                </template>

                <!-- EXPERIENCE -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Ceritakan pengalaman kerja kamu
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        v-model="reviewForm.answers.experience"
                        rows="4"
                        maxlength="500"
                        required
                        placeholder="Ceritakan pengalaman kamu bekerja di kantor ini..."
                        class="w-full resize-none rounded-xl border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-xs text-gray-400">
                            Maksimal 500 karakter. Gunakan bahasa yang sopan.
                        </p>

                        <p
                            class="text-[11px]"
                            :class="
                                (reviewForm.answers.experience?.length || 0) >
                                450
                                    ? 'text-red-500'
                                    : 'text-gray-300'
                            "
                        >
                            {{ reviewForm.answers.experience?.length || 0 }}/500
                        </p>
                    </div>

                    <p
                        v-if="reviewForm.errors['answers.experience']"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ reviewForm.errors["answers.experience"] }}
                    </p>
                </div>

                <!-- POSITIVE NOTES -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Hal positif dari kantor ini
                        <span class="text-gray-400">(opsional)</span>
                    </label>

                    <textarea
                        v-model="reviewForm.answers.positive_notes"
                        rows="3"
                        maxlength="300"
                        placeholder="Contoh: tim suportif, lingkungan nyaman, fasilitas bagus..."
                        class="w-full resize-none rounded-xl border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <div class="mt-1 flex justify-end">
                        <p
                            class="text-[11px]"
                            :class="
                                (reviewForm.answers.positive_notes?.length ||
                                    0) > 260
                                    ? 'text-red-500'
                                    : 'text-gray-300'
                            "
                        >
                            {{
                                reviewForm.answers.positive_notes?.length || 0
                            }}/300
                        </p>
                    </div>

                    <p
                        v-if="reviewForm.errors['answers.positive_notes']"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ reviewForm.errors["answers.positive_notes"] }}
                    </p>
                </div>

                <!-- Foto Pendukung -->
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Foto pendukung
                        <span class="ml-1 font-normal text-gray-400">
                            (opsional, maks. 5 foto, 5MB/foto)
                        </span>
                    </label>

                    <p
                        class="mt-2 text-[10px] italic leading-relaxed text-gray-400"
                    >
                        Bagikan foto suasana ruang kerja, pantry, atau fasilitas
                        lainnya.
                        <br />
                        <span
                            class="text-[9px] font-medium italic text-amber-600"
                        >
                            Penting: Hindari foto yang menampilkan wajah orang,
                            data sensitif, atau layar komputer yang sedang
                            menyala.
                        </span>
                    </p>

                    <label
                        class="mt-2 flex h-28 w-full cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-blue-100 hover:border-blue-400 hover:bg-blue-50"
                    >
                        <span class="text-2xl text-blue-300">+</span>
                        <span class="text-xs text-gray-400">
                            Klik untuk upload foto
                        </span>

                        <input
                            type="file"
                            multiple
                            accept="image/jpg,image/jpeg,image/png"
                            class="hidden"
                            @change="previewReviewFiles"
                        />
                    </label>

                    <div
                        v-if="reviewFilePreviews.length"
                        class="mt-2 flex flex-wrap gap-2"
                    >
                        <div
                            v-for="(src, index) in reviewFilePreviews"
                            :key="index"
                            class="relative aspect-[4/3] w-16 overflow-hidden rounded-lg"
                        >
                            <img
                                :src="src"
                                class="h-full w-full object-cover"
                            />

                            <button
                                type="button"
                                @click="removeReviewFilePreview(index)"
                                class="absolute right-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-black/50 text-xs text-white"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="reviewForm.errors.files"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ reviewForm.errors.files }}
                    </p>
                </div>

                <!-- Kirim sebagai anonim -->
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input
                        v-model="reviewForm.is_anonymous"
                        type="checkbox"
                        class="rounded border-blue-100 text-blue-600 focus:ring-blue-500"
                    />
                    Kirim sebagai anonim
                </label>

                <!-- tombol submit ddan kembali -->
                <div
                    class="flex justify-end gap-2 border-t border-blue-50 pt-4"
                >
                    <button
                        type="button"
                        @click="closeReviewModal"
                        class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50"
                    >
                        Kembali
                    </button>

                    <button
                        type="submit"
                        :disabled="reviewForm.processing"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{
                            reviewForm.processing
                                ? "Mengirim..."
                                : "Kirim Ulasan"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- review photo modal -->
    <div
        v-if="previewImage"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
    >
        <div class="relative max-w-3xl w-full">
            <!-- CLOSE -->
            <button
                @click="previewImage = null"
                class="absolute -top-8 right-0 text-white text-xl"
            >
                ✕
            </button>

            <img :src="previewImage" class="w-full rounded-xl shadow-lg" />
        </div>
    </div>
</template>
