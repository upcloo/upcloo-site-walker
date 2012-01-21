<?php 
namespace Walk;

class Writer
{
    public function printLine($line)
    {
        echo $line . PHP_EOL;
    }
    
    public function printGreenLine($line)
    {
        echo \Wally\Console\Console::getInstance()->green($line) . PHP_EOL;
    }
    
    public function printRedLine($line)
    {
        echo \Wally\Console\Console::getInstance()->red($line) . PHP_EOL;
    }
} 