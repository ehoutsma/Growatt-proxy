<?php

class Growatt {
	public $pvOutputApi = '';
	public $pvOutputSid = '';
	public $domoticzUrl = 'http://localhost:8080';
	public $domoticzPpv1Idx = 1;
	public $domoticzPpv2Idx = 2;
	public $domoticzPpvIdx = 3;
	public $domoticzPpvETotalIdx = 4;
	public $influxDbUrl = "http://localhost:8086/write?db=domoticz";
	public $lastEToday = 0;
		public function run() {

                        if (!is_file("report.txt")) exit;
		
			$hexstring = file_get_contents("report.txt");
			$this->lastEToday = file_get_contents("EToday.txt");


//        $hexstring = preg_replace("/\s/", "", $hexstring);
//        $testBin = hex2bin($hexstring);
	$testBin = $hexstring;
        if ($testBin[6] == hex2bin("51") && $testBin[7] == hex2bin("04") ) {
            $result = $this->decryptMsg($testBin);
            $msg = $result;
            $start = 0;
            $result = (object) [];
            $length = 10;
            $result->deviceId = $this->getValue($msg, $start, $length);
            $start += $length;

            $length = 10;
            $result->inverterId = $this->getValue($msg, $start, $length);
            $start += $length;

            $length = 5;
            $result->empty = $this->getValue($msg, $start, $length);
            $start += $length;

            $length = 6;
            $result->gwVersion = $this->getValue($msg, $start, $length);
            $start += $length;

            $length = 2;
            $result->invStat = $this->getSumValue($msg, $start, $length);
            $start += $length;

            $length = 4;
            $result->Ppv = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 2;
            $result->Vpv1 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 2;
            $result->Ipv1 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 4;
            $result->Ppv1 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 2;
            $result->Vpv2 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 2;
            $result->Ipv2 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 4;
            $result->Ppv2 = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 4;
            $result->Pac = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Fac = ($this->getSumValue($msg, $start, $length))/100;
            $start += $length;

            $length = 2;
            $result->Vac1 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Iac1 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 4;
            $result->Pac1 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Vac2 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Iac2 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 4;
            $result->Pac2 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Vac3 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Iac3 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 4;
            $result->Pac3 = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 4;
            $result->E_Today = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 4;
            $result->E_Total = ($this->getSumValue($msg, $start, $length))/10;
            $start += $length;

            $length = 4;
            $result->Tall = ($this->getSumValue($msg, $start, $length))/(60*60*2);
            $start += $length;

            $length = 2;
            $result->Tmp = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Isof = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->GFCIF = ($this->getSumValue($msg, $start, $length)) /10;
            $start += $length;

            $length = 2;
            $result->DCIF = ($this->getSumValue($msg, $start, $length)) /10;
            $start += $length;

            $length = 2;
            $result->Vpvfault = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Vacfault = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Isof = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Facfault = ($this->getSumValue($msg, $start, $length))/100;
            $start += $length;

            $length = 2;
            $result->Tmpfault = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Faultcode = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->IPMtemp = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Pbusvolt = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $length = 2;
            $result->Nbusvolt = ($this->getSumValue($msg, $start, $length));
            $start += $length;

            $start += 12;

            $length = 4;
            $result->Epv1today = ($this->getSumValue($msg, $start, $length)) / 10;
            $start += $length;

            $length = 4;
            $result->Epv1total = ($this->getSumValue($msg, $start, $length)) / 10;
            $start += $length;

            $length = 4;
            $result->Epv2today = ($this->getSumValue($msg, $start, $length)) / 10;
            $start += $length;

            $length = 4;
            $result->Epv2total = ($this->getSumValue($msg, $start, $length)) / 10;
            $start += $length;

            $length = 4;
            $result->Epvtotal = ($this->getSumValue($msg, $start, $length)) / 10;
            $start += $length;

            $length = 4;
            $result->ERac = ($this->getSumValue($msg, $start, $length)) * 100;
            $start += $length;

            $length = 4;
            $result->ERactoday = ($this->getSumValue($msg, $start, $length)) * 100;
            $start += $length;

            $length = 4;
            $result->ERactotal = ($this->getSumValue($msg, $start, $length)) * 100;
            $start += $length;

	    var_dump($result);
	    $this->uploadData($result);
	    $this->uploadDataDomoticz($result);
	    $this->uploadDataPvoutput($result);
	    unlink("report.txt");
	    file_put_contents("EToday.txt", $result->E_Today);
	}
		
		}

		
    private function decryptMsg($msg)
    {
        $msg = substr($msg, 8);
        $encryptionKey = "Growatt";
        $pos = 0;
        for ($i = 0; $i < strlen($msg); $i++) {
            $value = ord($encryptionKey[$pos++]);
//            var_dump($i);
//            var_dump($value);
//            var_dump(ord($msg[$i]));
            if ($pos +1 > strlen($encryptionKey)) $pos = 0;
            $workValue = ord($msg[$i]) ^ $value;
//            var_dump($workValue);
            if ($workValue < 0) $workValue += 256;
//            var_dump($workValue);
            $msg[$i] = chr($workValue);
//            var_dump($msg[$i]);
//            echo "<br>";
        }


//        $device = substr($msg, 0, 10);
        return $msg;
    }

    private function getValue($msg, $start, $length) {
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result .= $msg[($start+$i)];
        }
        return $result;
    }

    private function getSumValue($msg, $start, $length) {
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result *= 256;
            $result += ord($msg[($start+$i)]);
        }
        return $result;
    }

    private function uploadData($data) {
        $ch = curl_init();
        $timestamp = time()."000000000\n";
        $postData = "";
        $postData .= "Ppv,name=ZonnepanelenHuis value=".$data->Ppv." ".$timestamp;
        $postData .= "Ppv1,name=ZonnepanelenHuis value=".$data->Ppv1." ".$timestamp;
        $postData .= "Ipv1,name=ZonnepanelenHuis value=".$data->Ipv1." ".$timestamp;
        $postData .= "Vpv1,name=ZonnepanelenHuis value=".$data->Vpv1." ".$timestamp;
        $postData .= "Ppv2,name=ZonnepanelenHuis value=".$data->Ppv2." ".$timestamp;
        $postData .= "Ipv2,name=ZonnepanelenHuis value=".$data->Ipv2." ".$timestamp;
        $postData .= "Vpv2,name=ZonnepanelenHuis value=".$data->Vpv2." ".$timestamp;
        $postData .= "ETotal,name=ZonnepanelenHuis value=".$data->E_Total." ".$timestamp;
        $postData .= "EToday,name=ZonnepanelenHuis value=".$data->E_Today." ".$timestamp;

        // set url
        curl_setopt($ch, CURLOPT_URL, $this->influxDbUrl);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(

            "Content-Type: application/octet-stream",
            "Content-Length: " . strlen($postData)
        ));
        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
    }
    
    private function uploadDataDomoticz($data) {
        $ch = curl_init();
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$url = $this->domoticzUrl."/json.htm?type=command&param=udevice&idx='.$this->domoticzPpvETotalIdx.'&nvalue=0&svalue={$data->Ppv};".(1000*$data->E_Total);

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
	$url = $this->domoticzUrl."/json.htm?type=command&param=udevice&idx='.$this->domoticzPpv1Idx.'&nvalue=0&svalue={$data->Ppv1};0";

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
	$url = $this->domoticzUrl."/json.htm?type=command&param=udevice&idx=&'.$this->domoticzPpv2Idx.'nvalue=0&svalue={$data->Ppv2};0";

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
	$url = $this->domoticzUrl."/json.htm?type=command&param=udevice&idx='.$this->domoticzPpvIdx.'&nvalue=0&svalue={$data->Ppv};0";

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
    }
    
    private function uploadDataPvoutput($data) {
        $ch = curl_init();
	$timestamp = time()."000000000\n";
	$uploadData = [];
	$uploadData['d'] = strftime("%Y%m%d");
	$uploadData['t'] = strftime("%H:%M");
	if ($this->lastEToday < (0+$data->E_Today)) {
		$uploadData['v1'] = $data->E_Today * 1000;
	}
	$uploadData['v2'] = $data->Ppv;
        // set url
	if (!empty($uploadData['v1']))
		$data = $uploadData['d'].','.$uploadData['t'].','.$uploadData['v1'].','.$uploadData['v2'];
	else
		$data = $uploadData['d'].','.$uploadData['t'].',-1,'.$uploadData['v2'];
	$buffer = file_get_contents('buffer.txt');
	$buffer = implode(';', [$buffer, $data]);
	if (substr($buffer, 0, 1) == ';') $buffer = ltrim($buffer, ';');
	file_put_contents('buffer.txt', $buffer);
	if (substr_count($buffer, ';') > 6) {
	if (substr_count($buffer, ';') > 30) {
		$buffers = explode(';', $buffer);
		$buffer = "";
		for ($i=0; $i < 30; $i ++) {
			$buffer .= ($i > 0 ? ";" : "").$buffers[$i];
		}
		$succesBuffer = "";
		for ($i=30; $i <= substr_count($buffer, ';'); $i ++) {
			$succesBuffer .= ($i > 0 ? ";" : "").$buffers[$i];
		}

	}
	$uploadData = ['data'=>$buffer];
//        curl_setopt($ch, CURLOPT_URL, "https://pvoutput.org/service/r2/addstatus.jsp?".http_build_query($uploadData));
        curl_setopt($ch, CURLOPT_URL, "https://pvoutput.org/service/r2/addbatchstatus.jsp?".http_build_query($uploadData));

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Pvoutput-Apikey: '.$this->pvOutputApi,
    'X-Pvoutput-SystemId: '.$this->pvOutputSid
));
		
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
	curl_setopt($ch, CURLOPT_TIMEOUT, 4);

        // $output contains the output string
	$output = curl_exec($ch);
	if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
		file_put_contents('buffer.txt', $succesBuffer);
        // close curl resource to free up system resources
	curl_close($ch);
	}
    }
    
	}

$Growatt = new Growatt();
$Growatt->run();


