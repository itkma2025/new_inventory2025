<?php  
    function roleSuperAdmin($role) {
        return in_array($role, ["Super Admin"]);
    }

    function roleAdminPenjualan($role) {
        return in_array($role, ["Admin Penjualan"]);
    }

    function roleAdminGudang($role) {
        return in_array($role, ["Admin Gudang"]);
    }

    function roleDriver($role) {
        return in_array($role, ["Driver"]);
    }

    function roleOperatorGudang($role) {
        return in_array($role, ["Operator Gudang"]);
    }

    function rolePimpinan($role) {
        return in_array($role, ["Pimpinan"]);
    }

    function roleManagerGudang($role) {
        return in_array($role, ["Manager Gudang"]);
    }

    function roleFinance($role) {
        return in_array($role, ["Finance"]);
    }

    // Akses super admin
    function settingRoleSuperAdmin($role) {
        if (roleSuperAdmin($role)) {
            return "true"; // Class untuk Super Admin
        } else {
            return "d-none"; // Class default untuk role lain
        }
    }

    // Pengaturan role akses (Super Admin, Admin Penjualan dan Manager Gudang)
    function settingRoleSatu($role) {
        if (roleSuperAdmin($role)) {
            return "true"; // Class untuk Super Admin
        } else if (roleAdminPenjualan($role)) {
            return "true"; // Class untuk Admin Penjualan
        } else if (roleManagerGudang($role)) {
            return "true"; // Class untuk Admin Gudang
        } else {
            return "d-none"; // Class default untuk role lain
        }
    }

    function settingRoleSatuDisabled($role) {
        if (roleSuperAdmin($role)) {
            return "enabled"; // Class untuk Super Admin
        } else if (roleAdminPenjualan($role)) {
            return "enabled"; // Class untuk Admin Penjualan
        } else if (roleManagerGudang($role)) {
            return "enabled"; // Class untuk Admin Gudang
        } else {
            return "disabled"; // Class default untuk role lain
        }
    }

    // Pengaturan role akses (Super Admin dan Manager Gudang)
    function settingRoleDua($role) {
        if (roleSuperAdmin($role)) {
            return "true"; // Class untuk Super Admin
        } else if (roleManagerGudang($role)) {
            return "true"; // Class untuk Admin Gudang
        } else {
            return "d-none"; // Class default untuk role lain
        }
    }

?>

<!-- 'Super Admin' 
    'Admin Penjualan'
    'Admin Gudang' 
    'Driver'
    'Operator Gudang'
    'Pimpinan'
    'Manager Gudang'
    'Finance' -->