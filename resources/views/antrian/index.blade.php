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
                            <th>ESTIMASI</th>
                            <th>KETERANGAN</th>
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
                                <td class="estimasi-waktu" data-start="{{ $data->waktu_mulai }}" data-jenis="{{ $data->jenis_obat }}"></td>
                                <td>{{ $data->keterangan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let hasSpoken = {};

        function updateStatusToSelesai(idPasien, status) {
    fetch(`/update-status/${idPasien}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: status,
            keterangan: 'selesai', // Pastikan keterangan diubah menjadi "selesai"
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`Status pasien ID ${idPasien} berhasil diperbarui di database.`);
        } else {
            console.error(`Gagal memperbarui status pasien ID ${idPasien} di database.`);
        }
    })
    .catch(error => {
        console.error(`Terjadi kesalahan saat memperbarui status pasien ID ${idPasien}:`, error);
    });
}


        function updateEstimasi() {
    const rows = document.querySelectorAll('#patientTableBody tr');
    const currentTime = new Date();

    rows.forEach(row => {
        const startTimeStr = row.querySelector('.estimasi-waktu').getAttribute('data-start');
        const jenisObat = row.querySelector('.estimasi-waktu').getAttribute('data-jenis');
        const estimasiCell = row.querySelector('.estimasi-waktu');
        const keteranganCell = row.querySelector('td:last-child'); // Kolom keterangan
        const idPasien = row.querySelector('td:first-child').textContent;

        if (!startTimeStr || !jenisObat) {
            estimasiCell.textContent = "-";
            return;
        }

        const estimatedTime = calculateEstimatedEndTime(startTimeStr, jenisObat);
        const remainingTime = estimatedTime - currentTime;

        if (remainingTime > 0) {
            const hours = String(Math.floor(remainingTime / (1000 * 60 * 60))).padStart(2, '0');
            const minutes = String(Math.floor((remainingTime / (1000 * 60)) % 60)).padStart(2, '0');
            const seconds = String(Math.floor((remainingTime / 1000) % 60)).padStart(2, '0');
            estimasiCell.textContent = `${hours}:${minutes}:${seconds}`;
        } else {
            estimasiCell.textContent = "selesai";

            // Update keterangan di view
            keteranganCell.textContent = "selesai";

            // Update status ke database
            updateStatusToSelesai(idPasien, "selesai");
        }
    });
}


        function calculateEstimatedEndTime(startTimeStr, jenisObat) {
            const startTime = new Date(startTimeStr);
            const duration = jenisObat.toLowerCase() === 'racikan' ? 60 : 30;
            return startTime.setMinutes(startTime.getMinutes() + duration);
        }

        function speakText(text) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }

        setInterval(updateEstimasi, 1000);
    </script>
</body>
</html>
