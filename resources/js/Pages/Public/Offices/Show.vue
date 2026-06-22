<script setup>
import { computed, ref, watch, reactive } from "vue";
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { useSweetAlert } from "@/Composables/UseSweetAlert";

defineOptions({ layout: PublicLayout });

const props = defineProps({
    office: Object,
    templates: { type: Object, default: () => ({}) },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const { toast } = useSweetAlert();

// toggle like / dislike kantor
const isLiked = ref(props.office.is_liked_by_user ?? false);
const isDisliked = ref(props.office.is_disliked_by_user ?? false);
const likesCount = ref(props.office.likes_count ?? 0);
const dislikesCount = ref(props.office.dislikes_count ?? 0);
const isLoading = ref(false);
const justLiked = ref(false);
const justDisliked = ref(false);

function toggleReactionOffice(type) {
    const wasLiked = isLiked.value;
    const wasDisliked = isDisliked.value;

    if (isLoading.value) return;

    if (!page.props.auth.user) {
        toast({
            icon: "error",
            title: "Silakan login untuk melanjutkan aksi",
        });
        return;
    }

    const prevLiked = isLiked.value;
    const prevDisliked = isDisliked.value;
    const prevLikesCount = likesCount.value;
    const prevDislikesCount = dislikesCount.value;

    if (type === "like") {
        if (isLiked.value) {
            isLiked.value = false;
            likesCount.value = Math.max(0, likesCount.value - 1);
        } else {
            if (isDisliked.value) {
                isDisliked.value = false;
                dislikesCount.value = Math.max(0, dislikesCount.value - 1);
            }

            isLiked.value = true;
            likesCount.value++;
        }
    }

    if (type === "dislike") {
        if (isDisliked.value) {
            isDisliked.value = false;
            dislikesCount.value = Math.max(0, dislikesCount.value - 1);
        } else {
            if (isLiked.value) {
                isLiked.value = false;
                likesCount.value = Math.max(0, likesCount.value - 1);
            }

            isDisliked.value = true;
            dislikesCount.value++;
        }
    }

    isLoading.value = true;

    router.post(
        `/offices/${props.office.slug}/reaction`,
        { type },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (type === "like") {
                    toast({
                        icon: isLiked.value ? "success" : "info",
                        title: isLiked.value
                            ? "Menyukai kantor ini"
                            : "Batal menyukai kantor ini",
                    });
                }

                if (type === "dislike") {
                    toast({
                        icon: isDisliked.value ? "warning" : "info",
                        title: isDisliked.value
                            ? "Tidak menyukai kantor ini"
                            : "Batal tidak menyukai kantor ini",
                    });
                }
            },
            onError: () => {
                isLiked.value = prevLiked;
                isDisliked.value = prevDisliked;
                likesCount.value = prevLikesCount;
                dislikesCount.value = prevDislikesCount;
            },
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
}
// toggle like interaction
const toggleLikeInteraction = (interaction) => {
    if (!page.props.auth.user) {
        toast({
            icon: "error",
            title: "Silakan login untuk melanjutkan aksi",
        });
        return;
    }

    const wasLiked = interaction.is_liked; // simpan kondisi awal

    router.post(
        `/interactions/${interaction.ulid}/like`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                interaction.is_liked = !wasLiked;
                interaction.likes_count += wasLiked ? -1 : 1;

                toast({
                    icon: wasLiked ? "info" : "success",
                    title: wasLiked
                        ? "Batal menyukai postingan ini"
                        : "Menyukai postingan ini",
                });
            },
        },
    );
};
/* ------------------------------------------------------------------ */
/* TABS                                                                 */
/* ------------------------------------------------------------------ */
const TABS = [
    { key: "review", label: "Review" },
    { key: "qna", label: "QnA" },
    { key: "cerita_magang", label: "Cerita Magang" },
    { key: "menfess", label: "Menfess" },
];

const activeTab = ref("review");

/* ------------------------------------------------------------------ */
/* LABEL per tab                                                         */
/* ------------------------------------------------------------------ */
const tabWriteLabel = computed(() => {
    const map = {
        review: "Tulis Review",
        qna: "Ajukan Pertanyaan",
        cerita_magang: "Ceritakan Magang",
        menfess: "Kirim Menfess",
    };
    return map[activeTab.value] ?? "Tulis";
});

/* ------------------------------------------------------------------ */
/* Reply — Replying to                                                  */
/* ------------------------------------------------------------------ */
const replyingTo = ref(null); // menyimpan id interaction yang sedang dibalas

const replyForm = reactive({
    content: "",
    reply_to: null,
    direct_parent: null,
    first_parent: null,
    is_anonymous: false,
    office_id: props.office.id,
});
const submitReply = (interaction) => {
    if (!page.props.auth.user) {
        toast({
            icon: "error",
            title: "Silakan login untuk melanjutkan aksi",
        });
        return;
    }
    // console.log(`/interactions/${interaction.ulid}/reply`, replyForm);

    replyForm.direct_parent = interaction.ulid;
    replyForm.first_parent = interaction.first_parent ?? interaction.ulid;

    router.post(`/interactions/${interaction.ulid}/reply`, replyForm, {
        preserveScroll: true,
        onSuccess: async () => {
            replyForm.content = "";
            replyForm.is_anonymous = false;
            // replyingTo.value = null;

            await loadReplies(interaction, true);
        },
        
    });
};

const replyTo = (interaction) => {
    // if (replyingTo.value === interaction.ulid) {
    //     cancelReply();
    //     return;
    // }

    replyingTo.value = interaction.ulid;
    replyForm.content = "";
    replyForm.is_anonymous = false;
    replyForm.direct_parent = interaction.ulid;
    replyForm.reply_to = interaction.type === "reply"
        ? interaction.reply_to
        : interaction.type;

    replyForm.first_parent = interaction.first_parent ?? interaction.first_parent_id ?? interaction.ulid;
};

const cancelReply = (interaction) => {
    const id = interaction.first_parent ?? interaction.first_parent_id ?? interaction.ulid;

    replyForm.content = "";
    replyForm.is_anonymous = false;
    replyingTo.value = null;

    openedReplies.value[id] = false;
};

//show replies
const openedReplies = ref({});
const replies = reactive({});
const loadingReplies = reactive({});

const loadReplies = async (interaction, force = false) => {
    const id = interaction.first_parent ?? interaction.first_parent_id ?? interaction.ulid;

    openedReplies.value[id] = true;

    if (replies[id] && !force) return;

    loadingReplies[id] = true;

    try {
        const res = await fetch(`/interactions/${id}/replies`, {
            headers: {
                Accept: "application/json",
            },
        });

        const data = await res.json();

        replies[id] = data.data;
    } finally {
        loadingReplies[id] = false;
    }
};

const toggleReplies = async (interaction) => {
    const id = interaction.first_parent ?? interaction.first_parent_id ?? interaction.ulid;

    openedReplies.value[id] = !openedReplies.value[id];

    if (!openedReplies.value[id]) return;

    await loadReplies(interaction);
};

// const toggleReplies = async (interaction) => {
//     // console.log("toggleReplies", interaction.ulid);
//     const id = interaction.ulid;

//     openedReplies.value[id] = !openedReplies.value[id];

//     if (!openedReplies.value[id]) return;

//     if (replies[id]) return;

//     loadingReplies[id] = true;

//     try {
//         const res = await fetch(
//             `/interactions/${interaction.ulid}/replies`,
//             {
//                 headers: {
//                     Accept: "application/json",
//                 },
//             }
//         );

//         const data = await res.json();

//         replies[id] = data.data;
//     } finally {
//         loadingReplies[id] = false;
//     }
// };

/* ------------------------------------------------------------------ */
/* FEED — cache per tab                                                 */
/* ------------------------------------------------------------------ */
const cache = ref({}); // { review: { items, page, lastPage, loading } }
const loadingTab = ref(false);

const currentFeed = computed(() => cache.value[activeTab.value] ?? null);

async function fetchFeed(type, page = 1) {
    if (!cache.value[type]) {
        cache.value[type] = { items: [], page: 0, lastPage: 1, loading: false };
    }

    const feed = cache.value[type];
    if (feed.loading) return;
    if (page > 1 && page > feed.lastPage) return;

    feed.loading = true;

    try {
        const res = await fetch(
            `/detail/${props.office.slug}/feed?type=${type}&page=${page}`,
        );
        const json = await res.json();

        if (page === 1) {
            feed.items = json.data;
        } else {
            feed.items = [...feed.items, ...json.data];
        }

        feed.page = json.meta.current_page;
        feed.lastPage = json.meta.last_page;
    } finally {
        feed.loading = false;
    }
}

// Load saat tab berubah (hanya jika belum pernah di-fetch)
watch(
    activeTab,
    (type) => {
        if (!cache.value[type]) fetchFeed(type, 1);
    },
    { immediate: true },
);

function loadMore() {
    const feed = currentFeed.value;
    if (!feed || feed.loading || feed.page >= feed.lastPage) return;
    fetchFeed(activeTab.value, feed.page + 1);
}

/* ------------------------------------------------------------------ */
/* PHOTO CAROUSEL                                                        */
/* ------------------------------------------------------------------ */
const currentPhoto = ref(0);
const previewImage = ref(null);

/* ------------------------------------------------------------------ */
/* MODAL — DETAIL REVIEW                                                */
/* ------------------------------------------------------------------ */
const detailReview = ref(null);

const getUserData = (field) => {
    if (!field?.userData) return [];
    return Array.isArray(field.userData)
        ? field.userData.map(String)
        : [String(field.userData)];
};

const isSelectedOption = (field, option) =>
    getUserData(field).includes(String(option.value));

const getOtherAnswer = (field) =>
    getUserData(field)
        .find((i) => i.startsWith("other:"))
        ?.replace("other:", "") ?? null;

const isDisplayableField = (field) =>
    field.name && !["header", "paragraph"].includes(field.type);

/* ------------------------------------------------------------------ */
/* MODAL — TULIS ULASAN                                                 */
/* ------------------------------------------------------------------ */
const reviewModal = ref(false);
const reviewFilePreviews = ref([]);

const reviewForm = useForm({
    type: "review",
    content_form_id: null,
    is_anonymous: false,
    answers: {},
    files: [],
});

const activeTemplate = computed(
    () => props.templates[activeTab.value]?.schema ?? [],
);

const activeTemplateId = computed(
    () => props.templates[activeTab.value]?.id ?? null,
);

function openReviewModal() {
    reviewForm.reset();
    reviewForm.clearErrors();
    reviewForm.type = activeTab.value;
    reviewForm.content_form_id = activeTemplateId.value;
    reviewForm.is_anonymous = false;
    reviewForm.files = [];
    reviewFilePreviews.value = [];

    reviewForm.answers = {};
    activeTemplate.value.forEach((field) => {
        if (!field.name) return;
        reviewForm.answers[field.name] =
            field.type === "checkbox-group" ? [] : "";
    });

    reviewModal.value = true;
}

function closeReviewModal() {
    reviewForm.reset();
    reviewForm.clearErrors();
    reviewForm.answers = {};
    reviewForm.files = [];
    reviewFilePreviews.value = [];
    reviewModal.value = false;
}

function submitReview() {
    reviewForm.post(`/kantor/${props.office.slug}/interactions`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // Bust cache tab ini supaya data fresh
            delete cache.value[activeTab.value];
            fetchFeed(activeTab.value, 1);
            closeReviewModal();
        },
    });
}

function toggleCheckboxAnswer(name, value) {
    if (!Array.isArray(reviewForm.answers[name])) reviewForm.answers[name] = [];

    reviewForm.answers[name] = reviewForm.answers[name].includes(value)
        ? reviewForm.answers[name].filter((i) => i !== value)
        : [...reviewForm.answers[name], value];
}

function previewReviewFiles(event) {
    const files = Array.from(event.target.files);
    reviewForm.files = files;
    reviewFilePreviews.value = [];

    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => reviewFilePreviews.value.push(e.target.result);
        reader.readAsDataURL(file);
    });
}

function removeReviewFilePreview(index) {
    reviewFilePreviews.value.splice(index, 1);
    reviewForm.files = reviewForm.files.filter((_, i) => i !== index);
}
</script>

<template>
    <div>
        <!-- Back -->
        <Link
            href="/kantor"
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

        <!-- Header -->
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
                class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-green-600"
            >
                {{ office.status_label }}
            </span>
        </div>

        <!-- Photo + Info -->
        <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-[1.1fr_0.9fr]">
            <!-- Foto -->
            <div v-if="office.photos.length" class="relative">
                <div
                    class="relative aspect-[4/3] overflow-hidden rounded-xl bg-blue-50"
                >
                    <img
                        :src="office.photos[currentPhoto].url"
                        alt="Foto kantor"
                        class="h-full w-full object-cover transition-all duration-200"
                        @click="previewImage = office.photos[currentPhoto].url"
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

                    <!-- {{-- Counter badge --}} -->
                    <div
                        class="absolute bottom-2 right-2 rounded-full bg-black/40 px-2 py-0.5 text-xs text-white"
                    >
                        {{ currentPhoto + 1 }} / {{ office.photos.length }}
                    </div>
                </div>

                <!-- {{-- Thumbnail strip --}} -->
                <div
                    v-if="office.photos.length > 1"
                    class="mt-3 flex gap-2 overflow-x-auto pb-1"
                >
                    <button
                        v-for="(photo, index) in office.photos"
                        :key="photo.id"
                        type="button"
                        @click="currentPhoto = index"
                        class="relative h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg border-2 transition-all"
                        :class="
                            currentPhoto === index
                                ? 'border-blue-500 opacity-100'
                                : 'border-transparent opacity-50 hover:opacity-80'
                        "
                    >
                        <img
                            :src="photo.url"
                            alt=""
                            class="h-full w-full object-cover"
                        />
                    </button>
                </div>

                <!-- {{-- Dot indicator (opsional, bisa dihapus kalau sudah ada thumbnail) --}} -->
                <div
                    v-if="office.photos.length > 1"
                    class="mt-2 flex items-center justify-center gap-2"
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

            <!-- Info -->
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white"
            >
                <!-- Header Office -->
                <div class="flex items-start gap-4 p-5">
                    <!-- <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 21h18M6 21V7l6-3v17M18 21V9l-6-2M9 10h.01M9 14h.01M9 18h.01M15 12h.01M15 16h.01"
                            />
                        </svg>
                    </div> -->

                    <div class="min-w-0">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ office.name }}
                        </h2>

                        <div
                            class="mt-1 flex items-center gap-1 text-sm text-gray-600"
                        >
                            <span>
                                {{ office.address || "-" }} ·
                                {{ office.regency }}, {{ office.province }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Industries -->
                <div
                    v-if="office.industries?.length"
                    class="border-t border-gray-200 p-5"
                >
                    <!-- <p
                        class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500"
                    >
                        Industri
                    </p> -->

                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="industry in office.industries"
                            :key="industry.id"
                            class="rounded-full bg-blue-50 px-4 py-1 text-xs font-small text-blue-700"
                        >
                            {{ industry.name }}
                        </span>
                    </div>
                </div>

                <!-- Reactions -->
                <!-- Reactions -->
                <div class="border-t border-gray-200 px-5 py-5">
                    <div class="flex items-center gap-3">
                        <!-- Like -->
                        <button
                            type="button"
                            @click="toggleReactionOffice('like')"
                            :disabled="isLoading"
                            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-50"
                            :class="
                                isLiked
                                    ? 'border-red-300 bg-red-50 text-red-600'
                                    : 'border-gray-300 bg-white text-gray-700 hover:border-red-300 hover:text-red-500'
                            "
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 transition-transform duration-200"
                                :class="{ 'scale-125': justLiked }"
                                :fill="isLiked ? 'currentColor' : 'none'"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 001.97-1.66l1.38-8A2 2 0 0018.66 10H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"
                                />
                            </svg>

                            <span>{{ likesCount }}</span>
                        </button>

                        <div class="h-6 w-px bg-gray-200"></div>

                        <!-- Dislike -->
                        <button
                            type="button"
                            @click="toggleReactionOffice('dislike')"
                            :disabled="isLoading"
                            class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-50"
                            :class="
                                isDisliked
                                    ? 'border-gray-500 bg-gray-100 text-gray-900'
                                    : 'border-gray-300 bg-white text-gray-700 hover:border-gray-500 hover:text-gray-900'
                            "
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 transition-transform duration-200"
                                :class="{ 'scale-125': justDisliked }"
                                :fill="isDisliked ? 'currentColor' : 'none'"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-1.97 1.66l-1.38 8A2 2 0 004.34 14H10zM17 2h3a2 2 0 012 2v7a2 2 0 01-2 2h-3"
                                />
                            </svg>

                            <span>{{ dislikesCount }}</span>
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="border-t border-gray-200 p-5">
                    <div class="grid grid-cols-2 gap-2">
                        <div
                            v-for="tab in TABS"
                            :key="tab.key"
                            class="rounded-lg bg-blue-50 p-2.5 text-center"
                        >
                            <p class="mb-0.5 text-xs text-gray-400">
                                {{ tab.label }}
                            </p>

                            <p class="text-xl font-semibold text-gray-900">
                                {{ office.counts[tab.key] ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- -------------------------------------------------------- -->
        <!-- TABS                                                       -->
        <!-- -------------------------------------------------------- -->
        <div class="mb-4 border-b border-blue-100">
            <div class="flex gap-1 overflow-x-auto">
                <button
                    v-for="tab in TABS"
                    :key="tab.key"
                    type="button"
                    @click="activeTab = tab.key"
                    class="relative shrink-0 px-4 py-2.5 text-sm font-medium transition-colors"
                    :class="
                        activeTab === tab.key
                            ? 'text-blue-600 after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-blue-600'
                            : 'text-gray-400 hover:text-gray-600'
                    "
                >
                    {{ tab.label }}
                    <span
                        class="ml-1 rounded-full bg-blue-50 px-1.5 py-0.5 text-[10px] text-blue-500"
                    >
                        {{ office.counts[tab.key] ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Tombol tulis -->
        <div class="mb-4">
            <button
                v-if="user"
                type="button"
                @click="openReviewModal"
                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
            >
                {{ tabWriteLabel }}
            </button>
            <p v-else class="text-sm text-gray-400">
                <button type="button" class="text-blue-600 hover:underline">
                    Login
                </button>
                untuk menulis di sini.
            </p>
        </div>

        <!-- Feed -->
        <div v-if="currentFeed">
            <!-- Loading awal -->
            <div
                v-if="currentFeed.loading && !currentFeed.items.length"
                class="py-10 text-center text-sm text-gray-400"
            >
                Memuat...
            </div>

            <!-- Kosong -->
            <div
                v-else-if="!currentFeed.items.length"
                class="py-10 text-center text-sm text-gray-400"
            >
                Belum ada konten untuk tab ini.
            </div>

            <!-- List -->
            <div v-else class="space-y-3">
                <button
                    v-for="interaction in currentFeed.items"
                    :key="interaction.id"
                    type="button"
                    class="relative w-full rounded-xl border border-gray-100 bg-white p-5 text-left transition hover:border-gray-200 group"
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
                                {{ interaction.user.initials }}
                            </div>
                            <span class="text-sm font-medium text-gray-900">
                                {{ interaction.user.name }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-400 tracking-wide">
                            {{ interaction.created_at_human }}
                        </span>
                    </div>

                    <!-- Divider -->
                    <div class="mb-3 h-px bg-gray-100"></div>

                    <!-- Content -->
                    <div class="space-y-1.5">
                        <template
                            v-for="(item, key) in interaction.main_contents"
                            :key="key"
                        >
                            <p
                                v-if="item.value"
                                class="text-sm leading-relaxed text-gray-500"
                            >
                                {{ item.value }}
                            </p>
                        </template>
                    </div>

                    <!-- Photo badge -->
                    <div v-if="interaction.files?.length" class="mt-3">
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

                    <!-- Footer -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <!-- Like button -->
                            <button
                                type="button"
                                @click.stop="toggleLikeInteraction(interaction)"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs transition"
                                :class="
                                    interaction.is_liked
                                        ? 'text-red-500 hover:bg-red-50'
                                        : 'text-gray-400 hover:bg-gray-50 hover:text-red-400'
                                "
                            >
                                <span>{{
                                    interaction.is_liked ? "❤️" : "🤍"
                                }}</span>
                                <span class="tabular-nums">{{
                                    interaction.likes_count
                                }}</span>
                            </button>

                            <!-- Separator -->
                            <span class="h-3.5 w-px bg-gray-200"></span>

                            <!-- Reply button -->
                            <button
                                type="button"
                                @click.stop="
                                    replyTo(interaction);
                                    toggleReplies(interaction);
                                "
                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs transition"
                                :class="
                                    replyingTo === interaction.ulid
                                        ? 'bg-blue-50 text-blue-500'
                                        : 'text-gray-400 hover:bg-gray-50 hover:text-blue-500'
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3.5 w-3.5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="9 17 4 12 9 7" />
                                    <path d="M20 18v-2a4 4 0 0 0-4-4H4" />
                                </svg>
                                <span>Balas</span>
                            </button>

                            <!-- Tambah di template, di dalam card, setelah card-footer -->
                        </div>

                        <!-- See more -->
                        <!-- Tombol Selengkapnya -->
                        <button
                            type="button"
                            @click.stop="detailReview = interaction"
                            class="inline-flex items-center gap-1 text-xs text-blue-500 hover:underline"
                        >
                            Selengkapnya
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-3 w-3"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Reply Accordion -->
                    <transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-48"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 max-h-48"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div
                            v-if="replyingTo === interaction.ulid"
                            class="overflow-hidden border-t border-gray-100 bg-gray-50"
                        >
                            <div class="px-6 py-4">
                                <p
                                    class="mb-2 flex items-center gap-1.5 text-xs text-gray-400"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <polyline points="9 17 4 12 9 7" />
                                        <path d="M20 18v-2a4 4 0 0 0-4-4H4" />
                                    </svg>
                                    Membalas
                                    <strong class="font-medium text-gray-600">{{
                                        interaction.user.name
                                    }}</strong>
                                </p>

                                <textarea
                                    v-model="replyForm.content"
                                    rows="2"
                                    placeholder="Tulis balasan..."
                                    class="w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-400 outline-none transition focus:border-blue-400"
                                    @click.stop
                                ></textarea>

                                <div
                                    class="mt-2.5 flex items-center justify-between"
                                >
                                    <label
                                        class="flex cursor-pointer items-center gap-1.5 text-xs text-gray-400"
                                        @click.stop
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="replyForm.is_anonymous"
                                            class="accent-blue-600"
                                        />
                                        Kirim anonim
                                    </label>

                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            @click.stop="cancelReply(interaction)"
                                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs text-gray-500 transition hover:bg-gray-100"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            type="button"
                                            @click.stop="
                                                submitReply(interaction)
                                            "
                                            class="flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs text-white transition hover:bg-blue-700"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3.5 w-3.5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <line
                                                    x1="22"
                                                    y1="2"
                                                    x2="11"
                                                    y2="13"
                                                />
                                                <polygon
                                                    points="22 2 15 22 11 13 2 9 22 2"
                                                />
                                            </svg>
                                            Kirim
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <div
                        v-if="openedReplies[interaction.ulid]"
                        class="border-t border-gray-100 bg-white px-6 py-4"
                    >
                        <p
                            v-if="loadingReplies[interaction.ulid]"
                            class="text-xs text-gray-400"
                        >
                            Memuat balasan...
                        </p>

                        <div
                            v-for="reply in replies[interaction.ulid]"
                            :key="reply.ulid"
                            class="mb-3 border-l-2 border-gray-100 pl-3"
                        >
                            <p class="text-xs text-gray-500">
                                {{ reply.user?.name ?? "Anonim" }}
                                <span v-if="reply.direct_parent">
                                    membalas
                                    {{ reply.direct_parent.user?.name }}
                                </span>
                            </p>

                            <p class="mt-1 text-sm text-gray-700">
                                {{ reply.main_contents?.reply?.value }}
                            </p>

                            <div class="mt-2 flex items-center gap-3">
                               
                                <!-- Like button -->
                                <button
                                    type="button"
                                    @click.stop="toggleLikeInteraction(reply)"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs transition"
                                    :class="
                                        reply.is_liked
                                            ? 'text-red-500 hover:bg-red-50'
                                            : 'text-gray-400 hover:bg-gray-50 hover:text-red-400'
                                    "
                                >
                                    <span>{{
                                        reply.is_liked ? "❤️" : "🤍"
                                    }}</span>
                                    <span class="tabular-nums">{{
                                        reply.likes_count
                                    }}</span>
                                </button>
                                
                            </div>

                            <!-- <p class="mt-1 text-sm text-gray-700">
                                {{ reply.content }}
                            </p> -->

                            <!-- <button
                                type="button"
                                @click.stop="replyTo(reply)"
                                class="mt-1 text-xs text-blue-500"
                            >
                                Balas
                            </button> -->
                        </div>
                    </div>
                </button>
            </div>

            <!-- Load more -->
            <div
                v-if="currentFeed.page < currentFeed.lastPage"
                class="mt-4 text-center"
            >
                <button
                    type="button"
                    @click="loadMore"
                    :disabled="currentFeed.loading"
                    class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 disabled:opacity-50"
                >
                    {{
                        currentFeed.loading ? "Memuat..." : "Muat lebih banyak"
                    }}
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- MODAL DETAIL REVIEW                                               -->
    <!-- ================================================================ -->
    <div
        v-if="detailReview"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        @click.self="detailReview = null"
    >
        <div
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-blue-50 px-5 py-4"
            >
                <div class="flex flex-col gap-1">
                    <!-- Nama kantor (judul) -->
                    <p class="text-base font-semibold text-gray-900">
                        {{ office?.name ?? "-" }}
                    </p>

                    <!-- User + waktu -->
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-medium text-blue-700"
                        >
                            {{ detailReview.user.initials }}
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-700">
                                {{ detailReview.user.name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ detailReview.created_at_human }}
                            </p>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="detailReview = null"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 20 20">
                        <path
                            d="M6 6l8 8M14 6l-8 8"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                        />
                    </svg>
                </button>
            </div>

            <div class="space-y-4 px-5 py-4">
                <!-- Attributes -->
                <div
                    v-if="
                        detailReview.attributes?.filter(isDisplayableField)
                            .length
                    "
                >
                    <p
                        class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400"
                    >
                        Detail
                    </p>
                    <div class="grid gap-3 sm:grid-cols-1">
                        <div
                            v-for="field in detailReview.attributes.filter(
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

                <!-- Foto -->
                <div v-if="detailReview.files?.length">
                    <p
                        class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400"
                    >
                        Foto
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="file in detailReview.files"
                            :key="file.id"
                            type="button"
                            @click="previewImage = file.url"
                            class="group relative aspect-[4/3] w-24 overflow-hidden rounded-lg border border-blue-100"
                        >
                            <img
                                :src="file.url"
                                class="h-full w-full object-cover transition group-hover:scale-105"
                            />
                            <div
                                class="absolute inset-0 bg-black/0 transition group-hover:bg-black/20"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- MODAL TULIS ULASAN                                                -->
    <!-- ================================================================ -->
    <div
        v-if="reviewModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
        <div
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white"
        >
            <div class="border-b border-blue-50 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ tabWriteLabel }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Bagikan pengalamanmu secara sopan dan bermanfaat.
                </p>
            </div>

            <form class="space-y-4 px-5 py-4" @submit.prevent="submitReview">
                <!-- Dynamic template fields -->
                <template
                    v-for="field in activeTemplate"
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
                                        >Lainnya</span
                                    >
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

                <!-- Foto -->
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Foto pendukung
                        <span class="ml-1 font-normal text-gray-400"
                            >(opsional, maks. 5 foto, 5MB/foto)</span
                        >
                    </label>
                    <p
                        class="mt-2 text-[10px] italic leading-relaxed text-gray-400"
                    >
                        Bagikan foto suasana ruang kerja, pantry, atau fasilitas
                        lainnya.<br />
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
                        <span class="text-xs text-gray-400"
                            >Klik untuk upload foto</span
                        >
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

                <!-- Anonim -->
                <div class="flex justify-end">
                    <label
                        class="flex items-center gap-2 text-sm text-gray-700"
                    >
                        <input
                            v-model="reviewForm.is_anonymous"
                            type="checkbox"
                            class="rounded border-blue-100 text-blue-600 focus:ring-blue-500"
                        />
                        Kirim sebagai anonim
                    </label>
                </div>

                <!-- Action -->
                <div
                    class="flex justify-end gap-2 border-t border-blue-50 pt-4"
                >
                    <button
                        type="button"
                        @click="closeReviewModal"
                        class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="reviewForm.processing"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ reviewForm.processing ? "Mengirim..." : "Kirim" }}
                    </button>
                </div>
            </form>
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
