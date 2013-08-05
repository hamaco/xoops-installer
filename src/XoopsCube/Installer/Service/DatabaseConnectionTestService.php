<?php

namespace XoopsCube\Installer\Service;

use XoopsCube\Installer\Requirement\DatabaseConnectableRequirement;
use XoopsCube\Installer\Requirement\FileWritableRequirement;
use XoopsCube\Installer\DataTransferObject\ConfigrationTestDTO;
use XoopsCube\Installer\ValueObject\Database;

/**
 * Database connection test
 */
class DatabaseConnectionTestService
{
    /**
     * @var DatabaseConnectableRequirement
     */
    private $databaseConnectableRequirement;

    /**
     * @var FileWritableRequirement
     */
    private $fileWritableRequirement;

    /**
     * @param  DatabaseConnectableRequirement $databaseConnectableRequirement
     * @return DatabaseConnectionTestService
     */
    public function setDatabaseConnectableRequirement(DatabaseConnectableRequirement $databaseConnectableRequirement)
    {
        $this->databaseConnectableRequirement = $databaseConnectableRequirement;

        return $this;
    }

    /**
     * @param  FileWritableRequirement $fileWritableRequirement
     * @return RequirementTestService
     */
    public function setFileWritableRequirement(FileWritableRequirement $fileWritableRequirement)
    {
        $this->fileWritableRequirement = $fileWritableRequirement;

        return $this;
    }

    /**
     * Test if the database can be connected
     * @param  Database $database
     * @return bool
     */
    public function test(Database $database)
    {
        return $this->databaseConnectableRequirement->isSatisfiedBy($database);
    }

    public function test2(Database $database, $config, ConfigrationTestDTO $dto)
    {
        $config = $dto->getConfig();

        $r = $this->databaseConnectableRequirement->isSatisfiedBy($database);
        if ($r === false) return false;

        $notWritableFiles = $this->fileWritableRequirement->getUnsatisfiedBy($config['writable']);
        $dto->setNotWritableFiles($notWritableFiles);
        if (empty($notWritableFiles) === false) return false;

        return true;
    }
}
