<?php

namespace XoopsCube\Installer\Controller;

use Pimple;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extensions_Extension_I18n;
use Exception;
use XoopsCube\Installer\InstallerConfig;
use XoopsCube\Installer\DataTransferObject\ConfigrationTestDTO;
use XoopsCube\Installer\DataTransferObject\RequirementTestDTO;
use XoopsCube\Installer\ValueObject\Site;
use XoopsCube\Installer\Form\ConfigurationForm;
use XoopsCube\Installer\Factory\SiteFactory;
use XoopsCube\Installer\HTTP\Request;
use XoopsCube\Installer\Service\SiteConstructionService;
use XoopsCube\Installer\TrustPathFinder;

class DefaultController
{
    /**
     * @var InstallerConfig
     */
    private $config;

    /**
     * @var Pimple
     */
    private $container;

    /**
     * @var array
     */
    private $response = array();

    /**
     * @var string
     */
    private $template = '';

    public function __construct(InstallerConfig $config, Pimple $container)
    {
        $this->config = $config;
        $this->container = $container;
        $this->response = array(
            'base' => $this->config->get('installer_base_url'),
        );
    }

    /**
     * @param  string $name
     * @return object
     */
    public function get($name)
    {
        return $this->container[$name];
    }

    public function run()
    {
        $response = $this->inputAction();
        $this->response = array_merge($this->response, $response);
        // var_dump($this->response);exit;
    }

    private function inputAction()
    {
        $requirementTestResult = $this->testRequirement();
        $configrationTestResult = null;

        $finder = new TrustPathFinder();
        $xoopsTrustPath = $finder->find($this->config->get('xoops_root_path'));

        $request = new Request();
        $form = new ConfigurationForm();
        $form->setURL($request->getSiteUrl());
        $form->setXoopsTrustPath($xoopsTrustPath);

        if ($form->isMethod('POST')) {
            $form->fetch($_POST);
            if ($form->isValid() == false) {
                goto input_page;
            }

            $this->config->set('xoops_trust_path', $form->getXoopsTrustPath());

            $siteFactory = new SiteFactory();
            $site = $siteFactory->createByConfigurationForm($form, $this->config);

            $configrationTestResult = $this->testDatabaseConnection($site);

            if ($configrationTestResult->configrationIsSatisfied() === false) {
                goto input_page;
            }

            try {
                /** @var $service SiteConstructionService */
                $service = $this->get('service.site_construction');
                $service->construct($site);
            } catch ( Exception $e ) {
                $form->addError($e->getMessage());
                goto input_page;
            }

            $this->template = 'step2.twig';

            return array(
                'site' => $site,
            );
        }

        input_page:

        $this->template = 'step1.twig';

        return array(
            'form' => $form,
            'xoops_url' => $request->getSiteUrl(),
            'testResult' => $requirementTestResult,
            'configrationTestResult' => $configrationTestResult,
        );
    }

    public function respond()
    {
// Set language to French
putenv('LC_ALL=en_US');
setlocale(LC_ALL, 'en_US');

// Specify the location of the translation tables
bindtextdomain('messages', $this->config->get('installer.locale_dir'));
bind_textdomain_codeset('messages', 'UTF-8');

// Choose domain
textdomain('messages');

        $loader = new Twig_Loader_Filesystem($this->config->get('installer.template_dir'));
        $twig = new Twig_Environment($loader, array(
            'debug' => true,
            'strict_variables' => true,
    'cache' => '/tmp/cache/',
    'auto_reload' => true,
        ));
        $twig->addExtension(new Twig_Extensions_Extension_I18n());
        $twig->addGlobal('layout', $twig->loadTemplate('layout.twig'));
        echo $twig->render($this->template, $this->response);
    }

    /**
     * @return RequirementTestDTO
     */
    private function testRequirement()
    {
        $dto = new RequirementTestDTO($this->config->getRequirements());
        // $service = $this->container['service.requirement_test'];
        $service = $this->get('service.requirement_test');
        $service->test($dto);

        return $dto;
    }

    /**
     * @param  \XoopsCube\Installer\ValueObject\Site $site
     * @return mixed
     */
    private function testDatabaseConnection(Site $site)
    {
        $dto = new ConfigrationTestDTO($this->config->getWritable());
        $service = $this->get('service.database_connection_test');
        $service->test2($site->getDB(), $this->config, $dto);

        return $dto;

        return $service->test2($site->getDB(), $this->config, $dto);

        return $this->get('service.database_connection_test')->test($site->getDB());
    }
}
