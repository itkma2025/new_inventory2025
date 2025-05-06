<?php
    include 'koneksi.php'; // Pastikan koneksi database sudah terhubung

    // Query untuk mendapatkan daftar URL website yang diakses user
    $query = "
        SELECT w.url_website
        FROM user_akses ua
        LEFT JOIN website_management w ON ua.id_website = w.id_website
        WHERE ua.id_user = '$id_user' AND w.url_website != '$current_domain'
    ";
    
    $result = mysqli_query($koneksi2, $query);
    
    // Array untuk menyimpan URL yang diakses
    $url_accessed = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Pemetaan gambar berdasarkan URL
            switch ($row['url_website']) {
                case 'https://localhost:8083':
                    $row['icon'] = 'http://localhost:8083//assets/images/domain/SSO.png';
                    $row['name'] = 'IT';
                    break;
                case 'https://test-bertamed.mandirialkesindo.co.id':
                    $row['icon'] = 'http://test-bertamed/assets/images/domain/BUM.png';
                    $row['name'] = 'BUM';
                    break;
                case 'http://localhost:8082':
                    $row['icon'] = 'http://localhost:8082/assets/images/domain/ECAT.png';
                    $row['name'] = 'ECAT';
                    break;
                case 'http://localhost:8080':
                    $row['icon'] = 'http://localhost:8080/assets/img/KMA.png';
                    $row['name'] = 'Karsa';
                    break;
                case 'https://localhost/test-inventory2026':
                    $row['icon'] = 'http://localhost/test-inventory/assets/img/KMA.png';
                    $row['name'] = 'Karsa';
                    break;
                default:
                    $row['icon'] = null;
                    $row['name'] = null; 
                    break;
            }
    
            if ($row['icon'] !== null) {
                $url_accessed[] = $row; // Menyimpan hasil query dengan ikon yang sesuai
            }
        }
    }
?>
<?php if (count($url_accessed) === 1): ?>
    <!-- Tampilkan hanya tag li pertama -->
    <?php $website = $url_accessed[0]; ?>
    <li class="nav-item lh-1 me-3 mt-1">
        <a href="<?= htmlspecialchars($website['url_website']) ?>" target="_blank">
            <img src="<?= htmlspecialchars($website['icon']) ?>" class="rounded-circle border" style="width: 40px; height: 40px; border: 2px solid #007bff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
        </a>
    </li>
<?php elseif (count($url_accessed) > 1): ?>
    <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-grid-3x3-gap"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow p-2 dropdown-custom">
            <div class="dropdown-shortcuts-list scrollable-container ps">
                <div class="p-1">
                    <div class="row row-bordered overflow-visible g-0">
                        <?php foreach ($url_accessed as $website): ?>
                            <div class="col text-center" style="border: 0.5px solid #ddd; padding: 8px; width: 50px;">
                                <a class="dropdown-icon-item" href="<?= htmlspecialchars($website['url_website']) ?>" target="_blank" style="display: flex; flex-direction: column; align-items: center; text-decoration: none;">
                                    <img src="<?= htmlspecialchars($website['icon']) ?>" alt="<?= htmlspecialchars($website['name']) ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                                    <span style="margin-top: 5px; color: black;"><?= htmlspecialchars($website['name']) ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </li>
<?php endif; ?>