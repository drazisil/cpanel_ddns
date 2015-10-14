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
        $this->cddns->setAclModeDefault();

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('single', $aclMode);
    }

    /**
     * Test if able to set ACL mode to single ip
     */
    public function testSetAclModeSingle()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('single');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('single', $aclMode);
    }

    /**
     * Test if a valid IP is accepted in single mode
     */
    public function testInACLSingleIpAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('single');
        
        $this->cddns->addAclSingle('192.168.5.1');
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.1');

        // Assert
        $this->assertEquals(true, $isAllowed);
    }

    
    /**
     * Test if a not allowed ip is rejected by the ACL in single mode
     */
    public function testInACLSingleIpNotAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('single');
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.0');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Test if an invalid ip is rejected by the ACL in single mode
     */
    public function testInACLSingleIpInvalid()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('single');
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Check if can set ACL mode to multiple ips
     **/
    public function testSetAclModeMulti()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('multi');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('multi', $aclMode);
    }

    /**
     * Test if a valid IP is accepted in multi mode
     */
    public function testInACLMultiIpAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('multi');
        
        $ipMulti = ['192.168.5.1, 192.168.5.2']
        
        $this->cddns->addAclMulti($ipMulti);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.1');

        // Assert
        $this->assertEquals(true, $isAllowed);
    }

    
    /**
     * Test if an not allowed ip is rejected by the ACL in multi mode
     */
    public function testInACLMultiIpNotAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('multi');
        
        $ipMulti = ['192.168.5.1, 192.168.5.2']
        
        $this->cddns->addAclMulti($ipMulti);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.3');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Test if an invalid ip is rejected by the ACL in multi mode
     */
    public function testInACLMultiIpInvalid()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('multi');
        
        $ipMulti = ['192.168.5.1, 192.168.5.2', '192.168.5']
        
        $this->cddns->addAclMulti($ipMulti);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Check if can set ACL mode to range of ips
     **/
    public function testSetAclModeRange()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('range');

        $aclMode =  $this->cddns->getAclMode();

        $this->assertEquals('range', $aclMode);

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Test if a valid IP is accepted in range mode as a from->to
     */
    public function testInACLSingleIpAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('range');

        $ipRange = ['192.168.5.1, 192.168.5.2']
        
        $this->cddns->addAclRange($ipRange);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.3');

        $this->assertEquals(false, $isAllowed);
    }

    
    /**
     * Test if a valid IP is accepted in range mode as a subnet
     */
    public function testInACLSingleIpAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('range');

        $ipRange = ['192.168.5.1/24']
        
        $this->cddns->addAclRange($ipRange);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.6.1');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Test if an not allowed ip is rejected by the ACL in range mode
     */
    public function testInACLSingleIpNotAllowed()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('range');

        $ipRange = ['192.168.5.1, 192.168.5.2']
        
        $this->cddns->addAclRange($ipRange);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5.3');

        $this->assertEquals(false, $isAllowed);
    }

    /**
     * Test if an invalid ip is rejected by the ACL in single mode
     */
    public function testInACLSingleIpInvalid()
    {
        $this->cddns->setAclModeDefault();

        $this->cddns->setAclMode('range');

        $ipRange = ['192.168.5.1, 192.168.5.2']
        
        $this->cddns->addAclRange($ipRange);
        
        $isAllowed =  $this->cddns->checkAclAllowed('192.168.5');

        $this->assertEquals(false, $isAllowed);
    }
}
