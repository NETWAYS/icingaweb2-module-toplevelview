<?php
/* Copyright (C) 2017 Icinga Development Team <info@icinga.com> */

namespace Icinga\Module\Toplevelview\Tree;

use Icinga\Exception\NotImplementedError;

/**
 * Leaf nodes that fetch data from the database are TLVIcingaNodes.
 * @codeCoverageIgnore
 */
class TLVIcingaNode extends TLVTreeNode
{
    protected static bool $canHaveChildren = false;

    /**
     * Interface to fetch data for the implementation
     *
     * Needs to be extended / replaced by class
     *
     * @param $root
     *
     * @throws NotImplementedError
     */
    public static function fetch(/** @noinspection PhpUnusedParameterInspection */ TLVTree $root): void
    {
        throw new NotImplementedError('fetch() has not been implemented for %s', static::class);
    }
}
