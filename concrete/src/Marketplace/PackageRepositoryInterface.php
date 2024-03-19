<?php

namespace Concrete\Core\Marketplace;

use Concrete\Core\Marketplace\Exception\InvalidConnectResponseException;
use Concrete\Core\Marketplace\Exception\InvalidPackageException;
use Concrete\Core\Marketplace\Exception\PackageAlreadyExistsException;
use Concrete\Core\Marketplace\Exception\UnableToConnectException;
use Concrete\Core\Marketplace\Exception\UnableToPlacePackageException;
use Concrete\Core\Marketplace\Model\RemotePackage;

interface PackageRepositoryInterface
{
    /**
     * Load a package by its remote ID
     */
    public function getPackage(ConnectionInterface $connection, string $packageId): ?RemotePackage;

    /**
     * Get a list of remote packages that are available for this connection
     * @param ConnectionInterface $connection
     * @param bool $latestOnly Only show the latest version of each package
     * @param bool $compatibleOnly Only show package versions that are compatible with the current concrete version
     * @return RemotePackage[]
     */
    public function getPackages(ConnectionInterface $connection, bool $latestOnly = false, bool $compatibleOnly = false): array;

    /**
     * Download a remote package and make it available for install
     * @throws PackageAlreadyExistsException If the package already exists and $overwrite is false.
     * @throws UnableToPlacePackageException If we're unable to move the downloaded package into it's proper spot.
     * @throws InvalidPackageException If the downloaded package doesn't look right, for example if there's no controller.php
     */
    public function download(ConnectionInterface $connection, RemotePackage $package, bool $overwrite = false): void;

    /**
     * Get the existing connection if one is set
     */
    public function getConnection(): ?ConnectionInterface;

    /**
     * Attempt to connect to the package repository
     * @throws InvalidConnectResponseException When a connection response doesn't match what was expected
     * @throws UnableToConnectException When connection fails for any other reason
     */
    public function connect(): ConnectionInterface;

    /**
     * Determine if a given connection is valid for the current site
     */
    public function validate(ConnectionInterface $connection): bool;
}
