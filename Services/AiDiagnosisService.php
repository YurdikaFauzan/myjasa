<?php

namespace Services;

use Config\Env;

class AiDiagnosisService
{
    /**
     * Run AI Diagnosis Engine
     *
     * @param string $categoryName
     * @param string $complaint
     * @param array  $chatHistory (optional previous Q&A for interactive consultation)
     * @return array
     */
    public function diagnose(string $categoryName, string $complaint, array $chatHistory = []): array
    {
        $activeConfig = \Services\Ai\AiManager::getActiveConfig();

        if ($activeConfig['has_api_key']) {
            $externalResult = $this->callExternalAiApi($activeConfig, $categoryName, $complaint, $chatHistory);
            if ($externalResult) {
                return $this->applySafetyFilters($externalResult);
            }
        }

        // Fallback to Intelligent Safety-Compliant Local AI Service Engine
        return $this->runLocalDiagnosisEngine($categoryName, $complaint, $chatHistory);
    }

    /**
     * Call External AI API via AiManager
     */
    private function callExternalAiApi(array $activeConfig, string $categoryName, string $complaint, array $chatHistory): ?array
    {
        $prompt = "Anda adalah AI Assistant Teknisi Perbaikan Peralatan Rumah Tangga di SmartService AI.\n"
            . "Kategori Peralatan: {$categoryName}\n"
            . "Gejala Kerusakan: {$complaint}\n\n"
            . "Tugas Anda: Berikan diagnosis awal peralatan rumah tangga yang AMAN.\n"
            . "ATURAN KESELAMATAN WAJIB (SAFETY RULES):\n"
            . "- DILARANG menyarankan customer membongkar komponen kelistrikan/bertegangan tinggi (misal kapasitor TV/AC).\n"
            . "- DILARANG menyarankan customer membongkar pipa gas, saluran freon, atau kompresor.\n"
            . "- Jika bahaya (korsleting, bau gas, bocor air mengenai listrik), sarankan hentikan penggunaan & panggil teknisi.\n\n"
            . "Kembalikan output murni dalam format JSON. Format Output JSON wajib mengandung key:\n"
            . "{\n"
            . '  "possible_issues": ["string", "string"],'."\n"
            . '  "possible_causes": ["string", "string"],'."\n"
            . '  "simple_checks": ["string", "string"],'."\n"
            . '  "service_recommendation": ["string", "string"],'."\n"
            . '  "urgency_level": "Rendah / Sedang / Tinggi",'."\n"
            . '  "needs_technician": "Ya / Tidak",'."\n"
            . '  "safety_warning": "string atau null",'."\n"
            . '  "consultation_questions": ["string", "string"],'."\n"
            . '  "summary": "string"'."\n"
            . "}";

        $result = \Services\Ai\AiManager::chat([
            ['role' => 'system', 'content' => 'You are a helpful AI assistant that outputs only valid JSON. No markdown, no explanation, just pure JSON.'],
            ['role' => 'user', 'content' => $prompt]
        ], [
            'temperature'     => 0.2,
            'max_tokens'      => 1024,
            'response_format' => ['type' => 'json_object']
        ]);

        if ($result['success'] && !empty($result['output'])) {
            $text = preg_replace('/```json|```/', '', $result['output']);
            $parsed = json_decode(trim($text), true);
            if (is_array($parsed) && isset($parsed['possible_issues'])) {
                return $parsed;
            }
        }

        return null; // Fallback to local engine
    }

    /**
     * Local Safety-Compliant Diagnosis Engine
     */
    private function runLocalDiagnosisEngine(string $categoryName, string $complaint, array $chatHistory): array
    {
        $cat = mb_strtolower($categoryName);
        $q   = mb_strtolower($complaint);

        // Detect risk factors for safety rules
        $isDangerous = $this->detectHighRiskCondition($q);

        $issues      = [];
        $causes      = [];
        $checks      = [];
        $recs        = [];
        $questions   = [];
        $urgency     = 'Sedang';
        $needsTech   = 'Ya';
        $safetyAlert = null;

        if ($isDangerous['flag']) {
            $urgency     = 'Tinggi';
            $needsTech   = 'Ya';
            $safetyAlert = "⚠️ PERINGATAN KESELAMATAN DARURAT: " . $isDangerous['reason']
                . " Demi keselamatan Anda, segera hentikan penggunaan alat, cabut stop kontak / matikan arus utama, dan hubungi teknisi profesional. Dilarang membongkar komponen listrik atau jalur gas sendiri!";
        }

        // Category-specific logic
        if (str_contains($cat, 'ac') || str_contains($q, 'ac') || str_contains($q, 'pendingin')) {
            if (str_contains($q, 'tidak dingin') || str_contains($q, 'kurang dingin') || str_contains($q, 'panas')) {
                $issues   = ['AC Tidak Dingin / Udara Hanya Angin Biasa', 'Performa Pendinginan Menurun'];
                $causes   = ['Filter udara AC kotor atau tersumbat debu tebal', 'Kekurangan atau kebocoran gas Freon', 'Evaporator / Kondenser kotor', 'Pengaturan mode remote belum di set ke Mode COOL (20-24°C)'];
                $checks   = [
                    'Periksa remote AC: Pastikan mode terpasang pada indikator COOL (lambang salju/bintang), bukan FAN atau DRY.',
                    'Periksa apakah lampu indikator unit indoor menyala normal tanpa berkedip error.',
                    'Periksa kebersihan kisi-kisi filter plastik indoor (dapat dicuci dengan air mengalir).'
                ];
                $recs     = ['Jasa Cuci AC Standard / Inverter', 'Pengecekan Kebocoran & Isi Ulang Freon'];
                $questions = ['Apakah kipas unit indoor masih bertiup kencang?', 'Apakah unit outdoor di luar rumah terdengar menyala/berbunyi?'];
                $urgency  = 'Sedang';
            } elseif (str_contains($q, 'bocor') || str_contains($q, 'menetes')) {
                $issues   = ['Kebocoran Air pada Unit Indoor AC'];
                $causes   = ['Selang buangan air (drainage) tersumbat lumut/kotoran', 'Evaporator beku lalu mencair berlebihan', 'Kemiringan posisi indoor AC kurang pas'];
                $checks   = [
                    'Periksa apakah air menetes dari talang indoor ke lantai.',
                    'Pastikan ujung selang pembuangan di luar rumah tidak tertindih atau tertekuk.'
                ];
                $recs     = ['Jasa Cuci AC & Pembersihan Saluran Drainase', 'Reposisi Unit Indoor AC'];
                $questions = ['Apakah tetesan air terjadi terus menerus saat AC menyala?', 'Kapan terakhir kali AC Anda dicuci?'];
                $urgency  = 'Sedang';
            } else {
                $issues   = ['Gangguan Operasional Unit AC'];
                $causes   = ['Filter debu kotor', 'Remote kontrol baterai lemah', 'Kelistrikan indoor terganggu'];
                $checks   = ['Periksa baterai remote AC.', 'Periksa steker / MCB AC di dinding.'];
                $recs     = ['Cuci & Servis Rutin AC'];
                $questions = ['Apakah AC merespons tombol remote?'];
            }
        } elseif (str_contains($cat, 'kulkas') || str_contains($q, 'kulkas') || str_contains($q, 'freezer')) {
            if (str_contains($q, 'tidak dingin') || str_contains($q, 'tidak beku')) {
                $issues   = ['Kulkas / Freezer Tidak Cold / Pembekuan Gagal'];
                $causes   = ['Karet seal pintu kulkas longgar atau robek', 'Penumpukan bunga es menutup saluran udara (defrost sistem bermasalah)', 'Freon kulkas habis atau bocor', 'Relay starter / Overload kompresor lemah'];
                $checks   = [
                    'Periksa apakah pintu kulkas dapat menutup rapat tanpa ada celah.',
                    'Cek apakah lampu dalam kulkas menyala saat pintu dibuka.',
                    'Dengarkan apakah ada bunyi dengung lembut dari bagian belakang kulkas.'
                ];
                $recs     = ['Servis Kulkas Tidak Dingin', 'Pengecekan Kompresor & Relay Defrost', 'Penggantian Karet Pintu Kulkas'];
                $questions = ['Apakah bagian pembeku (freezer) atas masih dingin?', 'Apakah di belakang kulkas terdengar bunyi klik berkala?'];
                $urgency  = 'Tinggi';
            } elseif (str_contains($q, 'bocor') || str_contains($q, 'genangan')) {
                $issues   = ['Genangan Air di Bawah Kulkas'];
                $causes   = ['Penampung air defrost di belakang kulkas penuh / miring', 'Saluran pembuangan air es tersumbat kotoran'];
                $checks   = ['Periksa baki penampung air di bagian bawah belakang kulkas.', 'Lap genangan air di sekitar luar kulkas agar lantai tidak licin.'];
                $recs     = ['Servis Perbaikan Pembuangan Air Kulkas'];
                $questions = ['Apakah genangan air berasal dari dalam atau belakang bawah kulkas?'];
                $urgency  = 'Sedang';
            } else {
                $issues   = ['Masalah Suhu & Performa Kulkas'];
                $causes   = ['Settingan thermostat terlalu rendah', 'Ventilasi kulkas tertutup makanan'];
                $checks   = ['Cek tombol pengatur suhu thermostat di dalam kulkas.', 'Beri jarak antara makanan dengan lubang angin.'];
                $recs     = ['Pengecekan & Servis Kulkas'];
                $questions = ['Apakah kipas kulkas terdengar berputar?'];
            }
        } elseif (str_contains($cat, 'mesin cuci') || str_contains($q, 'mesin cuci') || str_contains($q, 'cuci')) {
            if (str_contains($q, 'tidak berputar') || str_contains($q, 'macet') || str_contains($q, 'spin')) {
                $issues   = ['Tabung Mesin Cuci Tidak Berputar / Pemutar Macet'];
                $causes   = ['Tali kipas (V-Belt) kendur atau putus', 'Beban pakaian terlalu berat / tidak seimbang', 'Pintu / penutup mesin cuci tidak tertutup rapat (Lid Switch aktif)', 'Kapasitor pemutar atau modul PCB lemah'];
                $checks   = [
                    'Pastikan penutup / pintu mesin cuci terpasang rapat.',
                    'Kurangi jumlah pakaian di dalam tabung jika terlalu penuh.',
                    'Cek apakah mesin berbunyi dengung tetapi tabung tidak mau berputar.'
                ];
                $recs     = ['Servis Mesin Cuci Tidak Berputar', 'Penggantian V-Belt / Kapasitor Motor', 'Perbaikan Modul PCB Control'];
                $questions = ['Apakah tombol kontrol masih berfungsi normal?', 'Apakah terdengar bunyi motor berputar saat proses spin?'];
                $urgency  = 'Sedang';
            } elseif (str_contains($q, 'air tidak keluar') || str_contains($q, 'tidak mengalir') || str_contains($q, 'drain')) {
                $issues   = ['Pemasokan Air atau Pembuangan Air Tersumbat'];
                $causes   = ['Filter katup masuk air (inlet valve) kotor tersumbat pasir/lumut', 'Katup pembuangan (drain valve) tersumbat koin atau kotoran'];
                $checks   = [
                    'Periksa keran air masuk: pastikan aliran air bersih lancar.',
                    'Bersihkan saringan filter kecil pada selang masukan air.'
                ];
                $recs     = ['Servis Katup Water Inlet / Pembuangan Drain Valve'];
                $questions = ['Apakah ada kode error (misal: E1/E2/4E) pada layar?'];
                $urgency  = 'Sedang';
            } else {
                $issues   = ['Gangguan Fungsi Mesin Cuci'];
                $causes   = ['Sensor air bermasalah', 'Kabel kontrol longgar'];
                $checks   = ['Matikan dan cabut steker mesin cuci selama 5 menit untuk mentertibkan modul.'];
                $recs     = ['Servis Perbaikan Mesin Cuci'];
                $questions = ['Apakah mesin cuci Anda tipe 1 tabung atau 2 tabung?'];
            }
        } elseif (str_contains($cat, 'tv') || str_contains($q, 'tv') || str_contains($q, 'televisi') || str_contains($q, 'layar')) {
            if (str_contains($q, 'tidak menyala') || str_contains($q, 'mati total') || str_contains($q, 'gelap')) {
                $issues   = ['TV LED / Smart TV Mati Total atau Tidak Ada Gambar'];
                $causes   = ['Mainboard / Modul Power Supply mati', 'Lampu Backlight LED TV padam (suara ada tapi gambar gelap)', 'Kabel daya lepas atau sakelar stop kontak bermasalah'];
                $checks   = [
                    'Periksa lampu indikator standby (biasanya berwarna merah di sudut TV).',
                    'Coba sorot layar TV dengan senter smartphone saat TV ON: jika terlihat bayangan gambar tipis, maka lampu Backlight mati.',
                    'Coba colokkan perangkat lain ke stop kontak untuk memastikan daya listrik ada.'
                ];
                $recs     = ['Servis TV Mati Total / Power Supply', 'Penggantian Lampu Backlight LED TV'];
                $questions = ['Apakah lampu indikator merah di TV menyala?', 'Apakah ada suara dari siaran TV meskipun gambar tidak tampil?'];
                $urgency  = 'Tinggi';
            } elseif (str_contains($q, 'bergaris') || str_contains($q, 'gambar')) {
                $issues   = ['Layar TV Bergaris / Gambar Cacat'];
                $causes   = ['Modul T-Con atau kabel fleksibel COF longgar', 'Panel layar LCD retak atau aus'];
                $checks   = ['Coba ganti kabel HDMI / port input lain.'];
                $recs     = ['Servis Modul T-Con & Panel TV'];
                $questions = ['Apakah garis di layar berbentuk vertikal atau horizontal?'];
                $urgency  = 'Sedang';
            } else {
                $issues   = ['Gangguan Layar / Suara TV'];
                $causes   = ['Setting input (HDMI/AV) salah', 'Pengaturan audio bermasalah'];
                $checks   = ['Cek tombol Source / Input pada remote TV.'];
                $recs     = ['Servis Perbaikan TV'];
                $questions = ['Apakah remote TV masih dapat merespons?'];
            }
        } elseif (str_contains($cat, 'dispenser') || str_contains($q, 'dispenser') || str_contains($q, 'panas')) {
            if (str_contains($q, 'tidak panas') || str_contains($q, 'dingin')) {
                $issues   = ['Dispenser Air Tidak Panas / Pemanas Mati'];
                $causes   = ['Elemen pemanas (heating element) putus', 'Thermostat suhu panas rusak', 'Sakelar pemanas di bagian belakang dispenser dalam posisi OFF', 'Sekring termo (thermal fuse) terputus'];
                $checks   = [
                    'Periksa sakelar pemanas (tombol merah di belakang/samping dispenser): Pastikan dalam posisi ON (I).',
                    'Periksa apakah lampu indikator HEATING / PANAS di depan dispenser menyala.'
                ];
                $recs     = ['Servis Dispenser Air Tidak Panas', 'Penggantian Element Heater / Thermostat'];
                $questions = ['Apakah sakelar merah pemanas di belakang dispenser sudah di-ON-kan?', 'Apakah lampu indikator merah depan menyala?'];
                $urgency  = 'Sedang';
            } elseif (str_contains($q, 'bocor') || str_contains($q, 'menetes')) {
                $issues   = ['Kebocoran Air Dispenser'];
                $causes   = ['Seal karet kran air aus/robek', 'Galon air retak tidak kasat mata', 'Selang silikon internal terlepas'];
                $checks   = [
                    'Periksa kran air panas/dingin: pastikan tidak ada tetesan terus menerus.',
                    'Coba ganti galon air dengan galon baru untuk memastikan galon tidak retak.'
                ];
                $recs     = ['Servis Ganti Kran Dispenser / Selang Internal'];
                $questions = ['Apakah air keluar dari kran atau dari celah bawah tangki?'];
                $urgency  = 'Tinggi';
            } else {
                $issues   = ['Masalah Suhu / Kebocoran Dispenser'];
                $causes   = ['Thermostat rusak', 'Kabel internal longgar'];
                $checks   = ['Periksa sakelar daya listrik dispenser.'];
                $recs     = ['Servis Perbaikan Dispenser'];
                $questions = ['Apakah fungsi air dingin juga bermasalah?'];
            }
        } else {
            // General Appliances
            $issues   = ["Pemeriksaan Gejala Kerusakan {$categoryName}"];
            $causes   = ['Komponen operasional aus atau kotor', 'Kabel konektor daya terganggu', 'Pengaturan modul belum sesuai'];
            $checks   = [
                'Periksa steker dan stop kontak listrik.',
                'Pastikan tombol sakelar utama terpasang pada posisi ON.',
                'Bersihkan kotoran/debu pada bagian luar peralatan.'
            ];
            $recs     = ["Servis Perbaikan {$categoryName}"];
            $questions = ['Apakah lampu indikator peralatan menyala normal?', 'Sejak kapan gejala ini muncul?'];
        }

        // Apply Safety Guidelines strictly
        $checks = array_values(array_filter($checks, fn($chk) => $this->isCheckSafe($chk)));

        // Default safe checks fallback if all checks filtered out
        if (empty($checks)) {
            $checks = [
                'Periksa apakah kabel steker terhubung dengan baik ke stop kontak wall socket.',
                'Periksa apakah sakelar ON/OFF pada alat sudah dinyalakan.',
                'Pastikan pasokan listrik/air di rumah mengalir dengan stabil.'
            ];
        }

        $summary = sprintf(
            'Berdasarkan gejala "%s" pada %s: Kemungkinan masalah adalah %s. Tingkat Urgensi: %s. Diperlukan Teknisi: %s.',
            mb_substr($q, 0, 70) . (strlen($q) > 70 ? '...' : ''),
            $categoryName,
            implode(', ', array_slice($issues, 0, 2)),
            $urgency,
            $needsTech
        );

        return [
            'device_category'        => $categoryName,
            'possible_issues'        => $issues,
            'possible_causes'        => $causes,
            'simple_checks'          => $checks,
            'service_recommendation' => $recs,
            'urgency_level'          => $urgency,
            'needs_technician'       => $needsTech,
            'safety_warning'         => $safetyAlert,
            'consultation_questions' => $questions,
            'summary'                => $summary
        ];
    }

    /**
     * Safety Check Filter: Detect dangerous instruction keywords
     */
    private function isCheckSafe(string $checkText): bool
    {
        $dangerousKeywords = [
            'buka rangkaian', 'bongkar kelistrikan', 'bongkar pcb', 'lepas kabel listrik',
            'bongkar kompresor', 'potong pipa', 'bongkar selang gas', 'bongkar kompor gas',
            'sentuh kapasitor', 'tegangan tinggi', 'buka cover listrik', 'solder komponen'
        ];

        $lower = mb_strtolower($checkText);
        foreach ($dangerousKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                return false;
            }
        }
        return true;
    }

    /**
     * High-risk condition detector for emergency safety alerts
     */
    private function detectHighRiskCondition(string $q): array
    {
        if (str_contains($q, 'bau gas') || str_contains($q, 'bocor gas') || str_contains($q, 'regulator bocor')) {
            return ['flag' => true, 'reason' => 'Terdeteksi potensi kebocoran gas beracun/mudah terbakar!'];
        }
        if (str_contains($q, 'korslet') || str_contains($q, 'mcb jatuh') || str_contains($q, 'terbakar') || str_contains($q, 'bau hangus') || str_contains($q, 'asap') || str_contains($q, 'nyetrum') || str_contains($q, 'tersengat')) {
            return ['flag' => true, 'reason' => 'Terdeteksi risiko kebocoran arus listrik / korsleting tinggi!'];
        }
        if (str_contains($q, 'meledak') || str_contains($q, 'percikan api')) {
            return ['flag' => true, 'reason' => 'Terdeteksi ancaman percikan api atau bahaya ledakan komponen!'];
        }

        return ['flag' => false, 'reason' => ''];
    }

    /**
     * Safety filter for external AI API output to ensure strict compliance
     */
    private function applySafetyFilters(array $result): array
    {
        if (!isset($result['simple_checks']) || !is_array($result['simple_checks'])) {
            $result['simple_checks'] = [];
        }

        $result['simple_checks'] = array_values(array_filter($result['simple_checks'], fn($c) => $this->isCheckSafe($c)));

        if (empty($result['simple_checks'])) {
            $result['simple_checks'] = [
                'Periksa sambungan kabel steker luar.',
                'Pastikan sakelar utama dalam posisi ON.'
            ];
        }

        return $result;
    }
}
