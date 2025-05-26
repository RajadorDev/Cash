<?php

declare (strict_types=1);

namespace Cash\command;

use Cash\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use SmartCommand\command\argument\IntegerArgument;
use SmartCommand\command\argument\StringArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\utils\MemberPermissionTrait;

class PayCashCommand extends SmartCommand
{

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->argumentsDescription = $this->getDescription();
        $this->registerRules(
            new OnlyInGameCommandRule,
            new CooldownRule(CooldownRule::secondsToMs(3))
        );
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
        $cash = Plugin::getInstance()->getCash($sender);
        if ($amount > 0)
        {
            if ($cash >= $amount)
            {
                if (Plugin::getInstance()->playerExists($playerName))
                {
                    Plugin::getInstance()->addCash($playerName, $amount);
                    Plugin::getInstance()->removeCash($sender, $amount);
                    if ($targetPlayer = Server::getInstance()->getPlayerExact($playerName))
                    {
                        $targetPlayer->sendMessage(Plugin::PREFIX . "§7Você recebeu §e\$ §f{$amount} §7do jogador §f{$sender->getName()}§7.");
                    }
                    $sender->sendMessage(Plugin::PREFIX . "§7Você mandou §e\$§f $amount §7de cash para o jogador §f{$playerName} §7com §a§lSucesso§r§7!");
                } else {
                    $sender->sendMessage(Plugin::PREFIX . '§cJogador §f' . $playerName . '§c não encontrado!');
                }
            } else {
                $sender->sendMessage(Plugin::PREFIX . '§cVocê não tem esta quantidade de cash em sua conta!');
            }
        } else {
            $sender->sendMessage(Plugin::PREFIX . '§cVocê não pode mandar §f0 §cde cash!');
        }
    }
}