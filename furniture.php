<?php 
include 'config/db.php'; 

// Proses Tambah Data
if(isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    mysqli_query($conn, "INSERT INTO furniture (nama, harga, status) VALUES ('$nama', $harga, 'aktif')");
    header("Location: furniture.php");
}

// Proses Update Harga
if(isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $harga = (int)$_POST['harga'];
    mysqli_query($conn, "UPDATE furniture SET harga = $harga WHERE id = $id");
    header("Location: furniture.php");
}

// Proses Toggle Status (Aktif/Nonaktif)
if(isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $status = $_GET['s'] == 'aktif' ? 'nonaktif' : 'aktif';
    mysqli_query($conn, "UPDATE furniture SET status = '$status' WHERE id = $id");
    header("Location: furniture.php");
}

// Proses Hapus Data (BARU)
if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id = $id");
    header("Location: furniture.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - FurniKasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-4 font-sans text-gray-800">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-indigo-800"><i class="fas fa-boxes mr-2"></i>Manajemen Furniture</h1>
            <a href="index.php" class="bg-gray-600 text-white px-4 py-2 rounded-xl text-sm shadow-md hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Kasir
            </a>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm mb-6 border-2 border-indigo-50">
            <h2 class="font-bold mb-4 text-gray-700">Tambah Furniture Baru</h2>
            <form method="POST" class="flex flex-col md:flex-row gap-3">
                <input type="text" name="nama" placeholder="Nama Barang (Cth: Kursi Kantor)" required 
                    class="flex-1 border-2 border-gray-100 p-3 rounded-xl outline-none focus:border-indigo-500 transition-all">
                <input type="number" name="harga" placeholder="Harga (Rp)" required 
                    class="w-full md:w-48 border-2 border-gray-100 p-3 rounded-xl outline-none focus:border-indigo-500 transition-all">
                <button type="submit" name="tambah" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 active:scale-95 transition-all">
                    <i class="fas fa-plus mr-2"></i>Simpan
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                        <th class="p-4">Nama Barang</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM furniture ORDER BY id DESC");
                    if(mysqli_num_rows($res) == 0): ?>
                        <tr><td colspan="4" class="p-10 text-center text-gray-400 italic">Belum ada data barang.</td></tr>
                    <?php endif;
                    while($row = mysqli_fetch_assoc($res)): ?>
                    <tr class="hover:bg-indigo-50/30 transition">
                        <td class="p-4 font-bold text-gray-700"><?= $row['nama'] ?></td>
                        <td class="p-4 text-indigo-600 font-bold text-lg">Rp<?= number_format($row['harga'],0,',','.') ?></td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold <?= $row['status'] == 'aktif' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                                <?= strtoupper($row['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right flex justify-end gap-2">
                            <button onclick="editHarga(<?= $row['id'] ?>, '<?= $row['nama'] ?>', <?= $row['harga'] ?>)" 
                                class="w-9 h-9 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit Harga">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            
                            <a href="?toggle=<?= $row['id'] ?>&s=<?= $row['status'] ?>" 
                                class="w-9 h-9 <?= $row['status'] == 'aktif' ? 'bg-orange-100 text-orange-600 hover:bg-orange-600' : 'bg-green-100 text-green-600 hover:bg-green-600' ?> rounded-xl flex items-center justify-center hover:text-white transition-all shadow-sm" title="<?= $row['status'] == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                <i class="fas <?= $row['status'] == 'aktif' ? 'fa-eye-slash' : 'fa-eye' ?> text-xs"></i>
                            </a>

                            <button onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= addslashes($row['nama']) ?>')" 
                                class="w-9 h-9 bg-red-100 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus Permanen">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-edit" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-3xl p-6 shadow-2xl animate-slide-up">
            <h3 class="font-bold text-lg mb-1 text-gray-800" id="edit-nama">Edit Harga</h3>
            <p class="text-xs text-gray-400 mb-6">Masukkan harga baru untuk item ini.</p>
            <form method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="relative mb-6">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                    <input type="number" name="harga" id="edit-input-harga" required
                        class="w-full border-2 border-gray-100 p-4 pl-12 rounded-2xl outline-none focus:border-indigo-500 font-bold text-2xl transition-all">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEdit()" class="flex-1 bg-gray-100 text-gray-500 py-3 rounded-2xl font-bold hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" name="update" class="flex-1 bg-indigo-600 text-white py-3 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi Modal Edit
        function editHarga(id, nama, harga) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nama').innerText = "Edit Harga: " + nama;
            document.getElementById('edit-input-harga').value = harga;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        function closeEdit() {
            document.getElementById('modal-edit').classList.add('hidden');
        }

        // Fungsi Konfirmasi Hapus (BARU)
        function konfirmasiHapus(id, nama) {
            if (confirm("Hapus '" + nama + "' secara permanen?\n\nBarang yang dihapus tidak akan muncul lagi di kasir, namun data transaksi lama tetap aman di laporan.")) {
                window.location.href = "furniture.php?hapus=" + id;
            }
        }
        
        // Menutup modal dengan klik di luar area modal
        window.onclick = function(event) {
            let modal = document.getElementById('modal-edit');
            if (event.target == modal) {
                closeEdit();
            }
        }
    </script>

    <style>
        @keyframes slideUp { 
            from { transform: translateY(20px); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
    </style>
</body>
</html>