<?php

namespace Cash;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

final class EventListener implements Listener
{

    /** @var Plugin */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function join(PlayerJoinEvent $event)
    {
        $this->plugin->register($event->getPlayer());
    }

}