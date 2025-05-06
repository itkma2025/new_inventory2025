<div class="col">
    <div class="p-3">
        <label>Filter Sesuai Periode :</label><br>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
            <?php
                // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                $selectedOption = isset($_GET['date_range']) ? $_GET['date_range'] : 'today';
                if ($selectedOption === "today") {
                    echo "Hari ini";
                } elseif ($selectedOption === "weekly") {
                    echo "Minggu ini";
                } elseif ($selectedOption === "monthly") {
                    echo "Bulan ini";
                } elseif ($selectedOption === "lastMonth") {
                    echo "Bulan Kemarin";
                } elseif ($selectedOption === "year") {
                    echo "Tahun ini";
                } elseif ($selectedOption === "lastyear") {
                    echo "Tahun Lalu";
                } else {
                    echo "Pilih Tanggal";
                }
            ?>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?date_range=today">Hari ini</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?date_range=weekly">Minggu ini</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?date_range=monthly">Bulan ini</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?date_range=lastMonth">Bulan Kemarin</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?date_range=year">Tahun ini</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?date_range=lastyear">Tahun Lalu</a>
                    <a class="custom-dropdown-item dropdown-item rounded <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal') ? 'active' : ''; ?>">Pilih Tanggal</a>

                </form>
                <li><hr class="dropdown-divider"></li>
                <form action="" method="GET" class="form-group newsletter-group" id="dateForm">
                    <div class="row p-2">
                        <div class="col-md-6 mb-3">
                            <label for="startDate">From</label>
                            <input type="date" id="startDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endDate">To</label>
                            <input type="date" id="endDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="end_date">
                        </div>
                        <input type="hidden" name="date_range" value="pilihTanggal">
                        <input type="hidden" name="cs" value="<?php echo base64_encode($id_cs) ?>">
                    </div>
                    
                    <!-- Add the submit button with name="tampilkan" -->
                    <a href="list-tagihan-pembelian.php?date_range=year" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const endDateInput = document.getElementById('endDate');
                        const startDateInput = document.getElementById('startDate');
                        const dateForm = document.getElementById('dateForm');
                        const resetLink = document.getElementById('resetLink');

                        // Cek apakah data tanggal tersimpan di localStorage
                        const savedStartDate = localStorage.getItem('startDate');
                        const savedEndDate = localStorage.getItem('endDate');

                        if (savedStartDate) {
                            startDateInput.value = savedStartDate;
                        }

                        if (savedEndDate) {
                            endDateInput.value = savedEndDate;
                        }

                        startDateInput.addEventListener('change', () => {
                            const startDateValue = new Date(startDateInput.value);
                            const maxEndDateValue = new Date(startDateValue);
                            maxEndDateValue.setDate(maxEndDateValue.getDate() + 30);

                            endDateInput.value = ''; // Reset nilai endDate

                            endDateInput.min = startDateValue.toISOString().split('T')[0];
                            endDateInput.max = maxEndDateValue.toISOString().split('T')[0];

                            endDateInput.disabled = false; // Aktifkan kembali input endDate
                        });

                        endDateInput.addEventListener('change', () => {
                            const startDateValue = new Date(startDateInput.value);
                            const endDateValue = new Date(endDateInput.value);

                            const daysDifference = Math.floor((endDateValue - startDateValue) / (1000 * 60 * 60 * 24));

                            if (daysDifference > 30) {
                                endDateInput.value = '';
                            }

                            startDateInput.value = startDateValue.toISOString().split('T')[0]; // Menampilkan pada field startDate
                            endDateInput.value = endDateValue.toISOString().split('T')[0]; // Menampilkan pada field endDate

                            const queryParams = new URLSearchParams({
                                start_date: startDateValue.toISOString().split('T')[0],
                                end_date: endDateValue.toISOString().split('T')[0],
                                date_range: 'pilihTanggal'
                                // isi kode php jika filter berada di dalam detail yang memiliki get id misal id_cs
                                // cs: ''
                            });

                            const newUrl = `list-tagihan-pembelian.php.php?${queryParams.toString()}`;

                            dateForm.action = newUrl;
                            dateForm.submit();

                            // Simpan tanggal ke localStorage
                            localStorage.setItem('startDate', startDateInput.value);
                            localStorage.setItem('endDate', endDateInput.value);
                        });

                        resetLink.addEventListener('click', () => {
                            // Hapus data dari localStorage
                            localStorage.removeItem('startDate');
                            localStorage.removeItem('endDate');

                            // Hapus nilai dari field input
                            startDateInput.value = '';
                            endDateInput.value = '';
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>