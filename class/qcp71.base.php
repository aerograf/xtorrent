<?php

if (!class_exists('qcp71_base')) {
    error_reporting(0);
    
    class qcp71_base extends qcp71
    {
        public $base;
        public $seed;
        public $mode;
        public $roll;
        public $num_evr;
        
        public function __construct($seed = 127)
        {
            if ($seed<1) {
                $this->seed = 1;
            } elseif ($seed>255) {
                $this->seed = 256;
            } else {
                $this->seed = $seed;
            }
            $base = $this->_set_base();
            return $this->get_base();
        }
        
        private function _set_base()
        {
            if ($this->seed < 65) {
                $case=true;
            } else {
                $case=false;
            }
            
            $this->roll = ($this->seed / (3+(1/6)));
            $this->num_evr = floor((34.32 / ($this->roll/$this->seed))/($this->seed*($this->roll/17.8)));
            
            if ($this->roll<16) {
                $this->mode = '1';
            } elseif ($this->roll >15 && $this->roll<32) {
                $this->mode = '2';
            } elseif ($this->roll >32 && $this->roll<48) {
                $this->mode = '3';
            } elseif ($this->roll >48) {
                $this->mode = '4';
            }
            
            if ($this->num_evr==0) {
                $this->num_evr = floor(($this->seed / $this->mode) / ($this->mode * 3.015));
            } elseif ($this->num_evr>8) {
                $this->num_evr = $this->num_evr - floor($this->mode*1.35);
            }
                
            
                        
            $this->base = [];
            switch ($this->mode) {
            case 1:
                $ii = 0;
                $num = 0;
                $letter = 'a';
                for ($qcb=1;$qcb<32;$qcb++) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=64;$qcb>31;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }
                break;
            case 2:
                $ii     = 0;
                $num    = 0;
                $letter = 'a';
                for ($qcb=32;$qcb>0;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=32;$qcb<65;$qcb++) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }
                break;
            case 3:
                $ii     = 0;
                $num    = 0;
                $letter = 'a';
                for ($qcb=1;$qcb<17;$qcb++) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=64;$qcb>47;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=32;$qcb>16;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }
                
                
                for ($qcb=32;$qcb<48;$qcb++) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }
                break;
            case 4:
                $ii     = 0;
                $num    = 0;
                $letter = 'a';

                for ($qcb=17;$qcb>0;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=17;$qcb<49;$qcb++) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }

                for ($qcb=64;$qcb>48;$qcb--) {
                    $ii++;
                    $done = false;
                    if ($sofar == $this->num_evr) {
                        if ($num < 9) {
                            $this->base[$qcb] = $num;
                            $num++;
                            $sofar = 0;
                            $done = true;
                        }
                    } else {
                        $sofar++;
                    }
                    
                    if ($done == false) {
                        if (floor($qcb / ($this->roll/$this->num_evr))>$this->mode) {
                            switch ($case) {
                            case true:
                                $this->base[$qcb] = $letter;
                                break;
                            case false:
                                $this->base[$qcb] = strtoupper($letter);
                                break;
                            }
                        } else {
                            $this->base[$qcb] = $letter;
                        }
                        $letter++;
                        if (strlen($letter++)>1) {
                            $letter= 'a';
                        }
                    }
                }
                break;
            }
        }
        
        public function get_base()
        {
            return $this->base;
        }
        
        public function debug_base()
        {
            $base = [];
            foreach ($this->base as $key => $data) {
                $base[$key] = [
                    'char' => $data,
                    'ord'  => ord($data),
                    'bin'  => decbin(ord($data))];
            }
            
            return [
                'mode'    => $this->mode, 'roll' => $this->roll,
                'seed'    => $this->seed, 'mode' => $this->mode,
                'num_evr' => $this->num_evr, 'base' => $this->base,
                'debug'   => $base];
        }
    }
}
