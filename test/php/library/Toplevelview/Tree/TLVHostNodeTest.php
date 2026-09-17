<?php

namespace Tests\Icinga\Module\Toplevelview;

use Icinga\Module\Toplevelview\Tree\TLVHostNode;

use PHPUnit\Framework\TestCase;
use stdClass;
use ReflectionClass;

final class TLVHostNodeTest extends TestCase
{
    public function testGetTitle()
    {
        $n = new TLVHostNode();
        $n->setProperties(['host'=>'unit']);
        $this->assertSame('unit', $n->getKey());

        $mockRoot = new class {
            public function getFetched($type, $key) {
                $h = new stdClass;
                $h->display_name = 'host';
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('unit', $n->getTitle());
    }

    public function testGetStatus()
    {
        $n = new TLVHostNode();
        $n->setProperties(['host'=>'unit']);

        $mockRoot = new class {
            public function get($thing) {
                return false;
            }
            public function getFetched($type, $key) {
                $h = new stdClass;
                $h->display_name = 'host';
                $s = new stdClass;
                $s->hard_state = 2;
                $s->is_handled = false;
                $s->is_overdue = false;
                $s->in_downtime = false;
                $h->notifications_enabled = false;
                $h->state = $s;
                return $h;
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('critical unhandled', $n->getStatus()->getOverall());
    }
}
