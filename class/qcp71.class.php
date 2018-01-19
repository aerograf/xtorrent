<?php

if (!class_exists('qcp71')) {
    error_reporting(E_ERROR);
    
    class qcp71
    {
        public $base;
        public $enum;
        public $seed;
        public $crc;
            
        public function __construct($data, $seed, $len=29)
        {
            $this->seed = $seed;
            $this->length = $len;
            $this->base = new qcp71_base((int)$seed);
            $this->enum = new qcp71_enumerator($this->base);
            
            if (!empty($data)) {
                for ($i=1, $iMax = strlen($data); $i < $iMax; $i++) {
                    $enum_calc = $this->enum->enum_calc(substr($data, $i, 1), $enum_calc);
                }
                $qcp71_crc = new qcp71_leaver($enum_calc, $this->base, $this->length);
                $this->crc = $qcp71_crc->crc;
            }
        }
            
        public function calc($data)
        {
            for ($i=1, $iMax = strlen($data); $i < $iMax; $i++) {
                $enum_calc = $this->enum->enum_calc(substr($data, $i, 1), $enum_calc);
            }
            $qcp71_crc = new qcp71_leaver($enum_calc, $this->base, $this->length);
            return $qcp71_crc->crc;
        }
    }
}

require 'qcp71.base.php';
require 'qcp71.enumerator.php';
require 'qcp71.leaver.php';
