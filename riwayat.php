<?php
include 'config/db.php';

// 1. Ambil Pengaturan Toko untuk Header & Struk
$q_set = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
$set = mysqli_fetch_assoc($q_set);

// 2. Proses Hapus Transaksi
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM transaksi_detail WHERE transaksi_id = $id");
    mysqli_query($conn, "DELETE FROM transaksi WHERE id = $id");
    header("Location: riwayat.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - <?= $set['nama_toko'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap');
        .font-receipt { font-family: 'Inconsolata', monospace; }
    </style>
</head>
<body class="bg-gray-100 p-4 font-sans text-gray-800">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-indigo-800"><i class="fas fa-history mr-2"></i>Riwayat Transaksi</h1>
            <a href="index.php" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm shadow-md hover:bg-indigo-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Kasir
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead class="bg-indigo-600 text-white text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Bayar</th>
                        <th class="p-4">Kembali</th>
                        <th class="p-4">No. HP</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY tanggal DESC");
                    while ($row = mysqli_fetch_assoc($res)):
                        $id_t = $row['id'];
                        $q_item = mysqli_query($conn, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_t");
                        $items = [];
                        while ($i = mysqli_fetch_assoc($q_item)) { $items[] = $i; }
                        $items_json = htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="p-4 font-medium text-gray-600"><?= date('d/m H:i', strtotime($row['tanggal'])) ?></td>
                            <td class="p-4 font-bold text-indigo-700">Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                            <td class="p-4 text-green-600">Rp<?= number_format($row['bayar'], 0, ',', '.') ?></td>
                            <td class="p-4 text-orange-600">Rp<?= number_format($row['kembalian'], 0, ',', '.') ?></td>
                            <td class="p-4 text-blue-600"><?= $row['pelanggan_hp'] ?: '-' ?></td>
                            <td class="p-4 flex justify-center gap-2">
                                <button onclick='printStruk(<?= json_encode($row) ?>, <?= $items_json ?>)' class="w-9 h-9 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition shadow-sm"><i class="fas fa-print text-xs"></i></button>
                                <button onclick='sendWA(<?= json_encode($row) ?>, <?= $items_json ?>)' class="w-9 h-9 bg-green-100 text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition shadow-sm"><i class="fab fa-whatsapp text-xs"></i></button>
                                <button onclick="confirmHapus(<?= $row['id'] ?>)" class="w-9 h-9 bg-red-100 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition shadow-sm"><i class="fas fa-trash text-xs"></i></button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="receipt-canvas" class="fixed -left-[9999px] top-0 bg-white p-8 w-[400px] font-receipt text-gray-800">
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-4">
            <?php if(!empty($set['logo'])): ?>
                <img src="assets/<?= $set['logo'] ?>" class="h-16 mx-auto mb-2">
            <?php endif; ?>
            <h1 class="text-2xl font-bold uppercase"><?= $set['nama_toko'] ?></h1>
            <p class="text-sm font-bold">Struk Pembayaran Resmi</p>
            <p class="text-xs"><?= $set['alamat'] ?> | WA: <?= $set['no_wa'] ?></p>
        </div>
        <div class="text-xs mb-4 space-y-1">
            <div class="flex justify-between"><span>No. Transaksi :</span> <span id="img-id"></span></div>
            <div class="flex justify-between"><span>Tanggal       :</span> <span id="img-tgl"></span></div>
        </div>
        <div class="border-y-2 border-gray-800 py-2 mb-4">
            <div class="flex justify-between font-bold text-xs mb-2 uppercase"><span>ITEM</span><span>HARGA</span></div>
            <div id="img-items-list" class="space-y-1 text-xs"></div>
        </div>
        <div class="space-y-1 text-sm border-b-2 border-gray-800 pb-4">
            <div class="flex justify-between font-bold"><span>TOTAL</span><span id="img-total"></span></div>
            <div class="flex justify-between text-gray-600"><span>BAYAR</span><span id="img-bayar"></span></div>
            <div class="flex justify-between font-bold"><span>KEMBALI</span><span id="img-kembali"></span></div>
        </div>
        <div class="mt-4 text-center text-[10px] leading-tight italic">
            <p>Terima kasih telah berbelanja di <?= $set['nama_toko'] ?></p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>

    <script>
        function fUang(n) { return parseInt(n || 0).toLocaleString('id-ID'); }

        function confirmHapus(id) {
            if (confirm("Hapus permanen transaksi ini?")) window.location.href = "riwayat.php?hapus=" + id;
        }

        function printStruk(t, items) {
            let itemText = items.map(i => {
                let nama = (i.nama_item || "Item").substring(0, 18).padEnd(20);
                let harga = fUang(i.harga).padStart(12);
                return `${nama}${harga}`;
            }).join('\n');

            const w = window.open('', '', 'width=400,height=600');
            w.document.write(`<pre style="font-family:monospace; font-size:12px; line-height:1.2;">
<?= strtoupper($set['nama_toko']) ?>
<?= $set['alamat'] ?>
WA: <?= $set['no_wa'] ?>
================================
No. Transaksi : TX-${t.id}
Tanggal       : ${new Date(t.tanggal).toLocaleString('id-ID')}
================================
ITEM                    HARGA
--------------------------------
${itemText}
--------------------------------
TOTAL           ${fUang(t.total).padStart(12)}
BAYAR           ${fUang(t.bayar).padStart(12)}
KEMBALI         ${fUang(t.kembalian).padStart(12)}
================================
Terima kasih telah berbelanja
di <?= $set['nama_toko'] ?>
================================</pre>`);
            w.document.close();
            w.print();
        }

        async function sendWA(t, items) {
            const phone = t.pelanggan_hp ? t.pelanggan_hp.replace(/^0/, '62') : prompt("Kirim ke No WhatsApp (62xxx):");
            if (!phone) return;

            document.getElementById('img-id').innerText = `TX-${t.id}`;
            document.getElementById('img-tgl').innerText = new Date(t.tanggal).toLocaleString('id-ID');
            document.getElementById('img-total').innerText = fUang(t.total);
            document.getElementById('img-bayar').innerText = fUang(t.bayar);
            document.getElementById('img-kembali').innerText = fUang(t.kembalian);
            document.getElementById('img-items-list').innerHTML = items.map(i => `
                <div class="flex justify-between uppercase">
                    <span>${i.nama_item}</span>
                    <span>${fUang(i.harga)}</span> 
                </div>
            `).join('');

            try {
                const canvas = await html2canvas(document.getElementById('receipt-canvas'), { scale: 2 });
                const imgData = canvas.toDataURL("image/png");
                const link = document.createElement('a');
                link.download = `Struk_TX${t.id}.png`;
                link.href = imgData;
                link.click();
                window.open(`https://api.whatsapp.com/send?phone=${phone}&text=Berikut struk belanja Anda di *<?= $set['nama_toko'] ?>*.`, '_blank');
            } catch (err) { alert("Error: " + err); }
        }
    </script>
</body>
</html>