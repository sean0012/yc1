<input type="hidden" name="PayMethod" value=""> <!--결제 수단 -->
<input type="hidden" name="GoodsName" value="<?php echo $goods; ?>"><!-- 결제 상품명 -->
<input type="hidden" name="Amt" value=""><!-- 결제 상품금액 -->
<input type="hidden" name="MID" value="<?php echo $MID; ?>"><!-- 상점 아이디 -->
<input type="hidden" name="Moid" value="<?php echo $od_id; ?>"> <!-- 상품 주문번호 -->
<input type="hidden" name="BuyerName" value=""> <!-- 구매자명 -->
<input type="hidden" name="BuyerEmail" value=""> <!-- 구매자명 이메일 -->
<input type="hidden" name="BuyerTel" value=""> <!-- 구매자 연락처 -->
<input type="hidden" name="ReturnURL" value="<?php echo $returnURL; ?>"> <!-- 인증완료 결과처리 URL (모바일 결제창 전용)PC 결제창 사용시 필요 없음 -->
<input type="hidden" name="VbankExpDate" id="vExp" value=""> <!-- 가상계좌입금만료일(YYYYMMDD) -->
<!-- 옵션 -->	 
<input type="hidden" name="GoodsCl" value="1"/>						<!-- 상품구분(실물(1),컨텐츠(0)) -->
<input type="hidden" name="TransType" value="0"/>					<!-- 일반(0)/에스크로(1) --> 
<input type="hidden" name="CharSet" value="utf-8"/>				<!-- 응답 파라미터 인코딩 방식 -->
<input type="hidden" name="ReqReserved" value=""/>					<!-- 상점 예약필드 -->

<!-- 변경 불가능 -->
<input type="hidden" name="EdiDate" value=""/>			<!-- 전문 생성일시 -->
<input type="hidden" name="SignData" value=""/>	<!-- 해쉬값 -->

<?php /* 주문폼 자바스크립트 에러 방지를 위해 추가함 */ ?>
<input type="hidden" name="good_mny"    value="<?php echo $tot_price; ?>">