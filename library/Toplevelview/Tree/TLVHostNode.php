<?php
/* Copyright (C) 2017 Icinga Development Team <info@icinga.com> */

namespace Icinga\Module\Toplevelview\Tree;

use Icinga\Application\Benchmark;
use Icinga\Exception\NotFoundError;
use Icinga\Module\Icingadb\Model\Host;
use ipl\Stdlib\Filter;
use stdClass;

/**
 * TLVHostNode represents a Host in the tree
 */
class TLVHostNode extends TLVIcingaNode
{
    protected string $type = 'host';
    protected ?string $key = 'host';
    protected static string $titleKey = 'host';

    public function getTitle(): string
    {
        $key = $this->getKey();
        $obj = $this->root->getFetched($this->type, $key);

        $n = $this->get($this->key);

        if (isset($obj->display_name)) {
            $n = $obj->display_name;
        }

        return $n;
    }

    public static function fetch(TLVTree $root): void
    {
        Benchmark::measure('Begin fetching hosts');

        if (! array_key_exists('host', $root->registeredObjects) or empty($root->registeredObjects['host'])) {
            throw new NotFoundError('No hosts registered to fetch!');
        }

        $hostnameFilter = Filter::any();
        foreach ($root->registeredObjects['host'] as $name => $_) {
            $hostnameFilter->add(Filter::equal('name', $name));
        }

        $hosts = Host::on($root->getDb())->with([
            'state'
        ]);

        $hosts->filter($hostnameFilter);

        foreach ($hosts as $host) {
            // TODO We cannot store the ORM Models with json_encore
            // Thus I'm converting things to objects that can be stored
            // Maybe there's a better way? iterator_to_array does not work.
            $h = new stdClass;
            $h->state = new stdClass;
            $h->display_name = $host->display_name;
            $h->notifications_enabled = $host->notifications_enabled;
            $h->state->hard_state = $host->state->hard_state;
            $h->state->is_flapping = $host->state->is_flapping;
            $h->state->is_overdue = $host->state->is_overdue;
            $h->state->is_handled = $host->state->is_handled;
            $h->state->in_downtime = $host->state->in_downtime;

            $root->registeredObjects['host'][$host->name] = $h;
        }

        Benchmark::measure('Finished fetching hosts');
    }

    /**
     * getStatus returns the current status for the Host
     *
     * @return TLVStatus
     */
    public function getStatus(): TLVStatus
    {
        if ($this->status !== null) {
            return $this->status;
        }

        $this->status = $status = new TLVStatus();
        $key = $this->getKey();

        $host = $this->root->getFetched($this->type, $key);

        if ($host === null) {
            $this->status->add('missing', 1);
            return $this->status;
        }

        $status->zero();
        $status->add('total');

        if ($host->state->is_overdue) {
            $status->add('overdue', 1);
            return $this->status;
        }

        // We only care about the hard state in TLV
        $state = $host->state->hard_state;

        // Check if the downtime is enabled if set_downtime_if_notification_disabled is true
        $downtime_if_no_notifications = $host->notifications_enabled === false &&
                                      $this->getRoot()->get('set_downtime_if_notification_disabled');

        if ($host->state->in_downtime || $downtime_if_no_notifications) {
            // Set downtime if notifications are disabled for the host
            $status->add('downtime_active');
            $state = 10;
            $handled = '';
        } elseif ($host->state->is_handled ||
                  $this->getRoot()->get('override_host_problem_to_handled')) {
            // Set the state to handled if it actually is handled, and the option is set to override the state
            $handled = '_handled';
        } else {
            $handled = '_unhandled';
        }

        match ($state) {
            0 => $status->add('ok', 1),
            1, 2 => $status->add('critical' . $handled, 1),
            10 => $status->add('downtime_handled'),
            default => $status->add('unknown_handled' . $handled, 1),
        };

        return $this->status;
    }
}
