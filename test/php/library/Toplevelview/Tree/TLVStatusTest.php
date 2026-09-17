<?php

namespace Tests\Icinga\Module\Toplevelview;

use Icinga\Module\Toplevelview\Tree\TLVStatus;

use PHPUnit\Framework\TestCase;

final class TLVStatusTest extends TestCase
{
    public function testGetOverall()
    {
        $t = new TLVStatus();
        $t->add('ok', 1);
        $this->assertSame('ok', $t->getOverall());

        $t->add('missing', 1);
        $this->assertSame('ok', $t->getOverall());

        $t->add('critical_handled', 1);
        $this->assertSame('critical handled', $t->getOverall());

        $t->zero();
        $t->add('total');

        $this->assertSame(1, $t->get('total'));
        $this->assertSame(0, $t->get('missing'));
    }

    public function testGetOverallWithMissing()
    {
        $t = new TLVStatus();
        $this->assertSame('missing', $t->getOverall());
    }

    public function testGetterSetter()
    {
        $t = new TLVStatus();
        $t->set('missing', 123);
        $this->assertSame(123, $t->get('missing'));

        $t->setMeta('hosts_total', 321);
        $this->assertSame(321, $t->getMeta('hosts_total'));
        $this->assertSame(0, $t->getMeta('services_total'));
    }

    public function testMerge()
    {
        $b = new TLVStatus();
        $b->set('ok', 1);
        $b->set('missing', 2);
        $b->set('warning_handled', 3);
        $b->set('critical_unhandled', 4);

        $a = new TLVStatus();
        $a->set('ok', 3);
        $a->set('unknown_unhandled', 2);
        $a->set('missing', 1);

        $a->merge($b);
        $this->assertSame(3, $a->get('missing'));
        $this->assertSame(4, $a->get('ok'));
        $this->assertSame(3, $a->get('warning_handled'));
        $this->assertSame(4, $a->get('critical_unhandled'));
        $this->assertSame(2, $a->get('unknown_unhandled'));
        $this->assertSame(0, $a->get('warning_unhandled'));
    }
}
