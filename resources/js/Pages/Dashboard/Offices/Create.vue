<script setup>
import { computed, ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useSweetAlert } from '@/Composables/UseSweetAlert'

const { toast } = useSweetAlert()

defineOptions({
    layout: DashboardLayout,
});

const props = defineProps({
    provinces: Array,
    industries: Array,
});

const regencies = ref([]);
const previews = ref([]);

const form = useForm({
    name: "",
    industries: [],
    province_id: "",
    regency_id: "",
    address: "",
    photos: [],
});

const selectedIndustriesCount = computed(() => form.industries.length);

const toggleIndustry = (id) => {
    if (form.industries.includes(id)) {
        form.industries = form.industries.filter((item) => item !== id);
        return;
    }

    if (form.industries.length >= 3) {
        // alert("Anda hanya bisa memilih maksimal 3 industri");
        toast({ icon: 'warning', title: 'Anda hanya bisa memilih maksimal 3 industri' })
        return;
    }

    form.industries.push(id);
};

const loadRegencies = async () => {
    form.regency_id = "";
    regencies.value = [];

    if (!form.province_id) return;

    const response = await fetch(
        `/api/regencies?province_id=${form.province_id}`,
    );
    regencies.value = await response.json();
};

watch(() => form.province_id, loadRegencies);

const previewPhotos = (event) => {
    const files = Array.from(event.target.files);

    form.photos = files;
    previews.value = [];

    files.forEach((file) => {
        const reader = new FileReader();

        reader.onload = (e) => {
            previews.value.push(e.target.result);
        };

        reader.readAsDataURL(file);
    });
};

const removePreview = (index) => {
    previews.value.splice(index, 1);

    form.photos = form.photos.filter((_, photoIndex) => photoIndex !== index);
};

const submit = () => {
    form.post("/dashboard/kantor", {
        forceFormData: true,
        onSuccess: () => {
            toast({ icon: 'success', title: 'Data berhasil disimpan!' })
        },
        onError: () => {
            toast({ icon: 'error', title: 'Terjadi kesalahan!' })
        },
    });
};


</script>

<template>
    <div>
        <div class="mb-5">
            <Link
                href="/dashboard/kantor"
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

            <div>
                <h1 class="text-base font-semibold text-gray-900">
                    Tambah kantor
                </h1>
                <p class="mt-0.5 text-sm text-gray-400">
                    Isi informasi kantor yang ingin kamu ajukan
                </p>
            </div>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div
                class="space-y-4 rounded-xl border border-blue-100 bg-white p-4"
            >
                <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                >
                    Informasi kantor
                </p>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Nama kantor / perusahaan
                        <span class="text-red-500">*</span>
                    </label>
                    <p class="mt-1 text-[10px] text-gray-400 italic">
                        Tuliskan nama lengkap tempat atau perusahaan tempat Anda
                        bekerja saat ini atau sebelumnya.
                    </p>

                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.name"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Industri <span class="text-red-500">*</span>
                        <span class="ml-1 font-normal text-gray-400">
                            (maks. 3)
                        </span>
                    </label>
                    <p
                        class="mt-1 text-[10px] text-gray-400 italic leading-relaxed"
                    >
                        Pilih bidang bisnis yang paling menggambarkan kantor
                        tersebut (misal: Manufaktur, E-commerce, atau Retail).
                    </p>

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <button
                            v-for="industry in industries"
                            :key="industry.id"
                            type="button"
                            @click="toggleIndustry(industry.id)"
                            class="flex items-center gap-2.5 rounded-lg border px-3 py-2.5 text-left text-sm transition"
                            :class="
                                form.industries.includes(industry.id)
                                    ? 'border-blue-400 bg-blue-50 text-blue-700'
                                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                            "
                        >
                            <span
                                class="flex h-4 w-4 flex-shrink-0 items-center justify-center rounded border transition"
                                :class="
                                    form.industries.includes(industry.id)
                                        ? 'border-blue-500 bg-blue-500'
                                        : 'border-gray-300'
                                "
                            >
                                <svg
                                    v-if="form.industries.includes(industry.id)"
                                    class="h-2.5 w-2.5 text-white"
                                    viewBox="0 0 10 8"
                                    fill="none"
                                >
                                    <path
                                        d="M1 4L3.5 6.5L9 1"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>
                            <span>{{ industry.name }}</span>
                        </button>
                    </div>

                    <p class="mt-1 text-xs text-gray-400">
                        {{ selectedIndustriesCount }}/3 industri dipilih
                    </p>

                    <p
                        v-if="form.errors.industries"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.industries }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Alamat lengkap <span class="text-red-500">*</span>
                    </label>
                    <p
                        class="mt-1 text-[10px] text-gray-400 italic leading-relaxed"
                    >
                        Masukkan lokasi kantor cabang atau pusat tempat Anda
                        bekerja agar ulasan lebih spesifik.
                    </p>

                    <textarea
                        v-model="form.address"
                        rows="3"
                        required
                        class="w-full resize-none rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />

                    <p
                        v-if="form.errors.address"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.address }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-gray-600"
                        >
                            Provinsi <span class="text-red-500">*</span>
                        </label>

                        <select
                            v-model="form.province_id"
                            required
                            class="w-full rounded-lg border border-blue-100 bg-white px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">Pilih provinsi</option>
                            <option
                                v-for="province in provinces"
                                :key="province.id"
                                :value="province.id"
                            >
                                {{ province.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.province_id"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.province_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-gray-600"
                        >
                            Kota / Kabupaten <span class="text-red-500">*</span>
                        </label>

                        <select
                            v-model="form.regency_id"
                            required
                            :disabled="!regencies.length"
                            class="w-full rounded-lg border border-blue-100 bg-white px-3 py-2 text-sm disabled:opacity-50 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">Pilih kota</option>
                            <option
                                v-for="regency in regencies"
                                :key="regency.id"
                                :value="regency.id"
                            >
                                {{ regency.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.regency_id"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.regency_id }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Foto kantor <span class="text-red-500">*</span>
                        <span class="ml-1 font-normal text-gray-400">
                            (maks. 10 foto, 5MB/foto)
                        </span>
                    </label>
                    <p
                        class="mt-2 text-[10px] text-gray-400 italic leading-relaxed"
                    >
                        Bagikan foto suasana ruang kerja, pantry, atau fasilitas
                        lainnya. <br />
                        <span
                            class="text-amber-600 font-medium italic text-[9px]"
                            >Penting: Hindari foto yang menampilkan wajah orang,
                            data sensitif, atau layar komputer yang sedang
                            menyala.</span
                        >
                    </p>

                    <label
                        class="flex h-28 w-full cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-blue-100 hover:border-blue-400 hover:bg-blue-50"
                    >
                        <span class="text-2xl text-blue-300">+</span>
                        <span class="text-xs text-gray-400"
                            >Klik untuk upload foto</span
                        >

                        <input
                            type="file"
                            multiple
                            required=""
                            accept="image/jpg,image/jpeg,image/png"
                            class="hidden"
                            @change="previewPhotos"
                        />
                    </label>

                    <div
                        v-if="previews.length"
                        class="mt-2 flex flex-wrap gap-2"
                    >
                        <div
                            v-for="(src, index) in previews"
                            :key="index"
                            class="relative aspect-[4/3] w-16 overflow-hidden rounded-lg"
                        >
                            <img
                                :src="src"
                                class="h-full w-full object-cover"
                            />

                            <button
                                type="button"
                                @click="removePreview(index)"
                                class="absolute right-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-black/50 text-xs text-white"
                            >
                                ×
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="form.errors.photos"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.photos }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Link
                    href="/dashboard/kantor"
                    class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50"
                >
                    Batal
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ form.processing ? "Mengirim..." : "Ajukan kantor" }}
                </button>
            </div>
        </form>
    </div>
</template>
