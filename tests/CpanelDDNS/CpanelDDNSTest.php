<?php
Class CpanelDDNSTest extends PHPUnit_Framework_TestCase
{
    // ...

    public function testCanReadConfig()
    {
        // Arrange
        $cp_ddns = new CpanelDDNS\CpanelDDNS(1);

        // Act
        $config =  $cp_ddns->fetchConfig();
        $config = json_decode($config, true);

        // Assert
        $this->assertEquals("moo", $config['domain']);
    }

    // ...
}
