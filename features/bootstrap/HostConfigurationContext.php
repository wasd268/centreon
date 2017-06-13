<?php

use Centreon\Test\Behat\CentreonContext;
use Centreon\Test\Behat\Configuration\HostConfigurationPage;
use Centreon\Test\Behat\Configuration\HostConfigurationListingPage;

class HostConfigurationContext extends CentreonContext
{
    protected $page;
    protected $hostName = 'AcceptanceHost';

    /**
     * @Given a configured host
     */
    public function aConfiguredHost()
    {

    }

    /**
     * @when I modify the ip address
     */
    public function iModifyTheIpAddress()
    {

    }

    /**
     * @Then the ip address is Updated
     */
    public function theIpAddressIsUpdated()
    {

    }
}