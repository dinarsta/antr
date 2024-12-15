@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Sistem Antrian</h1>

    <!-- Status pasien yang sedang dipanggil -->
    <div class="mt-4" id="current-antrian">Nomor Antrian: -</div>
    <div id="current-nama">Nama: -</div>

    <!-- Tabel untuk antrian pasien -->
    <table class="table table-striped">
        <thead>
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
                            Belum dipanggil
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada pasien dalam antrian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Status Display -->
    <div id="status-message">Menunggu pasien berikutnya...</div>
</div>
<script>
    // Function to call the next patient automatically every 30 seconds
    async function panggilPasien() {
        try {
            const response = await fetch("{{ route('antrian.panggil') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });

            const data = await response.json();

            if (data.success) {
                // Update the table row for the called patient
                const row = document.querySelector(`#queue-body tr[data-id="${data.pasien.id}"]`);
                if (row) {
                    row.cells[3].textContent = new Date().toLocaleTimeString(); // Update time of call
                }

                // Update the status message
                document.getElementById('status-message').textContent = `Memanggil nomor antrian ${data.pasien.nomor_antrian} - ${data.pasien.nama}`;

                // Update information for the current called patient
                document.getElementById('current-antrian').textContent = `Nomor Antrian: ${data.pasien.nomor_antrian}`;
                document.getElementById('current-nama').textContent = `Nama: ${data.pasien.nama}`;
            } else {
                // If no patient can be called
                document.getElementById('status-message').textContent = "Antrian selesai. Tidak ada pasien yang tersisa.";
                document.getElementById('current-antrian').textContent = "Nomor Antrian: -";
                document.getElementById('current-nama').textContent = "Nama: -";
            }
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('status-message').textContent = "Terjadi kesalahan. Coba lagi.";
        }
    }

    // Call the next patient automatically every 30 seconds
    setInterval(panggilPasien, 30000); // 30000 ms = 30 seconds

    // Call the first patient immediately on page load
    panggilPasien();
</script>

@endsection
