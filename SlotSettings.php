<?php 
namespace VanguardDK\Games\CrazyMonkey2IG;

use VanguardDK\Game;
use VanguardDK\GameLog;
use VanguardDK\Jackpot;
use VanguardDK\JackpotStat;
use VanguardDK\Lib\LicenseDK;
use VanguardDK\StatGame;
use VanguardDK\Transaction;
use VanguardDK\User;
use VanguardDK\Demogame;

class SlotSettings
{
    public $playerId = null;
    public $splitScreen = null;
    public $reelStrip1 = null;
    public $reelStrip2 = null;
    public $reelStrip3 = null;
    public $reelStrip4 = null;
    public $reelStrip5 = null;
    public $reelStrip6 = null;
    public $reelStripBonus1 = null;
    public $reelStripBonus2 = null;
    public $reelStripBonus3 = null;
    public $reelStripBonus4 = null;
    public $reelStripBonus5 = null;
    public $reelStripBonus6 = null;
    public $slotId = "";
    public $slotDBId = "";
    public $Line = null;
    public $scaleMode = null;
    public $numFloat = null;
    public $gameLine = null;
    public $Bet = null;
    public $Balance = null;
	public $demoBalans = null;
    public $SymbolGame = null;
    public $GambleType = null;
    public $lastEvent = null;
    public $Jackpots = [];
    public $keyController = null;
    public $slotViewState = null;
    public $hideButtons = null;
    public $slotReelsConfig = null;
    public $slotFreeCount = null;
    public $slotFreeMpl = null;
    public $slotWildMpl = null;
    public $slotExitUrl = null;
    public $slotBonus = null;
    public $slotBonusType = null;
    public $slotScatterType = null;
    public $slotGamble = null;
    public $fuseBet = null;
    public $cardsID = null;
    public $Paytable = [];
    public $slotSounds = [];
	public $demo = [];
    private $jacks = null;
    private $Bank = null;
    private $Percent = null;
    private $WinLine = null;
    private $WinGamble = null;
    private $Bonus = null;
    public $licenseDK = null;
    public function __construct($sid, $playerId)
    {
        $this->licenseDK = true;
        $checked = new LicenseDK();
        $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
        if( $license_notifications_array["notification_case"] != "notification_license_ok" ) 
        {
            $this->licenseDK = false;
        }
        $this->slotId = $sid;
        $this->playerId = $playerId;
        $user = User::find($this->playerId);
		if($this->playerId == 999999999){###########
			$demo = Demogame::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
			$this->DemoBalans = $demo->balance;
		}
        $game = Game::where("name", $this->slotId)->first();
        $this->scaleMode = $game->scaleMode;
        $this->numFloat = $game->numFloat;
        $this->cardsID["2D"] = 66;
        $this->cardsID["3D"] = 67;
        $this->cardsID["4D"] = 68;
        $this->cardsID["5D"] = 69;
        $this->cardsID["6D"] = 70;
        $this->cardsID["7D"] = 71;
        $this->cardsID["8D"] = 72;
        $this->cardsID["9D"] = 73;
        $this->cardsID["TD"] = 74;
        $this->cardsID["JD"] = 75;
        $this->cardsID["QD"] = 76;
        $this->cardsID["KD"] = 77;
        $this->cardsID["AD"] = 78;
        $this->cardsID["2C"] = 34;
        $this->cardsID["3C"] = 35;
        $this->cardsID["4C"] = 36;
        $this->cardsID["5C"] = 37;
        $this->cardsID["6C"] = 38;
        $this->cardsID["7C"] = 39;
        $this->cardsID["8C"] = 40;
        $this->cardsID["9C"] = 41;
        $this->cardsID["TC"] = 42;
        $this->cardsID["JC"] = 43;
        $this->cardsID["QC"] = 44;
        $this->cardsID["KC"] = 45;
        $this->cardsID["AC"] = 46;
        $this->cardsID["2S"] = 18;
        $this->cardsID["3S"] = 19;
        $this->cardsID["4S"] = 20;
        $this->cardsID["5S"] = 21;
        $this->cardsID["6S"] = 22;
        $this->cardsID["7S"] = 23;
        $this->cardsID["8S"] = 24;
        $this->cardsID["9S"] = 25;
        $this->cardsID["TS"] = 26;
        $this->cardsID["JS"] = 27;
        $this->cardsID["QS"] = 28;
        $this->cardsID["KS"] = 29;
        $this->cardsID["AS"] = 30;
        $this->cardsID["2H"] = 18;
        $this->cardsID["3H"] = 19;
        $this->cardsID["4H"] = 20;
        $this->cardsID["5H"] = 21;
        $this->cardsID["6H"] = 22;
        $this->cardsID["7H"] = 23;
        $this->cardsID["8H"] = 24;
        $this->cardsID["9H"] = 25;
        $this->cardsID["TH"] = 26;
        $this->cardsID["JH"] = 27;
        $this->cardsID["QH"] = 28;
        $this->cardsID["KH"] = 29;
        $this->cardsID["AH"] = 30;
        $this->Paytable[0] = [0, 0, 0, 0, 0, 0];
        $this->Paytable[1] = [0, 0, 0, 200, 1000, 5000];
        $this->Paytable[2] = [0, 0, 0, 100, 500, 2000];
        $this->Paytable[3] = [0, 0, 0, 30, 100, 500];
        $this->Paytable[4] = [0, 0, 0, 20, 50, 200];
        $this->Paytable[5] = [0, 0, 0, 10, 30, 100];
        $this->Paytable[6] = [0, 0, 0, 5, 10, 50];
        $this->Paytable[7] = [0, 0, 0, 3, 5, 20];
        $this->Paytable[8] = [0, 0, 0, 2, 3, 10];
        foreach( ["reelStrip1", "reelStrip2", "reelStrip3", "reelStrip4", "reelStrip5", "reelStrip6"] as $reelStrip ) 
        {
            if( $game->gamereel->$reelStrip != "" ) 
            {
                $data = explode(",", $game->gamereel->$reelStrip);
                foreach( $data as &$item ) 
                {
                    $item = str_replace("\"", "", $item);
                    $item = trim($item);
                }
                $this->$reelStrip = $data;
            }
        }
        $this->slotViewState = ($game->slotViewState == "" ? "Normal" : $game->slotViewState);
        if( isset($game->slotKeyConfig) ) 
        {
            $this->slotKeyConfig = $game->slotKeyConfig;
        }
        else
        {
            $this->slotKeyConfig = "{\"h_Exit\":[45,109,189],\"h_Bet\":[43,61,107],\"h_Start\":[13],\"h_Line1\":[49],\"h_Line3\":[50],\"h_Line5\":[51],\"h_Line7\":[52],\"h_Line9\":[53],\"h_AutoPlay\":[48],\"h_FullScreen\":[54],\"h_Help\":[55],\"h_MaxBet\":[56],\"h_Bet\":[61,43],\"h_Sound\":[57]}";
        }
        $this->keyController = ["13" => "uiButtonSpin,uiButtonSkip", "49" => "uiButtonInfo", "50" => "uiButtonCollect", "51" => "uiButtonExit2", "52" => "uiButtonLinesMinus", "53" => "uiButtonLinesPlus", "54" => "uiButtonBetMinus", "55" => "uiButtonBetPlus", "56" => "uiButtonGamble", "57" => "uiButtonRed", "48" => "uiButtonBlack", "189" => "uiButtonAuto", "187" => "uiButtonSpin"];
        $this->slotReelsConfig = [[425, 142, 3], [669, 142, 3], [913, 142, 3], [1157, 142, 3], [1401, 142, 3]];
        $this->slotBonusType = 1;
        $this->slotScatterType = 0;
        $this->splitScreen = ($game->monitor == 2 ? true : false);
        $this->slotBonus = true;
        $this->slotGamble = true;
        $this->slotFastStop = 1;
        $this->slotExitUrl = "/";
        $this->slotWildMpl = 1;
        $this->GambleType = 1;
        $this->slotFreeCount = 15;
        $this->slotFreeMpl = 1;
        $this->slotViewState = ($game->slotViewState == "" ? "Normal" : $game->slotViewState);
        $this->hideButtons = [];
        $this->jacks = Jackpot::get();
        $this->Line = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $this->gameLine = explode(",", $game->gameline);
        $this->Bet = explode(",", $game->bet);
		if($this->playerId != 999999999){
			$this->Balance = $user->balance;
		}else{
			$this->Balance = $this->DemoBalans;
		}
        $this->SymbolGame = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $this->Bank = $game->gamebank;
        $this->Percent = ($user->count_balance == 0 ? 100 : $game->percent);
        $this->WinLine = $game->game_win->winline;
        $this->WinGamble = $game->rezerv;
        $this->Bonus = $game->game_win->winbonus;
        $this->slotDBId = $game->id;
        $this->fuseBet = $game->cask;
    }
    public function GetDealerCard()
    {
        $tbvcqasermi = ["2", "3", "4", "5", "6", "7", "8", "9", "T", "J", "Q", "K", "A"];
        $kiolmjunp = ["C", "D", "S", "H"];
        $slot_stringes = [];
        shuffle($tbvcqasermi);
        shuffle($kiolmjunp);
        $slot_stringes = $tbvcqasermi[0] . $kiolmjunp[0];
        return $slot_stringes;
    }
    public function Bonus($bet, $fuseBet,$type)
    {
        $nu_nuRicter_sof = [0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 2, 2, 2, 2, 5, 5, 5, 5, 5, 10, 10, 10, 10, 10, 25, 25, 15, 15, 20, 20, 30, 30, 50, 50];
        shuffle($nu_nuRicter_sof);
        $dsapoinion_resion = 0;
        $nonuyion_ric = [];
        $_obf_0D362708262F09041B3F301C28093D17012909172E1011 = false;
        $nonlion_rustop_data = "";
        for( $i = 0; $i < 5; $i++ ) 
        {
			if($type == false){
				$gtion_jon_rio = $bet;
				$dsapoinion_resion += $gtion_jon_rio;
				$nonuyion_ric[] = "{\"Coef\":" . $nu_nuRicter_sof[$i] . ",\"Win\":" . $gtion_jon_rio . "}";
			}else{
					$gtion_jon_rio = $bet;
				$nonuyion_ric[] = "{\"Coef\":" . $bet . ",\"Win\":" .$bet . "}";
			}
		  if( $i >= 4 ) 
            {
                $_obf_0D362708262F09041B3F301C28093D17012909172E1011 = true;
            }
            if( $gtion_jon_rio == 0 && $fuseBet ) 
            {
                $fuseBet = false;
            }
            else if( $gtion_jon_rio == 0 ) 
            {
                $_obf_0D362708262F09041B3F301C28093D17012909172E1011 = false;
                break;
            }
        }
        if( $_obf_0D362708262F09041B3F301C28093D17012909172E1011 ) 
        {
            $_obf_0D2218070B130822270801365B3D331F172F050C1A3132 = [0, 0, 50, 100, 150];
            shuffle($_obf_0D2218070B130822270801365B3D331F172F050C1A3132);
            $gtion_jon_rio = $_obf_0D2218070B130822270801365B3D331F172F050C1A3132[0] * $bet;
            $nonlion_rustop_data = "\"BonusWins\":[" . implode(",", $nonuyion_ric) . "],\"SuperWin\":{\"Coef\":" . $_obf_0D2218070B130822270801365B3D331F172F050C1A3132[0] . ",\"Win\":" . $gtion_jon_rio . "},";
        }
        else
        {
            $nonlion_rustop_data = "\"BonusWins\":[" . implode(",", $nonuyion_ric) . "],";
        }
        return ["win" => $dsapoinion_resion, "info" => $nonlion_rustop_data];
    }
    public function SuperWin($bet)
    {
        $dsapoinion_resion = 0;
        $_obf_0D2218070B130822270801365B3D331F172F050C1A3132 = [150];
        shuffle($_obf_0D2218070B130822270801365B3D331F172F050C1A3132);
        $gtion_jon_rio = $_obf_0D2218070B130822270801365B3D331F172F050C1A3132[0] * $bet;
        $dsapoinion_resion += $gtion_jon_rio;
        $nonlion_rustop_data = "\"SuperWin\":{\"Coef\":" . $_obf_0D2218070B130822270801365B3D331F172F050C1A3132[0] . ",\"Win\":" . $gtion_jon_rio . "},";
        return ["win" => $dsapoinion_resion, "info" => $nonlion_rustop_data];
    }
    public function GetHistory()
    {
        $history = GameLog::whereRaw("game_id=? and user_id=? ORDER BY id DESC LIMIT 10", [$this->slotDBId, $this->playerId])->get();
        $this->lastEvent = "NULL";
        foreach( $history as $log ) 
        {
            $data_json_log = json_decode($log->str);
            if( isset($data_json_log->cmd) && $data_json_log->cmd == "start" ) 
            {
                $this->lastEvent = $log->str;
                break;
            }
        }
        if( isset($data_json_log) ) 
        {
            return $data_json_log;
        }
        else
        {
            return "NULL";
        }
    }
    public function UpdateJackpots($bet)
    {
        $percent_jacks = [];
        $pay_sum_jacks = 0;
        for( $i = 0; $i < count($this->jacks); $i++ ) 
        {
            $percent_jacks[$i] = $bet / 100 * $this->jacks[$i]->percent + $this->jacks[$i]->balance;
            if( $this->jacks[$i]->pay_sum < $percent_jacks[$i] ) 
            {
                $pay_sum_jacks = $this->jacks[$i]->pay_sum;
                $percent_jacks[$i] = $percent_jacks[$i] - $this->jacks[$i]->pay_sum;
                $this->SetBalance($this->jacks[$i]->pay_sum);
                Transaction::create(["user_id" => $this->playerId, "summ" => $this->jacks[$i]->pay_sum, "system" => $this->jacks[$i]->name]);
            }
            $this->jacks[$i]->update(["balance" => $percent_jacks[$i]]);
            $this->jacks[$i] = $this->jacks[$i]->refresh();
            if( $this->jacks[$i]->balance < $this->jacks[$i]->start_balance ) 
            {
                $summ = $this->jacks[$i]->start_balance;
                if( $summ > 0 ) 
                {
                    $this->jacks[$i]->increment("balance", $summ);
                    JackpotStat::create(["system" => "System", "type" => "add", "jackpot_id" => $this->jacks[$i]->id, "summ" => $summ]);
                }
            }
        }
        if( $pay_sum_jacks > 0 ) 
        {
            $pay_sum_jacks = sprintf("%01.2f", $pay_sum_jacks);
            $this->Jackpots["jackPay"] = $pay_sum_jacks;
        }
    }
    public function GetBank()
    {
        $game = Game::where("name", $this->slotId)->first();
        $this->Bank = $game->gamebank;
        return $this->Bank;
    }
    public function GetPercent()
    {
        return $this->Percent;
    }
    public function SetBank($sum)
    {
        $game = Game::where("name", $this->slotId)->first();
if($this->playerId != 999999999){   
			$game->gamebank += $sum;
			$game->save();
		}
        return $game;
    }
    public function SetBalance($sum)
    {
        $user = User::find($this->playerId);
		if($this->playerId == 999999999){###########
			$demo = Demogame::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
			$this->DemoBalans = $demo->balance;
		}
        if( $sum < 0 ) 
        {
            $user->increment("wager", $sum);
            $user->increment("count_balance", $sum);
        }

        /*
        if($this->playerId != 999999999){
			$user->increment("balance", $sum);   
			$user = $user->fresh();
		}else{
			$demo = Demogame::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
			$demo->increment("balance", $sum); 
		}*/

        if($this->playerId != 999999999){
            if($sum > 0) {
                $user->increment("balance2", $sum);
            }else {
                $user->increment("balance", $sum);
            }
            $user = $user->fresh();
        }else{
            $demo = Demogame::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
            if($sum > 0) {
                $demo->increment("balance2", $sum);
            }else {
                $demo->increment("balance", $sum);
            }
        }

        $user = $user->fresh();
        if( $user->balance == 0 ) 
        {
            $user->update(["wager" => 0, "bonus" => 0]);
        }
        if( $user->wager == 0 ) 
        {
            $user->update(["bonus" => 0]);
        }
        if( $user->wager < 0 ) 
        {
            $user->update(["wager" => 0, "bonus" => 0]);
        }
        if( $user->count_balance < 0 ) 
        {
            $user->update(["count_balance" => 0]);
        }
        return $user;
    }
    public function GetBalance()
    {
        $user = User::find($this->playerId);
		if($this->playerId == 999999999){###########
			$demo = Demogame::where(['ip' => $_SERVER['REMOTE_ADDR']])->first();
			$this->DemoBalans = $demo->balance;
		}
		if($this->playerId != 999999999){
			$this->Balance = $user->balance;
		}else{
			$this->Balance = $this->DemoBalans;
		}
		 return $this->Balance;
    }
    public function SaveLogReport($spinSymbols, $bet, $lines, $win, $slotState)
    {
        $slot_id_dupic = $this->slotId . " " . $slotState;
        if( $slotState == "freespin" ) 
        {
            $slot_id_dupic = $this->slotId . " FG";
        }
        else if( $slotState == "bet" ) 
        {
            $slot_id_dupic = $this->slotId . "";
        }
        else if( $slotState == "slotGamble" ) 
        {
            $slot_id_dupic = $this->slotId . " DG";
        }
        $this->GetBalance();
		if($this->playerId != 999999999){##############
			GameLog::create(["game_id" => $this->slotDBId, "user_id" => $this->playerId, "ip" => $_SERVER["REMOTE_ADDR"], "str" => $spinSymbols]);
			StatGame::create(["user_id" => $this->playerId, "balance" => $this->Balance, "bet" => $bet * $lines, "win" => $win, "game" => $slot_id_dupic]);
		}
    }
    public function GetSpinSettings($bet)
    {
        $bonusWin = 0;
        $spinWin = 0;
        $game = Game::where("name", $this->slotId)->first();
        $garant_win = $game->garant_win;
        $garant_bonus = $game->garant_bonus;
        $winbonus = $game->winbonus;
        $winline = $game->winline;
        $garant_win++;
        $garant_bonus++;
        $return = ["none", 0];
        if( $winbonus <= $garant_bonus ) 
        {
            $bonusWin = 1;
            $garant_bonus = 0;
            $game->winbonus = $this->getNewSpin($game, 0, 1);
        }
        else if( $winline <= $garant_win ) 
        {
            $spinWin = 1;
            $garant_win = 0;
            $game->winline = $this->getNewSpin($game, 1, 0);
        }
        $game->garant_win = $garant_win;
        $game->garant_bonus = $garant_bonus;
        $game->save();
        if( $bonusWin == 1 && $this->slotBonus ) 
        {
            $GetBank_data = $this->GetBank();
            $return = ["bonus", $GetBank_data];
        }
        else if( $spinWin == 1 || $bonusWin == 1 && !$this->slotBonus ) 
        {
            $GetBank_data = $this->GetBank();
            $return = ["win", $GetBank_data];
        }
        return $return;
    }
    public function getNewSpin($game, $spinWin = 0, $bonusWin = 0)
    {
        if( $spinWin ) 
        {
            $winline = explode(",", $game->game_win->winline);
            $number = rand(0, count($winline) - 1);
            return $winline[$number];
        }
        if( $bonusWin ) 
        {
            $winbonus = explode(",", $game->game_win->winbonus);
            $number = rand(0, count($winbonus) - 1);
            return $winbonus[$number];
        }
    }
    public function GetRandomScatterPos($rp)
    {
        $array_push_dip = [];
        for( $i = 0; $i < count($rp); $i++ ) 
        {
            if( $rp[$i] == "0" ) 
            {
                if( isset($rp[$i + 1]) && isset($rp[$i + 2]) ) 
                {
                    array_push($array_push_dip, $i);
                }
                if( isset($rp[$i + 1]) && isset($rp[$i - 1]) ) 
                {
                    array_push($array_push_dip, $i - 1);
                }
                if( isset($rp[$i - 2]) && isset($rp[$i - 1]) ) 
                {
                    array_push($array_push_dip, $i - 2);
                }
            }
        }
        shuffle($array_push_dip);
        return $array_push_dip[0];
    }
    public function GetGambleSettings()
    {
        $spinWin = rand(1, $this->WinGamble);
        return $spinWin;
    }
    public function GetReelStrips($winType)
    {
        if( !$winType ) 
        {
            $mt_siting_count = [];
            foreach( ["reelStrip1", "reelStrip2", "reelStrip3", "reelStrip4", "reelStrip5", "reelStrip6"] as $index => $reelStrip ) 
            {
                if( is_array($this->$reelStrip) && count($this->$reelStrip) > 0 ) 
                {
                    $mt_siting_count[$index + 1] = mt_rand(0, count($this->$reelStrip) - 3);
                }
            }
        }
        else
        {
            $cards_dup = [];
            foreach( ["reelStrip1", "reelStrip2", "reelStrip3", "reelStrip4", "reelStrip5", "reelStrip6"] as $index => $reelStrip ) 
            {
                if( is_array($this->$reelStrip) && count($this->$reelStrip) > 0 ) 
                {
                    $mt_siting_count[$index + 1] = $this->GetRandomScatterPos($this->$reelStrip);
                    $cards_dup[] = $index + 1;
                }
            }
            $random_strops = rand(3, count($cards_dup));
            shuffle($cards_dup);
            for( $i = 0; $i < count($cards_dup); $i++ ) 
            {
                if( $i < $random_strops ) 
                {
                    $mt_siting_count[$cards_dup[$i]] = $this->GetRandomScatterPos($this->{"reelStrip" . $cards_dup[$i]});
                }
                else
                {
                    $mt_siting_count[$cards_dup[$i]] = rand(0, count($this->{"reelStrip" . $cards_dup[$i]}) - 3);
                }
            }
        }
        $reel = ["rp" => []];
        foreach( $mt_siting_count as $index => $value ) 
        {
            $key = $this->{"reelStrip" . $index};
            $cnt = count($key);
            $key[-1] = $key[$cnt - 1];
            $key[$cnt] = $key[0];
            $reel["reel" . $index][0] = $key[$value];
            $reel["reel" . $index][1] = $key[$value + 1];
            $reel["reel" . $index][2] = $key[$value + 2];
            $reel["reel" . $index][3] = "";
            $reel["rp"][] = $value;
        }
        return $reel;
    }
}
