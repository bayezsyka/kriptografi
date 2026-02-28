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

            <!-- Jenis Input -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Input</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="input_type" value="text" checked class="form-radio text-gray-900 focus:ring-gray-900" onchange="toggleInputType()">
                        <span class="ml-2 text-sm text-gray-700">Teks</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="input_type" value="file" class="form-radio text-gray-900 focus:ring-gray-900" onchange="toggleInputType()">
                        <span class="ml-2 text-sm text-gray-700">File</span>
                    </label>
                </div>
            </div>

            <!-- Input Teks -->
            <div id="input-text-container" class="mb-5">
                <label for="input-text" class="block text-sm font-medium text-gray-700 mb-1.5">Teks</label>
                <textarea id="input-text" name="text" rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-mono text-gray-900 placeholder-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors resize-none"
                    placeholder="Masukkan teks yang akan diproses..."></textarea>
            </div>

            <!-- Input File -->
            <div id="input-file-container" class="mb-5 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">File</label>
                
                <div class="flex items-center justify-center w-full">
                    <label for="input-file" id="file-drop-area" class="flex flex-col items-center justify-center w-full min-h-[140px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                        <!-- State: Belum ada file -->
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="file-upload-prompt">
                            <svg class="w-8 h-8 mb-3 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-1 text-sm text-gray-600"><span class="font-medium text-gray-900">Klik untuk mengunggah</span> file</p>
                            <p class="text-xs text-gray-400 mb-2">Maksimal ukuran file: 100 KB</p>
                        </div>
                        
                        <!-- State: File terpilih -->
                        <div id="file-info" class="hidden flex-col items-center justify-center pt-5 pb-6 w-full px-4 text-center">
                            <svg class="w-8 h-8 mb-2 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <p id="file-name-display" class="text-sm font-medium text-gray-900 truncate w-full max-w-[240px]"></p>
                            <p id="file-size-display" class="text-xs text-gray-500 mt-0.5 mb-3"></p>
                            
                            <button type="button" onclick="clearFileInput(event)" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                Hapus File
                            </button>
                        </div>
                        
                        <input id="input-file" name="file" type="file" class="hidden" onchange="handleFileSelect(this)" />
                    </label>
                </div>
                
                <p class="text-xs text-gray-500 mt-2.5 bg-gray-100 p-2.5 rounded-md border border-gray-200">
                    <span class="font-medium text-gray-700">Enkripsi:</span> Pilih file biner apa saja (contoh: gambar, dokumen, dll).<br>
                    <span class="font-medium text-gray-700">Dekripsi:</span> Pilih file <code class="bg-gray-200 px-1 py-0.5 rounded text-gray-800">.txt</code> hasil enkripsi sebelumnya.
                </p>
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

    <!-- Floating Mini Button (Toast) -->
    <button id="disclaimer-toast" onclick="openDisclaimerModal()" class="hidden fixed bottom-5 right-5 z-40 bg-white border border-yellow-300 shadow-lg hover:shadow-xl rounded-full px-4 py-2.5 text-sm font-medium text-yellow-700 hover:bg-yellow-50 flex items-center gap-2 transition-all">
        <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Info Keamanan
    </button>

    <!-- Disclaimer Modal -->
    <div id="disclaimer-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm px-4 hidden transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border-t-4 border-yellow-400">
            <div class="p-6 sm:p-8">
                <div class="flex items-start justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                        <svg class="w-7 h-7 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Term of Service & Disclaimer Keamanan
                    </h2>
                </div>
                
                <div class="text-sm text-gray-600 space-y-4 leading-relaxed mb-8">
                    <p><strong>Peringatan Penggunaan:</strong> Kalkulator kriptografi klasik ini bertujuan <strong>hanya untuk edukasi dan pembelajaran akademis</strong>. Dilarang keras menggunakan tool ini untuk mengamankan dokumen rahasia, data finansial perusahaan, dokumen administratif, atau Data Pribadi sesungguhnya.</p>
                    <p><strong>Kerentanan Cipher Klasik:</strong> Cipher klasik seperti Vigenere, Hill, Affine, dan Playfair <u>tidak memenuhi standar keamanan modern</u> (seperti halnya AES/RSA). Enkripsi jenis ini sangat rentan dieksploitasi melalui teknik pembobolan <em>digital forensics</em>, terutama menggunakan <strong>analisis frekuensi kemunculan huruf</strong> dan metode eksploitasi pola.</p>
                    <p><strong>Batasan Sistem & Konfigurasi Server:</strong> Untuk menghindari kerentanan beban sistem seperti <em>Maximum Execution Time</em> akibat proses iterasi berlebihan, batas unggahan pada sistem ini ditetapkan maksimal <strong>100 KB</strong>. Sangat disarankan untuk mengaksesnya selalu melalui <strong>HTTPS/SSL</strong> untuk mencegah penyadapan.</p>
                    <div class="bg-yellow-50/50 p-4 rounded-xl border border-yellow-100 mt-6 text-xs text-gray-500">
                        <strong>Pelepasan Tanggung Jawab Hukum (Disclaimer):</strong> Dengan mengakses dan menggunakan layanan ini, Anda memahami risikonya dan secara otomatis melepaskan kami selaku pembuat sistem maupun penyedia layanan dari segala pertanggungjawaban yuridis atau gugatan hukum apabila di kemudian hari terjadi kebocoran data, kerusakan file, atau insiden keamanan lainnya yang diakibatkan oleh penyalahgunaan algoritma lawas ini.
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeDisclaimerModal()" class="bg-gray-900 text-white px-8 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 transition-all w-full sm:w-auto shadow-md hover:shadow-lg">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentCipher = 'vigenere';

        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        function handleFileSelect(input) {
            const promptEl = document.getElementById('file-upload-prompt');
            const infoEl = document.getElementById('file-info');
            const nameEl = document.getElementById('file-name-display');
            const sizeEl = document.getElementById('file-size-display');
            const dropArea = document.getElementById('file-drop-area');

            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                nameEl.textContent = file.name;
                sizeEl.textContent = formatBytes(file.size);
                
                if (file.size > 100 * 1024) {
                    sizeEl.innerHTML = `<span class="text-red-500 font-medium">Ukuran file terlalu besar (${formatBytes(file.size)})</span>`;
                    dropArea.classList.add('border-red-300', 'bg-red-50');
                    dropArea.classList.remove('border-gray-300', 'hover:bg-gray-50');
                } else {
                    dropArea.classList.remove('border-red-300', 'bg-red-50');
                    dropArea.classList.add('border-gray-300', 'hover:bg-gray-50');
                }
                
                promptEl.classList.add('hidden');
                infoEl.classList.remove('hidden');
                infoEl.classList.add('flex');
            } else {
                promptEl.classList.remove('hidden');
                infoEl.classList.add('hidden');
                infoEl.classList.remove('flex');
            }
        }

        function clearFileInput(event) {
            // Stop button click from bubbling up to label which would open file dialog
            if(event) event.preventDefault(); 
            
            const input = document.getElementById('input-file');
            input.value = '';
            
            // Trigger change manual
            handleFileSelect(input);
            
            const dropArea = document.getElementById('file-drop-area');
            dropArea.classList.remove('border-red-300', 'bg-red-50');
            dropArea.classList.add('border-gray-300', 'hover:bg-gray-50');
        }

        // Setup Drag & Drop
        document.addEventListener('DOMContentLoaded', () => {
            const dropArea = document.getElementById('file-drop-area');
            const fileInput = document.getElementById('input-file');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.add('border-gray-900', 'bg-gray-50');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.remove('border-gray-900', 'bg-gray-50');
                }, false);
            });

            dropArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if(files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(fileInput);
                }
            }, false);
        });

        // Modal Disclaimer Logic
        function openDisclaimerModal() {
            document.getElementById('disclaimer-modal').classList.remove('hidden');
            document.getElementById('disclaimer-toast').classList.add('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDisclaimerModal() {
            document.getElementById('disclaimer-modal').classList.add('hidden');
            document.getElementById('disclaimer-toast').classList.remove('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Tampilkan modal pada saat muat awal
        window.addEventListener('DOMContentLoaded', () => {
            openDisclaimerModal();
        });

        function toggleInputType() {
            const isFile = document.querySelector('input[name="input_type"]:checked').value === 'file';
            document.getElementById('input-text-container').classList.toggle('hidden', isFile);
            document.getElementById('input-file-container').classList.toggle('hidden', !isFile);
        }

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
            const isFile = document.querySelector('input[name="input_type"]:checked').value === 'file';
            const formData = new FormData();
            formData.append('cipher', currentCipher);
            formData.append('action', action);
            formData.append('inputType', isFile ? 'file' : 'text');

            if (isFile) {
                const fileInput = document.getElementById('input-file');
                if (!fileInput.files.length) {
                    showError('Silakan pilih file terlebih dahulu.');
                    return;
                }
                
                const file = fileInput.files[0];
                if (file.size > 100 * 1024) {
                    showError('Ukuran file terlalu besar! Batas maksimal adalah 100 KB agar proses tidak memberatkan server.');
                    return;
                }
                
                formData.append('file', file);
            } else {
                const text = document.getElementById('input-text').value.trim();
                if (!text) {
                    showError('Teks tidak boleh kosong.');
                    return;
                }
                formData.append('text', text);
            }

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
            .then(async res => {
                document.getElementById('loading-section').classList.add('hidden');
                document.getElementById('btn-encrypt').disabled = false;
                document.getElementById('btn-decrypt').disabled = false;

                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await res.json();
                    if (data.error) {
                        showError(data.error);
                    } else {
                        showResult(data.result, data.cipher, data.action);
                    }
                } else {
                    if (res.ok) {
                        const blob = await res.blob();
                        const disposition = res.headers.get('Content-Disposition');
                        let filename = 'download';
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            const matches = filenameRegex.exec(disposition);
                            if (matches != null && matches[1]) { 
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                        
                        document.getElementById('result-section').classList.add('hidden');
                        document.getElementById('error-section').classList.add('hidden');
                        
                        const btnId = action === 'encrypt' ? 'btn-encrypt' : 'btn-decrypt';
                        const btn = document.getElementById(btnId);
                        const originalText = action === 'encrypt' ? 'Enkripsi' : 'Dekripsi';
                        btn.textContent = 'Berhasil!';
                        setTimeout(() => { btn.textContent = originalText; }, 2000);
                    } else {
                        showError('Gagal mendownload file.');
                    }
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
