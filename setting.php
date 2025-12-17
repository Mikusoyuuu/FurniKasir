<?php 
include 'config/db.php'; 

// Ambil data saat ini
$q = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
$s = mysqli_fetch_assoc($q);

if(isset($_POST['simpan'])) {
    $nama = $_POST['nama_toko'];
    $alamat = $_POST['alamat'];
    $wa = $_POST['no_wa'];
    
    // Logika upload logo
    $logo_name = $s['logo'];
    if($_FILES['logo']['name']) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_name = "logo_toko." . $ext;
        move_uploaded_file($_FILES['logo']['tmp_name'], "assets/" . $logo_name);
    }

    mysqli_query($conn, "UPDATE pengaturan SET nama_toko='$nama', alamat='$alamat', no_wa='$wa', logo='$logo_name' WHERE id=1");
    header("Location: setting.php?status=success");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setting Toko - FurniKasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-indigo-800">Pengaturan Toko</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase">Nama Toko</label>
                <input type="text" name="nama_toko" value="<?= $s['nama_toko'] ?>" class="w-full border p-3 rounded-xl outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase">Alamat</label>
                <textarea name="alamat" class="w-full border p-3 rounded-xl outline-none focus:border-indigo-500"><?= $s['alamat'] ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase">No. WhatsApp Toko</label>
                <input type="text" name="no_wa" value="<?= $s['no_wa'] ?>" class="w-full border p-3 rounded-xl outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase">Logo (PNG/JPG)</label>
                <input type="file" name="logo" class="w-full border p-2 rounded-xl">
                <?php if($s['logo']): ?>
                    <img src="assets/<?= $s['logo'] ?>" class="h-12 mt-2">
                <?php endif; ?>
            </div>
            <button name="simpan" class="w-full bg-indigo-700 text-white py-3 rounded-xl font-bold">Simpan Perubahan</button>
            <a href="index.php" class="block text-center text-gray-400 text-sm mt-4">Kembali</a>
        </form>
    </div>
</body>
</html>