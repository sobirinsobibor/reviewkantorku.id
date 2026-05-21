<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { useSweetAlert } from '@/Composables/UseSweetAlert'

const previewImageModal = ref(null);
const { toast } = useSweetAlert()

defineOptions({
    layout: PublicLayout,
});

const props = defineProps({
    user: Object,
});

const preview = ref(props.user.profile_photo_url);

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    profile_photo: null,
    _method: "PUT",
});

const previewPhoto = (event) => {
    const file = event.target.files[0];

    if (!file) return;

    form.profile_photo = file;
    preview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post("/my-profile", {
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
            <h1 class="text-base font-semibold text-gray-900">Profil saya</h1>
            <p class="mt-0.5 text-sm text-gray-400">
                Kelola nama, username, dan foto profil kamu.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="space-y-4 rounded-2xl border border-blue-100 bg-white p-4"
        >
            <div>
                <p class="mb-2 text-xs font-medium text-gray-500">
                    Foto profil
                </p>

                <div class="flex items-center gap-4">
                    <div
                        class="flex h-20 w-20 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-blue-50"
                        @click="previewImageModal = preview"
                    >
                        <img
                            v-if="preview"
                            :src="preview"
                            class="h-full w-full object-cover"
                        />

                        <span
                            v-else
                            class="text-xl font-semibold text-blue-600"
                        >
                            {{ form.name?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>

                    <label
                        class="cursor-pointer rounded-lg border border-blue-100 px-3 py-2 text-sm text-gray-600 hover:bg-blue-50"
                    >
                        Upload foto
                        <input
                            type="file"
                            accept="image/jpg,image/jpeg,image/png"
                            class="hidden"
                            @change="previewPhoto"
                        />
                    </label>
                </div>

                <p class="mt-1 text-xs text-gray-400">
                    Maksimal 2MB. Format JPG atau PNG.
                </p>

                <p
                    v-if="form.errors.profile_photo"
                    class="mt-1 text-xs text-red-500"
                >
                    {{ form.errors.profile_photo }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">
                    Nama <span class="text-red-500">*</span>
                </label>

                <input
                    v-model="form.name"
                    required
                    type="text"
                    class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                />
                <p class="mt-1 text-xs text-gray-400">
                    Maksimal 100 karakter.
                </p>
                <p class="mt-1 text-[11px] text-gray-300 text-right">
                    {{ form.name?.length || 0 }}/100
                </p>

                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">
                    Username <span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.username"
                    required
                    type="text"
                    class="w-full rounded-lg border border-blue-100 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    @input="form.username = form.username.replace(/ /g, '-')"
                />
                <p class="mt-1 text-xs text-gray-400">
                    Maksimal 50 karakter. Hanya huruf, angka, tanda minus (-)
                    dan underscore (_).
                </p>
                <p class="text-[11px] text-gray-300">
                    Username harus unik dan akan menjadi identitas publik kamu.
                </p>
                <p class="mt-1 text-[11px] text-gray-300 text-right">
                    {{ form.username?.length || 0 }}/50
                </p>
                <p
                    v-if="form.errors.username"
                    class="mt-1 text-xs text-red-500"
                >
                    {{ form.errors.username }}
                </p>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600">
                    Email
                </label>
                <input
                    :value="user.email"
                    type="email"
                    disabled
                    class="w-full rounded-lg border border-blue-100 bg-gray-50 px-3 py-2 text-sm text-gray-400"
                />
            </div>

            <div class="flex justify-end">
                <!-- KEMBALI -->
                <button
                    type="button"
                    @click="$inertia.visit('/')"
                    class="rounded-lg border border-blue-100 px-4 py-2 text-sm text-gray-600 hover:bg-blue-50 mx-3"
                >
                    Kembali
                </button>

                <!-- SUBMIT -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    {{ form.processing ? "Menyimpan..." : "Simpan profil" }}
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Image Modal -->
    <div
        v-if="previewImageModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
        @click.self="previewImageModal = null"
    >
        <div class="relative max-w-md w-full">
            <!-- CLOSE -->
            <button
                @click="previewImageModal = null"
                class="absolute -top-8 right-0 text-white text-xl"
            >
                ✕
            </button>

            <!-- IMAGE -->
            <img :src="previewImageModal" class="w-full rounded-xl shadow-lg" />
        </div>
    </div>
</template>
