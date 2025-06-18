<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import { BadgeCheck, Package, Layers, FileText, ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps<{
    barang: {
        id: number;
        kode_barang: string;
        nama_barang: string;
        kategori: string;
        stok: number;
        deskripsi: string;
    };
}>();

const form = useForm({
    kode_barang: props.barang.kode_barang,
    nama_barang: props.barang.nama_barang,
    kategori: props.barang.kategori,
    stok: props.barang.stok.toString(), // tetap string agar binding input tidak error
    deskripsi: props.barang.deskripsi,
});

const isSuccess = ref(false);

const submit = () => {
    const stokAngka = parseInt(form.stok);
    if (isNaN(stokAngka) || stokAngka < 0) {
        form.errors.stok = 'Stok harus berupa angka positif';
        return;
    }

    form.put(route('barang.update', props.barang.id), {
        data: {
            ...form.data(),
            stok: stokAngka, // pastikan dikirim sebagai angka
        },
        onSuccess: () => {
            isSuccess.value = true;
            setTimeout(() => isSuccess.value = false, 2000);
        },
    });
};
</script>
