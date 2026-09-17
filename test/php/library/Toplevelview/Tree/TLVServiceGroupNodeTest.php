<?php

namespace Tests\Icinga\Module\Toplevelview;

use Icinga\Module\Toplevelview\Tree\TLVServiceGroupNode;

use PHPUnit\Framework\TestCase;
use stdClass;
use ReflectionClass;

final class TLVServiceGroupNodeTest extends TestCase
{
    public function testGetTitle()
    {
        $n = new TLVServiceGroupNode();
        $n->setProperties(['servicegroup'=>'unit']);
        $this->assertSame('unit', $n->getKey());

        $mockRoot = new class {
            public function getFetched($type, $key) {
                $h = new stdClass;
                $h->display_name = 'service';
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('unit', $n->getTitle());
    }

    public function testGetStatus()
    {
        $n = new TLVServiceGroupNode();
        $n->setProperties(['servicegroup'=>'unit']);

        $mockRoot = new class {
            public function get($thing) {
                return false;
            }
            public function getFetched($type, $key) {
                $h = new stdClass;
                $s = new stdClass;
                $s->hard_state = 1;
                $s->is_handled = true;
                $h->services_overdue_total = 1;
                $h->display_name = 'servicegroup';

                $h->services_total = 1;
                $h->services_ok = 1;
                $h->services_warning_handled = 1;
                $h->services_warning_unhandled = 1;
                $h->services_critical_handled = 1;
                $h->services_critical_unhandled = 1;
                $h->services_unknown_handled = 1;
                $h->services_unknown_unhandled = 1;
                $h->services_downtime_handled = 1;
                $h->services_downtime_active = 1;

                return $h;
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('critical unhandled', $n->getStatus()->getOverall());
    }
}
