<?php
/* Copyright (C) 2017 Icinga Development Team <info@icinga.com> */

namespace Icinga\Module\Toplevelview\Tree;

use Icinga\Module\Toplevelview\Model\View;

use Icinga\Application\Logger;
use Icinga\Exception\IcingaException;
use Icinga\Exception\NotFoundError;
use Icinga\Exception\ProgrammingError;
use Icinga\Module\Icingadb\Common\Database;
use Icinga\Util\Json;
use Icinga\Web\FileCache;
use stdClass;

/**
 * TLVTree represents the root of the TLV tree
 */
class TLVTree extends TLVTreeNode
{
    use Database;

    protected static string $titleKey = 'name';
    public array $registeredTypes = [];
    public array $registeredObjects = [];
    protected array $fetchedData = [];
    protected bool $fetched = false;
    protected int $fetchTime;
    protected int $cacheLifetime = 60;
    protected string $viewName;
    protected string $viewChecksum;

    /**
     * Return a child by its ID
     *
     * @throws NotFoundError if the id cannot be found
     */
    public function getById(string $id)
    {
        $ids = explode('-', $id);
        $currentNode = $this;

        foreach ($ids as $i) {
            $children = $currentNode->getChildren();
            if (! empty($children) && array_key_exists($i, $children)) {
                $currentNode = $children[$i];
            } else {
                throw new NotFoundError(
                    'Could not find ID %s after %s for path %s',
                    $i,
                    $currentNode->getFullId(),
                    $id
                );
            }
        }

        return $currentNode;
    }

    public function getViewName(): ?string
    {
        return $this->viewName;
    }

    public function setViewName(string $name)
    {
        $this->viewName = $name;
        return $this;
    }

    public function getViewChecksum(): ?string
    {
        return $this->viewChecksum;
    }

    public function setViewChecksum(string $checksum)
    {
        $this->viewChecksum = $checksum;
        return $this;
    }

    /**
     * registerObject adds a new object via its type, name and class
     *
     * @throws ProgrammingError if the same type by multiple classes is registered
     */
    public function registerObject($type, $name, $class): void
    {
        if (array_key_exists($type, $this->registeredTypes) && $this->registeredTypes[$type] !== $class) {
            throw new ProgrammingError(
                'Tried to register the same type by multiple classes: %s - %s - %s',
                $type,
                $this->registeredTypes[$type],
                $class
            );
        }

        $this->registeredTypes[$type] = $class;
        $this->registeredObjects[$type][$name] = null;
    }

    protected function getCacheName(): string
    {
        $n = $this->getViewName();
        $c = $this->getViewChecksum();
        return sprintf('%s-%s.json', $n, $c);
    }

    protected function loadCache(): void
    {
        if (($lifetime = $this->getCacheLifetime()) <= 0) {
            return;
        }

        $cacheName = $this->getCacheName();
        try {
            $cache = FileCache::instance('toplevelview');
            $currentTime = time();
            $newerThan = $currentTime - $lifetime;

            if ($cache->has($cacheName)) {
                $cachedData = Json::decode($cache->get($cacheName));

                if (property_exists($cachedData, 'data')
                    && $cachedData->data !== null
                    && property_exists($cachedData, 'ts')
                    && $cachedData->ts <= $currentTime // too new maybe
                    && $cachedData->ts > $newerThan // too old
                ) {
                    foreach ($cachedData->data as $type => $objects) {
                        $this->registeredObjects[$type] = (array) $objects;
                        $this->fetchedData[$type] = true;
                    }

                    $this->fetchTime = $cachedData->ts;
                    $this->fetched = true;
                }
            }
        } catch (IcingaException $e) {
            Logger::error('Could not load from toplevelview cache %s: %s', $cacheName, $e->getMessage());
        }
    }

    protected function storeCache(): void
    {
        if (($lifetime = $this->getCacheLifetime()) <= 0) {
            return;
        }

        $cacheName = $this->getCacheName();
        try {
            $cache = FileCache::instance('toplevelview');

            $cachedData = new stdClass;
            $cachedData->ts = $this->fetchTime;
            $cachedData->data = $this->registeredObjects;

            $cache->store($cacheName, Json::encode($cachedData));
        } catch (IcingaException $e) {
            Logger::error('Could not store to toplevelview cache %s: %s', $cacheName, $e->getMessage());
        }
    }

    /**
     * fetchType returns a given type from the registered types
     *
     * @throws ProgrammingError if the type has not been registered
     */
    protected function fetchType(string $type): self
    {
        if (! array_key_exists($type, $this->registeredTypes)) {
            throw new ProgrammingError('Type %s has not been registered', $type);
        }

        if (! array_key_exists($type, $this->fetchedData)) {
            /** @var TLVIcingaNode $class */
            $class = $this->registeredTypes[$type];
            $class::fetch($this);
            $this->fetchedData[$type] = true;
        }

        return $this;
    }

    protected function ensureFetched(): self
    {
        if ($this->fetched !== true) {
            $this->loadCache();

            if ($this->fetched !== true) {
                foreach (array_keys($this->registeredTypes) as $type) {
                    $this->fetchType($type);
                }

                $this->fetchTime = time();
                $this->fetched = true;

                $this->storeCache();
            }
        }

        return $this;
    }

    public function getFetched(string $type, string $key)
    {
        $this->ensureFetched();

        if (array_key_exists($key, $this->registeredObjects[$type])
            && $this->registeredObjects[$type][$key] !== null
        ) {
            return $this->registeredObjects[$type][$key];
        } else {
            return null;
        }
    }

    /**
     * @return int time
     */
    public function getFetchTime(): int
    {
        $this->ensureFetched();

        return $this->fetchTime;
    }

    /**
     * @return int seconds
     */
    public function getCacheLifetime(): int
    {
        return $this->cacheLifetime;
    }

    /**
     * @param int $cacheLifetime In seconds
     *
     * @return $this
     */
    public function setCacheLifetime($cacheLifetime): self
    {
        $this->cacheLifetime = $cacheLifetime;
        return $this;
    }
}
