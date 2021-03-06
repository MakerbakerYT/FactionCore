<?php
namespace ConstructStudios\Factions\command;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use ConstructStudios\Factions\Loader;

class FEnemy extends BaseCommand {

    /**
     * @return string
     */
    public function getCommand(): string {
        return "enemy";
    }

    /**
     * @param bool $player
     * @return string
     */
    public function getDescription(bool $player = true): string {
        return "Enemy a faction";
    }

    /**
     * @return array
     */
    public function getAliases(): array {
        return[
            "en"
        ];
    }

    /**
     * @param bool $player
     * @return array
     */
    public function getUsage(bool $player = true): array {
        return [
            "[faction: string]"
        ];
    }

    /**
     * @param ConsoleCommandSender $console
     * @param array $args
     */
    protected function onConsoleRun(ConsoleCommandSender $console, array $args): void {
        $this->noConsole($console);
    }

    /**
     * @param Player $player
     * @param array $args
     */
    protected function onPlayerRun(Player $player, array $args): void {
        do{
            if(($member = $this->getLoader()->getMember($player))->getFaction() == null){
                $player->sendMessage(Loader::ALERT_RED . "You don't have a faction");

                break;
            }
            if($member->isCanEnemyFaction() == false){
                $player->sendMessage(Loader::ALERT_RED . "You don't have permission to enemy a faction");

                break;
            }
            if(isset($args[0]) == false){
                $this->sendUsage(0, $player);

                break;
            }
            $name = array_shift($args);

            if(($target = $this->getLoader()->getFaction($name)) == null){
                $player->sendMessage(Loader::ALERT_RED . "The faction: " . $name . " doesn't exist");

                break;
            }
            if($target === $member->getFaction()){
                $player->sendMessage(Loader::ALERT_RED . "You cannot enemy your own faction!");

                break;
            }
            if($member->getFaction()->isEnemy($target)){
                $player->sendMessage(Loader::ALERT_RED . "That faction is already an enemy!");

                break;
            }

            $member->getFaction()->addEnemy($target);
            $member->getFaction()->removeAlly($target);
            $target->removeAlly($member->getFaction());

            $member->getFaction()->broadcastMessage(Loader::ALERT_RED . "Faction: " . $name . " is now marked as our enemy by " . $player->getName());

            $target->broadcastMessage(Loader::ALERT_RED . "Faction: " . $member->getFaction()->getName() . " has marked us as an enemy");
        }while(false);
    }
}