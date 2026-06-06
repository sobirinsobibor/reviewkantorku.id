<script setup>
import { Link } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth?.user ?? null

defineOptions({
    layout: PublicLayout,
});

const features = [
    {
        icon: "🏢",
        bg: "bg-blue-50",
        name: "Ulasan Perusahaan",
        desc: "Baca dan bagikan review jujur tentang budaya kerja, gaji, manajemen, dan lingkungan perusahaan.",
    },
    {
        icon: "🎭",
        bg: "bg-orange-50",
        name: "Cerita Anonim",
        desc: "Berbagi pengalaman kerja tanpa khawatir — identitasmu sepenuhnya terlindungi dan aman.",
    },
    {
        icon: "💬",
        bg: "bg-green-50",
        name: "Tanya Komunitas",
        desc: "Ajukan pertanyaan seputar dunia kerja dan dapatkan jawaban dari para profesional berpengalaman.",
    },
    {
        icon: "🎒",
        bg: "bg-purple-50",
        name: "Pengalaman Magang",
        desc: "Temukan cerita magang nyata untuk mempersiapkan dirimu sebelum terjun ke dunia kerja.",
    },
];

const journeys = [
    {
        emoji: "🔍",
        name: "Eksplorasi Karier",
        desc: "Baru mulai karier? Temukan gambaran nyata tentang berbagai profesi dan industri dari orang yang sudah menjalaninya.",
    },
    {
        emoji: "📊",
        name: "Riset Sebelum Apply",
        desc: "Sebelum melamar, ketahui budaya kerja, ekspektasi gaji, dan proses rekrutmen dari karyawan yang ada di sana.",
    },
    {
        emoji: "💼",
        name: "Cerita Dunia Kerja",
        desc: "Sudah bekerja? Bagikan pengalamanmu dan bantu ribuan orang lain membuat keputusan karier yang lebih baik.",
    },
];

const whys = [
    {
        icon: "👥",
        title: "Komunitas Aktif",
        desc: "Ribuan pengguna saling berbagi pengalaman dan memberikan dukungan satu sama lain setiap harinya.",
    },
    {
        icon: "🔒",
        title: "Aman & Anonim",
        desc: "Berbagi lebih nyaman tanpa khawatir. Identitasmu terlindungi dengan sistem keamanan berlapis kami.",
    },
    {
        icon: "💡",
        title: "Ribuan Insight Nyata",
        desc: "Informasi jujur langsung dari pengalaman nyata para profesional Indonesia.",
    },
    {
        icon: "🤝",
        title: "Jawaban dari Komunitas",
        desc: "Diskusi hidup dan respons cepat dari pengguna yang berpengalaman di bidangnya.",
    },
    {
        icon: "🎯",
        title: "Bantu Ambil Keputusan",
        desc: "Agar lebih yakin menentukan langkah karier berikutnya dengan data dan cerita nyata sebagai dasar.",
    },
    {
        icon: "🌿",
        title: "Budaya Kerja",
        desc: "Kenali lingkungan dan budaya perusahaan secara mendalam sebelum memutuskan untuk bergabung.",
    },
];
</script>

<template>
    <!-- ─────────────────────────────────────────
         HERO
    ───────────────────────────────────────── -->
    <section class="mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 via-blue-50/60 to-white px-6 py-12 sm:px-10 sm:py-16">
        <div class="mx-auto grid max-w-5xl items-center gap-10 lg:grid-cols-2">
            <!-- Kiri -->
            <div>
                <span class="mb-4 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                    +12.000 review kantor terpercaya 🔥
                </span>

                <h1 class="mb-4 text-3xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">
                    Cari Tahu Dunia Kerja<br />
                    dari
                    <span class="relative inline-block text-blue-600">
                        Cerita Nyata
                        <span class="absolute -bottom-1 left-0 h-1 w-full rounded-full bg-gradient-to-r from-blue-400 to-blue-200 opacity-60" />
                    </span>
                </h1>

                <p class="mb-8 max-w-md text-base leading-relaxed text-gray-500">
                    Baca review kantor, tanya pengalaman kerja, dan bagikan cerita
                    secara anonim. Semua dalam satu platform yang jujur dan terpercaya.
                </p>

                <div class="flex flex-wrap items-center gap-3">
                    <Link
                        href="/register"
                        class="rounded-full bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        🚀 Mulai Review
                    </Link>
                    <Link
                        href="/kantor"
                        class="rounded-full border border-blue-200 bg-white px-6 py-2.5 text-sm font-semibold text-blue-600 hover:bg-blue-50"
                    >
                        Lihat Kantor
                    </Link>
                </div>

                <!-- Stats -->
                <div class="mt-10 flex items-center gap-6 border-t border-blue-100 pt-6">
                    <div>
                        <p class="text-xl font-extrabold text-blue-600">12K+</p>
                        <p class="text-xs text-gray-400">Review Kantor</p>
                    </div>
                    <div class="h-8 w-px bg-blue-100" />
                    <div>
                        <p class="text-xl font-extrabold text-blue-600">5.8K+</p>
                        <p class="text-xs text-gray-400">Perusahaan</p>
                    </div>
                    <div class="h-8 w-px bg-blue-100" />
                    <div>
                        <p class="text-xl font-extrabold text-blue-600">98%</p>
                        <p class="text-xs text-gray-400">Anonim & Aman</p>
                    </div>
                </div>
            </div>

            <!-- Kanan – preview card -->
            <div class="hidden lg:block">
                <div class="relative">
                    <!-- Main review card -->
                    <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-lg">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-xl">
                                    🏢
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">PT. Maju Digital</p>
                                    <p class="text-xs text-gray-400">Technology · Jakarta</p>
                                </div>
                            </div>
                            <span class="text-sm text-yellow-400">★★★★★</span>
                        </div>
                        <p class="mb-3 text-sm leading-relaxed text-gray-500">
                            "Lingkungan kerja sangat mendukung, tim solid, dan manajemen yang transparan. Work-life balance terjaga dengan baik..."
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 text-sm">
                                👤
                            </div>
                            <span>Software Engineer · Anonim</span>
                            <span class="ml-auto font-medium text-blue-500">2 jam lalu</span>
                        </div>
                    </div>

                    <!-- Float card kanan atas -->
                    <div class="absolute -right-4 -top-4 flex items-center gap-2 rounded-xl border border-blue-100 bg-white px-3 py-2 shadow-md">
                        <span class="text-base">💼</span>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Info Gaji</p>
                            <p class="text-[11px] text-gray-400">Rata-rata Rp 12 jt/bln</p>
                        </div>
                    </div>

                    <!-- Float card kiri bawah -->
                    <div class="absolute -bottom-4 -left-4 flex items-center gap-2 rounded-xl border border-blue-100 bg-white px-3 py-2 shadow-md">
                        <span class="text-base">✅</span>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Verified</p>
                            <p class="text-[11px] text-gray-400">512 karyawan aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────
         FITUR
    ───────────────────────────────────────── -->
    <section class="mb-10">
        <div class="mb-8 text-center">
            <span class="mb-2 inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                Platform
            </span>
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                Satu Platform untuk<br />
                <span class="text-blue-600">Semua Cerita</span> Dunia Kerja
            </h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-gray-400">
                Dari ulasan perusahaan hingga pengalaman magang — semua ada di ReviewKantorku.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="(item, i) in features"
                :key="i"
                class="group rounded-2xl border border-blue-100 bg-white p-6 transition hover:-translate-y-1 hover:shadow-md"
            >
                <div
                    :class="item.bg"
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl text-3xl"
                >
                    {{ item.icon }}
                </div>
                <p class="mb-1.5 text-sm font-bold text-gray-800">{{ item.name }}</p>
                <p class="text-xs leading-relaxed text-gray-400">{{ item.desc }}</p>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────
         PERJALANAN KARIER
    ───────────────────────────────────────── -->
    <section class="mb-10 rounded-2xl bg-blue-50/50 px-6 py-10 sm:px-10">
        <div class="mb-8 text-center">
            <span class="mb-2 inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                Perjalanan Karier
            </span>
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                Cocok untuk <span class="text-blue-600">Setiap Tahap</span><br />
                Perjalanan Karier
            </h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-gray-400">
                Apapun fase kariermu, ReviewKantorku hadir untuk menemanimu membuat keputusan terbaik.
            </p>
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <div
                v-for="(item, i) in journeys"
                :key="i"
                class="rounded-2xl border border-blue-100 bg-white p-6 text-center transition hover:-translate-y-1 hover:shadow-md"
            >
                <div class="relative mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-blue-50 text-5xl">
                    {{ item.emoji }}
                    <span class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        {{ i + 1 }}
                    </span>
                </div>
                <p class="mb-2 text-sm font-bold text-gray-800">{{ item.name }}</p>
                <p class="text-xs leading-relaxed text-gray-400">{{ item.desc }}</p>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────
         KENAPA REVIEWKANTORKU
    ───────────────────────────────────────── -->
    <section class="mb-10">
        <div class="mb-8 text-center">
            <span class="mb-2 inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                Keunggulan
            </span>
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                Kenapa <span class="text-blue-600">ReviewKantorku?</span>
            </h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-gray-400">
                Kami hadir dengan komitmen membantu kamu membuat pilihan karier yang lebih cerdas.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="(item, i) in whys"
                :key="i"
                class="rounded-2xl border border-blue-100 bg-white p-6 transition hover:-translate-y-1 hover:shadow-md"
            >
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-2xl">
                    {{ item.icon }}
                </div>
                <p class="mb-1.5 text-sm font-bold text-gray-800">{{ item.title }}</p>
                <p class="text-xs leading-relaxed text-gray-400">{{ item.desc }}</p>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────
         CTA
    ───────────────────────────────────────── -->
    <section class="mb-2">
        <div class="relative overflow-hidden rounded-2xl bg-blue-600 px-8 py-12 sm:px-12">
            <!-- Background decoration -->
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-blue-500/40" />
            <div class="pointer-events-none absolute -bottom-12 left-1/3 h-48 w-48 rounded-full bg-blue-700/40" />

            <div class="relative z-10 flex flex-col items-center justify-between gap-8 lg:flex-row">
                <!-- Teks -->
                <div class="text-center lg:text-left">
                    <span class="mb-3 inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                        Bergabung Sekarang
                    </span>
                    <h2 class="mb-3 text-2xl font-extrabold leading-tight text-white sm:text-3xl">
                        Yuk, jadi bagian dari<br />
                        <span class="text-yellow-300">ReviewKantorku!</span>
                    </h2>
                    <p class="max-w-md text-sm leading-relaxed text-blue-100">
                        Ribuan profesional sudah berbagi cerita dan membantu satu sama lain. Saatnya kamu ikut berkontribusi.
                    </p>

                    <!-- Avatar stack -->
                    <div class="mt-5 flex items-center gap-3 justify-center lg:justify-start">
                        <div class="flex -space-x-2">
                            <div v-for="n in 4" :key="n"
                                class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-blue-600 bg-blue-400 text-base"
                            >
                                {{ ['👩','👨','👩‍💼','🧑‍💻'][n - 1] }}
                            </div>
                        </div>
                        <p class="text-xs text-blue-200">+12.000 anggota aktif</p>
                    </div>
                </div>

                <!-- Aksi -->
                <div class="flex shrink-0 flex-col items-center gap-3">
                    <!-- Sudah login -->
                    <a
                        v-if="user"
                        href="/kantor"
                        class="rounded-full bg-white px-8 py-3 text-sm font-bold text-blue-600 shadow-md transition hover:shadow-lg hover:-translate-y-0.5"
                    >
                        ✍️ Tulis Ulasan
                    </a>

                    <!-- Belum login -->
                    <template v-else>
                        <a
                            href="/?login=1"
                            class="rounded-full bg-white px-8 py-3 text-sm font-bold text-blue-600 shadow-md transition hover:shadow-lg hover:-translate-y-0.5"
                        >
                            🚀 Gabung Sekarang
                        </a>
                       
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>