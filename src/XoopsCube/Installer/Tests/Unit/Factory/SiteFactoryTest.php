<?php

namespace XoopsCube\Installer\Tests\Unit\Factory;

use XoopsCube\Installer\ValueObject\Site;
use XoopsCube\Installer\ValueObject\Database;
use XoopsCube\Installer\ValueObject\Admin;
use XoopsCube\Installer\Factory\SiteFactory;
use XoopsCube\Installer\Form\ConfigurationForm;
use XoopsCube\Installer\Tests\Unit\Form\ConfigurationFormTest;
use XoopsCube\Installer\InstallerConfig;

class SiteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function getInstallerConfigStub()
    {
        $stub = $this->getMockBuilder('XoopsCube\Installer\InstallerConfig')->disableOriginalConstructor()->getMock();
        $stub->expects($this->at(0))->method('get')->with('xoops_root_path')->will($this->returnValue('/path/to/root'));
        $stub->expects($this->at(1))->method('get')->with('xoops_trust_path')->will($this->returnValue('/path/to/trust'));
        $stub->expects($this->at(2))->method('get')->with('database.type')->will($this->returnValue('mysql'));
        $stub->expects($this->at(3))->method('get')->with('database.permanent_connection')->will($this->returnValue(false));

        return $stub;
    }

    public function testCreateByConfigurationForm()
    {
        $form = new ConfigurationForm();
        $form->fetch(ConfigurationFormTest::getValidData());
        $installerConfig = $this->getInstallerConfigStub();

        $factory = new SiteFactory();
        $site = $factory->createByConfigurationForm($form, $installerConfig);
        $this->assertTrue($site instanceof Site);
        $this->assertSame('/path/to/root', $site->getRootPath());
        $this->assertSame('/path/to/trust', $site->getTrustPath());
        $this->assertSame('http://example.com', $site->getUrl());
        $this->assertNotEmpty($site->getSalt());

        $database = $site->getDB();
        $this->assertTrue($database instanceof Database);
        $this->assertSame('mysql', $database->getType());
        $this->assertSame('localhost', $database->getHost());
        $this->assertSame('root', $database->getUser());
        $this->assertSame('p@ssW0rd', $database->getPassword());
        $this->assertSame('xoops', $database->getName());
        $this->assertSame('prefix', $database->getPrefix());
        $this->assertFalse($database->isPermanentConnectionEnabled());

        $admin = $site->getAdmin();
        $this->assertSame('admin', $admin->getUsername());
        $this->assertSame('admin@example.com', $admin->getEmail());
        $this->assertSame('http://example.com', $admin->getUrl());
        $this->assertSame('adminP@ss', $admin->getPassword());
        $this->assertSame(date('Z') / 3600, $admin->getTimezoneOffset());
        $this->assertTrue($admin instanceof Admin);
    }

    public function testTrailLastSlashFromSiteURL()
    {
        $postData = ConfigurationFormTest::getValidData();
        $postData['url'] = 'http://example.com/'; // <-- This last slash should be removed by the factory.

        $form = new ConfigurationForm();
        $form->fetch($postData);
        $installerConfig = $this->getInstallerConfigStub();

        $factory = new SiteFactory();
        $site = $factory->createByConfigurationForm($form, $installerConfig);
        $this->assertTrue($site instanceof Site);
        $this->assertSame('http://example.com', $site->getUrl());
    }
}
