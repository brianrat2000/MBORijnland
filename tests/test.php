<?php

class test
{
    public function add($a, $b){
        return $a + $b;
    }

    public function testAdd(){
        $result = $this->add(5, 9);

        dump($result);
    }
}