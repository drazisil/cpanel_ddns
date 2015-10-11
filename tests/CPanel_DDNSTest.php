<?php
require "lib/Drazisil/CPanel_DDNS/CPanel_DDNS.php";

namespace Drazisil\CPanel_DDNS;

class CPanelDDNSTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testCanReadConfig()
    {
        // Arrange
        $cp_ddns = new \Drazisil\CPanel_DDNS(1);

        // Act
        $config = $a->fetchConfig();

        // Assert
        $this->assertEquals(-1, $config['domain']);
    }

    // ...
}
