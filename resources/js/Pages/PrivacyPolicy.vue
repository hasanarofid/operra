<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head title="Kebijakan Privasi - Operra" />

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
            <h1 class="text-4xl font-black tracking-tight text-gray-950 mb-4 uppercase">Kebijakan Privasi</h1>
            <p class="text-gray-500 font-medium mb-12 italic">Terakhir diperbarui: 5 Februari 2026</p>

            <div class="prose prose-operra max-w-none space-y-10 text-gray-700 leading-relaxed font-medium">
                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">1. Informasi yang Kami Kumpulkan</h2>
                    <p>
                        Kami mengumpulkan data yang diperlukan untuk menyediakan layanan CRM terbaik bagi bisnis Anda. Ini termasuk namun tidak terbatas pada:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Informasi akun (nama, email, nomor telepon).</li>
                        <li>Data pelanggan yang Anda masukkan ke dalam sistem.</li>
                        <li>Log interaksi WhatsApp melalui Meta Cloud API.</li>
                        <li>Informasi pembayaran dan langganan.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">2. Penggunaan Data</h2>
                    <p>
                        Data Anda digunakan secara eksklusif untuk:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Mengelola alur kerja CRM (Sales, Marketing, Support).</li>
                        <li>Memproses pengiriman dan penerimaan pesan WhatsApp.</li>
                        <li>Memberikan notifikasi real-time terkait aktivitas pelanggan.</li>
                        <li>Analisis statistik untuk membantu pertumbuhan bisnis Anda.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">3. Keamanan Data</h2>
                    <p>
                        Keamanan adalah prioritas kami. Kami mengimplementasikan enkripsi standar industri dan standar keamanan server terkini untuk melindungi data Anda dari akses yang tidak sah. Untuk data WhatsApp, kami bekerja sesuai dengan protokol keamanan yang ditetapkan oleh Meta Cloud API.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">4. Hak Anda</h2>
                    <p>
                        Anda memiliki hak penuh untuk mengakses, memperbarui, atau meminta penghapusan data Anda dari sistem kami kapan saja melalui pengaturan profil atau dengan menghubungi tim support kami.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-gray-900 mb-4">5. Kontak</h2>
                    <p>
                        Jika Anda memiliki pertanyaan mengenai kebijakan ini, silakan hubungi kami di <span class="text-operra-600 font-bold">hasanarofid@gmail.com</span>.
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
