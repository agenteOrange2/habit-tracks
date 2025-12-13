<?php

namespace App\Listeners;

use App\Services\DefaultDataService;
use Illuminate\Auth\Events\Registered;

class CreateDefaultUserData
{
    public function __construct(
        protected DefaultDataService $defaultDataService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $this->defaultDataService->createAllDefaults($event->user);
    }
}
