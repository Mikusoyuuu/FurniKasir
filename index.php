<?php 
include 'config/db.php'; 

// 1. Ambil Pengaturan Toko
$q_set = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
$set = mysqli_fetch_assoc($q_set);

// 2. Query untuk Ringkasan Hari Ini
$today = date('Y-m-d');
$q_total = mysqli_query($conn, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tanggal) = '$today'");
$row_total = mysqli_fetch_assoc($q_total);
$total_hari_ini = $row_total['total'] ?? 0;

$q_trans = mysqli_query($conn, "SELECT COUNT(*) as jml FROM transaksi WHERE DATE(tanggal) = '$today'");
$row_trans = mysqli_fetch_assoc($q_trans);
$jml_transaksi = $row_trans['jml'] ?? 0;

$q_laris = mysqli_query($conn, "SELECT nama_item, COUNT(*) as total_terjual 
                                FROM transaksi_detail 
                                GROUP BY nama_item 
                                ORDER BY total_terjual DESC LIMIT 1");
$row_laris = mysqli_fetch_assoc($q_laris);
$furniture_terlaris = $row_laris['nama_item'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $set['nama_toko'] ?> - Pro POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .active-btn:active { transform: scale(0.95); }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.3s ease-out; }
        @import url('https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap');
        .font-receipt { font-family: 'Inconsolata', monospace; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden font-sans">

    <header class="bg-indigo-700 text-white p-4 shadow-lg flex justify-between items-center">
        <h1 class="text-xl font-bold italic"><i class="fas fa-store mr-2"></i><?= $set['nama_toko'] ?></h1>
        <div class="flex gap-2">
            <a href="setting.php" class="text-[10px] bg-indigo-800 px-3 py-1 rounded-full border border-indigo-500"><i class="fas fa-cog"></i></a>
            <a href="riwayat.php" class="text-[10px] bg-indigo-600 px-3 py-1 rounded-full border border-indigo-500">Riwayat</a>
            <a href="furniture.php" class="text-[10px] bg-white text-indigo-700 px-3 py-1 rounded-full font-bold shadow-sm">Data Barang</a>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 space-y-4">
        <div class="grid grid-cols-3 gap-3 mb-2">
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 p-3 rounded-2xl text-white shadow-md">
                <p class="text-[8px] font-bold opacity-80 uppercase">Omzet Hari Ini</p>
                <p class="text-xs font-black truncate">Rp <?= number_format($total_hari_ini, 0, ',', '.') ?></p>
            </div>
            <div class="bg-white p-3 rounded-2xl border border-indigo-100 shadow-sm">
                <p class="text-[8px] text-gray-400 font-bold uppercase">Transaksi</p>
                <p class="text-sm font-black text-indigo-900"><?= $jml_transaksi ?></p>
            </div>
            <div class="bg-white p-3 rounded-2xl border border-indigo-100 shadow-sm">
                <p class="text-[8px] text-gray-400 font-bold uppercase">Terlaris</p>
                <p class="text-[10px] font-black text-orange-500 truncate uppercase"><?= $furniture_terlaris ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-5 border-2 border-indigo-50">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Bayar</p>
            <div id="display-total" class="text-4xl font-black text-indigo-900 text-right">Rp 0</div>
            <div id="item-list" class="mt-3 text-sm text-gray-500 max-h-24 overflow-y-auto border-t pt-2 space-y-1"></div>
        </div>

        <div class="flex overflow-x-auto gap-3 pb-2 no-scrollbar">
            <?php
            $q = mysqli_query($conn, "SELECT * FROM furniture WHERE status='aktif'");
            while ($f = mysqli_fetch_assoc($q)): ?>
                <button onclick="addItem('<?= $f['nama'] ?>', <?= $f['harga'] ?>, <?= $f['id'] ?>)"
                    class="flex-shrink-0 bg-white p-3 rounded-2xl shadow-sm border border-gray-100 active-btn w-28 text-center transition-all hover:border-indigo-300">
                    <div class="bg-indigo-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 text-indigo-600">
                        <i class="fas fa-tag text-sm"></i>
                    </div>
                    <p class="text-[10px] font-bold truncate text-gray-700"><?= strtoupper($f['nama']) ?></p>
                    <p class="text-[10px] text-indigo-500 font-bold">Rp<?= number_format($f['harga'], 0, ',', '.') ?></p>
                </button>
            <?php endwhile; ?>
        </div>

        <div class="grid grid-cols-4 gap-2">
            <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $n): ?>
                <button onclick="appendNum(<?= $n ?>)" class="bg-white py-4 rounded-2xl font-bold text-xl shadow-sm active-btn border border-gray-100 text-gray-700"><?= $n ?></button>
            <?php endforeach; ?>
            <button onclick="clearAll()" class="bg-red-50 text-red-500 py-4 rounded-2xl font-bold text-xl active-btn">C</button>
            <button onclick="appendNum(0)" class="bg-white py-4 rounded-2xl font-bold text-xl shadow-sm active-btn">0</button>
            <button onclick="appendNum('000')" class="bg-white py-4 rounded-2xl font-bold text-xl shadow-sm active-btn">000</button>
            <button onclick="addManual()" class="bg-indigo-700 text-white py-4 rounded-2xl font-bold text-xl active-btn shadow-indigo-200 shadow-lg">+</button>
        </div>
    </main>

    <footer class="p-4 bg-white border-t border-gray-100">
        <button onclick="openModal()" class="w-full bg-indigo-700 text-white py-4 rounded-2xl font-bold text-lg shadow-xl shadow-indigo-100">
            KONFIRMASI PEMBAYARAN
        </button>
    </footer>

    <div id="modal-bayar" class="hidden fixed inset-0 bg-black/60 z-50 flex items-end sm:items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl p-6 animate-slide-up shadow-2xl">
            <div class="flex justify-between items-center mb-5">
                <h3 class="font-bold text-lg text-gray-800">Detail Pembayaran</h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full"><i class="fas fa-times text-gray-400"></i></button>
            </div>
            <div class="bg-indigo-700 p-5 rounded-2xl mb-5 text-center">
                <p class="text-xs text-indigo-200 uppercase font-bold tracking-widest mb-1">Total Tagihan</p>
                <h2 id="modal-total-text" class="text-3xl font-black text-white">Rp 0</h2>
            </div>
            <div id="quick-cash" class="grid grid-cols-2 gap-2 mb-5"></div>
            <div class="space-y-4 mb-6">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-2">Uang Tunai (Rp)</label>
                    <input type="number" id="input-tunai" oninput="hitungKembalian()" placeholder="0" class="w-full border-2 border-gray-100 p-4 rounded-2xl text-xl font-bold focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase ml-2">WhatsApp Pelanggan</label>
                    <input type="text" id="pelanggan-hp" placeholder="0812..." class="w-full border-2 border-gray-100 p-3 rounded-2xl text-lg focus:border-indigo-500 outline-none">
                </div>
            </div>
            <div class="flex flex-col p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mb-6 text-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase">Kembalian</span>
                <span id="text-kembalian" class="text-3xl font-black text-gray-300">Rp 0</span>
            </div>
            <button onclick="prosesSimpanFinal()" id="btn-konfirmasi" disabled class="w-full bg-gray-300 text-white py-4 rounded-2xl font-bold text-lg shadow-lg">KONFIRMASI SEKARANG</button>
        </div>
    </div>

    <div id="receipt-canvas" class="fixed -left-[9999px] top-0 bg-white p-8 w-[400px] font-receipt text-gray-800">
        <div class="text-center border-b-2 border-gray-800 pb-4 mb-4">
            <?php if(!empty($set['logo'])): ?>
                <img src="assets/<?= $set['logo'] ?>" class="h-16 mx-auto mb-2">
            <?php endif; ?>
            <h1 class="text-2xl font-bold uppercase"><?= $set['nama_toko'] ?></h1>
            <p class="text-sm font-bold">Pusat Furniture Berkualitas</p>
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
        let currentTotal = 0, manualInput = "", cart = [], uangBayar = 0, lastTransactionData = null;

        const fUang = (n) => parseInt(n || 0).toLocaleString('id-ID');
        const formatR = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);

        function appendNum(n) { manualInput += n; updateDisplay(); }
        function clearAll() { currentTotal = 0; manualInput = ""; cart = []; updateDisplay(); }
        function addManual() {
            if (manualInput) {
                cart.push({ id: null, nama: "Input Manual", harga: parseInt(manualInput) });
                manualInput = "";
                updateDisplay();
            }
        }
        function addItem(n, h, id) {
            cart.push({ id: id, nama: n, harga: h });
            updateDisplay();
        }
        function updateDisplay() {
            let tempTotal = cart.reduce((s, i) => s + i.harga, 0);
            if (manualInput) tempTotal += parseInt(manualInput);
            currentTotal = tempTotal;
            document.getElementById('display-total').innerText = formatR(currentTotal);
            document.getElementById('item-list').innerHTML = cart.map(i => `
                <div class="flex justify-between border-b border-gray-50 pb-1 uppercase">
                    <span>${i.nama}</span><span>${formatR(i.harga)}</span>
                </div>`).join('');
        }

        function openModal() {
            if (currentTotal <= 0) return alert("Pilih item dulu!");
            document.getElementById('modal-bayar').classList.remove('hidden');
            document.getElementById('modal-total-text').innerText = formatR(currentTotal);
            const qc = document.getElementById('quick-cash');
            qc.innerHTML = "";
            [currentTotal, 50000, 100000, 200000, 500000].forEach(v => {
                if (v >= currentTotal) {
                    const b = document.createElement('button');
                    b.className = "bg-white border-2 border-gray-100 p-3 rounded-xl text-sm font-bold text-indigo-700 active-btn";
                    b.innerText = v == currentTotal ? "Uang Pas" : formatR(v);
                    b.onclick = () => { document.getElementById('input-tunai').value = v; hitungKembalian(); };
                    qc.appendChild(b);
                }
            });
        }

        function closeModal() { document.getElementById('modal-bayar').classList.add('hidden'); }

        function hitungKembalian() {
            uangBayar = parseInt(document.getElementById('input-tunai').value) || 0;
            const k = uangBayar - currentTotal;
            const tk = document.getElementById('text-kembalian');
            const bk = document.getElementById('btn-konfirmasi');
            if (k >= 0) {
                tk.innerText = formatR(k);
                tk.classList.replace('text-gray-300', 'text-green-600');
                bk.disabled = false;
                bk.className = "w-full bg-indigo-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-200";
            } else {
                tk.innerText = "Kurang!";
                tk.classList.replace('text-green-600', 'text-red-600');
                bk.disabled = true;
                bk.className = "w-full bg-gray-300 text-white py-4 rounded-2xl font-bold";
            }
        }

        async function prosesSimpanFinal() {
            const hpValue = document.getElementById('pelanggan-hp').value || "";
            const payload = {
                total: currentTotal,
                bayar: uangBayar,
                kembalian: uangBayar - currentTotal,
                pelanggan_hp: hpValue,
                items: [...cart]
            };

            try {
                const res = await fetch('api/simpan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const out = await res.json();
                if (out.status === 'success') {
                    lastTransactionData = { ...payload, id: out.id, tanggal: new Date().toLocaleString('id-ID') };
                    closeModal();
                    showReceiptOption();
                    clearAll();
                }
            } catch (e) { alert("Gagal Simpan Database!"); }
        }

        function showReceiptOption() {
            const m = document.createElement('div');
            m.className = "fixed inset-0 bg-black/70 z-[60] flex items-center justify-center p-4";
            m.innerHTML = `<div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center animate-slide-up">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-6 text-gray-800">Transaksi Sukses!</h3>
                <button onclick="printStruk()" class="w-full bg-indigo-700 text-white py-4 rounded-2xl font-bold mb-3 flex items-center justify-center gap-2"><i class="fas fa-print"></i> Cetak Kertas</button>
                <button id="wa-btn" onclick="sendWhatsAppImage()" class="w-full bg-green-500 text-white py-4 rounded-2xl font-bold mb-3 flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-2xl"></i> Kirim Struk Gambar
                </button>
                <button onclick="location.reload()" class="text-gray-400 font-bold uppercase text-xs tracking-widest mt-4">Tutup</button>
            </div>`;
            document.body.appendChild(m);
        }

        function printStruk() {
            const t = lastTransactionData;
            let itemText = t.items.map(i => {
                let nama = (i.nama || "Item").substring(0, 18).padEnd(20);
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
Tanggal       : ${t.tanggal}
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

        async function sendWhatsAppImage() {
            const t = lastTransactionData;
            const btn = document.getElementById('wa-btn');
            const phone = t.pelanggan_hp ? t.pelanggan_hp.replace(/^0/, '62') : prompt("Nomor WhatsApp (62xxx):");
            if (!phone) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';

            document.getElementById('img-id').innerText = `TX-${t.id}`;
            document.getElementById('img-tgl').innerText = t.tanggal;
            document.getElementById('img-total').innerText = fUang(t.total);
            document.getElementById('img-bayar').innerText = fUang(t.bayar);
            document.getElementById('img-kembali').innerText = fUang(t.kembalian);
            document.getElementById('img-items-list').innerHTML = t.items.map(i => `
                <div class="flex justify-between uppercase"><span>${i.nama}</span><span>${fUang(i.harga)}</span></div>
            `).join('');

            try {
                const canvas = await html2canvas(document.getElementById('receipt-canvas'), { scale: 2 });
                const imgData = canvas.toDataURL("image/png");
                const link = document.createElement('a');
                link.download = `Struk_TX${t.id}.png`;
                link.href = imgData;
                link.click();

                const msg = `Halo, berikut adalah struk belanja Anda di *<?= $set['nama_toko'] ?>*. Gambar terunduh otomatis.`;
                window.open(`https://api.whatsapp.com/send?phone=${phone}&text=${msg}`, '_blank');
                btn.innerHTML = '<i class="fab fa-whatsapp text-2xl"></i> Kirim WhatsApp';
            } catch (err) { alert("Gagal membuat gambar."); }
        }
    </script>
</body>
</html>