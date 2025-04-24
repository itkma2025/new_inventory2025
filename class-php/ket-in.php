<?php
class KeteranganIn {
    private $connect;
    private $table_name = "keterangan_in";

    public function __construct($db) {
        $this->connect = $db;
    }

    public function read() {
        $query = "SELECT id_ket_in, ket_in FROM " . $this->table_name;
        $result = $this->connect->query($query);

        return $result;
    }
}
?>