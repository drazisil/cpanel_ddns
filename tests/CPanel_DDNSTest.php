<?php
namespace drazisil\CPanel_DDNS;

require __DIR__ . '/../vendor/autoload.php';

use drazisil\CPanel_DDNS\CPanel_DDNS;

class CPanel_DDNSTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testCanReadConfig()
    {
        // Arrange
        $cp_ddns = new CPanel_DDNS(1);

        // Act
        $config = $a->fetchConfig();

        // Assert
        $this->assertEquals(-1, $config['domain']);
    }

    // ...
}
