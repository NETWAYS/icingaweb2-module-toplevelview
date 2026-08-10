<?php
/* Copyright (C) 2017 Icinga Development Team <info@icinga.com> */

namespace Icinga\Module\Toplevelview\Tree;

use Icinga\Application\Benchmark;
use Icinga\Exception\NotFoundError;
use Icinga\Module\Icingadb\Model\Service;
use ipl\Stdlib\Filter;
use stdClass;

/**
 * TLVServiceNode represents a Service in the tree
 */
class TLVServiceNode extends TLVIcingaNode
{
    protected string $type = 'service';
    protected ?string $key = '{host}!{service}';

    public function getTitle(): string
    {
        $key = $this->getKey();
        $obj = $this->root->getFetched($this->type, $key);

        $name = $this->get($this->type);
        $hostname = $this->get('host');

        if (isset($obj->display_name)) {
            $name = $obj->display_name;
        }

        if (isset($obj->display_name)) {
            $hostname = $obj->host->display_name;
        }

        return sprintf('%s: %s', $hostname, $name);
    }

    public function register(): self
    {
        // also register host, because that's what we fetch data with
        $hostDummy = new TLVHostNode();
        $this->root->registerObject($hostDummy->getType(), $this->get('host'), get_class($hostDummy));

        // register myself
        return parent::register();
    }

    public function getKey(): string
    {
        return sprintf('%s!%s', $this->properties['host'] ?? 'nosuchhost', $this->properties['service'] ?? 'nosuchservice');
    }

    public static function fetch(TLVTree $root): void
    {
        Benchmark::measure('Begin fetching services');

        if (! array_key_exists('service', $root->registeredObjects) or empty($root->registeredObjects['service'])) {
            throw new NotFoundError('No services registered to fetch!');
        }

        $hostnameFilter = Filter::any();

        foreach (array_keys($root->registeredObjects['host']) as $name) {
            $hostnameFilter->add(Filter::equal('host.name', $name));
        }

        $services = Service::on($root->getDb())->with([
            'host',
            'state'
        ]);

        $services->filter($hostnameFilter);

        foreach ($services as $service) {
            // TODO We cannot store the ORM Models with json_encore
            // Thus I'm converting things to objects that can be stored
            // Maybe there's a better way? iterator_to_array does not work.
            $s = new stdClass;
            $s->state = new stdClass;
            $s->host = new stdClass;
            $s->name = $service->name;
            $s->display_name = $service->display_name;
            $s->notifications_enabled = $service->notifications_enabled;
            $s->host->display_name = $service->host->display_name;
            $s->state->hard_state = $service->state->hard_state;
            $s->state->is_flapping = $service->state->is_flapping;
            $s->state->is_overdue = $service->state->is_overdue;
            $s->state->is_handled = $service->state->is_handled;
            $s->state->in_downtime = $service->state->in_downtime;

            $key = sprintf('%s!%s', $service->host->name, $service->name);
            if (array_key_exists($key, $root->registeredObjects['service'])) {
                $root->registeredObjects['service'][$key] = $s;
            }
        }

        Benchmark::measure('Finished fetching services');
    }

    /**
     * getStatus returns the current status for the Service
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

        $service = $this->root->getFetched($this->type, $key);

        if ($service === null) {
            $status->add('missing', 1);
            return $this->status;
        }

        $status->zero();
        $status->add('total');

        if ($service->state->is_overdue) {
            $status->add('overdue', 1);
            return $this->status;
        }

        // We only care about the hard state in TLV
        $state = $service->state->hard_state;

        // Get the service's current handled state
        $isHandled = $service->state->is_handled;

        // In TLV flapping means the state is handled
        $isHandled = $isHandled || $service->state->is_flapping;

        // Check if the downtime is enabled if set_downtime_if_notification_disabled is true
        $downtime_if_no_notifications = $service->notifications_enabled === false &&
                                      $this->getRoot()->get('set_downtime_if_notification_disabled');

        if ($service->state->in_downtime || $downtime_if_no_notifications) {
            $status->add('downtime_active');
            if ($state !== 0) {
                $state = 10;
            }
        }

        if ($isHandled) {
            $handled = '_handled';
        } else {
            $handled = '_unhandled';
        }

        if ($state === 0 || $state === 99) {
            $status->add('ok', 1);
        } elseif ($state === 1) {
            $status->add('warning' . $handled, 1);
        } elseif ($state === 2) {
            $status->add('critical' . $handled, 1);
        } elseif ($state === 10) {
            $status->add('downtime_handled');
        } else {
            $status->add('unknown' . $handled, 1);
        }

        return $this->status;
    }
}
