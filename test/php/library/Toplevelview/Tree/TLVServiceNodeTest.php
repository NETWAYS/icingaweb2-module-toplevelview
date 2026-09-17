<?php

namespace Tests\Icinga\Module\Toplevelview;

use Icinga\Module\Toplevelview\Tree\TLVServiceNode;

use PHPUnit\Framework\TestCase;
use stdClass;
use ReflectionClass;

final class TLVServiceNodeTest extends TestCase
{
    public function testGetTitle()
    {
        $n = new TLVServiceNode();
        $n->setProperties(['service'=>'unit', 'host'=>'test']);
        $this->assertSame('test!unit', $n->getKey());

        $mockRoot = new class {
            public function getFetched($type, $key) {
                $h = new stdClass;
                $h->display_name = 'service';
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('test: unit', $n->getTitle());
    }

    public function testGetStatus()
    {
        $n = new TLVServiceNode();
        $n->setProperties(['service'=>'unit', 'host'=>'test']);

        $mockRoot = new class {
            public function get($thing) {
                return false;
            }
            public function getFetched($type, $key) {
                $h = new stdClass;
                $s = new stdClass;
                $s->hard_state = 1;
                $s->is_handled = true;
                $s->is_overdue = false;
                $s->in_downtime = true;
                $h->display_name = 'service';
                $h->notifications_enabled = false;
                $h->state = $s;
                return $h;
            }
        };

        $reflection = new ReflectionClass($n);
        $reflection_root = $reflection->getProperty('root');
        $reflection_root->setValue($n, $mockRoot);

        $this->assertSame('downtime handled', $n->getStatus()->getOverall());
    }
}
