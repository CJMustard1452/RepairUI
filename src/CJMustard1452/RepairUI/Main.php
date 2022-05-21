<?php

declare(strict_types=1);

namespace CJMustard1452\FixUI;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\FormAPI;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->economyAPI = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($sender instanceof Player){
			if($sender->hasPermission("fix.cmd")){
				if(isset($args[0]) && $args[0] == "all"){
					$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 All the tools in your inventory were repaired.");
					foreach($sender->getInventory()->getContents() as $index => $item){
						if($item instanceof Tool || $item instanceof Armor){
							if($item->getDamage() > 0){
								$sender->getInventory()->setItem($index, $item->setDamage(0));
							}
						}
					}
				}elseif(isset($args[0]) && $args[0] == "hand"){
					if($sender->getInventory()->getItemInHand() instanceof Tool || $sender->getInventory()->getItemInHand() instanceof Armor){
						if($sender->getInventory()->getItemInHand()->getDamage() > 0){
							$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 Item successfully repaired.");
							$sender->getInventory()->setItem($sender->getInventory()->getHeldItemIndex(), $sender->getInventory()->getItemInHand()->setDamage(0));
						}else{
							$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 This item already has maximum durability.");
						}
					}else{
						$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 This item cannot be repaired.");
					}
				}else{
					$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 Usage: /repair hand/all");
				}
			}else{
				$form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, ?array $data = null){
					if($data == true){
						if(implode($data) == 0){
							if($this->economyAPI->myMoney($sender) >= 500){
								if($sender->getInventory()->getItemInHand() instanceof Tool || $sender->getInventory()->getItemInHand() instanceof Armor){
									if($sender->getInventory()->getItemInHand()->getDamage() > 0){
										$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 Item successfully repaired.");
										$this->economyAPI->reduceMoney($sender, 500);
										$sender->getInventory()->setItem($sender->getInventory()->getHeldItemIndex(), $sender->getInventory()->getItemInHand()->setDamage(0));
									}else{
										$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 This item already has maximum durability.");
									}
								}else{
									$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 This item cannot be repaired.");
								}
							}else{
								$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 You do not have enough money to buy this.");
							}
						}elseif(implode($data) == "1"){
							if($this->economyAPI->myMoney($sender) >= 10000){
								$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 All the tools in your inventory were repaired.");
								$this->economyAPI->reduceMoney($sender, 10000);
								foreach($sender->getInventory()->getContents() as $index => $item){
									if($item instanceof Tool || $item instanceof Armor){
										if($item->getDamage() > 0){
											$sender->getInventory()->setItem($index, $item->setDamage(0));
										}
									}
								}
							}else{
								$sender->sendMessage("§l§8(§bREPAIR§8)§r§7 You do not have enough money to buy this.");
							}
						}
					}
				});
				$form->setTitle("§l§8(§bRepair Items§8)");
				$form->addLabel("§cRepair Hand:§a $500 \n§cRepair Inventory:§a $10,000");
				$form->addDropdown("", ["§0Hand", "§0Inventory"]);
				$form->sendToPlayer($sender);
			}
		}else{
			$sender->sendMessage("This command can only be run in game.");
		}
		return true;
	}
}
