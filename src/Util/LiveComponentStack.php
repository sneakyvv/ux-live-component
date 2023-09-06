<?php

declare(strict_types=1);

namespace Symfony\UX\LiveComponent\Util;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\TwigComponent\ComponentStack;
use Symfony\UX\TwigComponent\MountedComponent;

/**
 * This class decorates the TwigComponent\ComponentStack adding specific Live component functionalities.
 *
 * @author Bart Vanderstukken <bart.vanderstukken@gmail.com>
 *
 * @internal
 */
final class LiveComponentStack
{
    public function __construct(private readonly ComponentStack $componentStack)
    {
    }

    public function getCurrentLiveComponent(): ?MountedComponent
    {
        foreach ($this->componentStack as $mountedComponent) {
            if ($this->isLiveComponent($mountedComponent->getComponent()::class)) {
                return $mountedComponent;
            }
        }

        return null;
    }

    private function isLiveComponent(string $classname): bool
    {
        return [] !== (new \ReflectionClass($classname))->getAttributes(AsLiveComponent::class);
    }
}
