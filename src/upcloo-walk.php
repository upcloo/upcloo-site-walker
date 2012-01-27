<?php
/** 
 * Copyright (C) 2012 Corley S.R.L.
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
            __DIR__,
            realpath(__DIR__ . '/../vendor/zf2/library'),
            realpath(__DIR__ . '/../vendor/php-color-console/src'),
            realpath(__DIR__ . '/../vendor/phly/Phly_PubSub/library'),
            get_include_path()
        )
    )
);

require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new \Zend\Loader\StandardAutoloader();
$loader->registerNamespace("Zend", __DIR__ . '/../vendor/zf2/library/Zend');
$loader->registerNamespace("Wally", __DIR__ . '/../vendor/php-color-console/src/Wally');
$loader->registerNamespace("phly", __DIR__ . '/../vendor/phly/Phly_PubSub/library/phly');
$loader->registerNamespace("Walk", __DIR__ . '/Walk');
$loader->register();

use Walk\Setting;

$writer = new Walk\Writer();
\phly\PubSub::subscribe(Setting::RED_CONSOLE_TOPIC, $writer, 'printRedLine');
\phly\PubSub::subscribe(Setting::GREEN_CONSOLE_TOPIC, $writer, 'printGreenLine');
\phly\PubSub::subscribe(Setting::CONSOLE_TOPIC, $writer, 'printLine');

$opts = array(
	'site|domain|d|s=s'    => 'Set the site to walk [mandatory]',
	'sitekey|k=s'          => 'Sitekey for the site [mandatory]',
	'output|o=s'           => 'Set the output directory [optional], if missing this folder will be set.',
	'sitemap|m'	           => 'Use sitemap strategy during your walk...'
);

$console = \Wally\Console\Console::getInstance();

try {
    $optsConsole = new \Zend\Console\Getopt($opts);
    $site = $optsConsole->getOption("s");
    $sitekey = $optsConsole->getOption("k");
    $outputDirectory = $optsConsole->getOption("o");
    
    //Mandatory parameter
    if (!$site || !$sitekey) {
        throw new \Zend\Console\Exception\RuntimeException("ASG", $optsConsole->getUsageMessage());
    }
   
    if (!$outputDirectory) {
        $outputDirectory = __DIR__ . "/../files";
    }
    
    if (!file_exists($outputDirectory)) {
        echo "Directory {$outputDirectory} not exits, I create it..." . PHP_EOL;
        if (!mkdir($outputDirectory)) {
            echo $console->red("Unable to create the output directory on path: {$outputDirectory}") . PHP_EOL;
            exit;
        }
    } else {
        echo $console->red("WARNING, the output folder already exists may be not clean...") . PHP_EOL;
    }
    
    echo "Start walking on " . $console->yellow($site) . " with sitekey " .$console->yellow($sitekey) .".". PHP_EOL;
    echo "Output folder: " . $console->yellow($outputDirectory) . PHP_EOL;
    
    $manager = \Walk\Site::getInstance();
    $manager->setSite($site);
    $manager->setSiteKey($sitekey);
    $manager->setOutputDirectory($outputDirectory);
    
    if ($optsConsole->getOption("m")) {
        $manager->setWalkMethod(new \Walk\Strategy\Sitemap());
    } else {
        $manager->setWalkMethod(new \Walk\Strategy\Crawler());
    }
    
    echo "Sleep for five seconds before start..." . PHP_EOL;
    sleep(5);
    $manager->walk();
} catch (\Zend\Console\Exception $e) {
    echo $console->red("Please check your arguments" . PHP_EOL);
    echo $e->getUsageMessage();
    exit;
}