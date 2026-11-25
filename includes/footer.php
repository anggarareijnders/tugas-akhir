<?php
// includes/footer.php
if (!isset($base_path)) {
    $base_path = "./";
}
?>
    <!-- Footer -->
    <footer class="bg-gradient-to-b from-forest-900 to-[#0d2410] text-neutral-300 mt-auto border-t border-forest-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Branding column -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-white text-forest-950 text-lg shadow-lg shadow-white/10 font-extrabold">
                            <i class="fa-solid fa-mountain"></i>
                        </div>
                        <span class="text-xl font-extrabold text-white tracking-tight">MOORA<span class="text-emerald-400">Trip</span></span>
                    </div>
                    <p class="text-sm text-neutral-400 max-w-sm leading-relaxed">
                        Sistem Pendukung Keputusan (SPK) pendakian gunung terbaik. Kami membantu Anda membandingkan dan memilih paket trip gunung dengan metode ilmiah MOORA agar petualangan Anda terencana dengan sempurna.
                    </p>
                </div>

                <!-- Navigation Quick Links -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Navigasi Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="<?php echo $base_path; ?>index.php" class="hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-[10px]"></i> Beranda
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_path; ?>paket.php" class="hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center gap-2">
                                <i class="fa-solid fa-angle-right text-[10px]"></i> Paket Trip
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact & Social Media -->
                <div>
                    <h3 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Hubungi Kami</h3>
                    <p class="text-sm text-neutral-400 leading-relaxed mb-4">
                        Ada pertanyaan atau butuh bantuan dalam meregistrasikan vendor Anda?
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="h-9 w-9 rounded-xl bg-forest-800 hover:bg-forest-700 text-white flex items-center justify-center hover:scale-110 hover:shadow-lg transition-all duration-300" title="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-xl bg-forest-800 hover:bg-forest-700 text-white flex items-center justify-center hover:scale-110 hover:shadow-lg transition-all duration-300" title="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-xl bg-forest-800 hover:bg-forest-700 text-white flex items-center justify-center hover:scale-110 hover:shadow-lg transition-all duration-300" title="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-forest-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-neutral-400 backdrop-blur-sm">
                <p>&copy; <?php echo date("Y"); ?> MOORATrip. Hak Cipta Dilindungi.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                    <span>&bull;</span>
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Modal: Detail Paket Trip -->
    <div id="tripDetailModal" class="fixed inset-0 bg-neutral-900/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-3xl ring-1 ring-white/10 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-[0_25px_60px_rgb(0,0,0,0.3)] border border-neutral-100 p-6 sm:p-8 space-y-6 relative transform transition-all duration-350 scale-95 opacity-0" id="modalCard">
            
            <!-- Close Button -->
            <button onclick="closeTripDetailModal()" class="absolute top-6 right-6 text-neutral-400 hover:text-neutral-700 h-9 w-9 rounded-xl hover:bg-neutral-150 flex items-center justify-center hover:rotate-90 transition-all duration-300 z-30">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Modal Header -->
            <div class="space-y-1.5">
                <span id="modalGunung" class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold bg-forest-50 text-forest-800 border border-forest-150 uppercase tracking-widest">Gunung</span>
                <h2 id="modalNamaPaket" class="text-xl sm:text-2xl font-black text-neutral-800 pr-10 leading-tight">Nama Paket Trip</h2>
                <span id="modalVendor" class="block text-xs font-bold text-soil-700 uppercase tracking-wider">Lestari Adventure</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                <!-- Left: Description and Image (md:col-span-5) -->
                <div class="md:col-span-5 space-y-4">
                    <div class="rounded-2xl overflow-hidden aspect-[16/10] bg-neutral-100 border border-neutral-200">
                        <img id="modalGambar" src="" alt="Gunung" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-neutral-450 uppercase tracking-wider mb-1">Deskripsi Paket Trip:</h4>
                        <p id="modalDeskripsi" class="text-xs text-neutral-500 leading-relaxed max-h-36 overflow-y-auto pr-1">
                            Deskripsi lengkap perjalanan...
                        </p>
                    </div>
                </div>

                <!-- Right: Specs and Facilities checklist (md:col-span-7) -->
                <div class="md:col-span-7 space-y-5">
                    <!-- Specifications summary -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-neutral-50 p-4 rounded-xl border border-neutral-100">
                        <div>
                            <span class="block text-[8px] text-neutral-400 font-bold uppercase tracking-wider">Harga Paket (C1)</span>
                            <span id="modalHarga" class="font-extrabold text-forest-800 text-sm">Rp -</span>
                        </div>
                        <div>
                            <span class="block text-[8px] text-neutral-400 font-bold uppercase tracking-wider">Ketinggian (C2)</span>
                            <span id="modalKetinggian" class="font-bold text-neutral-800 text-sm">- mdpl</span>
                        </div>
                        <div>
                            <span class="block text-[8px] text-neutral-400 font-bold uppercase tracking-wider">Kesulitan Medan (C3)</span>
                            <span id="modalMedan" class="font-bold text-neutral-800 text-sm">Sedang</span>
                        </div>
                        <div>
                            <span class="block text-[8px] text-neutral-400 font-bold uppercase tracking-wider">Waktu ke Camp (C5)</span>
                            <span id="modalWaktu" class="font-bold text-neutral-800 text-sm">- Jam</span>
                        </div>
                    </div>

                    <!-- Facilities checklist F1-F12 -->
                    <div class="space-y-3">
                        <h4 class="text-[10px] font-bold text-neutral-450 uppercase tracking-wider"><i class="fa-solid fa-list-check text-forest-750 mr-1"></i> Rincian Fasilitas Paket (C4):</h4>
                        <div id="modalFacilitiesList" class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[10px]">
                            <!-- Dynamically loaded -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal script controller -->
    <script>
        const globalFacilitiesList = {
            'F1': 'Tenda Dome',
            'F2': 'Tas Gunung (Carrier)',
            'F3': 'Sleeping Bag',
            'F4': 'Cemilan Tambahan',
            'F5': 'Transportasi Ojek',
            'F6': 'Homestay / Basecamp',
            'F7': 'Buah-buahan Segar',
            'F8': 'Jasa Porter Tim',
            'F9': 'Atribut / Stiker',
            'F10': 'Fasilitas Surat Sehat',
            'F11': 'Makan Selama Pendakian',
            'F12': 'P3K Standar'
        };

        const terrainMapping = {
            1: 'Mudah (Skala 1)',
            2: 'Sedang (Skala 2)',
            3: 'Sulit (Skala 3)'
        };

        function showTripDetail(trip) {
            const modal = document.getElementById('tripDetailModal');
            const card = document.getElementById('modalCard');

            // Fill values
            document.getElementById('modalGunung').innerText = 'Gunung ' + trip.gunung;
            document.getElementById('modalNamaPaket').innerText = trip.nama_paket;
            document.getElementById('modalVendor').innerText = trip.vendor_name || 'Mitra Vendor';
            document.getElementById('modalGambar').src = trip.gambar || 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=600&q=80';
            document.getElementById('modalDeskripsi').innerText = trip.deskripsi || 'Tidak ada deskripsi.';
            
            // Format Harga
            const price = parseFloat(trip.c1_harga);
            document.getElementById('modalHarga').innerText = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('modalKetinggian').innerText = parseInt(trip.c2_ketinggian).toLocaleString('id-ID') + ' mdpl';
            document.getElementById('modalMedan').innerText = terrainMapping[trip.c3_medan] || 'Sedang';
            document.getElementById('modalWaktu').innerText = trip.c5_waktu_tempuh + ' Jam';

            // Generate facilities checkboxes visual list
            const activeFacilities = trip.c4_fasilitas ? trip.c4_fasilitas.split(',') : [];
            const listContainer = document.getElementById('modalFacilitiesList');
            listContainer.innerHTML = '';

            for (const [code, name] of Object.entries(globalFacilitiesList)) {
                const isChecked = activeFacilities.includes(code);
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-center gap-2 p-1.5 border border-neutral-100 rounded-lg transition-colors';
                
                if (isChecked) {
                    itemDiv.className += ' bg-emerald-50/40 text-neutral-800 font-bold border-emerald-100';
                    itemDiv.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i> <span class="truncate">${code} &bull; ${name}</span>`;
                } else {
                    itemDiv.className += ' bg-neutral-50 text-neutral-400 font-medium opacity-65';
                    itemDiv.innerHTML = `<i class="fa-solid fa-circle-xmark text-neutral-300 text-xs"></i> <span class="truncate">${code} &bull; ${name}</span>`;
                }
                listContainer.appendChild(itemDiv);
            }

            // Show animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeTripDetailModal() {
            const modal = document.getElementById('tripDetailModal');
            const card = document.getElementById('modalCard');

            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close on overlay click
        document.getElementById('tripDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTripDetailModal();
            }
        });
    </script>
</body>
</html>
