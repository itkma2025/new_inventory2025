<?php
    // Query berada di nav header
    include "query/notifikasi.php";
    // Inisialisasi counter
    $status_notif_0 = 0;
    $status_notif_1 = 0;

    // Loop melalui hasil query
    if ($query2->num_rows > 0) {
        while($data = $query2->fetch_assoc()) {
            if ($data['status_notif'] == 0) {
                if ($role == "Super Admin"  || $role == "Admin Penjualan" || $role == "Manager Gudang") { 
                    ?>
                        <script>
                            // Periksa apakah browser mendukung notifikasi
                            if ('Notification' in window) {
                                // Meminta izin untuk menampilkan notifikasi
                                Notification.requestPermission().then(function (permission) {
                                    // Jika izin diberikan
                                    if (permission === 'granted') {
                                        // Periksa apakah notifikasi telah ditampilkan sebelumnya dalam sesi
                                        var status_tampil = sessionStorage.getItem('<?php echo $data['no_spk'] ?>_status_tampil_notif');
                                        if (!status_tampil) {
                                            
                                            // Membuat objek notifikasi
                                            var notification = new Notification('<?php echo $data['no_spk'] ?>', {
                                                body: 'Pesanan <?php echo $data['nama_cs'] ?> Sudah Siap Kirim.',
                                                icon: 'assets/img/logo-notif.png', // Ganti dengan URL gambar ikon notifikasi
                                            });
                                            // Event listener untuk menangkap klik pada notifikasi
                                            notification.onclick = function() {
                                                // Arahkan pengguna ke halaman spk-siap-kirim.php?sort=baru
                                                window.location.href = 'detail-produk-spk-reg-siap-kirim.php?id=<?php echo base64_encode($data['id_spk_reg']) ?>&status=1';
                                                
                                                // Menutup notifikasi
                                                notification.close();
                                            };

                                            // Simpan status tampil ke dalam session storage
                                            var status_tampil = sessionStorage.setItem('<?php echo $data['no_spk'] ?>_status_tampil_notif', 1);
                                        }
                                    }
                                });
                            } else {
                                console.log('Browser tidak mendukung notifikasi.');
                            }
                        </script>
                    <?php 
                }
            } 
        }
    } 
?>
