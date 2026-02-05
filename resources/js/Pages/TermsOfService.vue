<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head title="Syarat dan Ketentuan - Operra" />

    <div class="min-h-screen bg-gray-50 text-gray-900 selection:bg-operra-100 selection:text-operra-900">
        <!-- Minimal Navbar -->
        <nav class="bg-white border-b border-gray-100 py-6">
            <div class="container mx-auto px-6 flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-2">
                    <ApplicationLogo class="h-8 w-auto" />
                    <span class="text-xl font-black tracking-tighter uppercase text-gray-900">OPERRA</span>
                </Link>
                <Link :href="route('welcome')" class="text-sm font-bold text-gray-500 hover:text-operra-600 transition-colors">
                    Kembali ke Beranda
                </Link>
            </div>
        </nav>

        <main class="container mx-auto px-6 py-16 max-w-4xl">
            <h1 class="text-4xl font-black tracking-tight text-gray-950 mb-4 uppercase">Syarat dan Ketentuan</h1>
            <p class="text-gray-500 font-medium mb-12 italic">Terakhir diperbarui: 5 Februari 2026</p>

            <div class="prose prose-operra max-w-none space-y-10 text-gray-700 leading-relaxed font-medium">
                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">1. Penerimaan Layanan</h2>
                    <p>
                        Dengan mendaftar dan menggunakan layanan Operra, Anda setuju untuk terikat oleh syarat dan ketentuan ini. Layanan kami disediakan "sebagaimana adanya" dan kami terus berupaya memperbarui fitur untuk kenyamanan Anda.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">2. Penggunaan yang Diperbolehkan</h2>
                    <p>
                        Anda setuju untuk tidak menggunakan layanan kami (terutama fitur WhatsApp Blast) untuk aktivitas spamming, penipuan, atau tindakan yang melanggar hukum Negara Republik Indonesia serta aturan kebijakan WhatsApp/Meta. Pelanggaran terhadap poin ini dapat menyebabkan suspend akun secara permanen tanpa pengembalian dana.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">3. Biaya dan Langganan</h2>
                    <p>
                        Layanan Operra berbasis langganan bulanan atau tahunan. Pembayaran dilakukan di muka dan tidak dapat dikembalikan setalah layanan diaktifkan, kecuali ditentukan lain dalam kesepakatan tertulis khusus (Custom Plan).
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">4. Batasan Tanggung Jawab</h2>
                    <p>
                        Operra tidak bertanggung jawab atas kerugian bisnis yang disebabkan oleh gangguan teknis dari pihak ketiga (seperti downtime API WhatsApp/Meta atau gangguan layanan internet) atau penyalahgunaan akun oleh pengguna.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">5. Perubahan Ketentuan</h2>
                    <p>
                        Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan diinformasikan melalui dashboard atau email terdaftar. Penggunaan berkelanjutan setelah perubahan menunjukkan persetujuan Anda.
                    </p>
                </section>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-100 py-12 mt-20">
            <div class="container mx-auto px-6 text-center">
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">© 2026 Operra CRM. Hak Cipta Dilindungi.</p>
            </div>
        </footer>
    </div>
</template>
