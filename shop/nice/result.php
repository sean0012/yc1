<?php
include_once('./settle_nice.inc.php');

/*
****************************************************************************************
* <인증 결과 파라미터>
****************************************************************************************
*/
$authResultCode = $_POST['AuthResultCode'];		// 인증결과 : 0000(성공)
$authResultMsg = $_POST['AuthResultMsg'];		// 인증결과 메시지
$nextAppURL = $_POST['NextAppURL'];				// 승인 요청 URL
$txTid = $_POST['TxTid'];						// 거래 ID
$authToken = $_POST['AuthToken'];				// 인증 TOKEN
$payMethod = $_POST['PayMethod'];				// 결제수단
$mid = $_POST['MID'];							// 상점 아이디
$moid = $_POST['Moid'];							// 상점 주문번호
$amt = $_POST['Amt'];							// 결제 금액
$reqReserved = $_POST['ReqReserved'];			// 상점 예약필드
$netCancelURL = $_POST['NetCancelURL'];			// 망취소 요청 URL
	
/*
****************************************************************************************
* <승인 결과 파라미터 정의>
* 샘플페이지에서는 승인 결과 파라미터 중 일부만 예시되어 있으며, 
* 추가적으로 사용하실 파라미터는 연동메뉴얼을 참고하세요.
****************************************************************************************
*/

$response = "";

if($authResultCode === "0000"){
	/*
	****************************************************************************************
	* <해쉬암호화> (수정하지 마세요)
	* SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
	****************************************************************************************
	*/	
	$ediDate = date("YmdHis");
	$merchantKey = $default['de_nice_key']; // 상점키
	$signData = bin2hex(hash('sha256', $authToken . $default['de_nice_mid'] . $amt . $ediDate . $merchantKey, true));

	try{
		$data = Array(
			'TID' => $txTid,
			'AuthToken' => $authToken,
			'MID' => $mid,
			'Amt' => $amt,
			'EdiDate' => $ediDate,
			'SignData' => $signData,
			'CharSet' => 'utf-8'
		);		
		$response = reqPost($data, $nextAppURL); //승인 호출
		//jsonRespDump($response); //response json dump example
		$jsondata = json_decode($response);


		$paySuccess = false;
		if($payMethod == "CARD"){
			if($jsondata->ResultCode == "3001"){
				$paySuccess = true;                                                    // 신용카드(정상 결과코드:3001)
			}
		}else if($payMethod == "BANK"){
			if($jsondata->ResultCode == "4000"){
				$paySuccess = true;                                                    // 계좌이체(정상 결과코드:4000)
				if($default['de_escrow_use'] == 1){
					$escw_yn = 'Y';
				}
			}
		}else if($payMethod == "CELLPHONE"){
			if($jsondata->ResultCode == "A000"){
				$paySuccess = true;                                                    // 휴대폰(정상 결과코드:A000)
			}
		}else if($payMethod == "VBANK"){
			if($jsondata->ResultCode == "4100"){
				$paySuccess = true;                                                   // 가상계좌(정상 결과코드:4100)
				if($default['de_escrow_use'] == 1){
					$escw_yn = 'Y';
				}
			}
		}else if($payMethod == "SSG_BANK"){
			if($jsondata->ResultCode == "0000"){
				$paySuccess = true;                                                  // SSG은행계좌(정상 결과코드:0000)
			}
		}

		//결제오류라면
		if(!$paySuccess){
			$data = Array(
				'TID' => $txTid,
				'AuthToken' => $authToken,
				'MID' => $mid,
				'Amt' => $amt,
				'EdiDate' => $ediDate,
				'SignData' => $signData,
				'NetCancel' => '1',
				'CharSet' => 'utf-8'
			);
			$response = reqPost($data, $netCancelURL); //예외 발생시 망취소 진행			
			alert($jsondata->ResultMsg);
		}
		
		//최종결제요청 결과 성공 DB처리
        $tno             = $txTid;
        $amount          = $amt;
        $app_time        = $jsondata->AuthDate;
		if($payMethod == "BANK"){
	        $bank_name = $bankname = $jsondata->BankName;
		}else{
			$bank_name = $bankname = $jsondata->VbankBankName;
		}
        $depositor       = "";
        $account         = $jsondata->VbankNum;
        $commid          = $jsondata->VbankBankName;
        $mobile_no       = "";
        $app_no = $od_app_no = $jsondata->AuthCode;
        $card_name       = $jsondata->CardName;
        $pay_type        = $payMethod;
        $escw_yn         = "";
		
	}catch(Exception $e){
		$e->getMessage();
		$data = Array(
			'TID' => $txTid,
			'AuthToken' => $authToken,
			'MID' => $mid,
			'Amt' => $amt,
			'EdiDate' => $ediDate,
			'SignData' => $signData,
			'NetCancel' => '1',
			'CharSet' => 'utf-8'
		);
		$response = reqPost($data, $netCancelURL); //예외 발생시 망취소 진행
		//jsonRespDump($response); //response json dump example
		alert($jsondata->ResultMsg);
	}	
	
}else{
	//인증 실패 하는 경우 결과코드, 메시지
	$ResultCode = $authResultCode; 	
	$ResultMsg = $authResultMsg;
}

// API CALL foreach 예시
function jsonRespDump($resp){
	$respArr = json_decode($resp);
	foreach ( $respArr as $key => $value ){
		echo "$key=". $value."<br />";
	}
}

//Post api call
function reqPost(Array $data, $url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);					//connection timeout 15 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));	//POST data
	curl_setopt($ch, CURLOPT_POST, true);
	$response = curl_exec($ch);
	curl_close($ch);	 
	return $response;
}