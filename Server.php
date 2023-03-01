<?php 
namespace VanguardDK\Games\CrazyMonkey2IG;

use VanguardDK\Lib\LicenseDK;

class Server
{
    public function get($request, $game, $userId)
    {
        $response = "";
        $checked = new LicenseDK();
        $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
        if( $license_notifications_array["notification_case"] != "notification_license_ok" ) 
        {
            $response = "{\"responseEvent\":\"error\",\"responseType\":\"error\",\"serverResponse\":\"Error LicenseDK\"}";
            exit( $response );
        }
        
        $slotSettings = new SlotSettings($game, $userId);
        $json_data = json_decode(trim(file_get_contents("php://input")), true);
        if( $json_data["action"] == "start" ) 
        {
            if( !in_array($json_data["bet"], $slotSettings->Bet) ) 
            {
                $response = "{\"responseEvent\":\"error\",\"responseType\":\"" . $json_data["action"] . "\",\"serverResponse\":\"invalid bet\"}";
                exit( $response );
            }
            if( $json_data["lines"] < 1 || $json_data["lines"] > 9 ) 
            {
                $response = "{\"responseEvent\":\"error\",\"responseType\":\"" . $json_data["action"] . "\",\"serverResponse\":\"invalid lines\"}";
                exit( $response );
            }
            if( $slotSettings->GetBalance() < ($json_data["lines"] * $json_data["bet"]) ) 
            {
                $response = "{\"responseEvent\":\"error\",\"responseType\":\"" . $json_data["action"] . "\",\"serverResponse\":\"invalid balance\"}";
                exit( $response );
            }
        }
        else if( $json_data["action"] == "risk" && session("CrazyMonkey2IGWin") <= 0 ) 
        {
            $response = "{\"responseEvent\":\"error\",\"responseType\":\"" . $json_data["action"] . "\",\"serverResponse\":\"invalid gamble state\"}";
            exit( $response );
        }
        switch( $json_data["action"] ) 
        {
            case "super":
                $bet = session("CrazyMonkey2IGLines");
                $lines = session("CrazyMonkey2IGBet");
                $bank = $slotSettings->GetBank();
                for( $i = 0; $i <= 1000; $i++ ) 
                {
                    $diclys_hinh_ki = $slotSettings->SuperWin($bet * $lines);
                    $totalWin = $diclys_hinh_ki["win"];
                    $gonhit_des_gur = $diclys_hinh_ki["info"];
                    if( $totalWin <= $bank ) 
                    {
                        break;
                    }
                }
                if( $totalWin > 0 ) 
                {
                    $slotSettings->SetBalance($totalWin);
                    $slotSettings->SetBank($totalWin * -1);
                }
                $balance = $slotSettings->GetBalance();
                $response = "{\"Amount\":\"" . $balance . "\"," . $gonhit_des_gur . "\"Credit\":" . $balance . ",\"Denomination\":\"1.00\",\"RawCredit\":" . $balance . ",\"Win\":" . $totalWin . ",\"args\":{\"Super\":1},\"cmd\":\"super\",\"time\":\"20190328T124737\"}";
                $slotSettings->SaveLogReport($response, $bet, $lines, $totalWin, "BG2");
                break;
            case "init":
                $balance = $slotSettings->GetBalance();
                $bet_null_hisrory = $slotSettings->GetHistory();
                if( $bet_null_hisrory != "NULL" ) 
                {
                    $bet = $bet_null_hisrory->Bet;
                    $lines = $bet_null_hisrory->Lines;
                    $reels = $slotSettings->GetReelStrips(false);
                    $dyuiopmnuyby = "[" . implode(",", $bet_null_hisrory->Reels[0]) . "]," . "[" . implode(",", $bet_null_hisrory->Reels[1]) . "]," . "[" . implode(",", $bet_null_hisrory->Reels[2]) . "]";
                }
                else
                {
                    $bet = 1;
                    $lines = 1;
                    $reels = $slotSettings->GetReelStrips(false);
                    $dyuiopmnuyby = "[" . $reels["reel1"][0] . "," . $reels["reel2"][0] . "," . $reels["reel3"][0] . "," . $reels["reel4"][0] . "," . $reels["reel5"][0] . "],";
                    $dyuiopmnuyby .= ("[" . $reels["reel1"][1] . "," . $reels["reel2"][1] . "," . $reels["reel3"][1] . "," . $reels["reel4"][1] . "," . $reels["reel5"][1] . "],");
                    $dyuiopmnuyby .= ("[" . $reels["reel1"][2] . "," . $reels["reel2"][2] . "," . $reels["reel3"][2] . "," . $reels["reel4"][2] . "," . $reels["reel5"][2] . "]");
                }
                $response = "{\"Amount\":\"" . $balance . "\",\"BetArray\":[" . implode(",", $slotSettings->Bet) . "],\"Credit\":\"" . $balance . "\",\"Currency\":\"".env('CURRENCY')."\",\"Denomination\":\"1.00\",\"FuseBet\":" . $slotSettings->fuseBet . ",\"HelpCoef\":[[0,0,0],[200,1000,5000],[100,500,2000],[30,100,500],[20,50,200],[10,30,100],[5,10,50],[3,5,20],[2,3,10]],\"LastBet\":" . $bet . ",\"LastFuse\":false,\"LastLines\":" . $lines . ",\"MaxWin\":675000,\"RawCredit\":1000,\"Reels\":[" . $dyuiopmnuyby . "],\"Win\":0,\"slotViewState\":\"" . $slotSettings->slotViewState . "\",\"slotKeyConfig\":" . $slotSettings->slotKeyConfig . ",\"args\":{},\"cmd\":\"init\",\"time\":\"20181111T111512\"}";
                break;
            case "status":
                if( !$request->session()->has("CrazyMonkey2IGLines") || !$request->session()->has("CrazyMonkey2IGBet") ) 
                {
                    $request->session()->put("CrazyMonkey2IGLines", 1);
                    $request->session()->put("CrazyMonkey2IGBet", 1);
                }
                $balance = $slotSettings->GetBalance();
                if( session("CrazyMonkey2IGWin") > 0 ) 
                {
                    $balance = $balance - session("CrazyMonkey2IGWin");
                }
                $response = "{\"Amount\":\"" . $balance . "\",\"BetArray\":[" . implode(",", $slotSettings->Bet) . "],\"Credit\":\"" . $balance . "\",\"Currency\":\"".env('CURRENCY')."\",\"Denomination\":\"1.00\",\"FuseBet\":" . $slotSettings->fuseBet . ",\"HelpCoef\":[[0,0,0],[200,1000,5000],[100,500,2000],[30,100,500],[20,50,200],[10,30,100],[5,10,50],[3,5,20],[2,3,10]],\"LastBet\":" . session("CrazyMonkey2IGBet") . ",\"LastFuse\":false,\"LastLines\":" . session("CrazyMonkey2IGLines") . ",\"MaxWin\":675000,\"RawCredit\":\"" . $balance . "\",\"Reels\":[[7,1,5,3,8],[5,3,6,7,7],[6,8,7,8,1]],\"Win\":0,\"args\":{},\"cmd\":\"status\",\"time\":\"20181111T111512\"}";
                break;
            case "start":
                $balance = $slotSettings->GetBalance();
                $bet = $json_data["bet"];
                $lines = $json_data["lines"];
				
	
				$fiders_infort = false;
				if($lines == 9 && $bet * $lines == '45'){
					if(session("CrazyMonkey2IGBet_netling_3") < 1){
						$request->session()->put("CrazyMonkey2IGBet_netling_3", session("CrazyMonkey2IGBet_netling_3") + 1);					
					}
				}
				if($lines == 9 && $bet * $lines == '135'){
					if(session("CrazyMonkey2IGBet_netling_5") < 1){						
						$request->session()->put("CrazyMonkey2IGBet_netling_5", session("CrazyMonkey2IGBet_netling_5") + 1);					
					}
				}
				if($lines == 9 && $bet * $lines == '180'){
					if(session("CrazyMonkey2IGBet_netling_7") < 1){				
						$request->session()->put("CrazyMonkey2IGBet_netling_7", session("CrazyMonkey2IGBet_netling_7") + 1);
					}
				}
				if($lines == 9 && $bet * $lines == '90' && session("CrazyMonkey2IGBet_netling_3") >= 1 && session("CrazyMonkey2IGBet_netling_5") >=  1 && session("CrazyMonkey2IGBet_netling_3") >= 1){
					$fiders_infort = true;
					$sugrut_type = true;
					$suma_count = 5;
					$request->session()->put("CrazyMonkey2IGBet_netling_3", 1);
					$request->session()->put("CrazyMonkey2IGBet_netling_5", 1);
					$request->session()->put("CrazyMonkey2IGBet_netling_7", 1);
				}
				
				
				
                $rand_lic = false;
            
                $dibit_resinhik = $slotSettings->GetSpinSettings($bet * $lines);
                if( $dibit_resinhik[0] == "bonus" ) 
                {
                    $fiders_infort = true;
                }
                else if( $dibit_resinhik[0] == "win" ) 
                {
                    $rand_lic = true;
                }
                $bank = $dibit_resinhik[1];
                $fuseBet = false;
                if( $slotSettings->fuseBet <= ($bet * $lines) ) 
                {
                    $fuseBet = true;
                }
                $linesId = [];
                $linesId[1] = [2, 2, 2, 2, 2];
                $linesId[2] = [1, 1, 1, 1, 1];
                $linesId[3] = [3, 3, 3, 3, 3];
                $linesId[4] = [1, 2, 3, 2, 1];
                $linesId[5] = [3, 2, 1, 2, 3];
                $linesId[6] = [1, 1, 2, 1, 1];
                $linesId[7] = [3, 3, 2, 3, 3];
                $linesId[8] = [2, 3, 3, 3, 2];
                $linesId[9] = [2, 1, 1, 1, 2];
                $wild = 1;
                $scatter = 0;
                for( $i = 0; $i <= 1500; $i++ ) 
                {
                    if( $fiders_infort ) 
                    {
						if(isset($sugrut_type)){
							$diclys_hinh_ki = $slotSettings->Bonus($suma_count, $fuseBet, true);
						}else{
							$diclys_hinh_ki = $slotSettings->Bonus($bet * $lines, $fuseBet);
						}
                        $totalWin = $diclys_hinh_ki["win"];
                        $gonhit_des_gur = $diclys_hinh_ki["info"];
                    }
                    else
                    {
                        $totalWin = 0;
                        $gonhit_des_gur = "";
                    }
                    $reels = $slotSettings->GetReelStrips($fiders_infort);
                    $lineWins = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    $LineWins_json_run = [];
                    for( $line = 1; $line <= $lines; $line++ ) 
                    {
                        $Pos_jon_dumpersing = [];
                        $data_numersing_sofinger = [-1, -1, -1, -1, -1, -1];
                        for( $docristion_jok = 0; $docristion_jok <= 4; $docristion_jok++ ) 
                        {
                            $Pos_jon_dumpersing[$docristion_jok] = $linesId[$line][$docristion_jok] - 1;
                            $data_numersing_sofinger[$docristion_jok + 1] = $reels["reel" . ($docristion_jok + 1)][$Pos_jon_dumpersing[$docristion_jok]];
                        }
                        $numer_obidjec_cload = null;
                        foreach( $slotSettings->Paytable as $vinhon_deric_cart => $numer_obid_id_kosloton ) 
                        {
                            if( $vinhon_deric_cart == 1 ) 
                            {
                                $wild = -1;
                            }
                            else
                            {
                                $wild = 2;
                            }
                            if( ($data_numersing_sofinger[1] == $vinhon_deric_cart || $data_numersing_sofinger[1] == $wild) && ($data_numersing_sofinger[2] == $vinhon_deric_cart || $data_numersing_sofinger[2] == $wild) && ($data_numersing_sofinger[3] == $vinhon_deric_cart || $data_numersing_sofinger[3] == $wild) ) 
                            {
                                $Prev_Win_data = $numer_obid_id_kosloton[3] * $bet;
                                if( $lineWins[$line] < $Prev_Win_data ) 
                                {
                                    $lineWins[$line] = $Prev_Win_data;
                                    $numer_obidjec_cload = (object)["Pos" => [$Pos_jon_dumpersing[0], $Pos_jon_dumpersing[1], $Pos_jon_dumpersing[2], -1, -1], "Element" => $vinhon_deric_cart, "Count" => 3, "Line" => $line, "Coef" => $numer_obid_id_kosloton[3], "Win" => $Prev_Win_data];
                                }
                            }
                            if( ($data_numersing_sofinger[4] == $vinhon_deric_cart || $data_numersing_sofinger[4] == $wild) && ($data_numersing_sofinger[5] == $vinhon_deric_cart || $data_numersing_sofinger[5] == $wild) && ($data_numersing_sofinger[3] == $vinhon_deric_cart || $data_numersing_sofinger[3] == $wild) ) 
                            {
                                $Prev_Win_data = $numer_obid_id_kosloton[3] * $bet;
                                if( $lineWins[$line] < $Prev_Win_data ) 
                                {
                                    $lineWins[$line] = $Prev_Win_data;
                                    $numer_obidjec_cload = (object)["Pos" => [$Pos_jon_dumpersing[0], $Pos_jon_dumpersing[1], $Pos_jon_dumpersing[2], -1, -1], "Element" => $vinhon_deric_cart, "Count" => 3, "Line" => $line, "Coef" => $numer_obid_id_kosloton[3], "Win" => $Prev_Win_data];
                                }
                            }
                            if( ($data_numersing_sofinger[1] == $vinhon_deric_cart || $data_numersing_sofinger[1] == $wild) && ($data_numersing_sofinger[2] == $vinhon_deric_cart || $data_numersing_sofinger[2] == $wild) && ($data_numersing_sofinger[3] == $vinhon_deric_cart || $data_numersing_sofinger[3] == $wild) && ($data_numersing_sofinger[4] == $vinhon_deric_cart || $data_numersing_sofinger[4] == $wild) ) 
                            {
                                $Prev_Win_data = $numer_obid_id_kosloton[4] * $bet;
                                if( $lineWins[$line] < $Prev_Win_data ) 
                                {
                                    $lineWins[$line] = $Prev_Win_data;
                                    $numer_obidjec_cload = (object)["Pos" => [$Pos_jon_dumpersing[0], $Pos_jon_dumpersing[1], $Pos_jon_dumpersing[2], $Pos_jon_dumpersing[3], -1], "Element" => $vinhon_deric_cart, "Count" => 4, "Line" => $line, "Coef" => $numer_obid_id_kosloton[4], "Win" => $Prev_Win_data];
                                }
                            }
                            if( ($data_numersing_sofinger[5] == $vinhon_deric_cart || $data_numersing_sofinger[5] == $wild) && ($data_numersing_sofinger[2] == $vinhon_deric_cart || $data_numersing_sofinger[2] == $wild) && ($data_numersing_sofinger[3] == $vinhon_deric_cart || $data_numersing_sofinger[3] == $wild) && ($data_numersing_sofinger[4] == $vinhon_deric_cart || $data_numersing_sofinger[4] == $wild) ) 
                            {
                                $Prev_Win_data = $numer_obid_id_kosloton[4] * $bet;
                                if( $lineWins[$line] < $Prev_Win_data ) 
                                {
                                    $lineWins[$line] = $Prev_Win_data;
                                    $numer_obidjec_cload = (object)["Pos" => [$Pos_jon_dumpersing[0], $Pos_jon_dumpersing[1], $Pos_jon_dumpersing[2], $Pos_jon_dumpersing[3], -1], "Element" => $vinhon_deric_cart, "Count" => 4, "Line" => $line, "Coef" => $numer_obid_id_kosloton[4], "Win" => $Prev_Win_data];
                                }
                            }
                            if( ($data_numersing_sofinger[1] == $vinhon_deric_cart || $data_numersing_sofinger[1] == $wild) && ($data_numersing_sofinger[2] == $vinhon_deric_cart || $data_numersing_sofinger[2] == $wild) && ($data_numersing_sofinger[3] == $vinhon_deric_cart || $data_numersing_sofinger[3] == $wild) && ($data_numersing_sofinger[4] == $vinhon_deric_cart || $data_numersing_sofinger[4] == $wild) && ($data_numersing_sofinger[5] == $vinhon_deric_cart || $data_numersing_sofinger[5] == $wild) ) 
                            {
                                $Prev_Win_data = $numer_obid_id_kosloton[5] * $bet;
                                if( $lineWins[$line] < $Prev_Win_data ) 
                                {
                                    $lineWins[$line] = $Prev_Win_data;
                                    $numer_obidjec_cload = (object)["Pos" => [$Pos_jon_dumpersing[0], $Pos_jon_dumpersing[1], $Pos_jon_dumpersing[2], $Pos_jon_dumpersing[3], $Pos_jon_dumpersing[4]], "Element" => $vinhon_deric_cart, "Count" => 5, "Line" => $line, "Coef" => $numer_obid_id_kosloton[5], "Win" => $Prev_Win_data];
                                }
                            }
                        }
                        if( $numer_obidjec_cload != null ) 
                        {
                            $LineWins_json_run[] = $numer_obidjec_cload;
                            $totalWin += $lineWins[$line];
                        }
                    }
                    if( $i >= 1200 ) 
                    {
                        $fiders_infort = false;
                        $rand_lic = false;
                    }
                    if( $i >= 1500 ) 
                    {
                          $fiders_infort = false;
                        $rand_lic = false;
                    }
                    $bonusSym = 0;
                    for( $docristion_jok = 0; $docristion_jok <= 4; $docristion_jok++ ) 
                    {
                        if( $reels["reel" . ($docristion_jok + 1)][0] == 0 ) 
                        {
                            $bonusSym++;
                        }
                        if( $reels["reel" . ($docristion_jok + 1)][1] == 0 ) 
                        {
                            $bonusSym++;
                        }
                        if( $reels["reel" . ($docristion_jok + 1)][2] == 0 ) 
                        {
                            $bonusSym++;
                        }
                    }
                    if( $bonusSym >= 3 && !$fiders_infort ) 
                    {
                    }
                    else if( $bank < $totalWin ) 
                    {
                    }
                    else
                    {
                        if( $totalWin > 0 && $fiders_infort ) 
                        {
                            break;
                        }
                        if( $totalWin > 0 && $rand_lic ) 
                        {
                            break;
                        }
                        if( $totalWin == 0 && !$rand_lic ) 
                        {
                            break;
                        }
                    }
                }
                $nubion_retio = $totalWin - ($bet * $lines);
                if( $nubion_retio != 0 ) 
                {
                    $slotSettings->SetBalance($nubion_retio);
                    $slotSettings->SetBank($nubion_retio * -1);
                }
                $slotSettings->UpdateJackpots($bet * $lines);
                $dyuiopmnuyby = "[" . $reels["reel1"][0] . "," . $reels["reel2"][0] . "," . $reels["reel3"][0] . "," . $reels["reel4"][0] . "," . $reels["reel5"][0] . "],";
                $dyuiopmnuyby .= ("[" . $reels["reel1"][1] . "," . $reels["reel2"][1] . "," . $reels["reel3"][1] . "," . $reels["reel4"][1] . "," . $reels["reel5"][1] . "],");
                $dyuiopmnuyby .= ("[" . $reels["reel1"][2] . "," . $reels["reel2"][2] . "," . $reels["reel3"][2] . "," . $reels["reel4"][2] . "," . $reels["reel5"][2] . "]");
                $balance = $balance - ($bet * $lines);
                $RiskCard_riop = '3D';
				
                $response = "{\"bs\":\"" . $bonusSym . "\",\"Amount\":\"" . $balance . "\"," . $gonhit_des_gur . "\"Credit\":" . $balance . ",\"Fuse\":false,\"LineWins\":" . json_encode($LineWins_json_run) . ",\"RawCredit\":" . $balance . ",\"RiskCard\":" . $slotSettings->cardsID[$RiskCard_riop] . ",\"Reels\":[" . $dyuiopmnuyby . "],\"Lines\":" . $lines . ",\"Bet\":" . $bet . ",\"TotalBet\":" . ($bet * $lines) . ",\"Win\":" . $totalWin . ",\"args\":{},\"cmd\":\"start\",\"time\":\"20181111T174113\"}";
                $request->session()->put("CrazyMonkey2IGReels", $dyuiopmnuyby);
                $request->session()->put("CrazyMonkey2IGWin", $totalWin);
                $request->session()->put("CrazyMonkey2IGRisk", 1);
                $request->session()->put("CrazyMonkey2IGLines", $lines);
                $request->session()->put("CrazyMonkey2IGBet", $bet);
                $request->session()->put("CrazyMonkey2IGDealerCard", $RiskCard_riop);
				
                $parametar_dipoin = "bet";
                if( $fiders_infort ) 
                {
                    $parametar_dipoin = "BG";
                }
                $slotSettings->SaveLogReport($response, $bet, $lines, $totalWin, $parametar_dipoin);
                break;
            case "finish":
                $balance = $slotSettings->GetBalance();
                $request->session()->put("CrazyMonkey2IGWin", 0);
                $response = "{\"Amount\":\"" . $balance . "\",\"Credit\":" . $balance . ",\"RawCredit\":" . $balance . ",\"Reels\":[" . session("CrazyMonkey2IGReels") . "],\"args\":{},\"cmd\":\"finish\",\"time\":\"20181111T171830\"}";
                break;
            case "risk":
                $tbvcqasermi = ["2", "3", "4", "5", "6", "7", "8", "9", "T", "J", "Q", "K", "A"];
                $kiolmjunp = ["C", "D", "S", "H"];
                $dealerCard = session("CrazyMonkey2IGDealerCard");
                $randomizacjop = rand(1, 2);
                $bank = $slotSettings->GetBank();
                $Prev_Win_data = session("CrazyMonkey2IGWin");
                $logs_save_report = session("CrazyMonkey2IGWin");
                $Win_data_jokmn_rew = $Prev_Win_data * 2;
                $request->session()->put("CrazyMonkey2IGRisk", session("CrazyMonkey2IGRisk") + 1);
                $Step_diput_resin = session("CrazyMonkey2IGRisk");
                $Other_data_run = [];
                $Risk_Card_regiop = $slotSettings->GetDealerCard();
                $nmvcfghy_run = array_search($dealerCard[0], $tbvcqasermi);
				
                if( $randomizacjop == 1 && $Prev_Win_data <= $bank ) 
                {
                    $fihikl_fertiuyo = rand($nmvcfghy_run, 12);
                }
                else
                {
                    $fihikl_fertiuyo = rand(0, $nmvcfghy_run);
                }
                if( $nmvcfghy_run < $fihikl_fertiuyo ) 
                {
                    $balance = $slotSettings->SetBalance($Prev_Win_data);
                    $balance = $slotSettings->SetBank(-1 * $Prev_Win_data);
                }
                else if( $fihikl_fertiuyo == $nmvcfghy_run ) 
                {
                    $Win_data_jokmn_rew = $Prev_Win_data;
                }
                else
                {
                    $Win_data_jokmn_rew = 0;
                    $balance = $slotSettings->SetBalance(-1 * $Prev_Win_data);
                    $balance = $slotSettings->SetBank($Prev_Win_data);
                }
                shuffle($kiolmjunp);
                $Player_dum_regis = $tbvcqasermi[$fihikl_fertiuyo] . $kiolmjunp[0];
                $numiration_ror = 0;
                for( $i = 0; $i < 3; $i++ ) 
                {
                    shuffle($kiolmjunp);
                    shuffle($tbvcqasermi);
                    $Other_data_run[] = $slotSettings->cardsID[$tbvcqasermi[0] . $kiolmjunp[0]];
                }
                $balance = $slotSettings->GetBalance();
                $request->session()->put("CrazyMonkey2IGWin", $Win_data_jokmn_rew);
                $request->session()->put("CrazyMonkey2IGDealerCard", $Risk_Card_regiop);
                $response = "{\"cc\":\"" . $fihikl_fertiuyo . "|" . $nmvcfghy_run . "\",\"Lines\":" . session("CrazyMonkey2IGLines") . ",\"Bet\":" . session("CrazyMonkey2IGBet") . ",\"Amount\":\"" . $balance . "\",\"Reels\":[" . session("CrazyMonkey2IGReels") . "],\"Credit\":" . $balance . ",\"Dealer\":" . $slotSettings->cardsID[$dealerCard] . ",\"Other\":[" . implode(",", $Other_data_run) . "],\"Player\":" . $slotSettings->cardsID[$Player_dum_regis] . ",\"PrevWin\":" . $Prev_Win_data . ",\"RawCredit\":" . $balance . ",\"RiskCard\":" . $slotSettings->cardsID[$Risk_Card_regiop] . ",\"Step\":" . $Step_diput_resin . ",\"Win\":" . $Win_data_jokmn_rew . ",\"args\":{},\"cmd\":\"risk\",\"time\":\"20181111T172027\"}";
                if( $Win_data_jokmn_rew == 0 ) 
                {
                    $Win_data_jokmn_rew = -1 * $Prev_Win_data;
                }
                $slotSettings->SaveLogReport($response, $logs_save_report, 1, $Win_data_jokmn_rew, "slotGamble");
                break;
            default:
                break;
        }
        return $response;
    }
}
