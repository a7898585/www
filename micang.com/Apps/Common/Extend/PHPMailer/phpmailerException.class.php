<?php

namespace Common\Extend\PHPMailer;
class phpmailerException extends \Exception {
    public function errorMessage() {
        $errorMsg = '<strong>' . $this->getMessage () . "</strong><br />\n";
        return $errorMsg;
    }
}

?>