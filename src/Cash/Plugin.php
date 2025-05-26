<?php

declare (strict_types=1);

namespace Cash;

use Cash\command\CashCommand;
use Cash\command\PayCashCommand;
use Cash\command\SetCashCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SmartCommand\api\SmartCommandAPI;
use SmartCommand\message\DefaultMessages;

final class Plugin extends PluginBase
{

    const PREFIX = '§l§eCASH  §r§7';

    /** @var self */
    private static $instance;

    /** @var Config */
    private $cashList;

    public static function getInstance() : self 
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;    
    }

    public function onEnable()
    {
        $dir = $this->getDataFolder();
        if (!file_exists($dir))
        {
            mkdir($dir);
        }
        $this->cashList = new Config($dir . 'players_cash.json', Config::JSON);
        $this->getServer()->getPluginManager()->registerEvents(
            new EventListener($this),
            $this
        );
        SmartCommandAPI::registerCommands(
            'cash',
            [
                new CashCommand('cash', 'Ver seu cash ou de outro jogador', self::PREFIX, [], DefaultMessages::PORTUGUESE()),
                new PayCashCommand('paycash', 'Pagar um jogador', self::PREFIX, [], DefaultMessages::PORTUGUESE()),
                new SetCashCommand('setcash', 'Setar o cash de um jogador', self::PREFIX, [], DefaultMessages::PORTUGUESE())
            ]
        );
    }

    public function onDisable()
    {
        $this->cashList->save();
    }

    /**
     * @param Player|string $player
     * @return string
     */
    public static function hashPlayer($player) : string 
    {
        return strtolower($player instanceof Player ? $player->getName() : $player);
    }

    /**
     * @param Player|string $player
     * @param int $cash
     * @return void
     */
    public function setCash($player, int $cash)
    {
        $this->cashList->set(self::hashPlayer($player), $cash);
    }

    /**
     * @param string|Player $player
     * @return integer
     */
    public function getCash($player) : int 
    {
        return $this->cashList->get(self::hashPlayer($player), 0);
    }

    public function myCash($player) : int 
    {
        return $this->getCash($player);
    }

    public function register(Player $player)
    {
        $this->cashList->set(self::hashPlayer($player), 0);
    }

    /**
     * @param string|Player $player
     * @return bool
     */
    public function playerExists($player) : bool 
    {
        return $this->cashList->exists(self::hashPlayer($player));
    }

    /**
     * @param Player|string $player
     * @param integer $amount
     * @return void
     */
    public function addCash($player, int $amount)
    {
        $this->setCash($player, $this->getCash($player) + $amount);
    }

    /**
     * @param Player|string $player
     * @param integer $amount
     * @return bool
     */
    public function removeCash($player, int $amount) : bool 
    {
        $cash = $this->getCash($player);
        if ($cash >= $amount)
        {
            $this->setCash($player, $cash - $amount);
            return true;
        }
        return false;
    }

}