<?php
header('Content-Type: application/json');
include '../config/db.php';

// Ambil input JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

// Ekstraksi data dengan pengaman (Null Coalescing)
$total        = isset($data['total']) ? (int)$data['total'] : 0;
$bayar        = isset($data['bayar']) ? (int)$data['bayar'] : 0;
$kembalian    = isset($data['kembalian']) ? (int)$data['kembalian'] : 0;
// Menghindari error Undefined Key dan Deprecated Null
$hp_raw       = isset($data['pelanggan_hp']) ? $data['pelanggan_hp'] : "";
$pelanggan_hp = mysqli_real_escape_string($conn, (string)$hp_raw);
$items        = isset($data['items']) ? $data['items'] : [];

mysqli_begin_transaction($conn);

try {
    // Simpan Header Transaksi
    $queryTx = "INSERT INTO transaksi (total, bayar, kembalian, pelanggan_hp, tanggal) 
                VALUES ($total, $bayar, $kembalian, '$pelanggan_hp', NOW())";
    
    if (!mysqli_query($conn, $queryTx)) {
        throw new Exception(mysqli_error($conn));
    }
    
    $txId = mysqli_insert_id($conn);

    // Simpan Detail Item
    foreach ($items as $item) {
        $f_id  = (!empty($item['id'])) ? (int)$item['id'] : "NULL";
        $nama  = mysqli_real_escape_string($conn, $item['nama']);
        $harga = (int)$item['harga'];
        
        $queryDetail = "INSERT INTO transaksi_detail (transaksi_id, furniture_id, nama_item, harga) 
                        VALUES ($txId, $f_id, '$nama', $harga)";
        
        if (!mysqli_query($conn, $queryDetail)) {
            throw new Exception(mysqli_error($conn));
        }
    }

    mysqli_commit($conn);
    echo json_encode(['status' => 'success', 'id' => $txId]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>