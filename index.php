<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Kriptografi</title>
    <meta name="description" content="Kalkulator enkripsi dan dekripsi untuk Vigenere, Affine, Playfair, Hill, dan Enigma Cipher.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-active {
            background-color: #111827;
            color: #ffffff;
        }
        .result-box {
            font-family: 'JetBrains Mono', monospace;
            word-break: break-all;
        }
        .fade-in {
            animation: fadeIn 0.2s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen text-gray-900 antialiased">

    <div class="max-w-2xl mx-auto px-4 py-12">

        <!-- Header -->
        <header class="mb-10">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Kalkulator Kriptografi</h1>
            <p class="text-sm text-gray-500 mt-1">Enkripsi & dekripsi teks menggunakan berbagai algoritma cipher klasik.</p>
        </header>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-1.5 mb-8" id="cipher-tabs">
            <button onclick="switchCipher('vigenere')" id="tab-vigenere" class="tab-active px-3.5 py-1.5 text-sm font-medium rounded-md transition-colors">Vigenere</button>
            <button onclick="switchCipher('affine')" id="tab-affine" class="px-3.5 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-200 transition-colors">Affine</button>
            <button onclick="switchCipher('playfair')" id="tab-playfair" class="px-3.5 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-200 transition-colors">Playfair</button>
            <button onclick="switchCipher('hill')" id="tab-hill" class="px-3.5 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-200 transition-colors">Hill</button>
            <button onclick="switchCipher('enigma')" id="tab-enigma" class="px-3.5 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-200 transition-colors">Enigma</button>
        </div>

        <!-- Form -->
        <form id="cipher-form" onsubmit="return false;">

            <!-- Input Teks -->
            <div class="mb-5">
                <label for="input-text" class="block text-sm font-medium text-gray-700 mb-1.5">Teks</label>
                <textarea id="input-text" name="text" rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors resize-none"
                    placeholder="Masukkan teks yang akan diproses..."></textarea>
            </div>

            <!-- Key Fields: Vigenere -->
            <div id="key-vigenere" class="key-section mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Key</label>
                <input type="text" name="key_vigenere" placeholder="Contoh: KUNCI"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
            </div>

            <!-- Key Fields: Affine -->
            <div id="key-affine" class="key-section mb-5 hidden">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai a <span class="text-gray-400 font-normal">(coprime 26)</span></label>
                        <input type="number" name="affine_a" placeholder="Contoh: 5" value="5"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai b</label>
                        <input type="number" name="affine_b" placeholder="Contoh: 8" value="8"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Nilai a yang valid: 1, 3, 5, 7, 9, 11, 15, 17, 19, 21, 23, 25</p>
            </div>

            <!-- Key Fields: Playfair -->
            <div id="key-playfair" class="key-section mb-5 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Key</label>
                <input type="text" name="key_playfair" placeholder="Contoh: MONARCHY"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                <p class="text-xs text-gray-400 mt-1.5">Huruf J akan digabung menjadi I</p>
            </div>

            <!-- Key Fields: Hill -->
            <div id="key-hill" class="key-section mb-5 hidden">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Key Matrix</label>
                    <div class="flex gap-1 bg-gray-100 rounded-md p-0.5">
                        <button type="button" onclick="setHillSize(2)" id="hill-size-2"
                            class="hill-size-btn px-2.5 py-1 text-xs font-medium rounded bg-gray-900 text-white transition-colors">2x2</button>
                        <button type="button" onclick="setHillSize(3)" id="hill-size-3"
                            class="hill-size-btn px-2.5 py-1 text-xs font-medium rounded text-gray-600 hover:text-gray-900 transition-colors">3x3</button>
                    </div>
                </div>

                <!-- Matrix 2x2 -->
                <div id="hill-matrix-2" class="inline-flex items-center gap-2">
                    <div class="text-2xl text-gray-300 font-light select-none leading-none" style="font-size: 60px;">&#91;</div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="number" name="hill_2_0" value="3" class="w-16 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_2_1" value="3" class="w-16 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_2_2" value="2" class="w-16 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_2_3" value="5" class="w-16 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    </div>
                    <div class="text-2xl text-gray-300 font-light select-none leading-none" style="font-size: 60px;">&#93;</div>
                </div>

                <!-- Matrix 3x3 -->
                <div id="hill-matrix-3" class="hidden inline-flex items-center gap-2">
                    <div class="text-2xl text-gray-300 font-light select-none leading-none" style="font-size: 80px;">&#91;</div>
                    <div class="grid grid-cols-3 gap-1.5">
                        <input type="number" name="hill_3_0" value="6" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_1" value="24" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_2" value="1" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_3" value="13" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_4" value="16" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_5" value="10" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_6" value="20" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_7" value="17" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <input type="number" name="hill_3_8" value="15" class="w-14 h-10 rounded-md border border-gray-300 bg-white text-center text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    </div>
                    <div class="text-2xl text-gray-300 font-light select-none leading-none" style="font-size: 80px;">&#93;</div>
                </div>

                <p class="text-xs text-gray-400 mt-2">Masukkan angka 0-25 (A=0, B=1, ..., Z=25). Determinan harus coprime dengan 26.</p>
            </div>

            <!-- Key Fields: Enigma -->
            <div id="key-enigma" class="key-section mb-5 hidden">
                <div class="space-y-3">
                    <!-- Rotors + Posisi (grouped per column) -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rotor Kiri</label>
                                <select name="enigma_rotor_left"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                    <option value="III" selected>III</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Posisi <span class="text-gray-400">(0-25)</span></label>
                                <input type="number" name="enigma_pos_left" value="0" min="0" max="25"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rotor Tengah</label>
                                <select name="enigma_rotor_middle"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                    <option value="II" selected>II</option>
                                    <option value="I">I</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Posisi <span class="text-gray-400">(0-25)</span></label>
                                <input type="number" name="enigma_pos_middle" value="0" min="0" max="25"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rotor Kanan</label>
                                <select name="enigma_rotor_right"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                    <option value="I" selected>I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Posisi <span class="text-gray-400">(0-25)</span></label>
                                <input type="number" name="enigma_pos_right" value="0" min="0" max="25"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-mono focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Reflektor + Plugboard -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Reflektor</label>
                            <select name="enigma_reflector"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                <option value="B" selected>UKW-B</option>
                                <option value="C">UKW-C</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-2">Plugboard (Pasangan Huruf)</label>
                            <div class="grid grid-cols-5 gap-1.5">
                                <input type="text" name="enigma_pb_1" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_2" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_3" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_4" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_5" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_6" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_7" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_8" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_9" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                                <input type="text" name="enigma_pb_10" maxlength="2" placeholder="--" class="pb-input w-full text-center rounded-md border border-gray-300 bg-white px-1 py-1.5 text-xs font-mono focus:border-gray-900 outline-none uppercase transition-colors">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Masukkan maksimal 10 pasangan huruf (contoh: AB). Kosongkan yang tidak digunakan.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2.5 mb-8">
                <button type="button" onclick="processAction('encrypt')" id="btn-encrypt"
                    class="flex-1 bg-gray-900 text-white text-sm font-medium py-2.5 px-4 rounded-lg hover:bg-gray-800 active:bg-gray-950 transition-colors">
                    Enkripsi
                </button>
                <button type="button" onclick="processAction('decrypt')" id="btn-decrypt"
                    class="flex-1 bg-white text-gray-900 text-sm font-medium py-2.5 px-4 rounded-lg border border-gray-300 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    Dekripsi
                </button>
            </div>
        </form>

        <!-- Result -->
        <div id="result-section" class="hidden fade-in">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Hasil</label>
                <button onclick="copyResult()" id="btn-copy"
                    class="text-xs text-gray-500 hover:text-gray-900 font-medium transition-colors px-2 py-1 rounded hover:bg-gray-100">
                    Salin
                </button>
            </div>
            <div id="result-output"
                class="result-box w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-3 text-sm text-gray-900 min-h-[60px] select-all">
            </div>
            <p id="result-meta" class="text-xs text-gray-400 mt-2"></p>
        </div>

        <!-- Error -->
        <div id="error-section" class="hidden fade-in">
            <div class="rounded-lg border border-red-200 bg-red-50 px-3.5 py-3">
                <p id="error-message" class="text-sm text-red-700"></p>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading-section" class="hidden">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Memproses...
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-16 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-400">Kalkulator Kriptografi &mdash; Vigenere, Affine, Playfair, Hill, Enigma Cipher</p>
        </footer>
    </div>

    <script>
        let currentCipher = 'vigenere';

        function switchCipher(cipher) {
            currentCipher = cipher;

            // Update tabs
            document.querySelectorAll('#cipher-tabs button').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('text-gray-600', 'hover:bg-gray-200');
            });
            const activeTab = document.getElementById('tab-' + cipher);
            activeTab.classList.add('tab-active');
            activeTab.classList.remove('text-gray-600', 'hover:bg-gray-200');

            // Show/hide key sections
            document.querySelectorAll('.key-section').forEach(el => el.classList.add('hidden'));
            document.getElementById('key-' + cipher).classList.remove('hidden');

            // Hide results
            document.getElementById('result-section').classList.add('hidden');
            document.getElementById('error-section').classList.add('hidden');
        }

        let hillSize = 2;
        function setHillSize(size) {
            hillSize = size;
            document.getElementById('hill-matrix-2').classList.toggle('hidden', size !== 2);
            document.getElementById('hill-matrix-3').classList.toggle('hidden', size !== 3);

            document.querySelectorAll('.hill-size-btn').forEach(btn => {
                btn.classList.remove('bg-gray-900', 'text-white');
                btn.classList.add('text-gray-600');
            });
            const activeBtn = document.getElementById('hill-size-' + size);
            activeBtn.classList.add('bg-gray-900', 'text-white');
            activeBtn.classList.remove('text-gray-600');
        }

        function processAction(action) {
            const text = document.getElementById('input-text').value.trim();
            if (!text) {
                showError('Teks tidak boleh kosong.');
                return;
            }

            const formData = new FormData();
            formData.append('cipher', currentCipher);
            formData.append('action', action);
            formData.append('text', text);

            // Tambahkan key berdasarkan cipher
            switch (currentCipher) {
                case 'vigenere':
                    formData.append('key', document.querySelector('input[name="key_vigenere"]').value);
                    break;
                case 'affine':
                    formData.append('a', document.querySelector('input[name="affine_a"]').value);
                    formData.append('b', document.querySelector('input[name="affine_b"]').value);
                    break;
                case 'playfair':
                    formData.append('key', document.querySelector('input[name="key_playfair"]').value);
                    break;
                case 'hill':
                    const hillSize = document.getElementById('hill-matrix-2').classList.contains('hidden') ? 3 : 2;
                    const count = hillSize * hillSize;
                    const vals = [];
                    for (let k = 0; k < count; k++) {
                        vals.push(document.querySelector(`input[name="hill_${hillSize}_${k}"]`).value || '0');
                    }
                    formData.append('key', vals.join(','));
                    break;
                case 'enigma':
                    formData.append('rotor_right', document.querySelector('select[name="enigma_rotor_right"]').value);
                    formData.append('rotor_middle', document.querySelector('select[name="enigma_rotor_middle"]').value);
                    formData.append('rotor_left', document.querySelector('select[name="enigma_rotor_left"]').value);
                    formData.append('pos_right', document.querySelector('input[name="enigma_pos_right"]').value);
                    formData.append('pos_middle', document.querySelector('input[name="enigma_pos_middle"]').value);
                    formData.append('pos_left', document.querySelector('input[name="enigma_pos_left"]').value);
                    formData.append('reflector', document.querySelector('select[name="enigma_reflector"]').value);
                    
                    const pbPairs = [];
                    for (let i = 1; i <= 10; i++) {
                        const val = document.querySelector(`input[name="enigma_pb_${i}"]`).value.trim();
                        if (val) pbPairs.push(val);
                    }
                    formData.append('plugboard', pbPairs.join(' '));
                    break;
            }

            // Show loading
            document.getElementById('loading-section').classList.remove('hidden');
            document.getElementById('result-section').classList.add('hidden');
            document.getElementById('error-section').classList.add('hidden');

            // Disable buttons
            document.getElementById('btn-encrypt').disabled = true;
            document.getElementById('btn-decrypt').disabled = true;

            fetch('process.php', {
                method: 'POST',
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('btn-encrypt').disabled = false;
                document.getElementById('btn-decrypt').disabled = false;

                if (data.error) {
                    showError(data.error);
                } else {
                    showResult(data.result, data.cipher, data.action);
                }
            })
            .catch(err => {
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('btn-encrypt').disabled = false;
                document.getElementById('btn-decrypt').disabled = false;
                showError('Terjadi kesalahan saat memproses permintaan.');
            });
        }

        function showResult(result, cipher, action) {
            const section = document.getElementById('result-section');
            const output = document.getElementById('result-output');
            const meta = document.getElementById('result-meta');

            output.textContent = result;

            const cipherNames = {
                vigenere: 'Vigenere',
                affine: 'Affine',
                playfair: 'Playfair',
                hill: 'Hill',
                enigma: 'Enigma'
            };
            const actionName = action === 'encrypt' ? 'Enkripsi' : 'Dekripsi';
            meta.textContent = `${cipherNames[cipher]} Cipher — ${actionName} — ${result.length} karakter`;

            section.classList.remove('hidden');
            document.getElementById('error-section').classList.add('hidden');

            // Re-trigger animation
            section.classList.remove('fade-in');
            void section.offsetWidth;
            section.classList.add('fade-in');
        }

        function showError(message) {
            const section = document.getElementById('error-section');
            document.getElementById('error-message').textContent = message;
            section.classList.remove('hidden');
            document.getElementById('result-section').classList.add('hidden');

            section.classList.remove('fade-in');
            void section.offsetWidth;
            section.classList.add('fade-in');
        }

        function copyResult() {
            const text = document.getElementById('result-output').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btn-copy');
                btn.textContent = 'Tersalin!';
                setTimeout(() => { btn.textContent = 'Salin'; }, 1500);
            });
        }
    </script>

</body>
</html>
