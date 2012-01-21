<?php
/** 
 * Copyright (C) 2011 Walter Dal Mut, Gabriele Mittica
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(__DIR__ . '/../vendor/zf2/library'),
            realpath(__DIR__ . '/../vendor/php-color-console/src'),
            get_include_path()
        )
    )
);

require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new \Zend\Loader\StandardAutoloader();
$loader->registerNamespace("Zend", __DIR__ . '/../vendor/zf2/library/Zend');
$loader->registerNamespace("Wally", __DIR__ . '/../vendor/php-color-console/src/Wally');
$loader->register();

$opts = array(
	'site|domain|d|s=s'    => 'Set the site to walk [mandatory]',
	'sitekey|k=s' => 'Sitekey for the site [mandatory]',
	'output|o=s' => 'Set the output directory [optional]'
);

$console = \Wally\Console\Console::getInstance();

try {
    $optsConsole = new \Zend\Console\Getopt($opts);
    $site = $optsConsole->getOption("s");
    $sitekey = $optsConsole->getOption("k");
    
    //Mandatory parameter
    if (empty($site) || empty($sitekey)) {
        throw new \Zend\Console\Exception\RuntimeException("ASG", $optsConsole->getUsageMessage());
    }
    
    echo "Start walking on " . $console->green($site) . " with sitekey " .$console->green($sitekey) .".". PHP_EOL;

    
} catch (\Zend\Console\Exception $e) {
    echo $console->red("Please pass arguments" . PHP_EOL);
    echo $e->getUsageMessage();
    exit;
}