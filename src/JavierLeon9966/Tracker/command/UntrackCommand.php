<?php
namespace JavierLeon9966\Tracker\command;
use pocketmine\command\{Command, CommandSender, PluginIdentifiableCommand};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use JavierLeon9966\Tracker\Tracker;
class UntrackCommand extends Command implements PluginIdentifiableCommand{
	private $plugin;
	public function __construct(Tracker $plugin){
		$this->plugin = $plugin;
		parent::__construct(
			'untrack',
			'Untrack a player\'s location',
			'/untrack <name: player>'
		);
		$this->setPermission('tracker.command.untrack');
	}
	public function getPlugin(): Plugin{
		return $this->plugin;
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) == 0){
			throw InvalidCommandSyntaxException;
		}elseif(!$sender instanceof Player){
			$sender->sendMessage('This command must be executed as a player');
			return false;
		}
		$player = $sender->getServer()->getPlayer($args[0]);
		if($player === null){
			$sender->sendTranslation(TextFormat::RED.'%commands.generic.player.notFound');
			return true;
		}elseif($player === $sender){
			$sender->sendMessage('You can not untrack yourself');
			return true;
		}elseif(!$this->getPlugin()->isTracking($sender->getName(), $player)){
			$sender->sendMessage("You are already not tracking {$player->getName()}");
			return true;
		}
		$this->getPlugin()->removeTracker($sender->getName(), $player);
		$this->getPlugin()->updateCompass($sender);
		$sender->sendMessage(TextFormat::GREEN."Compass is no longer pointing to {$player->getName()}.");
		return true;
	}
}