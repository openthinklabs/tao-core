<?php

/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\tao\install\utils\seed;

use oat\generis\model\GenerisRdf;

class Seed
{
	private $extensionsToInstall;
	private $services;
	private $options;
	private $userData;
	private $postInstallScripts = [];

	public function __construct(SeedOptions $options, $extensions, $services, $user, $postInstallScripts = [])
	{
		$this->options = $options;
		$this->extensionsToInstall = $extensions;
		$this->services = $services;
		$this->userData = $user;
		$this->postInstallScripts = $postInstallScripts;
	}

	public function getExtensionsToInstall(): array
	{
	    return $this->extensionsToInstall;
	}

	/**
	 * Returns an associative array with property URI as key
	 * Defining the super user
	 */
	public function getUserData(): array
	{
	    return $this->userData;
	}

	public function getServices(): array
	{
	    return $this->services;
	}

	public function getPostInstallScripts(): array
	{
	    return $this->postInstallScripts;
	}

	public function getRootUrl(): string
	{
		return $this->options->getRootUrl();
	}

	public function getLocalFilePath(): string
	{
	    return $this->options->getLocalFilePath();
	}

	public function getLocalNamespace(): string
	{
		return $this->options->getLocalNamespace();
	}

	/**
	 * @return string language code, by default 'en-US'
	 */
	public function getDefaultLanguage(): string
	{
		return $this->options->getDefaultLanguage();
	}

	public function getAnonymousLanguage(): string
	{
	    return $this->options->getAnonymousLanguageCode();
	}

	/**
	 * @return string|NULL returns the name to use for sessions if specified
	 */
	public function getSessionName(): ?string
	{
	    return $this->options->getSessionName();
	}

	public function getDefaultTimezone(): string
	{
		return $this->options->getDefaultTimezone();
	}

	public function getInstanceName(): string
	{
		return $this->options->getInstanceName();
	}

	public function useDebugMode(): bool
	{
		return $this->options->useDebugMode();
	}

	public function installSamples(): bool
	{
		return $this->options->installSamples();
	}

}
