<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Resep di Apotik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center mb-4">STATUS RESEP DI APOTIK</h3>
        <div class="card">
            <div class="card-header bg-success text-white text-center">
                RESEP SEDANG DISIAPKAN
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-success text-center">
                        <tr>
                            <th>ID</th>
                            <th>NO. RESEP</th>
                            <th>NAMA</th>
                            <th>JENIS OBAT</th>
                            <th>WAKTU MULAI</th>
                            <th>ESTIMASI WAKTU SELESAI</th>
                            <th>ESTIMASI</th> <!-- Kolom Estimasi (Countdown) -->
                            <th>KETERANGAN</th> <!-- Kolom Keterangan -->
                        </tr>
                    </thead>
                    <tbody id="patientTableBody">
                        @foreach ($pasien as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->nomor_resep }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>{{ ucfirst($data->jenis_obat) }}</td>
                                <td>{{ $data->waktu_mulai ? \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i:s') : '-' }}</td>
                                <td>{{ $data->estimasi_waktu_selesai ? \Carbon\Carbon::parse($data->estimasi_waktu_selesai)->format('H:i:s') : '-' }}</td>
                                <td class="estimasi-waktu" data-start="{{ $data->waktu_mulai }}" data-jenis="{{ $data->jenis_obat }}"></td> <!-- Kolom estimasi -->
                                <td class="status-keterangan">{{ $data->keterangan ?? 'Belum Ditetapkan' }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
    // Variabel global untuk melacak apakah suara telah dipanggil
    let hasSpoken = {};

    // Fungsi untuk memperbarui status pasien di database
    function updateStatusToSelesai(idPasien, estimasiSelesai) {
        fetch(`/update-status/${idPasien}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: 'selesai',
                estimasi_selesai: estimasiSelesai.toISOString(),
                keterangan: 'Selesai'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Status dan keterangan pasien berhasil diperbarui.');
            } else {
                console.log('Gagal memperbarui status dan keterangan pasien.');
            }
        })
        .catch(error => {
            console.error('Terjadi kesalahan:', error);
        });
    }

    // Fungsi untuk menghitung waktu estimasi selesai
    function calculateEstimatedEndTime(startTimeStr, jenisObat) {
        const startTime = new Date(startTimeStr);
        let durationInMinutes = 0;

        switch (jenisObat.toLowerCase()) {
            case 'jadi':
                durationInMinutes = 30;
                break;
            case 'racikan':
                durationInMinutes = 60;
                break;
            default:
                durationInMinutes = 20;
        }

        startTime.setMinutes(startTime.getMinutes() + durationInMinutes);
        return startTime;
    }

    // Fungsi untuk memperbarui estimasi dan status
    function updateEstimasi() {
        const rows = document.querySelectorAll('#patientTableBody tr');
        const currentTime = new Date();

        rows.forEach(row => {
            const startTimeStr = row.querySelector('.estimasi-waktu').getAttribute('data-start');
            const jenisObat = row.querySelector('.estimasi-waktu').getAttribute('data-jenis');
            const estimasiCell = row.querySelector('.estimasi-waktu');
            const statusCell = row.querySelector('.status-keterangan');
            const idPasien = row.querySelector('td:first-child').textContent;

            if (!startTimeStr || !jenisObat) {
                estimasiCell.textContent = "-";
                statusCell.textContent = "Data tidak lengkap";
                return;
            }

            const estimatedTime = calculateEstimatedEndTime(startTimeStr, jenisObat);
            const remainingTime = Math.max(0, estimatedTime - currentTime);

            if (remainingTime > 0) {
                const timeRemaining = new Date(remainingTime);
                const hours = String(timeRemaining.getUTCHours()).padStart(2, '0');
                const minutes = String(timeRemaining.getUTCMinutes()).padStart(2, '0');
                const seconds = String(timeRemaining.getUTCSeconds()).padStart(2, '0');

                estimasiCell.textContent = `${hours}:${minutes}:${seconds}`;
                statusCell.textContent = "Sedang disiapkan";

                // Panggil Text-to-Speech jika waktu tersisa kurang dari 1 menit
                if (remainingTime <= 60000 && !hasSpoken[idPasien]) {
                    const nomorResep = row.querySelector('td:nth-child(2)').textContent;
                    const namaPasien = row.querySelector('td:nth-child(3)').textContent;

                    speakText(`Perhatian! Nomor resep ${nomorResep}, atas nama ${namaPasien}, dipersilahkan menuju loket.`);

                    hasSpoken[idPasien] = true; // Tandai sudah dipanggil
                }
            } else {
                statusCell.textContent = "Selesai";
                estimasiCell.textContent = "-";
                // Update status ke selesai di database
                updateStatusToSelesai(idPasien, estimatedTime);
            }
        });
    }

    // Fungsi untuk text-to-speech
    function speakText(text) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID'; // Bahasa Indonesia
        utterance.onstart = () => console.log('Text-to-Speech mulai berbicara:', text);
        utterance.onend = () => console.log('Text-to-Speech selesai berbicara.');
        window.speechSynthesis.speak(utterance);
    }

    // Update estimasi setiap detik
    setInterval(updateEstimasi, 1000);
</script>

</script>
</body>
</html>
