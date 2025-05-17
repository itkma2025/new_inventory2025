<?php  
    require_once "../../akses.php";
    require_once "../../function/function-enkripsi.php";
    require_once "../../function/uuid.php";
    $key = "Driver2024?";

    if(isset($_POST['diterima'])){
        $location = htmlspecialchars(decrypt($_POST['location'], $key));
        $date_now = date('d_m_Y');
        $datetime = date('Y-m-d H:i:s');
        $id_bukti_terima = htmlspecialchars($_POST['id_bukti_terima']);
        $id_inv_penerima_revisi = htmlspecialchars($_POST['id_inv_penerima']);   
        $id_inv = htmlspecialchars($_POST['id_inv']);   
        $id_inv_decrypt = decrypt($id_inv, $key);
        $id_komplain = htmlspecialchars($_POST['id_komplain']);   
        $id_komplain_decrypt = decrypt($id_komplain, $key); 
        $id_spk = htmlspecialchars($_POST['id_spk']);   
        $id_spk_decrypt = decrypt($id_spk, $key);
        $alamat = htmlspecialchars(decrypt($_POST['alamat'], $key)); 
        $diterima_oleh = htmlspecialchars($_POST['diterima_oleh']);
        $nama_penerima = htmlspecialchars($_POST['nama_penerima']);
        $tgl = htmlspecialchars($_POST['tgl']);
        $id_ekspedisi = htmlspecialchars($_POST['id_ekspedisi']);
        $resi = htmlspecialchars($_POST['resi']);
        $jenis_ongkir = htmlspecialchars($_POST['jenis_ongkir']);
        $ongkir = str_replace(',', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
        $ongkir = intval($ongkir); // Mengubah string harga menjadi integer
        $image = $_POST['image'];
        $nama_driver =  decrypt($_SESSION['tiket_nama'], $key_global);
        $nama_driver = str_replace(' ', '_', $nama_driver);
        // Decode base64 image
        $base64 = str_replace('data:image/png;base64,', '', $image);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);
        $day = date('d');
        $month = date('m');
        $year = date('Y');
        $fileName = $month . $year . uuid() .  $day;
        $fileNameEncrypt = encrypt($fileName, $key);
        $newFileName = 'IMG_' . $date_now . $fileNameEncrypt . '.png';

        if($diterima_oleh == 'Customer'){
            // Memulai transaksi
            mysqli_begin_transaction($connect); 

            try {
                // Cek apakah data sudah ada di inv_bukti_terima
                $cek_bukti = mysqli_query($connect, "SELECT id_komplain FROM inv_bukti_terima_revisi WHERE id_komplain = '$id_komplain_decrypt'");
                if (mysqli_num_rows($cek_bukti) > 0) {
                    // Jika data sudah ada, lakukan update
                    $bukti_terima = mysqli_query($connect, "UPDATE inv_bukti_terima_revisi 
                                                            SET bukti_satu = '$newFileName', lokasi = '$location', approval = '0', created_date = '$datetime', created_by = '$id_user'
                                                            WHERE id_komplain = '$id_komplain_decrypt'");
                } else {
                    // Jika data belum ada, lakukan insert
                    $bukti_terima = mysqli_query($connect, "INSERT INTO inv_bukti_terima_revisi (id_bukti_terima, id_komplain, bukti_satu, lokasi, created_date, created_by) 
                                                            VALUES ('$id_bukti_terima', '$id_komplain_decrypt', '$newFileName', '$location', '$datetime', '$id_user')");
                }

                // Cek apakah data sudah ada di inv_penerima
                $cek_penerima = mysqli_query($connect, "SELECT id_komplain FROM inv_penerima_revisi WHERE id_komplain = '$id_komplain_decrypt'");
                if (mysqli_num_rows($cek_penerima) > 0) {
                    // Jika data sudah ada, lakukan update
                    $query_diterima = mysqli_query($connect, "UPDATE inv_penerima_revisi 
                                                            SET nama_penerima = '$nama_penerima', alamat = '$alamat', tgl_terima = '$tgl' 
                                                            WHERE id_komplain = '$id_komplain_decrypt'");
                } else {
                    // Jika data belum ada, lakukan insert
                    $query_diterima = mysqli_query($connect, "INSERT INTO inv_penerima_revisi (id_inv_penerima_revisi, id_komplain, nama_penerima, alamat, tgl_terima) 
                                                            VALUES ('$id_inv_penerima_revisi', '$id_komplain_decrypt', '$nama_penerima', '$alamat', '$tgl')");
                }
                
                $id_inv_substr = $id_inv_decrypt;
                $inv_id = substr($id_inv_substr, 0, 3);
                $query_update_inv = "";
                if ($inv_id = "NON"){
                    $query_update_inv = mysqli_query($connect, "UPDATE inv_nonppn SET status_transaksi = 'Komplain Diterima' WHERE id_inv_nonppn = '$id_inv_decrypt'");
                } else if ($inv_id = "PPN"){
                    $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain Diterima' WHERE id_inv_ppn = '$id_inv_decrypt'");
                } else if ($inv_id = "BUM"){
                    $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain Diterima' WHERE id_inv_bum = '$id_inv_decrypt'");
                }

                $query_update_status = mysqli_query($connect, "UPDATE revisi_status_kirim SET jenis_penerima = 'Customer' WHERE id_komplain = '$id_komplain_decrypt'");

                $query_inv_revisi = mysqli_query($connect, "UPDATE inv_revisi SET status_pengiriman = '1', status_trx_komplain = '0' WHERE id_inv = '$id_inv_decrypt'");

                // Proses penyimpanan gambar
                // Create ../../gambar-revisi/bukti_kirim/ folder if it doesn't exist
                $baseDir = '../../gambar-revisi/bukti_kirim/';
                if (!file_exists($baseDir)) {
                    mkdir($baseDir, 0755, true); // Create base directory with permissions 755
                }

                // Create nama_driver folder inside ../../gambar-revisi/bukti_kirim/ if it doesn't exist
                $driverFolder = $baseDir . $nama_driver; 
                if (!file_exists($driverFolder)) {
                    mkdir($driverFolder, 0755, true); // Create nama_driver folder with permissions 755
                }

                // Generate file path
                $filePath = $driverFolder . '/' . $newFileName;

                // Simpan gambar Revisi
                $gambar_disimpan = file_put_contents($filePath, $data);

                if (!$bukti_terima && !$query_diterima && !$query_update_inv && $query_update_status && $query_inv_revisi && !$gambar_disimpan) {
                    throw new Exception("Gagal Proses");
                }
                // Jika semua query dan proses penyimpanan gambar berhasil, commit transaksi
                mysqli_commit($connect);
                $_SESSION['info'] = "Disimpan";
                header("Location:../list-invoice-revisi.php", true, 303);


            } catch (Exception $e) {
                // Jika terjadi pengecualian (exception), rollback transaksi
                mysqli_rollback($connect);
                $_SESSION['info'] = "Data Gagal Disimpan";
                echo "Error: " . $e->getMessage(); // hindari tampilkan error ke user di production
                // header("Location:../list-invoice-revisi.php", true, 303);
                exit;
            } finally {
                // Selalu akhiri koneksi
                mysqli_close($connect);
            }
        } else if($diterima_oleh == 'Ekspedisi'){
            // Memulai transaksi
            mysqli_begin_transaction($connect);

            try {
                // Cek apakah data sudah ada di inv_bukti_terima_revisi
                $query = mysqli_query($connect, "SELECT * FROM inv_bukti_terima_revisi WHERE id_bukti_terima = '$id_bukti_terima' AND id_komplain = '$id_komplain_decrypt'");
                $data_bukti_terima  = mysqli_fetch_assoc($query);
                if ($data_bukti_terima ) {
                    // Data sudah ada, update saja
                    $bukti_terima = mysqli_query($connect, "UPDATE inv_bukti_terima_revisi SET bukti_satu = '$newFileName' WHERE id_bukti_terima = '$id_bukti_terima' AND id_komplain = '$id_komplain_decrypt'");
                } else {
                    // Data tidak ada, insert baru
                    $bukti_terima = mysqli_query($connect, "INSERT INTO inv_bukti_terima_revisi (id_bukti_terima, id_komplain, bukti_satu) VALUES ('$id_bukti_terima', '$id_komplain_decrypt', '$newFileName')");
                }

                $update_revisi_status_kirim = mysqli_query($connect, "UPDATE revisi_status_kirim SET jenis_penerima = 'Ekspedisi', dikirim_ekspedisi = '$id_ekspedisi', no_resi = '$resi', jenis_ongkir = '$jenis_ongkir', ongkir = '$ongkir' WHERE id_komplain = '$id_komplain_decrypt'");
        
                // Proses penyimpanan gambar
                // Create ../../gambar-revisi/bukti_kirim/ folder if it doesn't exist
                $baseDir = '../../gambar-revisi/bukti_kirim/';
                if (!file_exists($baseDir)) {
                    mkdir($baseDir, 0755, true); // Create base directory with permissions 755
                }

                // Create nama_driver folder inside ../../gambar-revisi/bukti_kirim/ if it doesn't exist
                $driverFolder = $baseDir . $nama_driver; 
                if (!file_exists($driverFolder)) {
                    mkdir($driverFolder, 0755, true); // Create nama_driver folder with permissions 755
                }

                // Generate file path
                $filePath = $driverFolder . '/' . $newFileName;

                // Simpan gambar Revisi
                $gambar_disimpan = file_put_contents($filePath, $data);

                if (!$bukti_terima && !$update_revisi_status_kirim && !$gambar_disimpan) {
                    throw new Exception("Gagal Proses");
                }
                // Jika semua query dan proses penyimpanan gambar berhasil, commit transaksi
                mysqli_commit($connect);
                $_SESSION['info'] = "Disimpan";
                header("Location:../list-invoice-revisi.php", true, 303);

            } catch (Exception $e) {
                // Jika terjadi pengecualian (exception), rollback transaksi
                mysqli_rollback($connect);
                $_SESSION['info'] = "Data Gagal Disimpan";
                echo $e->getMessage();
                //  header("Location:../list-invoice-revisi.php", true, 303);
                exit;
            } finally {
                // Selalu akhiri koneksi
                mysqli_close($connect);
            }
        }
    }
?>