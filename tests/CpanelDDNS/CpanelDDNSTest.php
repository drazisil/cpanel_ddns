<?php
Class CpanelDDNSTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->cddns = new CpanelDDNS\CpanelDDNS();
    }
    
    /**
     * Check if can read config file
     **/
    public function testCanReadConfig()
    {
        // Act
        $config =  $this->cddns->fetchConfig();

        // Assert
        $this->assertEquals("moo", $config['domain']);
    }

    /**
     * Test ACL mode defaults to to single ip
     */
    public function testIsAclDefaultModeSingle()
    {
        $this->cddns->defaultAclMode();

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('single', $aclMode);
    }

    /**
     * Test if able to set ACL mode to single ip
     */
    public function testSetAclModeSingle()
    {
        $this->cddns->defaultAclMode();

        $this->cddns->setAclMode('single');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('single', $aclMode);
    }

    /**
     * @depends testSetAclModeSingle
     */
    public function testInACLSingleIpValid()
    {
        // Act
        $config =  $this->cddns->checkIpAcl('192.168.5.1');

        // Assert
        $this->assertEquals("moo", $config['domain']);
    }

    
    /**
     * @depends testSetAclModeSingle
     */
    public function testInACLSingleIpInvalid()
    {
        // Act
        $config =  $this->cddns->checkIpAcl('192.168.5.0');

        // Assert
        $this->assertEquals("moo", $config['domain']);
    }

    /**
     * Check if can set ACL mode to multipule ips
     **/
    public function testSetAclModeMulti()
    {
        $this->cddns->defaultAclMode();

        $this->cddns->setAclMode('multi');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('multi', $aclMode);
    }

    /**
     * Check if can set ACL mode to range of ips
     **/
    public function testSetAclModeRange()
    {
        $this->cddns->defaultAclMode();

        $this->cddns->setAclMode('range');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('range', $aclMode);
    }

}
