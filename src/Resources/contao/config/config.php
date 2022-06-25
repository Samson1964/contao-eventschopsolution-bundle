<?php

declare(strict_types=1);

/*
 * This file is part of the ContaoEventschopsolutionBundle.
 *
 * (c) Frank Hoppe
 *
 * @license LGPL-3.0-or-later
 */

use Schachbulle\ContaoEventschopsolutionBundle\EventListener\GetAllEventsListener;

$GLOBALS['TL_HOOKS']['getAllEvents'][] = [GetAllEventsListener::class, 'onGetAllEvents'];
