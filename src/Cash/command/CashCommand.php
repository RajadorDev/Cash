<?php

declare (strict_types=1);

namespace Cash\command;

use Cash\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class CashCommand extends SmartCommand
{

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->argumentsDescription = $this->getDescription();
        $this->setPrefix(Plugin::PREFIX);
        $this->registerRule(new OnlyInGameCommandRule);
        $this->registerRule(new CooldownRule(
            CooldownRule::secondsToMs(5)
        ));
        $this->registerArguments(
            [
                new StringArgument('player', false)
            ]
        );
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        if ($args->has('player'))
        {
            $target = $args->getString('player');
            $targetPlayer = Server::getInstance()->getPlayer($target);
            if ($targetPlayer instanceof Player)
            {
                $target = $targetPlayer->getName();
            }
        } else {
            $target = $sender->getName();
        }
        if (Plugin::getInstance()->playerExists($target))
        {
            $cash = Plugin::getInstance()->getCash($target);
            $sender->sendMessage(Plugin::PREFIX . '§7O jogador §f' . $target . ' §7tem §e$ §f' . $cash . ' §7de cash em sua conta.');
        } else {
            $sender->sendMessage(Plugin::PREFIX . '§cJogador §f' . $target . ' §cnunca entrou neste servidor!');
        }
    }

}