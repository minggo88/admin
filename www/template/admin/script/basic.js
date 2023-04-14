<!--
/******************************************************************************************************************

(체크)브라우저 체크(IE Or Others)												IsIE( )
(체크)접속 디바이스 체크														IsCheckDevice()

(체크)입력가능한 문자인지 체크													IsValueType( strValue, strCheckType )
(체크)빈값여부 체크																IsEmpty( strValue )
(체크)동일정보 체크																IsEqual( strValue1, strValue2 )
(체크)혼합문자 체크																IsMix_String( strValue, bSpecialChar )
(체크)연속문자 체크																IsRepeat(strValue, nCheckCount)
(체크)입력허용길이 체크															IsLength( strValue, intMin, intMax )
(체크)빈값여부 체크 후 포커스													IsCheck(obj, strMsg)
(체크)나모 엑티브 스퀘어6 입력내용 체크											IsBlank_NamoEditor( objForm, strEditorId )
(기능)문자열 자르기																getStringCut( strValue, strCutType, intStart, intCutSize )
(기능)바이트 수 구하기															getStringByte( strValue )
(기능)문자열 치환																getReplace( strValue, strFind, strChange )
(기능)공백제거																	getTrim( strValue, strTrimType )
(기능)입력 포커스 주기															setFocus( strEleId, nFormIndex )
(기능)TAB 효과 주기 (엔터키에 의해 동작)										setSendTab()
(기능)TAB 효과 주기 (지정한 길이에서 자동 전환)									setAutoTab(objEle,nLen, e)
(기능)지정한 함수 호출															setFunction_Call( strFunction )
(기능)입력박스 초기 셋팅된 텍스트값 지우기										setDefault_TextClear( objEle, strDefault_Text )
(함수)금지단어 필터링 치환   													getWord_Filter( strValue )
(기능)금지단어 필터링 체크   													IsWord_Filter( strFilter_Word, arrFilter_List )
(함수)셀렉트박스의 선택된 값 구하기												getSelected_Value( objEle )
(함수)멀티 셀렉트박스의 선택된 항목 수 구하기									getMultiSelected_Count( strObjName)
(함수)멀티 셀렉트박스의 선택된 항목들의 Value 목록 구하기						getMultiSelected_Value( strObjName)
(기능)멀티 셀렉트박스의 선택된 항목들을 제거									setMultiSelected_Delete( strObjName)
(체크)체크여부 확인(라디오박스/체크박스)										IsChecked( objEle )
(함수)체크된 항목수 구하기(라디오박스/체크박스)									getChecked_Count( objEle )
(함수)체크된 값 구하기(라디오박스/체크박스)										getChecked_Value( objEle )
(기능)체크박스 전체선택/해제(라디오박스/체크박스)								setChecked_All( strEleId, bChecked )
(기능)체크박스 전체반전 선택 (기존 선택값과 반대되는 모든값 선택)				setChecked_Reverse( strEleId )
(기능)자동콤마 찍기																setAutoComma( objEle )
(기능)숫자만 입력																SetNum(obj)
(기능)숫자만 입력(금액) - ,가 자동으로 표시됨									SetNum2(obj)
(함수)3자리마다 ,를 찍어주는 함수 												getDecimalFormat( value )

(체크)윤년 체크																	IsLeapYear( intYear )
(함수)지정한 달의 일 수 구하기													getMonthOfDays( intYear, intMonth )
(함수)두 날짜의 일 수 차이 구하기												getDateDiff( form.sDate.value, form.eDate.value );
(함수)이전달, 다음달을 구함														getAddMonth_PrevNext( move_type, yyyy, mm )
(체크)유효한 날짜인지를 체크													IsDate( strYear, strMonth, strDay )
(함수)날짜에 값을 더하거나, 뺀다.												dateAdd(cYY, cMM, cDD, cAddDay) 

(기능)팝업창 띄우기																setOpenPopup( cUrl, cOpenName, nWidth, nHeight )
(기능)팝업창 옵션에 따라 띄우기													setOpenPopupOption( cUrl, cOpenName, nWidth, nHeight, cOption )
(기능)팝업창 옵션에 따라 띄우기 전체화면										setOpenPopupScreen( cUrl, cOpenName, cOption )
(기능)팝업창 자동 사이즈 조절													popupAutoResize()

(기능)멀티셀렉트박스의 이동 (위로이동)											setSelected_MoveUp( strObjName )
(기능)멀티셀렉트박스의 이동 (아래로이동)										setSelected_MoveDown( strObjName )
(기능)멀티셀렉트박스의 이동 (맨위로이동)										setSelected_MoveTop( strObjName )
(기능)멀티셀렉트박스의 이동 (맨아래로이동)										setSelected_MoveBottom( strObjName )
(기능)멀티셀렉트박스의 이동														setSelected_Move( objSelect, intIndex1, intIndex2 )
(기능)여러 멀티셀렉트박스에서의 이동 교환										setSelected_MoveElements( strObjName_Original, strObjName_Target )
(기능)멀티셀렉트박스에서의 전체 토글 (체크박스사용)								setSelectBox_ToggleAll( objCheckBox, strObjName )
(기능)멀티셀렉트박스에서의 전체 선택/해제 {토글}								setSelectBox_AllSelected( objEle, nSelected )

(기능)화면 이동																	goPageMoveUrl( cUrl )
(기능)화면 이동(새창)															goPageOpenUrl( cUrl )
(기능)이미지에 맞는 팝업창을 띄운다.											showPicture( srcl )

(체크)이미지 파일 체크															uploadImg_Check( value )
(함수)파일 확장자 알아내기														getFileExtension( filePath )
(함수)이미지 보기																showImg( img )
(함수)파일 다운로드(ASP)														showImg( img )

(함수)타겟을 지정해서 Input Element 1개를 동적 생성한다.						appendInputElement(type, name, value, parentObj)
(함수)타겟을 지정해서 모든 Element를 제거한다.									removeAllElement(type, name, value, parentObj)

(함수)플래쉬 파일을 페이지에 삽일할때 사용한다.									flashWrite(파일경로, 가로, 세로[, 변수][,배경색][,윈도우모드])
(함수)지정된 영역만 출력한다.													printIt(printThis)

(체크)이메일주소 체크															IsEmail( strValue )
(체크)한메일 사용여부 체크														IsMail_Daum( strValue )
(체크)전화번호 체크																IsPhone( strValue, strPhoneType )
(체크)사업자번호 체크															IsSaupjaNumber( strValue )
(체크)법인번호 체크																IsCorpNumber( strValue )
(체크)주민등록번호 체크															IsJuminNumber( strValue )
(함수)만 나이 반환																getKorean_Age( yy, mm, gender_num )

-------------------------------------------------------------------------------------------------------------------------------------------------------

(함수)지정한 문자열만큼만 입력받기												checkByte_Length( objEle, intMax, bShow, strShow_SizeId )
유효성 체크 (아이디)															check_Id( objEle, nMin, nMax, bRequired, cMsgTitle )
유효성 체크 (비밀번호)															check_Pwd( objEle, objEle_re, nMin, nMax, bRequired, cMsgTitle )
유효성 체크 (제한적인 문자만 사용)												check_LimitString( objEle, nMin, nMax, bRequired, cValueType, cMsgTitle )
유효성 체크 (모든 문자 사용가능)												check_AllString( objEle, nMin, nMax, bRequired, cMsgTitle )
유효성 체크 (이메일)															check_Email( objEle,  bRequired, cMsgTitle, bDaumMaill )
유효성 체크 (주민등록번호)														check_Jumin( objEle1,  objEle2, bRequired, cMsgTitle )
유효성 체크 (사업자번호)														check_Saupja( objEle1,  objEle2, objEle3, bRequired, cMsgTitle )
유효성 체크 (법인번호)															check_CorpNumber( objEle1,  objEle2, bRequired, cMsgTitle )
유효성 체크 (전화번호)															check_Phone( objEle1,  objEle2, objEle3, bRequired, cPhoneType, cMsgTitle )
유효성 체크 (날짜)																check_Date( objEle, bRequired, cMsgTitle )
유효성 체크 (파일 확장자 체크)													check_FileExt(objEle, usableFileExts, cMsgTitle)

******************************************************************************************************************/

/*==========================================================================
*	전역 변수
*==========================================================================*/
var nMax_Repeat = 3;																// 반복문자의 최대 허용값
var arrId_FilterList = new Array("admin", "master", "manager", "test", "tester", "administrator", "sa", "super", "superuser", "su", "query", "delete", "select", "view", "create", "insert", "union");	// 아이디 필터링 리스트


/**
*	TO DO 	: 브라우저 체크
*
*	Return	: String
*
*	사용예제
*		var bIe = IsIE();
**/
function IsIE()
{
	return (navigator.userAgent.indexOf("MSIE") > -1) ? true : false;
}


/**
*	TO DO 	: 접속 디바이스 체크
*
*	Return	: String
*
*	사용예제
*		var device = IsCheckDevice();
**/
function IsCheckDevice()
{
    var mobileKeyWords = new Array('iPhone', 'iPod', 'iPad', 'BlackBerry', 'Android', 'Windows CE', 'LG', 'MOT', 'SAMSUNG', 'SonyEricsson');
    var device_name = '';
    
    for (var word in mobileKeyWords)
    {
        if (navigator.userAgent.match(mobileKeyWords[word]) != null)
        {
            device_name = mobileKeyWords[word];
            break;
        }
    }
    
    return device_name;
}


/**
*	TO DO 	: 입력가능한 문자인지 체크
*	Param	: strValue
*	Param	: strCheckType
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsValueType( form.title.value ) == false ) alert("허용되지 않은 문자열 포함");
*
*	참고: 이 함수는 극히 제한적으로 사용하고자 할때, 특정 정규식을 추가하여 사용하면 좋을듯 함.
**/
function IsValueType( strValue, strCheckType )
{
	if ( strValue.length == 0 )	return false;

	switch ( strCheckType.toUpperCase() )
	{
		case "H"		: if ( strValue.search(/[^가-힣]/) != -1 )								return false;   break;  // 한글(자소허용안함)
		case "E"		: if ( strValue.search(/[^A-Za-z]/) != -1 )								return false;   break;  // 영문
		case "N"		: if ( strValue.search(/[^0-9]/) != -1 )								return false;   break;  // 숫자
		case "HE"		: if ( strValue.search(/[^가-힣A-Za-z]/) != -1 )    					return false;   break;  // 한글(자소허용안함)+영문
		case "HN"		: if ( strValue.search(/[^가-힣0-9]/) != -1 )							return false;   break;  // 한글(자소허용안함)+숫자
		case "EN"		: if ( strValue.search(/[^A-Za-z0-9]/) != -1 )           				return false;   break;  // 영문+숫자
		case "HEN"		: if ( strValue.search(/[^가-힣A-Za-z0-9]/) != -1 )						return false;   break;  // 한글(자소허용안함)+영문+숫자
		case "HENB"		: if ( strValue.search(/[^가-힣A-Za-z0-9 ]/) != -1 )  					return false;   break;  // 한글(자소허용안함)+영문+숫자+공백
		case "N-"		: if ( strValue.search(/[^0-9-]/) != -1 )								return false;   break;  // 숫자+하이픈
		case "N,"		: if ( strValue.search(/[^0-9,]/) != -1 )								return false;   break;  // 숫자+콤마
		case "HB"		: if ( strValue.search(/[^가-힣 ]/) != -1 )								return false;   break;  // 한글(자소허용안함)+공백
		case "EB"		: if ( strValue.search(/[^A-Za-z ]/) != -1 )							return false;   break;  // 영문+공백
		case "PWD"		: if ( strValue.search(/[^A-Za-z0-9~!@#$%^&*()-\?]/) != -1 )			return false;   break;  // 영문+숫자+특문( ~!@#$%^&*()\? )
		case "SP"		: if ( strValue.search(/[^~!@#$%^&*()-\?]/) != -1 )						return false;   break;  // 특문( ~!@#$%^&*()\? )
		default			: 																		return false;	break;
	}
	return true;
}


/**
*	TO DO 	: 빈값여부 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsEmpty( form.title.value ) == true ) alert("입력된 값 없음");
**/
function IsEmpty( strValue )
{
	if ( strValue == null || getTrim(strValue,"A") == "" )  return true;
	return false;
}


/**
*	TO DO 	: 동일정보 체크
*	Param	: strValue1
*	Param	: strValue2
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsEqual( form.pwd[0].value, form.pwd[1].value ) == false ) alert("일치하지 않음");
**/
function IsEqual( strValue1, strValue2 )
{
	if ( strValue1 != strValue2 )   return false;
	return true;
}


/**
*	TO DO 	: 혼합문자 체크
*	Param	: strValue
*	Param	: bSpecialChar
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsMix_String( form.id.value ) == false ) alert("아이디는 영문과 숫자를 혼합하여 사용해야 함");
*		if ( IsMix_String( form.id.value ) == false ) alert("아이디는 영문과 숫자를 혼합하여 사용해야 함");
**/
function IsMix_String( strValue, bSpecialChar )
{
	var onlyEng	= IsValueType(strValue, "E");
	var onlyNum 	= IsValueType(strValue, "N");
	var onlySp		= IsValueType(strValue, "SP");
	
	if (bSpecialChar)
	{
		if ( !onlyEng && !onlyNum && !onlySp)
		{
			if ( IsValueType(strValue, "PWD") )	return true;
		}
	}
	else
	{
		if ( !onlyEng && !onlyNum)
		{
			if ( IsValueType(strValue, "EN") )	return true;
		}
	}

	return false;
}


/**
*	TO DO 	: 연속문자 체크
*	Param	: strYear
*	Param	: nCheckCount	(반복체크할 수)
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsRepeat( form.id.value, 3 ) == true ) alert("연속적임");
**/
function IsRepeat(strValue, nCheckCount)
{
	var bResult		= false;
	var chkRepeat	= ""; 	// 반복되는 형태 (예: aaaa)
	var chkAsc 		= ""; 	// 연속된 오름차순 형태 (예: abcd, 1234)
	var chkDesc		= ""; 	// 연속된 내림차순 형태 (예: dcba, 4321)

	for(var k=1; k<nCheckCount; k++)
	{
		chkRepeat	+= "strValue.charAt(i) == strValue.charAt(i + " + k + ")";
		chkAsc		+= "(strValue.charCodeAt(i) + " + k + ") == strValue.charCodeAt(i + " + k + ")";
		chkDesc		+= "(strValue.charCodeAt(i) - " + k + ") == strValue.charCodeAt(i + " + k + ")";

		if (k < nCheckCount - 1)
		{
			chkRepeat	+= " && ";
			chkAsc		+= " && ";
			chkDesc		+= " && ";
		}
	}

	for( var i=0; i<strValue.length-3; i++)
	{
		if ( eval(chkRepeat) || eval(chkAsc) || eval(chkDesc) )	bResult = true;
	}

	return bResult;
}


/**
*	TO DO 	: 입력허용길이 체크
*	Param	: strValue
*
*	Return	: String
*
*	사용예제
*		if ( IsLength( form.title.value, 2, 10 ) == false ) alert("입력길이 오류");
**/
function IsLength( strValue, intMin, intMax )
{
	var nTotalByte = getStringByte( strValue );
	if ( nTotalByte < intMin || nTotalByte > intMax )	return false;
	return true;
}


/**
*	TO DO	: 빈값여부 체크 후 포커스
*	Param	: obj (TextBox Name)
*
*	Return	: Boolean
*
*	사용예제
*	if( IsCheck(form.id, "아이디를 입력해주세요.") == false ) return;
**/
function IsCheck(obj, strMsg)
{
	if ( IsEmpty(obj.value) )
	{
		alert(strMsg);
		obj.focus();
		return false;
	}
	return true;
}


/**
*	TO DO 	: 나모 엑티브 스퀘어6 입력내용 체크
*	Param	: objForm				// 폼 객체
*	Param	: strEditorId			// 에디터의 id 값
*
*	사용예제
*	if ( IsBlank_NamoEditor(form, "wec") ) alert("입력값 없음");
**/
function IsBlank_NamoEditor( objForm, strEditorId )
{
	if ( strEditorId == null || getTrim(strEditorId,"A") == "" )
	{
		strEditorId = "wec";			// 기본적으로 사용하는 id
	}
	
	var form 			= objForm;
	var objEditor		= eval('form.' + strEditorId);
	var bodyValue		= objEditor.BodyValue;												// 에디터내 <body>에 있는 내용만을 가져옴
	var textValue		= objEditor.TextValue;												// 에디터내 사용자가 작성한 텍스트만을 가져옴
	var bImage			= ( bodyValue.indexOf('<img') == -1 )	? true	: false;			// <body>의 내용중 사용자 삽입 이미지가 있는지를 판별 (TextValue로 체크시 이미지는 체크되지 않음)
	var bText 			= ( getTrim(textValue, "A") == "" ) 		? true	: false;		// 전체공백을 제거하여 값을 입력했는지를 판별 (에디터 자체에 기본으로 하나의 공백이 들어가기때문)
	var bReturn			= (bText && bImage) ? true : false;

	return bReturn;
}


/**
*	TO DO	: 문자열 자르기
*	Param	: strValue
*	Param	: strCutType		(자르는방법)
*	Param	: intStart			(시작위치)
*	Param	: intCutSize		(자를크기)
*
*	Return	: String
*
*	사용예제
*		var cResult = getStringCut( form.title.value, 'MB', 4, 6 );
*
*	주의사항
*	1. L / R	: 왼쪽,오른쪽 자르기의 경우 {시작위치} 값은 무시함
*	2. MA		: {중간}시작점에서 끝까지 자르기의 경우  {자를크기} 값은 무시함
*	3. MA/MB	: {중간} 자르기의 경우 시작값은 1부터 적용함 (즉, sbustring(0,2) 방식이 아닌 VB함수의 Mid(1,2)와 같은 형식을 취함)
**/
function getStringCut( strValue, strCutType, intStart, intCutSize )
{
	if ( strValue.length == 0 ) 		return "";
	if ( intStart < 0 )	intStart			= 0;
	if ( intCutSize < 0 )	intCutSize	= 0;
	
	var cResult = "";
	
	switch ( strCutType.toUpperCase() )
	{
		case "L"	:	// 왼쪽에서 자르기
					cResult   = strValue.substring(0, intCutSize);				break;
		case "R"	:	// 오른쪽에서 자르기
					intStart  = strValue.length - intCutSize;
					cResult   = strValue.substring(intStart);					break;
		case "MB"	:	// {중간}시작점에서 지정위치까지 자르기
					intCutSize += intStart-1;
					cResult    = strValue.substring(intStart-1, intCutSize);	break;
		case "MA"	:	// {중간}시작점에서 끝까지 자르기
					cResult   = strValue.substring(intStart-1);					break;
		default		:
					cResult   = strValue;    									break;
	}

	return cResult;
}


/**
*	TO DO 	: 바이트 수 구하기
*	Param	: strValue
*
*	Return	: Number
*
*	사용예제
*		var cResult = getStringByte( form.title.value );
**/
function getStringByte( strValue )
{
	var nTotalByte = 0;
	var cOneChar = "";

	if ( strValue.length == 0 ) return nTotalByte;
	
	for( i=0; i < strValue.length; i++ )
	{
		cOneChar = strValue.charAt(i);

		if ( escape(cOneChar).length > 4 )
		{
			nTotalByte += 2;
		}
		else
		{
			nTotalByte ++;
		}
	}
	return nTotalByte;
}


/**
*	TO DO 	: 문자열 치환
*	Param	: strValue
*	Param	: strFind	(찾을단어)
*	Param	: strChange	(바꿀단어)
*
*	Return	: String
*
*	사용예제
*		var cResult = getReplace( form.title.value, 'a', 'A' );
**/
function getReplace( strValue, strFind, strChange )
{
	var nPos = strValue.indexOf( strFind );

	while ( nPos != -1 )
	{
		strValue 	= strValue.replace( strFind, strChange );
		nPos 	= strValue.indexOf( strFind );
	}
	
	return strValue;
}


/**
*	TO DO 	: 공백제거
*	Param	: strValue
*	Param	: strTrimType
*
*	Return	: String
*
*	사용예제
*		var strResult = getTrim( form.title.value, 'B');
**/
function getTrim( strValue, strTrimType )
{
	var strReturn = "";

	switch ( strTrimType.toUpperCase() )
	{
		case "L"	:   strReturn = strValue.replace(/^\s+/g,"");									break;  // 왼쪽공백제거
		case "R"	:   strReturn = strValue.replace(/\s+$/g,"");									break;  // 오른쪽공백제거
		case "B"	:   strReturn = strValue.replace(/^\s+/g,"").replace(/\s+$/g,"");				break;  // 양쪽공백제거
		case "A"	:   strReturn = strValue.replace(/\s+/g,"");									break;  // 전체공백제거
		default		:   strReturn = strValue;														break;
	}

	return strReturn;
}



/**
*	TO DO 	: 입력 포커스 주기
*	Param	: strEleId
*
*	Return	: 없음
*
*	사용예제
*		setFocus( 'userName', null );
**/
function setFocus( strEleId, nFormIndex )
{
	if (nFormIndex == null)	nFormIndex = 0;

	var form	= eval('document.forms[' + nFormIndex + "]");
	var objEle	= eval('form.' + strEle);
	objEle.focus();
}


/**
*	TO DO 	: TAB 효과 주기 (엔터키에 의해 동작)
*	Param	: 없음
*
*	Return	: 없음
*
*	사용예제
*		setSendTab();
**/
function setSendTab()
{
	if ( event.keyCode == 13 )  event.keyCode = 9;
}


/**
*	TO DO 	: TAB 효과 주기 (지정한 길이에서 자동 전환)
*	Param	: 없음
*
*	Return	: 없음
*
*	사용예제
*		<input type="text" name="jumin" onKeyUp="return setAutoTab(this, 6, event);">
*		<input type="text" name="jumin" onKeyUp="return setAutoTab(this, 7, event);">
**/
function setAutoTab( objele,nlen, e )
{
	var isNN = (navigator.appName.indexOf("Netscape")!=-1);
	
	var keyCode = (isNN) ? e.which : e.keyCode;
	var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];

	if(objele.value.length >= nlen && !containsElement(filter,keyCode))
	{
		objele.value = objele.value.slice(0, nlen);
		objele.form[(getIndex(objele)+1) % objele.form.length].focus();
	}

	function containsElement(arr, ele)
	{
		var found = false, index = 0;
		while(!found && index < arr.length)
			if(arr[index] == ele)
				found = true;
			else
				index++;
		return found;
	}

	function getIndex(objele)
	{
		var index = -1, i = 0, found = false;
		while (i < objele.form.length && index == -1)
			if (objele.form[i] == objele)index = i;
			else i++;
		return index;
	}

	return true;
}


/**
*	TO DO 	: 지정한 함수 호출
*	Param	: strFunction
*
*	Return	: 없음
*
*	사용예제
*		<input  type="text" name="search" onKeyDown="return setFunction_Call( 'goSearch()' );">
*	참고사항
*		이 함수의 본래 목적은 자동서브밋 방지 효과를 내기 위한 것 이었음.
*		즉, <form> 요소에 단 하나의 TextBox 만 존재할때,,
*		엔터키 이벤트가 발생하면 자동서브밋되는 현상을 방지하기 위함이었음
*		약간 변형하면, 이미지버튼 서브밋 방지에도 적용 가능해 보임
**/
function setFunction_Call( strFunction )
{
	if ( event.keyCode == 13 )
	{
		eval(strFunction + ";");
		return false;
	}
}


/**
*	TO DO 	: 입력박스 초기 셋팅된 텍스트값 지우기
*	Param	: objEle
*	Param	: strDefault_Text
*
*	Return	: 없음
*
*	사용예제
*		<input type="text" name="search" onMouseDown="setDefault_TextClear(this.form.keyword, '검색어를 입력하세요');">
**/
function setDefault_TextClear( objEle, strDefault_Text )
{
	if (objEle.value == strDefault_Text) objEle.value = "";
}


/**
*	TO DO 	: 금지단어 필터링 치환
*	Param	: strValue
*
*	Return	: String
*
*	사용예제
*		form.content.value = getWord_Filter( form.content.value );
**/
function getWord_Filter( strValue )
{
	var strBadWord;
	var nBadCount = 0;    
	
	/* 금지단어 목록 */
	var arrBadList = new Array("바보/**님","멍청이/청님", "18/", "졸라/졸라서");
	
	for( var i=0; i < arrBadList.length; i++ )
	{
		strBadWord = arrBadList[i];
	
		var arrWord = strBadWord.split("/");						// 금지단어,대체단어를 분리
		if ( jsEmpty(arrWord[1]) == true )  arrWord[1] = "***";		// 대체단어가 빈값이면 임의의 값을 기록
		
		while(true)
		{
			if (strValue.indexOf(arrWord[0]) != -1 )
			{
				strValue = strValue.replace(arrWord[0], arrWord[1]);
				nBadCount++;
			}
			else
			{
				break;
			}
		}
	}
	//if ( nBadCount > 0 ) alert(nBadCount + "개의 불량단어 검출");
	return strValue;
}


/**
*	TO DO 	: 금지단어 필터링 체크
*	Param	: strFilter_Word
*	Param	: arrFilter_List
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsWord_Filter( form.content.value ) == false)	alert("사용할 수 없는 문자열입니다");
**/
function IsWord_Filter( strFilter_Word, arrFilter_List )
{
	for( var i=0; i<arrFilter_List.length; i++ )
	{
		if ( arrFilter_List[i] == strFilter_Word )	return true;
	}
	
	return false;
}


/**
*	TO DO 	: 셀렉트박스의  선택된 값 구하기
*	Param	: objEle	(셀렉트박스 이름)
*
*	Return	: String
*
*	사용예제
*		var cResult = getSelected_Value( form.selectData );
**/
function getSelected_Value( objEle )
{
	var strReturn = "";

	for( var i=0; i<objEle.length; i++ )
	{
		if ( objEle.options[i].selected )
		{
			if ( strReturn.length > 0 )   strReturn += ",";
			strReturn += objEle.options[i].value;
		}
	}
	return strReturn;
}


/**
*	TO DO 	: 멀티 셀렉트박스의  선택된 항목 수 구하기
*	Param	: strObjName	(셀렉트박스 이름)
*
*	Return	: Number
*
*	사용예제
*		var nResult = getMultiSelected_Count( "imgList" );
**/
function getMultiSelected_Count( strObjName)
{
	var nCount = 0;

	try
	{
		var objEle = document.getElementById(strObjName);

		for( var i=0; i < objEle.options.length; i++ )
		{
			if ( objEle.options[i].selected )	nCount += 1;
		}
	} catch(e) {}

	return nCount;
}


/**
*	TO DO 	: 멀티 셀렉트박스의  선택된 항목들의 Value 목록 구하기
*	Param	: strObjName	(셀렉트박스 이름)
*
*	Return	: String
*
*	사용예제
*		var cSelectedItemList = getMultiSelected_Value( "imgList" );
**/
function getMultiSelected_Value( strObjName)
{
	var strReturn = "";

	try
	{
		var objEle = document.getElementById(strObjName);

		for( var i=0; i < objEle.options.length; i++ )
		{
			if ( objEle.options[i].selected )
			{
				if ( strReturn.length > 0 )   strReturn += ",";
				strReturn += objEle.options[i].value;
			}
		}
	} catch(e) {}

	return strReturn;
}


/**
*	TO DO 	: 멀티 셀렉트박스의  선택된 항목들을 제거
*	Param	: strObjName	(셀렉트박스 이름)
*
*	Return	: 없음
*
*	사용예제
*		setMultiSelected_Delete( "imgList" );
**/
function setMultiSelected_Delete( strObjName)
{
	try
	{
		var objEle = document.getElementById(strObjName);

		// 멀티 삭제시 주의점: 밑에서 위로 루프 돌리며 삭제해야 한다.
		// for( var i=0; i < objEle.options.length; i++ ) 와 같이, 위에서 부터 삭제를 하면,
		// 데이터가 꼬이면서 원하지 않는 결과를 얻게 된다.
		for( var i=(objEle.options.length-1); i >= 0 ; i-- )
		{
			if (objEle.options[i].selected )	objEle.options[i] = null;
		}
	} catch(e) {}
}


/**
*	TO DO 	: 체크여부 확인
*	Param	: objEle	(라디오박스/체크박스의 이름)
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsChecked( form.chkData ) == false ) alert("체크된 항목 없음");
**/
function IsChecked( objEle )
{
	if ( String(objEle) != "undefined" )
	{
		if ( String(objEle.length) == "undefined" )
		{
			if ( objEle.checked )   return true;
		}
		else
		{
			for( var i=0; i<objEle.length; i++ )
			{
				if ( objEle[i].checked )    return true;
			}
		}
	}
	
	return false;
}


/**
*	TO DO 	: 체크된 항목수 구하기
*	Param	: objEle	(라디오박스/체크박스의 이름)
*
*	Return	: Number
*
*	사용예제
*		var nResult = getChecked_Count( form.chkData );
**/
function getChecked_Count( objEle )
{
	var nCount = 0;

	if ( String(objEle) != "undefined" )
	{
		if ( String(objEle.length) == "undefined" )
		{
			if ( objEle.checked )   nCount += 1;
		}
		else
		{
			for( var i=0; i<objEle.length; i++ )
			{
				if ( objEle[i].checked )    nCount += 1;
			}
		}
	}
	
	return nCount;
}


/**
*	TO DO 	: 체크된 값 구하기
*	Param	: objEle	(라디오박스/체크박스의 이름)
*
*	Return	: String
*
*	사용예제
*		var cResult = getChecked_Value( form.chkData );
**/
function getChecked_Value( objEle )
{
	var strReturn = "";


	if ( String(objEle) != "undefined" )
	{
		if ( String(objEle.length) == "undefined" )
		{
			strReturn = objEle.value;
		}
		else
		{
			for( var i=0; i<objEle.length; i++ )
			{
					if ( objEle[i].checked )
					{
						if ( strReturn.length > 0 )   strReturn += ",";
						strReturn += objEle[i].value;
					}
			}
		}
	}

	return strReturn;
}


/**
*	TO DO 	: 체크박스 전체선택/해제
*	Param	: strEleId
*	Param	: bChecked
*
*	Return	: 없음
*
*	사용예제
*		<input type="checkbox" onClick="setChecked_All('checkData', this.checked);">
**/
function setChecked_All( strEleId, bChecked )
{
	var objEle = eval("document.getElementsByName('"+ strEleId +"')");

	bChecked = bChecked ? true : false;
	for( var i=0; i < objEle.length; i++ )
	{
		objEle[i].checked = bChecked;
	}
}


/**
*	TO DO 	: 체크박스 전체반전 선택 (기존 선택값과 반대되는 모든값 선택)
*	Param	: strEleId
*
*	Return	: 없음
*
*	사용예제
*		<input type="checkbox" onClick="setChecked_Reverse('checkData');">
**/
function setChecked_Reverse( strEleId )
{
	var objEle = eval("document.getElementsByName('"+ strEleId +"')");
	
	for( var i=0; i < objEle.length; i++ )
	{
		objEle[i].checked = !objEle[i].checked;
	}
}



/**
*	TO DO	: 자동콤마 찍기
*	Param	: objEle (TextBox Name)
*
*	Return	: 없음 (TextBox 실시간 처리)
*
*	사용예제
*		<input type="text" name="money" onKeyup="setAutoComma(this)" style="text-align:right; ime-mode:disabled;">
**/
function setAutoComma( objEle )
{
	var str 		= "" + objEle.value.replace(/,/gi, '');	// 콤마 제거
	var pattern		= new RegExp(/(-?\d+)(\d{3})/);
	var bExists		= str.indexOf(".", 0);
	var strArr		= str.split('.');

	while(pattern.test(strArr[0]))
	{
		strArr[0] = strArr[0].replace(pattern, "$1, $2");
	}
	
	if (bExists > -1)
	{
		objEle.value = strArr[0] + "." + strArr[1]; 
	}
	else
	{
		objEle.value = strArr[0];
	}
}


/**
 * 숫자만 입력
 *	@Param	: obj (TextBox Name)
 * @Return	: 없음 (TextBox 실시간 처리)
 * @사용예제
 *		<input type="text" name="money" onkeypress='SetNum(this)' onblur='SetNum(this)'>
 */
function SetNum(obj){
	val=obj.value;
	re=/[^0-9]/gi;
	obj.value=val.replace(re,""); 
}


 /**
  * 숫자만 입력(금액) - ,가 자동으로 표시됨
  *	@Param	: obj (TextBox Name)
  * @Return	: 없음 (TextBox 실시간 처리)
  * @사용예제
  *		<input type="text" name="money" onkeypress='SetNum2(this)' onblur='SetNum2(this)'>
  */
function SetNum2(obj){
	val=obj.value;
	obj.value=Number(String(val).replace(/\..*|[^\d]/g,'')).toLocaleString().slice(0,-3);
}


/**
 *	3자리수마다 ,를 찍어주는 함수
 *	Param	: 숫자값
 *
 *	사용예제
 *	getDecimalFormat(숫자값);
 */
function getDecimalFormat(mValue)
{
	var temp_str = String(mValue);
	for(var i = 0 , retValue = String() , stop = temp_str.length; i < stop ; i++)
	{
		retValue = ((i%3) == 0) && i != 0 ? temp_str.charAt((stop - i) -1) + "," + retValue : temp_str.charAt((stop - i) -1) + retValue;
	}
	return retValue;
}


/**
*	TO DO 	: 윤년 체크
*	Param	: intYear
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsLeapYear( 2008 ) == false ) alert("윤년");
**/
function IsLeapYear( intYear )
{
	if ( intYear % 1000 != 0 && intYear % 4 == 0 )  return true;
	return false;
}


/**
*	TO DO 	: 지정한 달의 일 수 구하기
*	Param	: intYear
*	Param	: intMonth
*
*	Return	: Number
*
*	사용예제
*		var nResult = getMonthOfDays( 2008, 2);
**/
function getMonthOfDays( intYear, intMonth )
{
	var arrDays = new Array(12);
	
	arrDays[0]		= 31;
	arrDays[1]		= ( IsLeapYear(intYear) ) ? 29 : 28;
	arrDays[2]		= 31;
	arrDays[3]		= 30;
	arrDays[4]		= 31;
	arrDays[5]		= 30;
	arrDays[6]		= 31;
	arrDays[7]		= 31;
	arrDays[8]		= 30;
	arrDays[9]		= 31;
	arrDays[10]		= 30;
	arrDays[11]		= 31;
	
	return arrDays[intMonth-1];
}


/**
*	TO DO 	: 두 날짜의 일 수 차이 구하기
*	Param	: cStartDate
*	Param	: cEndDate
*
*	Return	: Integer
*
*	사용예제
*		var nDay = getDateDiff( form.sDate.value, form.eDate.value );	// 두 날짜의 차이
**/
function getDateDiff( cStartDate, cEndDate )
{
	var sDate = cStartDate.split("-");
	var eDate = cEndDate.split("-");

	var dtSDate = new Date(sDate[0], Number(sDate[1])-1, sDate[2]);
	var dtEDate = new Date(eDate[0], Number(eDate[1])-1, eDate[2]);
	
	var nDiffDay = ( dtEDate.getTime() - dtSDate.getTime() ) / (1000*60*60*24);
	
	return nDiffDay;
}



/**
*	TO DO 	: 이전달, 다음달을 구함
*	Param	: move_type	// prev:이전달, next:다음달
*	Param	: yyyy		// 기준년도
*	Param	: mm		// 기준 월(月)
*
*	Return	: String	// 2008-12 의 형태
*
*	사용예제
*		var cResult		= getAddMonth_PrevNext(cMoveType, yyyy, mm);			// 이전달, 다음달을 구함
*		var arrDate 	= cResult.split("-");
*		alert(arrDate[0] + "\n" + arrDate[1]);
**/
function getAddMonth_PrevNext( move_type, yyyy, mm )
{
	var yyyy	= parseInt(yyyy,10);	// 10진수 변환
	var mm	= parseInt(mm,10);

	var currentMM = mm - 1;		// 현재달을 구함 (실제 달력은 0~11 을 사용하므로 -1 해준다)

	var d = new Date(yyyy, currentMM, '01');
	var dd = (move_type == "prev") ? new Date(yyyy, d.getMonth()-1) : new Date(yyyy, d.getMonth()+1);
	
	yyyy = dd.getYear();
	mm = dd.getMonth()+1;					// 결과처리된 달을 가져온다 (실제 달력은 0~11 을 사용하므로 +1 해준다)
	
	mm = (mm < 10) ? "0"+mm : mm;	// 월을 표현할때 2자리 형태로 
	var cResult = yyyy + "-" + mm;
	
	return cResult;
}


/**
*	TO DO 	: 유효한 날짜인지를 체크
*	Param	: strYear
*	Param	: strMonth
*	Param	: strDay
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsDate( '2008', '13', '32' ) == false ) alert("올바른 날짜가 아님");
**/
function IsDate( strYear, strMonth, strDay )
{
	var intYear		= parseInt(strYear,10);		// 10진수 변환
	var intMonth	= parseInt(strMonth,10);
	var intDay  	= parseInt(strDay,10);
	
	var nMonthOfDays = getMonthOfDays(intYear, intMonth);
	
	if ( intDay < 1 || intDay > nMonthOfDays )	return false;
	if ( intMonth < 1 || intMonth > 12 )		return false;
	
	return true;
}


/**
*	TO DO 	: 날짜에 값을 더하거나, 뺀다.
*	Param	: cYY
*	Param	: cMM
*	Param	: cDD
*	Param	: cAddDay
*
*	Return	: String
*
*	사용예제
*		
**/
function dateAdd(cYY, cMM, cDD, cAddDay) 
{
	var cNowDate = new Date(Number(cYY), Number(cMM)-1, Number(cDD));
	var cAddDate = cNowDate.getTime() + (cAddDay * 24 * 60 * 60 * 1000);

	cNowDate.setTime(cAddDate);

	var cYear = cNowDate.getYear();
	var cMonth = cNowDate.getMonth() + 1;
	var cDate = cNowDate.getDate();

	if (cMonth < 10) cMonth = "0" + cMonth;
	if (cDate < 10) cDate = "0" + cDate;

	return cYear + "-" + cMonth + "-" + cDate;
}


/**
 *	팝업창 띄우기
 *
 *	Param	: cUrl
 *	Param	: cOpenName
 *	Param	: nWidth
 *	Param	: nHeight
 *	Return	: 없음
 *
 *	사용예제
 *		setOpenPopup( '/Popup/popPassword.jsp', 'popPassword', 300, 200 );
 */
function setOpenPopup( cUrl, cOpenName, nWidth, nHeight )
{
	// 팝업 위치 자동 조정
	LeftPosition = (screen.width) ? (screen.width-nWidth)/2 : 0;
	TopPosition = (screen.height) ? (screen.height-nHeight)/2 : 0;

	var windowprops = "width="+ nWidth+", height="+ nHeight+", toolbar=0, location=0, status=0, menubar=0, scrollbars=0, resizable=0, top="+ TopPosition +", left="+ LeftPosition +"  ";
	window.open(cUrl, cOpenName, windowprops);
}


/**
*	TO DO 	: 팝업창 옵션에 따라 띄우기
*	Param	: cUrl
*	Param	: cOpenName
*	Param	: nWidth
*	Param	: nHeight
*	Param	: cOption
*	Return	: 없음
*
*	사용예제
*		setOpenPopupOption( '/Popup/popPassword.jsp', 'popPassword', 300, 200, "scrollbars=no");
**/
function setOpenPopupOption( cUrl, cOpenName, nWidth, nHeight, cOption )
{
	var cDefaultOption = "location=no, scrollbars=no, menubars=no, toolbars=no, resizable=no";
	
	cOption = (cOption != "") ? cOption : cDefaultOption; 
	
	var windowprops = "width="+ nWidth+", height="+ nHeight+", "+ cOption +"";
	window.open(cUrl, cOpenName, windowprops);
}


/**
 *	TO DO 	: 팝업창 옵션에 따라 띄우기 전체화면
 *	Param	: cUrl
 *	Param	: cOpenName
 *	Param	: cOption
 *	Return	: 없음
 *
 *	사용예제
 *		setOpenPopupScreen( '/Popup/popPassword.jsp', 'popPassword', "scrollbars=no");
 **/
function setOpenPopupScreen( cUrl, cOpenName, cOption )
{
	var cDefaultOption = "location=no, scrollbars=no, menubars=no, toolbars=no, resizable=no";
	
	cOption = (cOption != "") ? cOption : cDefaultOption; 
	alert((screen.availWidth-10)+" / "+(screen.availHeight-30));
	var windowprops = "width=(screen.availWidth-10), height=(screen.availHeight-30), "+ cOption +"";
	window.open(cUrl, cOpenName, windowprops);
}


/**
 *	팝업창을 가운데로 조정하고, 포커스를 줌.
 *	Return	: 없음
 *
 *	사용예제
 *	window.onload = function(){popupCenterFocus();} 
 */
function popupCenterFocus()
{
	var thisX = parseInt(document.body.scrollWidth);
	var thisY = parseInt(document.body.scrollHeight);
	var maxThisX = screen.width - 50;
	var maxThisY = screen.height - 50;
	var marginY = 0;
	
	// 브라우저별 높이 조절.
	if (navigator.userAgent.indexOf("MSIE 6") > 0)			marginY = 45;	// IE 6.x
	else if(navigator.userAgent.indexOf("MSIE 7") > 0)		marginY = 75;	// IE 7.x
	else if(navigator.userAgent.indexOf("MSIE 8") > 0)		marginY = 75;	// IE 8.x
	else if(navigator.userAgent.indexOf("MSIE 9") > 0)		marginY = 75;	// IE 9.x
	else if(navigator.userAgent.indexOf("MSIE 10") > 0)		marginY = 75;	// IE 10.x
	else if(navigator.userAgent.indexOf("Firefox") > 0)		marginY = 50;	// FF
	else if(navigator.userAgent.indexOf("Opera") > 0)		marginY = 30;	// Opera
	else if(navigator.userAgent.indexOf("Netscape") > 0)	marginY = -2;	// Netscape

	if (thisX > maxThisX) {
		//window.document.body.scroll = "yes";
		thisX = maxThisX;
	}
	if (thisY > maxThisY - marginY) {
		//window.document.body.scroll = "yes";
		thisX += 19;
		thisY = maxThisY - marginY;
	}

	// 센터 정렬
	var windowX = (screen.width - (thisX+10))/2;
	var windowY = (screen.height - (thisY+marginY))/2 - 20;
	window.moveTo(windowX,windowY);
}


/**
*	TO DO 	: 팝업창 자동 사이즈 조절
*	Return	: 없음
*
*	사용예제
*	window.onload = function(){popupAutoResize();} 
**/
function popupAutoResize() 
{
	var thisX = Number(document.body.scrollWidth);
	var thisY = Number(document.body.scrollHeight);
	var maxThisX = screen.width - 50;
	var maxThisY = screen.height - 50;
	var marginY = 0;
	
	// 브라우저별 높이 조절.
	if (navigator.userAgent.indexOf("MSIE 6") > 0)			marginY = 45;	// IE 6.x
	else if(navigator.userAgent.indexOf("MSIE 7") > 0)		marginY = 75;	// IE 7.x
	else if(navigator.userAgent.indexOf("MSIE 8") > 0)		marginY = 75;	// IE 8.x
	else if(navigator.userAgent.indexOf("MSIE 9") > 0)		marginY = 75;	// IE 9.x
	else if(navigator.userAgent.indexOf("MSIE 10") > 0)		marginY = 75;	// IE 10.x
	else if(navigator.userAgent.indexOf("Firefox") > 0)		marginY = 50;	// FF
	else if(navigator.userAgent.indexOf("Opera") > 0)		marginY = 30;	// Opera
	else if(navigator.userAgent.indexOf("Netscape") > 0)	marginY = -2;	// Netscape

	if (thisX > maxThisX) {
		window.document.body.scroll = "yes";
		thisX = maxThisX;
	}
	if (thisY > maxThisY - marginY) {
		window.document.body.scroll = "yes";
		thisX += 19;
		thisY = maxThisY - marginY;
	}
	window.resizeTo(thisX+10, thisY+marginY);

	// 센터 정렬
	var windowX = (screen.width - (thisX+10))/2;
	var windowY = (screen.height - (thisY+marginY))/2 - 20;
	window.moveTo(windowX,windowY);
}


/**
 * 콤마를 제거한다.
 * @사용예제
 * replaceComma(form.replace);
 */
function replaceComma(obj) {
	var str = obj.value;
	while(str.indexOf(",") > -1) {
		str = str.replace(",", "");
	}
	obj.value = str;
}


/**
*	TO DO	: 빈값여부 체크 후 포커스 (hidden value)
*	Param	: obj (TextBox Name)
* 
*	Return	: Boolean
*
*	사용예제
*	if( IsCheck(form.id, "아이디를 입력해주세요.") == false ) return;
**/
function IsHiddenCheck(obj, strMsg)
{
	if ( IsEmpty(obj.value) )
	{
		alert(strMsg);
		return false;
	}
	return true;
}


/**
*	TO DO	: 입력값이 글자수 이상이 되면 자동으로 포커스( ex)주민등록번호,전화번호)
*	Param	: obj1 (TextBox Name)
*	Param	: obj2 (TextBox Name)
*	Param	: checkLeng (글자수)
*
*
*	사용예제
*	onkeyup="onAutoFocusElement(pageForm.userNo1,pageForm.userNo2,'6');"
**/
function onAutoFocusElement(obj1,obj2,checkLeng)
{
	isObjVal = obj1.value;
	if(isObjVal.length==checkLeng) obj2.focus();
}



/**
*	TO DO 	: 멀티셀렉트박스의 이동 (위로이동)
*	Param	: strObjName
*
*	사용예제
*	<input type="button" value="위로이동"	onClick="setSelected_MoveTop('strObjName')">
**/
function setSelected_MoveUp( strObjName )
{
	var objSelect = document.getElementById(strObjName);
	var i = objSelect.selectedIndex;
	if ( i > 0 )
	{
		setSelected_Move( objSelect, i, i-1 );

		objSelect.options[i-1].selected = true;
		objSelect.options[i].selected   = false;
	}
}


/**
*	TO DO 	: 멀티셀렉트박스의 이동 (아래로이동)
*	Param	: strObjName
*
*	사용예제
*	<input type="button" value="아래로이동"	onClick="setSelected_MoveDown('strObjName')">
**/
function setSelected_MoveDown( strObjName )
{
	var objSelect = document.getElementById(strObjName);
	var i = objSelect.selectedIndex;

	if ( i<objSelect.length-1 && i>-1 )
	{
		setSelected_Move(objSelect,i+1,i);

		objSelect.options[i+1].selected = true;
		objSelect.options[i].selected   = false;
	}
}


/**
*	TO DO 	: 멀티셀렉트박스의 이동 (맨위로이동)
*	Param	: strObjName
*
*	사용예제
*	<input type="button" value="맨위로이동"	onClick="setSelected_MoveTop('strObjName')">
**/
function setSelected_MoveTop( strObjName )
{
	var objSelect = document.getElementById(strObjName);
	var i  =objSelect.selectedIndex;
	for(; i>0; i--)
	{
		setSelected_Move(objSelect,i,i-1);

		objSelect.options[i-1].selected = true;
		objSelect.options[i].selected   = false;
	}
}


/**
*	TO DO 	: 멀티셀렉트박스의 이동 (맨아래로이동)
*	Param	: strObjName
*
*	사용예제
*	<input type="button" value="맨아래로이동"	onClick="setSelected_MoveBottom('strObjName')">
**/
function setSelected_MoveBottom( strObjName )
{
	var objSelect = document.getElementById(strObjName);
	var i = objSelect.selectedIndex;
	if ( i>-1 ) {
		for(; i<objSelect.length-1; i++)
		{
			setSelected_Move(objSelect,i+1,i);

			objSelect.options[i+1].selected = true;
			objSelect.options[i].selected   = false;
		}
	}
}


/**
*	TO DO 	: 멀티셀렉트박스의 이동
*	Param	: strObjName
*	Param	: intIndex1
*	Param	: intIndex2		
*
*	사용예제
*	<input type="button" value="이동테스트"	onClick="setSelected_Move( objSelect, intIndex1, intIndex2 )">
**/
function setSelected_Move( objSelect, intIndex1, intIndex2 )
{
	var savedValue  = objSelect.options[intIndex1].value;
	var savedText   = objSelect.options[intIndex1].text;

	objSelect.options[intIndex1].value = objSelect.options[intIndex2].value;
	objSelect.options[intIndex1].text  = objSelect.options[intIndex2].text;
	objSelect.options[intIndex2].value = savedValue;
	objSelect.options[intIndex2].text  = savedText;
}


/**
*	TO DO 	: 여러 멀티셀렉트박스에서의 이동 교환
*	Param	: strObjName_Original
*	Param	: strObjName_Target
*
*	사용예제
*	<input type="button" value="맞교환"	onClick="setSelected_MoveElements( strObjName_Original, strObjName_Target )">
**/
function setSelected_MoveElements( strObjName_Original, strObjName_Target )
{
	var objOriginal = document.getElementById(strObjName_Original);
	var objTarget   = document.getElementById(strObjName_Target);

	// 이동 (원본-->타켓)
	var intRemoveCount = 0;     // 이동할 항목수
	for( var i=0; i < objOriginal.options.length; i++ )
	{
		if ( objOriginal.options[i].selected == true )
		{
			var addText		= objOriginal.options[i].text;
			var addValue	= objOriginal.options[i].value;

			objTarget.options[objTarget.options.length] = new Option(addText,addValue);
			objOriginal.options[i].selected = false;
			++intRemoveCount;
		} else {
			objOriginal.options[i-intRemoveCount].selected = false;
			objOriginal.options[i-intRemoveCount].text  = objOriginal.options[i].text;
			objOriginal.options[i-intRemoveCount].value = objOriginal.options[i].value;
		}
	}

	// 이동후 원본에서 제거
	var intRemainCount = objOriginal.options.length - intRemoveCount;   // 이동후 남은 항목수
	for( i=objOriginal.options.length-1; i>=intRemainCount; i-- )
	{
		objOriginal.options[i] = null;
	}
}


/**
*	TO DO 	: 멀티셀렉트박스에서의 전체 토글 (체크박스사용)
*	Param	: objCheckBox	
*	Param	: strObjName
*
*	사용예제
*	<input type="button" value="전체토글"	onClick="setSelectBox_ToggleAll( objCheckBox, strObjName )">
**/
function setSelectBox_ToggleAll( objCheckBox, strObjName )
{
	var objSelect=document.getElementById(strObjName);
	if ( objCheckBox.checked )
	{
		for( var i=0; i<objSelect.options.length; i++ )
		{
			objSelect.options[i].selected = true;
		}
	} else {
		for( var i=0; i<objSelect.options.length; i++ )
		{
			objSelect.options[i].selected = false;
		}
	}
}


/**
*	TO DO 	: 멀티셀렉트박스에서의 전체 선택/해제 {토글}
*	Param	: strObjName
*	Param	: nBoolean
*
*	사용예제
*	<input type="button" value="전체선택"	onClick="setSelectBox_AllSelected( form.Category, nBoolean)">
**/
function setSelectBox_AllSelected( strObjName, nBoolean )
{
	var bSelected = (nBoolean == 0) ? false : true;
	for( var i=0; i<strObjName.options.length; i++ )
	{
		strObjName.options[i].selected = bSelected;
	}
}


/**
*	TO DO 	: 화면 이동
*	Param	: cUrl
*
*	사용예제
*	<input type="button" value="이동"	onClick="goPageMoveUrl('/')">
**/
function goPageMoveUrl( cUrl )
{
	window.location.href = cUrl;
}

/**
*	TO DO 	: 화면 이동(새창)
*	Param	: cUrl
*
*	사용예제
*	<input type="button" value="이동"	onClick="goPageOpenUrl('/')">
**/
function goPageOpenUrl( cUrl )
{
	window.open(cUrl, "_blank")
}


/**
*	TO DO 	: 이미지 파일 체크
*	Param	: value
*
*	사용예제
*	if ( !uploadImg_Check(form.attachfile.value) ) 	return;
**/
function uploadImg_Check( value )
{
	var src = getFileExtension(value);
	if (src == "")
	{
		alert('올바른 파일을 입력하세요');
		return false;
	}
	else if ( !((src.toLowerCase() == "png") || (src.toLowerCase() == "gif") || (src.toLowerCase() == "jpg") || (src.toLowerCase() == "jpeg") || (src.toLowerCase() == "bmp") ) )
	{
		alert('png, gif, jpg, bmp 파일만 지원합니다.');
		return false;
	}
	return true;
}


/**
*	TO DO 	: 파일 확장자 알아내기
*	Param	: filePath
*
*	사용예제
*	alert( getFileExtension(form.attachfile.value) );
**/
function getFileExtension( filePath )
{
	var lastIndex = -1;
	lastIndex = filePath.lastIndexOf('.');
	var extension = "";

	if ( lastIndex != -1 )
	{
		extension = filePath.substring( lastIndex+1, filePath.len );
	} else {
		extension = "";
	}
	
	return extension;
}


/**
*	TO DO 	: 파일 다운로드 ASP
*	Param	: filepath
*	Param	: fileSavename
*
**/
function fileDownAsp(path, save){
	
	var attribute

	//var oDiv = document.createElement("<div style='display: none;'></div>");
	var oDiv = document.createElement("div");
	attribute = document.createAttribute("style");
	attribute.nodeValue = "display: none;";
	oDiv.setAttributeNode(attribute);

	
	//var oForm = document.createElement("<form name='fileFomAction'></form>");
	//oForm.method = "post";
	//oForm.action = "/common/asp/fileDown.asp?";
	var oForm = document.createElement("form");
	attribute = document.createAttribute("name");
	attribute.nodeValue = "fileFomAction";
	oForm.setAttributeNode(attribute);
	attribute = document.createAttribute("method");
	attribute.nodeValue = "post";
	oForm.setAttributeNode(attribute);
	attribute = document.createAttribute("action");
	attribute.nodeValue = "/common/asp/fileDown.asp?";
	oForm.setAttributeNode(attribute);

	//var oInputHidden = document.createElement("<input text='hidden' name='path'>");
	//oInputHidden.value = path;
	//oForm.appendChild(oInputHidden);
	var oInputHidden = document.createElement("input");
	attribute = document.createAttribute("text");
	attribute.nodeValue = "hidden";
	oInputHidden.setAttributeNode(attribute);
	attribute = document.createAttribute("name");
	attribute.nodeValue = "path";
	oInputHidden.setAttributeNode(attribute);
	attribute = document.createAttribute("value");
	attribute.nodeValue = path;
	oInputHidden.setAttributeNode(attribute);
	oForm.appendChild(oInputHidden);

	//var oInputHidden = document.createElement("<input text='hidden' name='orcp'>");
	//oInputHidden.value = orcp;
	//oForm.appendChild(oInputHidden);
	//var oInputHidden = document.createElement("input");
	//attribute = document.createAttribute("text");
	//attribute.nodeValue = "hidden";
	//oInputHidden.setAttributeNode(attribute);
	//attribute = document.createAttribute("name");
	//attribute.nodeValue = "orcp";
	//oInputHidden.setAttributeNode(attribute);
	//attribute = document.createAttribute("value");
	//attribute.nodeValue = orcp;
	//oInputHidden.setAttributeNode(attribute);
	//oForm.appendChild(oInputHidden);
	
	//var oInputHidden = document.createElement("<input text='hidden' name='save'>");
	//oInputHidden.value = save;
	//oForm.appendChild(oInputHidden);
	var oInputHidden = document.createElement("input");
	attribute = document.createAttribute("text");
	attribute.nodeValue = "hidden";
	oInputHidden.setAttributeNode(attribute);
	attribute = document.createAttribute("name");
	attribute.nodeValue = "save";
	oInputHidden.setAttributeNode(attribute);
	attribute = document.createAttribute("value");
	attribute.nodeValue = save;
	oInputHidden.setAttributeNode(attribute);
	oForm.appendChild(oInputHidden);

	oDiv.appendChild(oForm);
	document.body.appendChild(oDiv);
	
	oForm.submit();
}


/**
 *	폼의 요소를 없애주는 함수
 *	Param	: 폼
 *
 *	사용예제
 *	getRemoveFormElement(폼);
 */
function getRemoveFormElement(targetForm)
{
	var targetElements = targetForm.elements;
	var tagetElementsCount = targetElements.length;
	for( var i = 0; i < tagetElementsCount; i++ )
	{
		targetForm.removeChild(targetElements[0]);
	}
}


/**
* 이미지에 맞는 팝업창을 띄운다.
* Param : 이미지 경로
*
*	사용예제
*	onclick="showPicture( src )"
*/
function showPicture(src) {
	var imgObj = new Image();
	imgObj.src = src;
	var wopt = "scrollbars=no,status=no,resizable=no";
	wopt += ",width=" + imgObj.width;
	wopt += ",height=" + imgObj.height;
	var wbody = "<head><title>사진 보기</title>";
	wbody += "<script language='javascript'>";
	wbody += "function finalResize(){";
	wbody += "  var oBody=document.body;";
	wbody += "  var oImg=document.images[0];";
	wbody += "  var xdiff=oImg.width-oBody.clientWidth;";
	wbody += "  var ydiff=oImg.height-oBody.clientHeight;";
	wbody += "  window.resizeBy(xdiff,ydiff);";
	wbody += "}";
	wbody += "</"+"script>";
	wbody += "</head>";
	wbody += "<body onLoad='finalResize()' style='margin:0'>";
	wbody += "<a href='javascript:window.close()'><img src='" + src + "' border=0></a>";
	wbody += "</body>";
	winResult = window.open("about:blank","",wopt);
	winResult.document.open("text/html", "replace");
	winResult.document.write(wbody);
	winResult.document.close();
	return;
}


/**
 * 난수를 발생시켜 해당위치의 문자로 치환하여 해당사이즈 만큼의 문자열을 만든다.
 * 
 *	사용예제
 * onclick = "randomString(8);"
 */
function randomString(size)
{
	var selectString = new Array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	var returnString = "";
	
	for(var i=0; i<size; i++)
	{
		var result = Math.floor(Math.random() * 62) + 1;
		returnString += selectString[result-1];
	}
	
	return returnString;
}

/**
* 지정된 영역만 출력한다.
* Param : 영영 ID 값
*
*	사용예제
*		<a href="javascript:printIt(document.getElementById('printme').innerHTML)">Print</a><p>
*		<div id="printme">내용</div>
*/
function printIt(printThis)  {
	var win = window.open();
	self.focus();
	win.document.open();
	win.document.write('<'+'html'+'><'+'head'+'><'+'style'+'>');
	win.document.write('body, td { font-family: Verdana; font-size: 10pt;}');
	win.document.write('<'+'/'+'style'+'><'+'/'+'head'+'><'+'body'+'>');
	win.document.write(printThis);
	win.document.write('<'+'/'+'body'+'><'+'/'+'html'+'>');
	win.document.close();
	win.print();
	win.close();
}


/**
 * flashWrite(파일경로, 가로, 세로[, 변수][,배경색][,윈도우모드])
 */
function flashWrite(url,w,h,vars,bg,win){
	
	var id=url.split("/")[url.split("/").length-1].split(".")[0]; //id는 파일명으로 설정
	if(vars==null) vars='';
	if(bg==null) bg='#FFFFFF';
	if(win==null) win='transparent';

	// 플래시 코드 정의
	var flashStr= "	<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'";
		flashStr+="			codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'";
		flashStr+="			width='"+w+"'";
		flashStr+="			height='"+h+"'";
		//flashStr+="			id='"+id+"'";
		flashStr+="			align='middle'>";

		flashStr+="		<param name='allowScriptAccess' value='always' />";
		flashStr+="		<param name='movie' value='"+url+"' />";
		flashStr+="		<param name='FlashVars' value='"+vars+"' />";
		flashStr+="		<param name='wmode' value='"+win+"' />";
		flashStr+="		<param name='menu' value='false' />";
		flashStr+="		<param name='quality' value='high' />";
		flashStr+="		<param name='bgcolor' value='"+bg+"' />";
	
	
		flashStr+="		<embed src='"+url+"'";
		flashStr+="		       flashVars='"+vars+"'";
		flashStr+="		       wmode='"+win+"'";
		flashStr+="		       menu='false'";
		flashStr+="		       quality='high'";
		flashStr+="		       bgcolor='"+bg+"'";
		flashStr+="		       width='"+w+"'";
		flashStr+="		       height='"+h+"'";
		flashStr+="		       name='"+id+"'";
		flashStr+="		       align='middle'";
		flashStr+="		       allowScriptAccess='always'";
		flashStr+="		       swLiveConnect='true'";
		flashStr+="		       type='application/x-shockwave-flash'";
		flashStr+="		       pluginspage='http://www.macromedia.com/go/getflashplayer' />";
		flashStr+=" </object>";

	// 플래시 코드 출력
	document.write(flashStr);
}


/**
 * 타겟을 지정해서 Input Element 1개를 동적 생성한다.
 *
 * @type 		: input의 type(hidden, text..)
 * @name 		: input의 이름
 * @value 		: input의 값
 * @parentObj	: 생성된 Element를 추가할 대상(Form)
 * @ex			: 
 */
function appendInputElement(type, name, value, parentObj)
{
	var obj = document.createElement("input");
	obj.setAttribute("type", type);
	obj.setAttribute("name", name);
	obj.setAttribute("value", value);
	parentObj.appendChild(obj);
}


/**
 * 타겟을 지정해서 모든 Element를 제거한다.
 *
 *	@parentObj	: 대상(Form)
 * @ex				: 
 */
function removeAllElement(parentObj)
{
	els = parentObj.elements;
	count = els.length;
	for( var i = 0; i < count; i++ )
	{
		parentObj.removeChild(els[0]);
	}
}


/**
 * 글자수 제한.
 *
 *	사용예제
 *	onkeypress="IsUpdateChar(this, 400);" onblur="IsUpdateChar(this, 400);
 */
function IsUpdateChar(obj, length_limit)
{
	var length = getStringByte(obj.value);
	
	if (length > length_limit) {
		obj.value = obj.value.replace(/\r\n$/, "");
		obj.value = getAssertMsglen(obj.value, length_limit );
	}
}

// 글자 자르기
function getAssertMsglen(message, maximum )
{
	var inc = 0;
	var nbytes = 0;
	var msg = "";
	var msglen = message.length;

	for (i=0; i<msglen; i++) {
		var ch = message.charAt(i);
		if (escape(ch).length > 4) {
			inc = 2;
		} else {
			inc = 1;
		}
		if ((nbytes + inc) > maximum) {
			break;
		}
		nbytes += inc;
		msg += ch;
	}
	return msg;
}




/**
*	TO DO 	: 이메일주소 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	패턴형식
*		아이디부분 = 영문+숫자만+언더바+하이픈 허용 / 최소 4자리 이상 최대 15자리 까지 허용 {1,15}
*	사용예제
*		if ( IsEmail( form.email.value ) == false ) alert("잘못된 형식의 이메일");
**/
function IsEmail( strValue )
{
	//대채가능패턴모음
	//var pattern = /[-!#$%&'*+\/^_~{}|0-9a-zA-Z]+(\.[-!#$%&'*+\/^_~{}|0-9a-zA-Z]+)*@[-!#$%&'*+\/^_~{}|0-9a-zA-Z]+(\.[-!#$%&'*+\/^_~{}|0-9a-zA-Z]+)*/;
	//var pattern = /^([A-Za-z0-9_-]{4,15})(@{1})([A-Za-z0-9_-]{1,15})(.{1})([A-Za-z0-9]{2,10})(.{1}[A-Za-z]{2,10})?(.{1}[A-Za-z]{2,10})?$/;
	//var pattern = /(^[a-zA-Z0-9]+@[a-zA-Z0-9]+[a-zA-Z0-9\-]+[a-zA-Z0-9]+\.[a-zA-Z]+$)/;
	//var pattern = /^(\w+)@(\w+)[.](\w+)[.](\w+)$/;
	//var pattern = /^(\w+(?:\.\w+)*)@((?:\w+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	//var pattern = /^([A-Za-z0-9]{4,15})(@{1})([A-Za-z0-9_-]{1,15})(.{1})([A-Za-z0-9]{2,4})(.{1}[A-Za-z]{2,4})?(.{1}[A-Za-z]{2,4})?$/;

	var pattern = /^([A-Za-z0-9_-]{1,15})(@{1})([A-Za-z0-9_-]{1,15})(.{1})([A-Za-z0-9]{2,10})(.{1}[A-Za-z]{2,10})?(.{1}[A-Za-z]{2,10})?$/;

	if ( (strValue.length == 0) || (!pattern.test(strValue)) )  return false;
	return true;
}


/**
*	TO DO 	: 한메일 사용여부 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsMail_Daum( form.email.value ) == false ) alert("한메일은 안돼");
**/
function IsMail_Daum( strValue )
{ 
	strValue = strValue.toLowerCase();

	if ( strValue.match("@hanmail.net") || strValue.match("@daum.net") )	return false;
	return true;
}


/**
*	TO DO 	: 전화번호 체크
*	Param	: strValue
*	Param	: strPhoneType
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsPhone( form.phone.value, "M" ) == false ) alert("잘못된 전화번호 형식");
**/
function IsPhone( strValue, strPhoneType )
{
	var pattern = /^[0-9]{2,4}-[0-9]{3,4}-[0-9]{4}$/;
	if ( (strValue.length == 0) || (!pattern.test(strValue)) )  return false;
	
	var groupNumber = null;
	switch ( strPhoneType.toUpperCase() )
	{
		case "M"	: groupNumber = new Array("010", "011", "016", "017", "018", "019");																												break;
		case "P"	: groupNumber = new Array("02","031","032","033","041","042","043","051","052","053","054","055","061","062","063","064","070", "0505");											break;
		case "MP"	: groupNumber = new Array("02","031","032","033","041","042","043","051","052","053","054","055","061","062","063","064","070","010", "011", "016", "017", "018", "019", "0505");	break;
		default		: return false;																																										break;
	}
	
	var bFlag		= false;
	var arrPhone	= strValue.split("-");
	
	for( var i=0; i<groupNumber.length; i++ )
	{
		if ( groupNumber[i] == arrPhone[0] )
		{
			bFlag = true;
			break;
		}
	}

	return bFlag;
}


/**
*	TO DO 	: 사업자번호 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsSaupjaNumber( form.saupja.value ) == false ) alert("올바르지 않은 사업자번호");
**/
function IsSaupjaNumber( strValue )
{
	if ( strValue.length != 10 )		return false;
	if ( !IsValueType(strValue, "N") )	return false;

	var sum = 0;
	sum += parseInt( strValue.substring(0,1) );
	sum += parseInt( strValue.substring(1,2) ) * 3 % 10;
	sum += parseInt( strValue.substring(2,3) ) * 7 % 10;
	sum += parseInt( strValue.substring(3,4) ) * 1 % 10;
	sum += parseInt( strValue.substring(4,5) ) * 3 % 10;
	sum += parseInt( strValue.substring(5,6) ) * 7 % 10;
	sum += parseInt( strValue.substring(6,7) ) * 1 % 10;
	sum += parseInt( strValue.substring(7,8) ) * 3 % 10;
	sum += Math.floor(parseInt( strValue.substring(8,9) ) * 5 / 10);
	sum += parseInt( strValue.substring(8,9) ) * 5 % 10;
	sum += parseInt( strValue.substring(9,10) );
	if ( sum % 10 != 0 )   return false;

	return true;
}


/**
*	TO DO 	: 법인번호 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsCorpNumber( form.corpnum.value ) == false ) alert("올바르지 않은 법인번호");
**/
function IsCorpNumber( strValue )
{
	if ( strValue.length != 13 )		return false;
	if ( !IsValueType(strValue, "N") )	return false;

	var sum 	= 0;
	var num 	= [1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
	var last 	= parseInt(corpnum.charAt(12));
	
	for(var i=0; i < 12; i++)
	{
		sum += parseInt(corpnum.charAt(i)) * num[i];
	}
	
	return ((10 - sum % 10) % 10 == last) ? true : false;
}


/**
*	TO DO 	: 주민등록번호 체크
*	Param	: strValue
*
*	Return	: Boolean
*
*	사용예제
*		if ( IsJuminNumber( form.jumin.value ) == false ) alert("올바르지 않은 주민등록번호");
**/
function IsJuminNumber( strValue )
{
	if ( strValue.length != 13 )		return false;
	if ( !IsValueType(strValue, "N") )	return false;

	var sum = 0;
	for( i=0; i<8; i++ )	sum += strValue.substring(i, i+1)*(i+2);
	for( i=8; i<12; i++ )	sum += strValue.substring(i, i+1)*(i-6);

	sum = 11 - (sum%11);
	if ( sum >=10 ) sum -= 10;
	
	if ( (strValue.substring(12, 13) != sum) || (	(strValue.substring(6, 7) != 1)
									  					&& (strValue.substring(6, 7) != 2)
									  					&& (strValue.substring(6, 7) != 3)
									  					&& (strValue.substring(6, 7) != 4)	)
	)	return false;

	return true;
}


/**
*	TO DO 	: 만 나이 반환
*	Param	: yy			(출생년도 2자리)
*	Param	: mm			(출생월 2자리)
*	Param	: gender_num	(주민등록번호 뒷자리의 첫번째 숫자)
*
*	Return	: Number
*
*	사용예제
*		var nAge = getKorean_Age( form.jumin[0].value.substr(0,2), form.jumin[0].value.substr(2,2), form.jumin[1].value.substr(0,1) );
**/
function getKorean_Age( yy, mm, gender_num )
{
	var nowDate	= new Date();
	var strYY		= nowDate.getYear();
	var strMM		= nowDate.getMonth()+1;
	var strBrith_YY;
	var nKorean_Age;

	if ( gender_num == 1 || gender_num == 2 )
	{
		strBrith_YY ='19' + String(yy);
	}
	else
	{8
		strBrith_YY ='20' + String(yy);
	}
	
	nKorean_Age = ( parseInt(mm) < parseInt(strMM) ) ? ( strYY-parseInt(strBrith_YY) ) : ( strYY-parseInt(strBrith_YY)-1 );
	return nKorean_Age;
}


/**
*	TO DO : 지정한 문자열만큼만 입력받기
*	Param	: objEle
*	Param	: intMax
*	Param	: bShow         	(입력되는 문자열의 크기를 보여줄것인지의 여부)
*	Param	: strShow_SizeId	(입력되는 문자열의 크기를 보여줄 Form Element)
*
*	Return	: 없음
*
*	사용예제
* 		<textarea name="content" onpropertychange="javascript:checkByte_Length(this, 10, false, '');"></textarea>
* 		<textarea name="content" onpropertychange="javascript:checkByte_Length(this, 10, true, 'lblLength');"></textarea>
**/
function checkByte_Length( objEle, intMax, bShow, strShow_SizeId )
{
	var objEle_Show;
	if ( bShow && !IsEmpty(strShow_SizeId) )
	{
		objEle_Show = eval("document.getElementById('"+ strShow_SizeId +"')");
	}

    var i = 0;
    var nTotalByte     	= 0;
    var nTotalByte_Old	= 0;
    var cOneChar;
    var cTempString;

    while( i < objEle.value.length )
    {
        cOneChar = objEle.value.charAt(i);
    
        if ( escape(cOneChar).length > 4 )
        {
            nTotalByte += 2;
        }
        else
        {
            nTotalByte ++;
        }

        if ( nTotalByte > intMax )
        {
            alert("입력 가능한 범위를 넘었습니다.");
        
            cTempString = objEle.value.substr(0,i);
            objEle.value = cTempString;
            nTotalByte = nTotalByte_Old;
            break;
        }

        nTotalByte_Old = nTotalByte;
        i++;
    }

	if ( typeof(objEle_Show) == "object" )
	{
		if (objEle_Show.type == "text")
		{
			objEle_Show.value = nTotalByte;				// text
		} else {
			objEle_Show.innerText = nTotalByte;			// span
		}
	}
}



/**
*	TO DO 	: 유효성 체크 (아이디)
*	Param	: objEle
*	Param	: nMin			(입력 최소길이)
*	Param	: nMax			(입력 최대길이)
*	Param	: bRequired		(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Id( form.id, 4, 12, true, '아이디' ) == false )	return;
**/
function check_Id( objEle, nMin, nMax, bRequired, cMsgTitle )
{
	if ( typeof(objEle) != "object" )   return false;
	
	objEle.value 	= getTrim( objEle.value, "A" );
	var strValue 	= objEle.value;
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue, "EN") )
		{
			alert(cMsgTitle + "의 입력값에 허용되지 않은 문자가 포함되어 있습니다.");
			objEle.value = "";
			objEle.focus();
			return false;
		}
		
		// 입력길이 체크
		if ( !IsLength(strValue, nMin, nMax) )
		{
			alert(cMsgTitle + "의 입력은  " + nMin + "~ " + nMax + " 글자 사이로 작성하여 주십시오." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
		
		// 금지단어 필터링 체크
		if ( IsWord_Filter(strValue, arrId_FilterList) )
		{
			alert(cMsgTitle + "의 입력값은 사용하실 수 없습니다.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 연속문자 체크
		if ( IsRepeat(strValue,  nMax_Repeat) )
		{
			alert(cMsgTitle + "의 입력값은 " +   nMax_Repeat + "회 연속 반복되는 문자를 사용하실 수 없습니다." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 	(비밀번호)
*	Param	: objEle
*	Param	: objEle_re
*	Param	: nMin			(입력 최소길이)
*	Param	: nMax			(입력 최대길이)
*	Param	: bRequired		(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Pwd( form.pwd[0],  form.pwd[1], 4, 12, true, '비밀번호' ) == false )	return;
**/
function check_Pwd( objEle, objEle_re, nMin, nMax, bRequired, cMsgTitle )
{
	if ( typeof(objEle) != "object" )   		return false;
	if ( typeof(objEle_re) != "object" )   	return false;
	
	var strValue 	= getTrim( objEle.value, "A" );
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue, "PWD") )
		{
			alert(cMsgTitle + "의 입력값에 허용되지 않은 문자가 포함되어 있습니다.");
			objEle.value = "";
			objEle.focus();
			return false;
		}
		
		// 입력길이 체크
		if ( !IsLength(strValue, nMin, nMax) )
		{
			alert(cMsgTitle + "의 입력은  " + nMin + "~ " + nMax + " 글자 사이로 작성하여 주십시오." );
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 연속문자 체크
		if ( IsRepeat(strValue,  nMax_Repeat) )
		{
			alert(cMsgTitle + "의 입력값은 " +   nMax_Repeat + "회 연속 반복되는 문자를 사용하실 수 없습니다." );
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 혼합문자 체크
		if ( !IsMix_String(strValue, true) )		//비밀번호는 특문을 포함할 수 있지만 반드시 포함할 필요는 없다. 단지 체크를 위해 true 전달
		{
			alert(cMsgTitle + "의 입력값은 반드시 영문과 숫자를 포함한 값이어야 합니다." );
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 동일정보 체크		
		if ( !IsEqual( strValue, objEle_re.value ) )
		{
			alert(cMsgTitle + "의 입력값이 " + cMsgTitle + " 확인의 입력값과 일치하지 않습니다." );
			objEle_re.value = "";
			objEle.value = "";
			objEle.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (제한적인 문자만 사용)
*	Param	: objEle
*	Param	: nMin			(입력 최소길이)
*	Param	: nMax			(입력 최대길이)
*	Param	: bRequired		(필수체크 여부)
*	Param	: cValueType	(입력가능문자 형식)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_LimitString( form.name, 4, 12, true, 'HE', '이름' ) == false )	return;
**/
function check_LimitString( objEle, nMin, nMax, bRequired, cValueType, cMsgTitle )
{
	if ( typeof(objEle) != "object" )   return false;
	var strValue;

	switch ( cValueType.toUpperCase() )
	{
		case "HENB"		:
		case "HB"		:	// HENB, HBEB, EB 등은 공백을 허용하므로 패스
		case "EB"		:	strValue = objEle.value;					break;
		default			:	strValue = getTrim( objEle.value, "A" );	break;
	}
	
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue, cValueType) )
		{
			alert(cMsgTitle + "의 입력값에 허용되지 않은 문자가 포함되어 있습니다.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue, nMin, nMax) )
		{
			alert(cMsgTitle + "의 입력은  " + nMin + "~ " + nMax + " 글자 사이로 작성하여 주십시오." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (모든 문자 사용가능)
*	Param	: objEle
*	Param	: nMin		(입력 최소길이)
*	Param	: nMax		(입력 최대길이)
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_AllString( form.content, 4, 4000, true, '내용' ) == false )	return;
**/
function check_AllString( objEle, nMin, nMax, bRequired, cMsgTitle )
{
	if ( typeof(objEle) != "object" )   return false;
	
	var strValue 	= objEle.value;
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue, nMin, nMax) )
		{
			alert(cMsgTitle + "의 입력은  " + nMin + "~ " + nMax + " 글자 사이로 작성하여 주십시오." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (이메일)
*	Param	: objEle
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*	Param	: bDaumMaill	(한메일 허용 여부)
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Email( form.email,  true, '이메일', true ) == false )	return;
**/
function check_Email( objEle,  bRequired, cMsgTitle, bDaumMaill )
{
	if ( typeof(objEle) != "object" )   return false;
	
	var strValue = getTrim( objEle.value, "A" );
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 이메일 유효성 체크
		if ( !IsEmail( strValue ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
		
		if ( bDaumMaill )
		{
			if ( !IsMail_Daum(strValue) )
			{
				alert(cMsgTitle + "의 입력값으로 다음메일(한메일)은 사용하실 수 없습니다." );
				objEle.value = "";
				objEle.focus();
				return false;
			}
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (주민등록번호)
*	Param	: objEle1
*	Param	: objEle2
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Jumin( form.jumin[0],  form.jumin[1], true, '주민등록번호' ) == false )	return;
**/
function check_Jumin( objEle1,  objEle2, bRequired, cMsgTitle )
{
	if ( typeof(objEle1) != "object" )   return false;
	if ( typeof(objEle2) != "object" )   return false;
	
	var strValue1 	= getTrim( objEle1.value, "A" );
	var strValue2 	= getTrim( objEle2.value, "A" );
	
	if ( bRequired == false && (strValue1 != "" ||strValue2 != "" ) )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue1) )
		{
			alert(cMsgTitle + "의 앞자리 값을 입력하여 주십시오.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( IsEmpty(strValue2) )
		{
			alert(cMsgTitle + "의 뒷자리 값을 입력하여 주십시오.");
			objEle2.value = "";			
			objEle2.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue1, 6, 6) )
		{
			alert(cMsgTitle + "의 앞자리는  6자리 숫자입니다." );
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsLength(strValue2, 7, 7) )
		{
			alert(cMsgTitle + "의 뒷자리는  7자리 숫자입니다." );
			objEle2.value = "";
			objEle2.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue1, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsValueType(strValue2, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}

		// 주민등록번호 유효성 체크
		if ( !IsJuminNumber( strValue1+strValue2 ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );
			objEle1.value = "";
			objEle2.value = "";
			objEle1.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (사업자번호)
*	Param	: objEle1
*	Param	: objEle2
*	Param	: objEle3
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Saupja( form.saupja[0],  form.saupja[1],  form.saupja[2], true, '사업자번호' ) == false )	return;
**/
function check_Saupja( objEle1,  objEle2, objEle3, bRequired, cMsgTitle )
{
	if ( typeof(objEle1) != "object" )   return false;
	if ( typeof(objEle2) != "object" )   return false;
	if ( typeof(objEle3) != "object" )   return false;
	
	var strValue1 	= getTrim( objEle1.value, "A" );
	var strValue2 	= getTrim( objEle2.value, "A" );
	var strValue3 	= getTrim( objEle3.value, "A" );
	
	if ( bRequired == false && (strValue1 != "" ||strValue2 != "" || strValue3 != "") )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue1) )
		{
			alert(cMsgTitle + "의 앞자리 값을 입력하여 주십시오.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( IsEmpty(strValue2) )
		{
			alert(cMsgTitle + "의 중간자리 값을 입력하여 주십시오.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( IsEmpty(strValue3) )
		{
			alert(cMsgTitle + "의 뒷자리 값을 입력하여 주십시오.");
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue1, 3, 3) )
		{
			alert(cMsgTitle + "의 앞자리는  3자리 숫자입니다." );
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsLength(strValue2, 2, 2) )
		{
			alert(cMsgTitle + "의 중간자리는  2자리 숫자입니다." );
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( !IsLength(strValue3, 5, 5) )
		{
			alert(cMsgTitle + "의 뒷자리는  5자리 숫자입니다." );
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue1, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsValueType(strValue2, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( !IsValueType(strValue3, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 사업자번호 유효성 체크
		if ( !IsSaupjaNumber( strValue1+strValue2+strValue3 ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );
			objEle1.value = "";
			objEle2.value = "";
			objEle3.value = "";
			objEle1.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (법인번호)
*	Param	: objEle1
*	Param	: objEle2
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_CorpNumber( form.saupja[0],  form.saupja[1],  true, '법인번호' ) == false )	return;
**/
function check_CorpNumber( objEle1,  objEle2, bRequired, cMsgTitle )
{
	if ( typeof(objEle1) != "object" )   return false;
	if ( typeof(objEle2) != "object" )   return false;
	
	var strValue1 	= getTrim( objEle1.value, "A" );
	var strValue2 	= getTrim( objEle2.value, "A" );
	
	if ( bRequired == false && (strValue1 != "" ||strValue2 != "") )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue1) )
		{
			alert(cMsgTitle + "의 앞자리 값을 입력하여 주십시오.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( IsEmpty(strValue2) )
		{
			alert(cMsgTitle + "의 뒷자리의 값을 입력하여 주십시오.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue1, 6, 6) )
		{
			alert(cMsgTitle + "의 앞자리는  6자리 숫자입니다." );
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsLength(strValue2, 7, 7) )
		{
			alert(cMsgTitle + "의 뒷자리는  7자리 숫자입니다." );
			objEle2.value = "";
			objEle2.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue1, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsValueType(strValue2, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}

		// 법인번호 유효성 체크
		if ( !IsCorpNumber( strValue1+strValue2 ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );
			objEle1.value = "";
			objEle2.value = "";
			objEle1.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (전화번호)
*	Param	: objEle1
*	Param	: objEle2
*	Param	: objEle3
*	Param	: bRequired	(필수체크 여부)
*	Param	: cPhoneType (P:일반전화, M:휴대폰)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Phone( form.phone[0],  form.phone[1],  form.phone[2], true, 'P', '전화번호' ) == false )	return;
*		if ( check_Phone( form.hp[0],  form.hp[1],  form.hp[2], true, 'M', '휴대전화번호' ) == false )	return;
**/
function check_Phone( objEle1,  objEle2, objEle3, bRequired, cPhoneType, cMsgTitle )
{
	if ( typeof(objEle1) != "object" )   return false;
	if ( typeof(objEle2) != "object" )   return false;
	if ( typeof(objEle3) != "object" )   return false;
	
	var strValue1	= (objEle1.type == "select-one") ? getSelected_Value(objEle1) : getTrim( objEle1.value, "A" );
	var strValue2 = getTrim( objEle2.value, "A" );
	var strValue3 = getTrim( objEle3.value, "A" );
	
	if ( objEle1.type == "select-one" )
	{
		if ( bRequired == false && (strValue2 != "" || strValue3 != "") )	bRequired = true;
	}
	else
	{
		if ( bRequired == false && (strValue1 != "" ||strValue2 != "" || strValue3 != "") )	bRequired = true;
	}

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue1) )
		{
			alert(cMsgTitle + "의 앞자리 값을 입력하여 주십시오.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( IsEmpty(strValue2) )
		{
			alert(cMsgTitle + "의 중간자리 값을 입력하여 주십시오.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( IsEmpty(strValue3) )
		{
			alert(cMsgTitle + "의 뒷자리 값을 입력하여 주십시오.");
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue1, 2, 4) )
		{
			alert(cMsgTitle + "의 앞자리는  2~4자리의 숫자로 입력하여 주십시오." );
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsLength(strValue2, 3, 4) )
		{
			alert(cMsgTitle + "의 중간자리는  3~4자리의 숫자로 입력하여 주십시오." );
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( !IsLength(strValue3, 4, 4) )
		{
			alert(cMsgTitle + "의 뒷자리는  4자리의 숫자로 입력하여 주십시오." );
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue1, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle1.value = "";
			objEle1.focus();
			return false;
		}
		if ( !IsValueType(strValue2, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle2.value = "";
			objEle2.focus();
			return false;
		}
		if ( !IsValueType(strValue3, "N") )
		{
			alert(cMsgTitle + "의 입력값에 숫자가 아닌 문자가 포함되어 있습니다.");
			objEle3.value = "";
			objEle3.focus();
			return false;
		}

		// 전화번호 유효성 체크
		if ( !IsPhone( strValue1+"-"+strValue2+"-"+strValue3, cPhoneType ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );

			if ( objEle1.type == "select-one" )
			{
				objEle1.options[0].selected = true;
			}
			else
			{
				objEle1.value = "";
			}
			objEle2.value = "";
			objEle3.value = "";
			objEle1.focus();
			return false;
		}
	}

	return true;
}


/**
*	TO DO 	: 유효성 체크 (날짜)
*	Param	: objEle
*	Param	: bRequired	(필수체크 여부)
*	Param	: cMsgTitle
*
*	Return	: Boolean
*
*	사용예제
*		if ( check_Date( form.regDate, true, '날짜' ) == false )	return;
**/
function check_Date( objEle, bRequired, cMsgTitle )
{
	if ( typeof(objEle) != "object" )   return false;
	var strValue = getTrim( objEle.value, "A" );
	
	if ( bRequired == false && strValue != "" )	bRequired = true;

	if ( bRequired )
	{
		// 널값체크
		if ( IsEmpty(strValue) )
		{
			alert(cMsgTitle + "의 값을 입력하여 주십시오.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력길이 체크
		if ( !IsLength(strValue, 10, 10) )
		{
			alert(cMsgTitle + "의 입력은 숫자와 하이픈을 사용하여 10자리로 입력하여 주십시오." );
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 입력가능문자 체크
		if ( !IsValueType(strValue, "N-") )
		{
			alert(cMsgTitle + "의 입력값은 입력은 숫자와 하이픈만 사용 가능합니다.");
			objEle.value = "";
			objEle.focus();
			return false;
		}

		// 날짜의 유효성 체크
		var arrDate = strValue.split("-");
		
		if ( !IsDate(arrDate[0], arrDate[1], arrDate[2] ) )
		{
			alert(cMsgTitle + "의 형식이 올바르지 않습니다." );
			objEle.value = "";
			objEle.focus();
			return false;
		}
	}

	return true;
}

/**
*	유효성 체크 (파일 확장자 체크)	
*	
* @Param	: objEle (폼 오브젝트)
* @Param	: usableFileExts	(사용가능한 확장자들)
* @Param	: cMsgTitle (유효하지 않을 시 나타낼 메세지)
*
* @Return	: Boolean
*
*	사용예제
*		if ( check_FileExt( form.fileName, 'jpg, jpeg, gif, png', '이미지 파일을 첨부하여 주십시요.' ) == false )	return;
**/
function check_FileExt(objEle, usableFileExts, cMsgTitle)
{
	var fileExt = "";
	var isValidFileExt = false;
	
	if( IsEmpty(objEle.value) )
	{
		alert(cMsgTitle);
		objEle.focus();
		return false;
	}
	
	var filePointer = objEle.value.lastIndexOf('.');
	fileExt = objEle.value.substring(filePointer + 1, objEle.value.length);
	fileExt = fileExt.toLowerCase();
	
	if( IsEmpty(usableFileExts) )
	{
		alert("사용가능한 파일 확장자를 설정하여 주십시요.");
		return false;
	}
	else
	{
		var arrUsableFileExt = usableFileExts.split(",");
		
		for( var i = 0; i < arrUsableFileExt.length; i++)
		{
		
			if( fileExt == getTrim(arrUsableFileExt[i], 'B'))
			{
				isValidFileExt = true;
				break;
			}
		}
		
		if( !isValidFileExt)
		{
			alert("유효하지 않은 파일입니다. \n\n["+ usableFileExts +"]로 다시 첨부하여 주십시요.");
			objEle.focus();
			return false;
		}
	}
	
	return isValidFileExt;
}

/**
*	레이어 표시하기(동일 페이지에 있는 레이어)	
*	
* @Param	: thisID (오픈 할 레이어의 DIV ID)
*
* @Return	: str
*
*	사용예제
*		Open_Layer(DIV ID 값) 
**/
function Open_Layer(thisID) {
	var open_layer_id = document.getElementById(thisID);
	open_layer_id.style.display="block";
}

/**
*	레이어 닫기(동일 페이지에 있는 레이어)	
*	
* @Param	: thisID (닫을 레이어의 DIV ID)
*
* @Return	: str
*
*	사용예제
*		Close_Layer(DIV ID 값) 
**/
function Close_Layer(thisID) {
	var open_layer_id = document.getElementById(thisID);
	open_layer_id.style.display="none";
}

/**
*	외부 페이지를 호출하여 레이어로 열고 닫기
*	
* @Param	: an (호출할 외부 페이지 경로)
*
* @Param	: width (레이어의 가로 싸이즈)
*
* @Param	: height (레이어의 세로 싸이즈)
*
* @Param	: borderStyle (레이어의 외곽선 타입)
*
* @Param	: layer_num (레이어 정렬 방식, 0 ~ 3)  // move_box에 추가 가능
*
* @Return	: 
*
*	사용예제
*		열기/닫기 : <a href="./address_layer2.asp" onClick="return show_hide_box(this,395,800,'0px solid',3)">
**/
function move_box(an, box, check_num) {
//링크된 위치에서 부터의 설정값 지정
  var check_num = check_num;
  var cleft = 0;  //왼쪽마진  
  var ctop = 0;  //상단마진
  
  if(check_num == 1) {
	cleft = -200;  //왼쪽마진  
	ctop = 0;  //상단마진
  }
  else if(check_num == 2) {
	cleft = -20;  //왼쪽마진  
	ctop = 0;  //상단마진
  }
  else if(check_num == 3) {
	cleft = -580;  //왼쪽마진  
	ctop = -43;  //상단마진
  }
  else {
	cleft = 0;  //왼쪽마진  
	ctop = 0;  //상단마진
  } 

  var obj = an;
  while (obj.offsetParent) {
    cleft += obj.offsetLeft;
    ctop += obj.offsetTop;
    obj = obj.offsetParent;
  }
  box.style.left = cleft + 'px';
  ctop += an.offsetHeight + 8;
  if (document.body.currentStyle &&
    document.body.currentStyle['marginTop']) {
    ctop += parseInt(
      document.body.currentStyle['marginTop']);
  }
  box.style.top = ctop + 'px';
}

function show_hide_box(an, width, height, borderStyle, layer_num, layer_id) {
  var href = an.href;
  var boxdiv = document.getElementById(href);
  var check_num = layer_num;

  if (boxdiv != null) {
    if (boxdiv.style.display=='none') {
      move_box(an, boxdiv, check_num);
      boxdiv.style.display='block';
    } else
      boxdiv.style.display='none';
    return false;
  }

  boxdiv = document.createElement('div');
  boxdiv.setAttribute('id', href);
  boxdiv.style.display = 'block';
  boxdiv.style.position = 'absolute';
  boxdiv.style.width = width + 'px';
  boxdiv.style.height = height + 'px';
  boxdiv.style.border = borderStyle;
  boxdiv.style.backgroundColor = '#fff';
	
  var contents = document.createElement('iframe');
  contents.id = 'selectMlist';
  contents.name = 'selectMlist';
  contents.scrolling = 'no';
  contents.frameBorder = '0';
  contents.style.width = width + 'px';
  contents.style.height = height + 'px';
  contents.src = href;

  boxdiv.appendChild(contents);
  document.body.appendChild(boxdiv);
  move_box(an, boxdiv, check_num);

  return false;
}

/**
*	페이지 위로 이동하기
*	
*
*	사용예제
*		goTop();
**/

function goTop(desy) { 
	var Timer; 
	var starty = document.body.scrollTop; 
	var oriy = 0;  //top 위치 
			var speed = 3; 

	if(Timer) clearTimeout(Timer); 

	if(!desy) desy = starty; 
	desy += (oriy - starty) / speed; 
	if (desy < oriy) desy = oriy; 
	var posY = Math.ceil(desy); 
	window.scrollTo(0, posY); 
	if((Math.floor(Math.abs(starty - oriy)) < 1)){ 
	clearTimeout(Timer); 
	window.scroll(0,oriy); 
	}else if(posY > oriy){ 
	Timer = setTimeout("goTop("+desy+")",1);//올라가는 속도(낮을수록 빠름) 
	}else{ 
	clearTimeout(Timer); 
	} 
} 


/**********************************
*
*	페이스북 공유 하기
*  사용예) goFacebook(ShareUrl, DocTitle, DocSummary, DocImage)
*
**********************************/

function goFacebook(ShareUrl, DocTitle, DocSummary, DocImage){
	
	newwindow = window.open('http://www.facebook.com/sharer.php?s=100&p[url]='+encodeURIComponent(ShareUrl)+'&p[title]='+encodeURIComponent(DocTitle)+'&p[summary]='+encodeURIComponent(DocSummary)+'&p[images][0]='+encodeURIComponent(DocImage), 'facebookpopup', 'toolbar=0, status=0, width=626, height=436');
	
	if(window.focus) {
		newwindow.focus();
	}
}


/**********************************
*
*	트위터 공유 하기
*  사용예) goTweeter(sUrl, title)
*
**********************************/
	
function goTweeter(sUrl, title) {
	var text = title+" "+sUrl;

	window.open("http://twitter.com/home?status="+encodeURIComponent(text), 'twitterpopup', 'toolbar=0, status=0, width=626, height=436');
}


/**********************************
*
*	새창에서 부모창 새로고침
*  사용예) parentWindowReload()
*
**********************************/
	
function parentWindowReload() {
	opener.location.reload(true);
}

//-->