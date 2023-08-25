<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\LiveComponent\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\Tests\Fixtures\Component\Component5;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AsLiveComponentTest extends TestCase
{
    public function testCanGetPreDehydrateMethods(): void
    {
        $methods = iterator_to_array(AsLiveComponent::preDehydrateMethods(new Component5()));

        $this->assertCount(1, $methods);
        $this->assertSame('method4', $methods[0]->getName());
    }

    public function testCanGetPostHydrateMethods(): void
    {
        $methods = iterator_to_array(AsLiveComponent::postHydrateMethods(new Component5()));

        $this->assertCount(1, $methods);
        $this->assertSame('method5', $methods[0]->getName());
    }

    public function testPreMountHooksAreOrderedByPriority(): void
    {
        $hooks = AsLiveComponent::preReRenderMethods(
            new class() {
                #[PreReRender(priority: -10)]
                public function hook1()
                {
                }

                #[PreReRender(priority: 10)]
                public function hook2()
                {
                }

                #[PreReRender]
                public function hook3()
                {
                }
            }
        );

        $this->assertCount(3, $hooks);
        $this->assertSame('hook2', $hooks[0]->name);
        $this->assertSame('hook3', $hooks[1]->name);
        $this->assertSame('hook1', $hooks[2]->name);
    }

    public function testCanGetLiveListeners(): void
    {
        $liveListeners = AsLiveComponent::liveListeners(new Component5());

        $this->assertCount(1, $liveListeners);
        $this->assertSame([
            'action' => 'aListenerActionMethod',
            'event' => 'the_event_name',
        ], $liveListeners[0]);
    }

    public function testCanCheckIfMethodIsAllowed(): void
    {
        $component = new Component5();

        $this->assertTrue(AsLiveComponent::isActionAllowed($component, 'method1'));
        $this->assertFalse(AsLiveComponent::isActionAllowed($component, 'method2'));
        $this->assertTrue(AsLiveComponent::isActionAllowed($component, 'aListenerActionMethod'));
    }
}
