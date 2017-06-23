<?php

namespace Home\Widget;

use Think\Controller;

class AdWidget extends Controller {

    public function _initialize() {
        
    }

    public function common() {
        $this->display('Widget:Ad/common');
    }

}

