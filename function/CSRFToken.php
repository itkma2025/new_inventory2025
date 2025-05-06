<?php
class CSRFToken {
    private $token;
    public function generateToken() {
        $this->token = bin2hex(random_bytes(32));
        return $this->token;
    }

    public function getToken() {
        return $this->token;
    }
}
?>
