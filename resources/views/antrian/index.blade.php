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
        <!-- Logo Rumah Sakit -->
        <div class="text-center mb-4">
            <img src="{{ asset('images/rumahsehat.png') }}" alt="Logo Rumah Sakit" class="img-fluid"
                style="max-width: 300px;" />
        </div>

        <!-- Judul Halaman -->
        <h3 class="text-center mb-4 text-uppercase text-success font-weight-bold">
            DAFTAR ANTRIAN
        </h3>

        <!-- Card untuk Menampilkan Tabel Status Resep -->
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center font-weight-bold">
                Resep Sedang Disiapkan
            </div>
            <div class="card-body p-4">
                <!-- Tabel untuk Data Resep -->
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>No. Resep</th>
                            <th>Nama Pasien</th>
                            <th>Jenis Obat</th>
                            <th>Waktu Mulai</th>
                            <th>Estimasi Waktu Selesai</th>
                            <th>Estimasi Proses</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="patientTableBody">
                        @foreach ($pasien as $data)
                        <tr>
                            <td>{{ $data->id }}</td>
                            <td>{{ $data->nomor_resep }}</td>
                            <td>{{ $data->nama }}</td>
                            <td>{{ ucfirst($data->jenis_obat) }}</td>
                            <td>
                                {{ $data->waktu_mulai ? \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i:s') : 'Belum Dimulai' }}
                            </td>
                            <td>
                                {{ $data->estimasi_waktu_selesai ? \Carbon\Carbon::parse($data->estimasi_waktu_selesai)->format('H:i:s') : 'Tidak Tersedia' }}
                            </td>
                            <td class="estimasi-waktu" data-start="{{ $data->waktu_mulai }}"
                                data-jenis="{{ $data->jenis_obat }}"
                                title="Estimasi waktu proses resep akan ditampilkan di sini.">
                                {{ $data->estimasi_waktu_selesai ? 'Sedang Diproses' : 'Estimasi belum tersedia' }}
                            </td>
                            <td>
                                {{ $data->keterangan ? $data->keterangan : 'Informasi tambahan tidak tersedia' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer dengan info tambahan -->
        <div class="mt-4">
            <p class="text-center text-muted">
                Jika Anda membutuhkan bantuan lebih lanjut, silakan hubungi apotik kami atau datang langsung untuk
                informasi lebih lanjut.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatusToSelesai(idPasien) {
            fetch(`/update-status/${idPasien}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        keterangan: 'selesai'
                    })
                })
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));
        }

        function updateEstimasi() {
            const rows = document.querySelectorAll('#patientTableBody tr');
            const currentTime = new Date();
            rows.forEach(row => {
                const startTimeStr = row.querySelector('.estimasi-waktu').getAttribute('data-start');
                const jenisObat = row.querySelector('.estimasi-waktu').getAttribute('data-jenis');
                const estimasiCell = row.querySelector('.estimasi-waktu');
                const keteranganCell = row.querySelector('td:last-child');
                const idPasien = row.querySelector('td:first-child').textContent;
                if (!startTimeStr || !jenisObat) {
                    estimasiCell.textContent = "-";
                    return;
                }
                const estimatedTime = calculateEstimatedEndTime(startTimeStr, jenisObat);
                const remainingTime = estimatedTime - currentTime;
                if (remainingTime > 0) {
                    const minutes = String(Math.floor((remainingTime / (1000 * 60)) % 60)).padStart(2, '0');
                    const seconds = String(Math.floor((remainingTime / 1000) % 60)).padStart(2, '0');
                    estimasiCell.textContent = `00:${minutes}:${seconds}`;
                    // Kirim estimasi ke server
                    fetch(`/update-estimasi/${idPasien}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            estimasi: `${minutes}:${seconds}`
                        })
                    }).catch(error => console.error('Error:', error));
                    // Panggil nama dan nomor resep 1 menit sebelumnya
                    if (remainingTime <= 60000 && keteranganCell.textContent !== 'selesai') {
                        speakPatientCompletion(idPasien, row.querySelector('td:nth-child(2)').textContent, row
                            .querySelector('td:nth-child(3)').textContent);
                    }
                } else {
                    if (keteranganCell.textContent !== "selesai") {
                        estimasiCell.textContent =
                        "selesai"; // Tampilkan "selesai" jika waktu estimasi telah tercapai
                        keteranganCell.textContent = "selesai";
                        updateStatusToSelesai(idPasien); // Update status pasien menjadi selesai
                    }
                }
            });
        }

        function calculateEstimatedEndTime(startTimeStr, jenisObat) {
            const startTime = new Date(startTimeStr);
            const duration = jenisObat === "racikan" ? 60 : 30;
            return startTime.setMinutes(startTime.getMinutes() + duration);
        }

        function speakPatientCompletion(idPasien, nomorResep, nama) {
            const message = new SpeechSynthesisUtterance(
                `Pasien atas nama ${nama}, dengan nomor resep ${nomorResep}, harap menuju ke loket pelayanan.`
            );
            message.lang = 'id-ID';
            speechSynthesis.speak(message);
        }
        setInterval(updateEstimasi, 1000);
    </script>
</body>

</html>
