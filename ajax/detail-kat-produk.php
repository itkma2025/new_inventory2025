<?php
include '../akses.php'; // Pastikan koneksi database tersedia
require_once __DIR__ . "/../function/function-enkripsi.php";

if (isset($_POST['id'])) {
    $id_kat_produk = decrypt($_POST['id'], $key_global); // Dekripsi ID

    $sql_kat_produk = $connect->query("SELECT 
                                tkp.id_kat_produk,
                                tkp.nama_kategori,
                                tkp.no_izin_edar,
                                tkp.tgl_terbit,
                                tkp.berlaku_sampai,
                                DATE_FORMAT(STR_TO_DATE(tkp.berlaku_sampai, '%d/%m/%Y'), '%Y-%m-%d') AS tanggal_berlaku_sampai,
                                tkp.file_nie,
                                tkp.created_date,
                                tkp.updated_date,
                                mr.nama_merk,
                                uc.nama_user AS created, 
                                uu.nama_user AS updated
                            FROM 
                                tb_kat_produk AS tkp
                            LEFT JOIN tb_merk AS mr ON tkp.id_merk = mr.id_merk
                            LEFT JOIN $database2.user AS uc ON (tkp.created_by = uc.id_user)
                            LEFT JOIN $database2.user AS uu ON (tkp.updated_by = uu.id_user)
                            WHERE 
                                tkp.id_kat_produk = '$id_kat_produk'
                            ORDER BY 
                                tkp.nama_kategori ASC
                            ");
    $data = mysqli_fetch_assoc($sql_kat_produk);
    $file_nie = $data['file_nie'];
    $nama_kategori = $data['nama_kategori'];
    $_SESSION['data_file_nie'] = $file_nie;
    $_SESSION['data_nama_kategori'] = $nama_kategori;

    if ($file_nie != '') {
        $view_file = '<a class="btn btn-primary btn-sm" data-fancybox data-src="#pdf-container" href="javascript:;"> Lihat File NIE</a>';
    } else {
        $view_file = '<span class="text-danger">File NPWP tidak ada</span>';
    }
    

    if ($data) {
        ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th class="text-bold col-4">Nama Kategori</th>
                        <td><?php echo htmlspecialchars($data['nama_kategori']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">No Izin Edar</th>
                        <td><?php echo htmlspecialchars($data['no_izin_edar']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">File No Izin Edar</th>
                        <td><!-- FancyBox Lightbox -->
                            <!-- Tombol untuk membuka PDF -->
                            <?php echo $view_file ?>
                            <!-- Kontainer PDF -->
                            <div style="display: none;">
                                <div id="pdf-container">
                                    <embed id="pdf-embed" type="application/pdf" style="width:100%; height:600px;">
                                    <script>
                                        fetch("view-nie.php")
                                            .then(response => response.blob())
                                            .then(blob => {
                                                const url = URL.createObjectURL(blob);
                                                document.getElementById("pdf-embed").src = url;
                                            })
                                            .catch(error => console.error("Gagal memuat PDF:", error));
                                    </script>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Tanggal Terbit</th>
                        <td><?php echo htmlspecialchars($data['tgl_terbit']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Berlaku Sampai</th>
                        <td><?php echo htmlspecialchars($data['berlaku_sampai']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Merk</th>
                        <td><?php echo htmlspecialchars($data['nama_merk']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Dibuat Tanggal</th>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($data['created_date']))); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Dibuat Oleh</th>
                        <td><?php echo $data['created']; ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Diubah Tangal</th>
                        <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($data['updated_date']))); ?></td>
                    </tr>
                    <tr>
                        <th class="text-bold col-4">Diubah Oleh</th>
                        <td><?php echo $data['updated']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger text-center'>Data tidak ditemukan.</div>";
    }
}
?>