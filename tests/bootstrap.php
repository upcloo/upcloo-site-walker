<?php 

set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            __DIR__,
            realpath(__DIR__ . '/../src'),
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
$loader->registerNamespace("Walk", __DIR__ . '/../src/Walk');
$loader->register();

class WriterMock
{
    public function emptyStub() { }
}
$writer = new WriterMock();
\phly\PubSub::subscribe(\Walk\Setting::RED_CONSOLE_TOPIC, $writer, 'emptyStub');
\phly\PubSub::subscribe(\Walk\Setting::GREEN_CONSOLE_TOPIC, $writer, 'emptyStub');
\phly\PubSub::subscribe(\Walk\Setting::CONSOLE_TOPIC, $writer, 'emptyStub');

//Boot ends
