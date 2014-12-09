<?php

function cout($msg)
{
    echo $msg;
}

class usuario extends Thread
{
    public $name = null;
    
    public function run()
    {
        $this->infinito();
    }
    
    public function infinito()
    {
        while(true)
        {
            cout($this->name);
            for($i=0; $i<10000000; $i++);
        }
    }
    
}

$alex = new usuario();
$alex->name='alex';
$jose = new usuario();
$jose->name='jose';


$alex->start();

$jose->start();


$alex->name='roberto';