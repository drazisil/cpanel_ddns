<?php
Class CpanelDDNSTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testCanReadConfig()
    {
        // Arrange
        $cp_ddns = new CpanelDDNS\CpanelDDNS(1);

        // Act
        $config = $a->fetchConfig();

        // Assert
        $this->assertEquals(-1, $config['domain']);
    }

    // ...
}