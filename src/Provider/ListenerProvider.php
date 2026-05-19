<?php

/**
 * Inane: Event
 * PSR-14 implementation: event dispatcher.
 * $Id$
 * $Date$
 * PHP version 8.5
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\event
 * @category event
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Event\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Class ListenerProvider
 *
 * @version 1.0.0
 */
class ListenerProvider implements ListenerProviderInterface {
    use ListenerProviderTrait;
}
