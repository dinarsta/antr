@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <h1 class="mb-4 text-center text-primary">Sistem Antrian</h1>

    <!-- Status pasien yang sedang dipanggil -->
    <div class="alert alert-info text-center" id="current-antrian">
        <strong>Nomor Antrian:</strong> <span id="current-nomor-antrian">-</span><br>
        <strong>Nama:</strong> <span id="current-nama">-</span>
    </div>

    <!-- Tabel untuk antrian pasien -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Nomor Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Jenis Obat</th>
                    <th>Waktu Pemanggilan</th>
                </tr>
            </thead>
            <tbody id="queue-body">
                @forelse ($antrian as $pasien)
                    <tr data-id="{{ $pasien->id }}">
                        <td>{{ $pasien->nomor_antrian }}</td>
                        <td>{{ $pasien->nama }}</td>
                        <td>{{ ucfirst($pasien->jenis_obat) }}</td>
                        <td>
                            @if($pasien->waktu_pemanggilan)
                                {{ $pasien->waktu_pemanggilan->format('H:i:s') }}
                            @else
                                <span class="text-muted">Belum dipanggil</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada pasien dalam antrian</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Status Display -->
    <div class="text-center my-3">
        <p id="status-message" class="text-secondary">Klik tombol <strong>"Mulai Panggil"</strong> untuk memulai pemanggilan pasien.</p>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-3">
        <button id="startButton" class="btn btn-success btn-lg me-2" onclick="startCalling()" title="Mulai Panggil">
            <i class="fa-solid fa-volume-high"></i>
        </button>
        <button id="stopButton" class="btn btn-danger btn-lg ms-2" onclick="stopCalling()" title="Berhenti Panggil" disabled>
            <i class="fa-solid fa-volume-xmark"></i>
        </button>
    </div>
</div>

<script>
    let lastCalledId = null; // Keep track of the last called patient
    let callingInterval = null; // Store the interval for polling

    // Function to fetch the next patient
    async function fetchNextPatient() {
        try {
            const response = await fetch("{{ route('antrian.panggil') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });

            const data = await response.json();

            if (data.success && data.pasien && data.pasien.id !== lastCalledId) {
                lastCalledId = data.pasien.id;

                const row = document.querySelector(`#queue-body tr[data-id="${data.pasien.id}"]`);
                if (row) {
                    row.cells[3].textContent = data.pasien.waktu_pemanggilan
                        ? new Date(data.pasien.waktu_pemanggilan).toLocaleTimeString()
                        : new Date().toLocaleTimeString();
                }

                document.getElementById('current-nomor-antrian').textContent = data.pasien.nomor_antrian;
                document.getElementById('current-nama').textContent = data.pasien.nama;

                const message = `Memanggil nomor antrian ${data.pasien.nomor_antrian} - ${data.pasien.nama} (${data.pasien.jenis_obat})`;
                document.getElementById('status-message').textContent = message;

                speak(message);
            } else if (!data.success) {
                document.getElementById('status-message').textContent = "Antrian selesai. Tidak ada pasien yang tersisa.";
                document.getElementById('current-nomor-antrian').textContent = "-";
                document.getElementById('current-nama').textContent = "-";
                speak("Antrian selesai. Tidak ada pasien yang tersisa.");
            }
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('status-message').textContent = "Terjadi kesalahan. Coba lagi.";
            speak("Terjadi kesalahan. Coba lagi.");
        }
    }

    // Function to convert text to speech
    function speak(text) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = "id-ID"; // Set language to Indonesian
            window.speechSynthesis.speak(utterance);
        } else {
            alert("Browser Anda tidak mendukung fitur text-to-speech.");
        }
    }

    // Start calling patients
    function startCalling() {
        if (callingInterval) return; // Prevent duplicate intervals
        document.getElementById('startButton').disabled = true;
        document.getElementById('stopButton').disabled = false;

        document.getElementById('status-message').textContent = "Memulai pemanggilan pasien...";
        fetchNextPatient();
        callingInterval = setInterval(fetchNextPatient, 12000);
    }

    // Stop calling patients
    function stopCalling() {
        if (!callingInterval) return; // Prevent stopping if not active
        clearInterval(callingInterval);
        callingInterval = null; // Reset interval

        document.getElementById('startButton').disabled = false;
        document.getElementById('stopButton').disabled = true;

        document.getElementById('status-message').textContent = "Pemanggilan pasien dihentikan.";
    }
</script>
@endsection
