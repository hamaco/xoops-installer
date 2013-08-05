<?php

// Dependency injection
use XoopsCube\Installer\Service\RequirementTestService;
use XoopsCube\Installer\Requirement\PHPExtensionRequirement;
use XoopsCube\Installer\Requirement\FileWritableRequirement;
use XoopsCube\Installer\Service\ConfigCreationService;
use XoopsCube\Installer\ConfigFormatter\ConstantFormatter;
use XoopsCube\Installer\Requirement\DatabaseConnectableRequirement;
use XoopsCube\Installer\Database\ConnectionFactory;
use XoopsCube\Installer\Service\DatabaseConnectionTestService;
use XoopsCube\Installer\Service\DatabaseConstructionService;
use XoopsCube\Installer\Service\SiteConstructionService;

$container = new Pimple();

$container['service.requirement_test'] = function($container) {
	$requirementTestService = new RequirementTestService();
    /*
	$requirementTestService
		->setPHPExtensionRequirement(new PHPExtensionRequirement())
		->setFileWritableRequirement(new FileWritableRequirement());
     */
	$requirementTestService
		->setPHPExtensionRequirement(new PHPExtensionRequirement());
	return $requirementTestService;
};

$container['service.database_connection_test'] = function($container) {
	$requirement = new DatabaseConnectableRequirement();
	$requirement->setConnectionFactory(new ConnectionFactory());
	$service = new DatabaseConnectionTestService();
	$service->setFileWritableRequirement(new FileWritableRequirement());
	$service->setDatabaseConnectableRequirement($requirement);
	return $service;
};

$container['service.site_construction'] = function($container) use ($installerConfig) {
	$encryptorClass = $installerConfig->get('password_encryptor');
	$service = new SiteConstructionService();
	$service
		->setConfigFilename($installerConfig->get('config_filename'))
		->setSchemaFile($installerConfig->get('database.structure'))
		->setDataFile($installerConfig->get('database.data'))
		->setFormatter(new ConstantFormatter())
		->setSqlUtility(new \XoopsCube\Installer\Database\SqlUtility())
		->setConnectionFactory(new \XoopsCube\Installer\Database\ConnectionFactory())
		->setEncryptor(new $encryptorClass);
	return $service;
};
