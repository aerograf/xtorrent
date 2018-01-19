<?php

if (!class_exists('qcp71_leaver')) {
    error_reporting(E_ERROR);

    class qcp71_leaver extends qcp71
    {
        public $crc;

        public function __construct($enum_calc, $base, $len = 29)
        {
            @$this->crc = $this->calc_crc($enum_calc, $base, $len);
        }

        public function calc_crc($enum_calc, $base, $len)
        {
            for ($qi = 0; $qi < $len + 1; $qi++) {
                $da  = floor(9 * ($qi / $len));
                $pos = $this->GetPosition($enum_calc, $len, $qi);
                $pos = ceil($pos / ($len / ($qi - 1)));
                for ($v = -$qi; $v < $pos; $v++) {
                    if ($c > 64) {
                        $c = 0;
                    }

                    $c++;
                }
                if (strlen($base->base[$c]) > 1) {
                    $crc .= $da;
                } else {
                    $crc .= $base->base[$c];
                }

                if ($qi < ceil($len / 2)) {
                    $crc = $this->nux_cycle($crc, $enum_calc['result'], $len);
                    $crc = $this->nux_cycle($crc, $enum_calc['prince'], $len);
                } elseif ($qi < ceil(($len / 3) * 2)) {
                    $crc = $this->nux_cycle($crc, $enum_calc['motivation'], $len);
                    $crc = $this->nux_cycle($crc, $enum_calc['official'], $len);
                } else {
                    $crc = $this->nux_cycle($crc, $enum_calc['outsidecause'], $len);
                    $crc = $this->nux_cycle($crc, $enum_calc['karma'], $len);
                }
                $crc = $this->nux_cycle($crc, $enum_calc['yin'], $len);
            }

            $crc = $this->nux_cycle($crc, $enum_calc['result'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['prince'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['karma'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['motivation'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['official'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['outsidecause'], $len);
            $crc = $this->nux_cycle($crc, $enum_calc['yang'], $len);

            $crc = $this->nux_xor($crc, $enum_calc['nx_key']);

            for ($qi = 0; $qi < $len + 1; $qi++) {
                $da  = $len - floor(9 * ($qi / $len));
                $pos = ceil(ord($crc{$qi}) / 4);
                for ($v = -$qi; $v < $pos; $v++) {
                    if ($c > 64) {
                        $c = 0;
                    }

                    $c++;
                }
                if (strlen($base->base[$c]) > 1) {
                    $final_crc .= $da;
                } else {
                    $final_crc .= $base->base[$c];
                }
            }
            return $final_crc;
        }

        private function GetPosition($enum_calc, $len, $qi)
        {
            if ($enum_calc['yin'] > $enum_calc['yang']) {
                $cycle = floor((256 * ($enum_calc['yin'] / $enum_calc['yang'])) / (256 * ($enum_calc['yang'] / $enum_calc['yin']))) + ($len - $qi);
            } else {
                $cycle = ceil((256 * ($enum_calc['yang'] / $enum_calc['yin'])) / (256 * ($enum_calc['yin'] / $enum_calc['yang']))) + ($len - $qi);
            }

            $result       = $this->nuc_step($enum_calc['nuclear'], $enum_calc['result'], $cycle + $qi);
            $prince       = $this->nuc_step($enum_calc['nuclear'], $enum_calc['prince'], $cycle + $qi);
            $karma        = $this->nuc_step($enum_calc['nuclear'], $enum_calc['karma'], $cycle + $qi);
            $motivation   = $this->nuc_step($enum_calc['nuclear'], $enum_calc['motivation'], $cycle + $qi);
            $official     = $this->nuc_step($enum_calc['nuclear'], $enum_calc['official'], $cycle + $qi);
            $outsidecause = $this->nuc_step($enum_calc['nuclear'], $enum_calc['outsidecause'], $cycle + $qi);

            $char = decbin($result . $prince . $karma . $motivation . $official . $outsidecause);

            return ord($char);
        }

        private function nuc_step($nuclear, $var, $cycle)
        {
            $c = 1;
            for ($v = 0; $v < ($var + $cycle); $v++) {
                if ($c > strlen($nuclear)) {
                    $c = 0;
                }

                $c++;
            }
            return substr($nuclear, $c, 1);
        }

        private function nux_cycle($crc, $var, $len)
        {
            for ($v = 0; $v < ($var + 1); $v++) {
                for ($y = 1; $y < $len; $y++) {
                    $crc = substr($crc, $y, $len - $y) . substr($crc, 0, $len - ($len - $y));
                }
            }
            return $crc;
        }

        private function nux_xor($text_crc, $key)
        {
            for ($i = 0, $iMax = strlen($text_crc); $i < $iMax;) { // Dont need to increment here
                for ($j = 0, $jMax = strlen($key); $j < $jMax; $j++, $i++) {
                    $crc .= $text_crc{$i} ^ $key{$j};
                }
            }
            return $crc;
        }
    }
}
