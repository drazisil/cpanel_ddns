<?php
namespace cpanel\cpanel_ddns\Tests;

Class CPanel_DDNSTest extends PHPUnit_Framework_TestCase
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
