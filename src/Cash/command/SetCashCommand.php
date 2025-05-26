<?php

declare (strict_types=1);

namespace Cash\command;

use Cash\Plugin;
use pocketmine\command\CommandSender;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\utils\AdminPermissionTrait;

class SetCashCommand extends SmartCommand
{

    use AdminPermissionTrait;

    protected function prepare()
    {
        $this->argumentsDescription = $this->getDescription();
        $this->registerArguments(
            [
                new StringArgument('player_name'),
                new IntegerArgument('amount')
            ]
        );
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $playerName = $args->getString('player_name');
        $amount = $args->getInteger('amount');
        Plugin::getInstance()->setCash($playerName, $amount);
        $sender->sendMessage(Plugin::PREFIX . "Você setou §e\$ §f{$amount} §7de cash para o jogador §f{$playerName} §7com §a§lSucesso§r§7!");
    }
}