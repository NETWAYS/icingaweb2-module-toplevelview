<?php
/* Copyright (C) 2017 Icinga Development Team <info@icinga.com> */

namespace Icinga\Module\Toplevelview\Tree;

/**
 * TLVStatus represents the status for a TLVTreeNode that is shown in the view
 */
class TLVStatus
{
    /**
     * Properties track each tree nodes Icinga states
     */
    protected array $properties = [
        'critical_unhandled' => 0,
        'critical_handled'   => 0,
        'warning_unhandled'  => 0,
        'warning_handled'    => 0,
        'unknown_unhandled'  => 0,
        'unknown_handled'    => 0,
        'downtime_handled'   => 0,
        'downtime_active'    => 0,
        'ok'                 => 0,
        'missing'            => 0,
        'overdue'            => 0,
        'total'              => 0,
    ];

    /**
     * statusPriority decribes the priority from worst to best
     */
    protected static array $statusPriority = [
        'critical_unhandled',
        'warning_unhandled',
        'unknown_unhandled',
        'overdue',
        'downtime_handled',
        'critical_handled',
        'warning_handled',
        'unknown_handled',
        'ok',
        'missing',
    ];

    /**
     * meta tracks get overall count of hosts and services if this status object
     */
    protected array $meta = [];

    /**
     * merge merges another TLVStatus object's properties into this object
     */
    public function merge(TLVStatus $status): TLVStatus
    {
        $properties = $status->getProperties();
        foreach ($this->properties as $key => $_) {
            if ($this->properties[$key] === 0) {
                $this->properties[$key] = $properties[$key];
            } else {
                $this->properties[$key] += $properties[$key];
            }
        }
        return $this;
    }

    /**
     * get returns the given key's value from the properties
     *
     * @param string $key key of the property
     */
    public function get(string $key)
    {
        return $this->properties[$key];
    }

    /**
     * set sets the given key/value in the properties
     *
     * @param string $key key of the property
     * @param int $value value to set to property to
     */
    public function set($key, $value): self
    {
        $this->properties[$key] = (int) $value;
        return $this;
    }

    /**
     * getProperties returns all properties
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * add adds the given value (integer) to the given property
     *
     * @param string $key key of the property
     * @param int $value value to add to the property
     */
    public function add(string $key, int $value = 1): self
    {
        $this->properties[$key] += (int) $value;
        return $this;
    }

    /**
     * zero sets all properties to zero (0)
     */
    public function zero(): TLVStatus
    {
        foreach ($this->properties as $key => $_) {
            $this->properties[$key] = 0;
        }
        return $this;
    }

    /**
     * getOverall returns the worst state of this TLVStatus,
     * given the statusPriority.
     *
     * @return string
     */
    public function getOverall(): string
    {
        foreach (static::$statusPriority as $key) {
            if ($this->properties[$key] !== 0 && $this->properties[$key] > 0) {
                return $this->cssFriendly($key);
            }
        }
        return 'missing';
    }

    /**
     * cssFriendly transforms the given key to be CSS friendly,
     * meaning using spaces between the state and the handled indicator
     */
    protected function cssFriendly(string $key): string
    {
        return str_replace('_', ' ', $key);
    }

    /**
     * getMeta returns the given key's value from the metadata
     */
    public function getMeta(string $key): int
    {
        if (array_key_exists($key, $this->meta)) {
            return $this->meta[$key];
        } else {
            return 0;
        }
    }

    /**
     * setMeta sets the given key/value in the metadata
     */
    public function setMeta(string $key, int $value): void
    {
        $this->meta[$key] = $value;
    }
}
