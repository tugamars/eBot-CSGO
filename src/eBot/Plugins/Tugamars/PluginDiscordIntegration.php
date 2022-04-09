<?php


namespace eBot\Plugins\Tugamars;


use eBot\Exception\PluginException;
use eBot\Plugins\Helpers\DiscordTrait;
use eBot\Plugins\Helpers\DiscordWebhookEmbed;
use eBot\Plugins\Plugin;
use eTools\Utils\Logger;

class PluginDiscordIntegration implements Plugin
{
    private $webhooksUrl;

    public function init($config)
    {
        Logger::log("Init PluginDiscordIntegration");

        $this->webhooksUrl=array(
            'logs'=>$config["webhooklogurl"],
            'live'=>$config["webhookurl"]
        );

        Logger::log("Webhook URL to perform: " . $this->webhooksUrl['logs']);
        Logger::log("Webhook URL to perform: " . $this->webhooksUrl['live']);
    }

    public function getEventList()
    {
        return array(\eBot\Events\EventDispatcher::EVENT_ROUNDSCORED,\eBot\Events\EventDispatcher::EVENT_MATCH_END,\eBot\Events\EventDispatcher::EVENT_SAY,\eBot\Events\EventDispatcher::EVENT_KILL,\eBot\Events\EventDispatcher::EVENT_MATCH_START,\eBot\Events\EventDispatcher::EVENT_HALFTIME_REACHED,\eBot\Events\EventDispatcher::EVENT_PAUSE_REQUESTED);
    }

    public function onStart()
    {
        Logger::log("Starting " . get_class($this));
    }

    public function onReload()
    {
        Logger::log("Reloading " . get_class($this));
    }

    public function onEnd()
    {
        Logger::log("Ending " . get_class($this));
    }

    public function onEventAdded($name)
    {
    }

    public function onEventRemoved($name)
    {
    }

    public function onEvent($event)
    {
        $embed=null;
        $title="";
        $message="‌";
        $embedMessage="";
        $sendTo=array();

        switch(get_class($event)){
            case \eBot\Events\EventDispatcher::EVENT_SAY:
                $sendTo=array('logs');

                $type=$event->getType();
                if($type === 1){
                    $type="Team";
                } else {
                    $type="All";
                }
                $embedMessage="[".$type."] (".$event->getUserTeam().") " . $event->getUserName().": " . $event->getText();

                break;
            case \eBot\Events\EventDispatcher::EVENT_ROUNDSCORED:
                $sendTo=array('logs');
                $embedMessage="Round ended";
                break;
            case \eBot\Events\EventDispatcher::EVENT_MATCH_START:
                $sendTo=array('logs','live');
                $embedMessage="Match started";
                break;
            case \eBot\Events\EventDispatcher::EVENT_MATCH_END:
                $sendTo=array('logs','live');
                $embedMessage="Match ended";
                break;
            case \eBot\Events\EventDispatcher::EVENT_HALFTIME_REACHED:
                $sendTo=array('logs','live');
                $embedMessage="Halftime reached";
                break;
            case \eBot\Events\EventDispatcher::EVENT_PAUSE_REQUESTED:
                $sendTo=array('logs','live');
                $embedMessage=strtoupper($event->getType())." Pause requested by " . $event->getTeamName() . "(" . $event->getTeamSide() . ")";
                break;
            case \eBot\Events\EventDispatcher::EVENT_KILL:
                $sendTo=array('logs');
                $embedMessage=$event->getUserName() . "(".$event->getUserTeam().") killed " .  $event->getKilledUserName() . "(".$event->getKilledUserTeam().") with " . $event->getWeapon();
                break;
            default:
                return false;
                break;
        }

        foreach($sendTo as $t){

            $embed=new DiscordWebhookEmbed();

            $matchInfo=$event->getMatch()->getMatchInformation();
            $embed->addEmbed('','**'.$matchInfo["team_a"]["name"] . "** [" . $matchInfo["team_a"]["score"] . "] " . "vs [" . $matchInfo["team_b"]["score"] . "] **" . $matchInfo["team_b"]["name"] . "** #". $event->getMatch()->getMatchId() . " :information_source: " . $matchInfo["status_text"] ."\n".$embedMessage,'',null,0x00aaf9);

            DiscordTrait::sendMessageToWebhook($message,$this->webhooksUrl[$t],$embed);
        }

        return false;
    }
}