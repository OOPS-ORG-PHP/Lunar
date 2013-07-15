<?php
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim: set filetype=php noet sw=4 ts=4 fdm=marker:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */

require_once 'Lunar.php';

// 수행 시간 체크
function get_microtime ($old, $new) {
	$start = explode(" ", $old);
	$end = explode(" ", $new);
	return sprintf("%.2f", ($end[1] + $end[0]) - ($start[1] + $start[0]));
}

// 이전달,다음달 링크
function prev_next ($y, $m) {
	$prevm = (int) $m - 1;
	$nextm = (int) $m + 1;

	if ( $prevm < 1 ) {
		$prevm = 12;
		$prevy = $y - 1;
	} else
		$prevy = $y;

	if ( $nextm > 12 ) {
		$nextm = 1;
		$nexty = $y + 1;
	} else
		$nexty = $y;

	if ( $prevm < 10 )
		$prevm = '0' . $prevm;

	if ( $nextm < 10 )
		$nextm = '0' . $nextm;

	return (object) array (
		'prev' => $prevy . '-' . $prevm,
		'next' => $nexty . '-' . $nextm
	);
}

$cur = date ('Y-m-d');

if ( preg_match ('/^(-?[0-9]{1,4})-([0-9]{1,2})$/', $_GET['v'], $match) ) {
	array_shift ($match);
	list ($year, $month) = $match;
} else
	list ($year, $month) = preg_split ('/-/', $cur);

// -2400 ~ 2200 까지로 제한한다.
if ( $year < -2400 || $year > 2200 ) {
	readfile ('./error.html');
	exit;
}

$lday = array (0, 31, 28, 31, 30, 31, 31, 30, 31, 30, 31, 30, 31);
$wday = array ('일' => 1, '월' => 2, '화' => 3, '수' => 4, '목' => 5, '금' => 6, '토' => 7);

$old = microtime ();

$lunar = new Lunar;

$cdate = $year . '-' . $month . '-01';
# 1일에 대한 음력 정보
$fday = $lunar->tolunar ($cdate);
# 1일에 대한 세차/월간/일진 정보
$tune = $lunar->dayfortune ($cdate);
# 지정한 달 1일의 음력달에 대한 합삭/망 정보
$moon = $lunar->moonstatus ($cdate);
# 1일의 28수 정보
$s28  = $lunar->s28day ($cdate);
# 지정한 달의 절기 정보
$season = $lunar->seasondate ($cdate);

# 윤년이면 2월의 마지막 날을 29일로 수정
if ( $lunar->is_yoon ($year) )
	$lday[2] = 29;

# 현재달의 마지막 날자
$lastday = $lday[(int) $month];

# 1일이 음력 윤달이면 음력월 정보 앞에 (윤)을 붙임
if ( $fday->moonyoon )
	$fday->month = '(閏)' . $fday->month;

$tdstart = $wday[$fday->week];
$tdend = $tdstart + $lastday;

// java script 음력 배열 정보 만들기
$lm = '[ \'\', \'' . $fday->month . '\'';
$ld = '[ 0, ' . $fday->day;
$l28s = '[ \'\', \'' . $s28->h . '\'';
$liljin = '[ \'\', \'' . $tune->hday . '\'';

$_s28 = $s28;
$mbuf = $fday->month;
$dbuf = $fday->day;
for ( $i=1; $i<$lastday; $i++ ) {
	$chk = false;
	$dbuf++;

	if ( $fday->largemoon ) {
		if ( $dbuf > 30 ) {
			$mbuf = preg_replace ('/[^0-9]/', '', $fday->month);
			$mbuf ++;
			$dbuf -= 30;
			$chk = true;
		}
	} else {
		if ( $dbuf > 29 ) {
			$mbuf = preg_replace ('/[^0-9]/', '', $fday->month);
			$mbuf ++;
			$dbuf -= 29;
			$chk = true;
		}
	}

	// 음력 마지막일이면, 음력 다음달의 음력 정보를 구한다.
	// 다음 음력달이 윤달일 경우 확인 해야 하며, 다음 음력달의
	// 합삭/망 정보도 구한다.
	if ( $chk ) {
		$cdate = $year . '-' . $month . '-' . ($i + 2);
		$r = $lunar->tolunar ($cdate);
		$mbuf = $r->moonyoon ? '(閏)' : '';
		$mbuf .= $r->month;
		$moon1 = $lunar->moonstatus ($cdate);
	}

	$lm .= ', \'' . $mbuf . '\'';
	$ld .= ', ' . $dbuf;
	$_s28 = $lunar->s28day ($_s28);
	$l28s .= ', \'' . $_s28->h . '\'';

	$gindex = $tune->data->d + $i;
	if ( $gindex >= 60 )
		$gindex -= 60;

	$liljin .= ', \'' . $lunar->ganji_ref($gindex, true) . '\'';
}
$lm .= ' ]';
$ld .= ' ]';
$l28s .= ' ]';
$liljin .= ' ]';

$np = prev_next ($year, $month);

$hyear = $lunar->human_year ($year);

$new = microtime ();
$ptime = get_microtime ($old, $new);
?><!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title>진짜 만세력 PHP version</title>
	<link type="text/css" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="manse.css">

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script type="text/javascript" src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>

	<script type="text/javascript">
	//<![CDATA[
		var td = 1;
		var std = 0;
		var ltd = 0;
		var Manse = {
			tdstart: <?=$tdstart?>,
			lastday: <?=$lday[(int) $month]?>,
			tdend:   <?=$tdend?>,
			holy: [
				[ ],
				[ , '신정' ],
				[ ],
				[ , '삼일절' ],
				[ ],
				[ , , , , , '어린이날' ],
				[ , , , , , , '현충일' ],
				[ , , , , , , , , , , , , , , , , , '제헌절' ],
				[ , , , , , , , , , , , , , , , '광복절' ],
				[ ],
				[ , , , '개천절', , , , , , '한글날' ],
				[ ],
				[ , , , , , , , , , , , , , , , , , , , , , , , , , '성탄절' ]
			],
			lmonth: <?=$lm?>,
			lday:   <?=$ld?>,
			liljin: <?=$liljin?>,
			l28s:   <?=$l28s?>,

			fixtable: function () {
				for ( td=1; td<this.tdstart; td++ ) {
					$( '#td' + td ).empty ();
				}

				var i = 1;
				for ( ; td<this.tdend; td++,i++ ) {
					var julip = '';
					if ( i == <?=$season->center->day?> ) {
						julip = '<span class="julip"><?=$season->center->name?></span>';
					} else if ( i == <?=$season->ccenter->day?> ) {
						julip = '<span class="julip"><?=$season->ccenter->name?></span>';
					}

					var data = '<span class="day">' + i + '</span>'
							+ '<span class="su">' + this.l28s[i] + '</span><br>'
							+ '<span class="lun">' + this.lmonth[i] + '.' + this.lday[i] + '</span><br>'
							+ '<span class="iljin">' + this.liljin[i] + ' </span>' + julip;
					$( '#td' + td ).html (data);

					if ( this.l28s[i] == '角' )
						$( '#td' + td + ' span.su' ).css ('color', '#da4f49');
					if ( this.liljin[i] == '甲子' )
						$( '#td' + td + ' span.iljin' ).css ('color', '#da4f49');

					// 음력 공휴일 처리
					var chkm = this.lmonth[i].replace (/[^0-9]/, '');
					if ( chkm == 1 ) {
						if ( this.lday[i] == 1 ) {
							$( '#td' + td + ' span.day' ).addClass ('sun');
							$( '#td' + (td - 1 ) + ' span.day' ).addClass ('sun');
						} else if ( this.lday[i] == 2 )
							$( '#td' + td + ' span.day' ).addClass ('sun');
					} else if ( chkm == 4 && this.lday[i] == 8 )
						$( '#td' + td + ' span.day' ).addClass ('sun');
					else if ( chkm == 8 ) {
						if ( this.lday[i] == 15 ) {
							$( '#td' + td + ' span.day' ).addClass ('sun');
							$( '#td' + (td - 1 ) + ' span.day' ).addClass ('sun');
						} else if ( this.lday[i] == 16 )
							$( '#td' + td + ' span.day' ).addClass ('sun');
					}

					if ( this.holy[<?=(int) $month?>][i] )
						$( '#td' + td + ' span.day' ).addClass ('sun');

				}

				var ndis = false;
				for ( ; td<=42; td++ ) {
					if ( ! ndis )
						ndis = (td % 7) == 1 ? true : false;

					if ( ndis ) {
						$( '#td' + td ).css ('display', 'none');
					} else
						$( '#td' + td ).empty ();
				}
			}
		}
	//]]>
	</script>
</head>

<body onload="Manse.fixtable ()">

<div class="guide">

	<h3>
		진짜 만세력 PHP API
		<a href="#desc" class="btn btn-info">About</a>
		<a href="./index.phps" class="btn btn-warning">View Source</a>
	</h3>

	<div class="year-title">
		<a href="./?v=<?=$np->prev?>" class="btn btn-warning btn-mini"><i class="icon-chevron-left icon-white"></i> 이전월</a>
		<span class="cur-month"><?=$hyear?>년 <?=$month?>월 (<?=$tune->hmonth?>)</span>
		<a href= "./?v=<?=$np->next?>" class="btn btn-warning btn-mini">다음월 <i class="icon-chevron-right icon-white"></i></a>
	</div>

	<div class="calendar">

		<div id="calendar">
			<div id="sun" class="ctd yoil sun">일</div>
			<div id="mon" class="ctd yoil">월</div>
			<div id="tue" class="ctd yoil">화</div>
			<div id="wed" class="ctd yoil">수</div>
			<div id="thu" class="ctd yoil">목</div>
			<div id="fri" class="ctd yoil">금</div>
			<div id="sat" class="ctd yoil sat">토</div>
			<div id="td1" class="ctd sun">1</div>
			<div id="td2" class="ctd">1</div>
			<div id="td3" class="ctd">1</div>
			<div id="td4" class="ctd">1</div>
			<div id="td5" class="ctd">1</div>
			<div id="td6" class="ctd">1</div>
			<div id="td7" class="ctd sat">1</div>
			<div id="td8" class="ctd sun">1</div>
			<div id="td9" class="ctd">1</div>
			<div id="td10" class="ctd">1</div>
			<div id="td11" class="ctd">1</div>
			<div id="td12" class="ctd">1</div>
			<div id="td13" class="ctd">1</div>
			<div id="td14" class="ctd sat">1</div>
			<div id="td15" class="ctd sun">1</div>
			<div id="td16" class="ctd">1</div>
			<div id="td17" class="ctd">1</div>
			<div id="td18" class="ctd">1</div>
			<div id="td19" class="ctd">1</div>
			<div id="td20" class="ctd">1</div>
			<div id="td21" class="ctd sat">1</div>
			<div id="td22" class="ctd sun">1</div>
			<div id="td23" class="ctd">1</div>
			<div id="td24" class="ctd">1</div>
			<div id="td25" class="ctd">1</div>
			<div id="td26" class="ctd">1</div>
			<div id="td27" class="ctd">1</div>
			<div id="td28" class="ctd sat">1</div>
			<div id="td29" class="ctd sun">1</div>
			<div id="td30" class="ctd">1</div>
			<div id="td31" class="ctd">1</div>
			<div id="td32" class="ctd">1</div>
			<div id="td33" class="ctd">1</div>
			<div id="td34" class="ctd">1</div>
			<div id="td35" class="ctd sat">1</div>
			<div id="td36" class="ctd sun">1</div>
			<div id="td37" class="ctd">1</div>
			<div id="td38" class="ctd">1</div>
			<div id="td39" class="ctd">1</div>
			<div id="td40" class="ctd">1</div>
			<div id="td41" class="ctd">1</div>
			<div id="td42" class="ctd sat">1</div>
		</div>

		<div id="context">
			<span class="b">Processing Time:</span>: <?=$ptime?> sec<br><br>

	 		<div class="stitle b">이번달 절입:</div> <?=$season->center->name?>
						<?=$season->center->hyear?>년
						<?=$season->center->month?>월
						<?=$season->center->day?>일
						<?=$season->center->hour?>시
						<?=$season->center->min?>분<br>
 			<div class="stitle b">이번달 중기:</div> <?=$season->ccenter->name?>
						<?=$season->ccenter->hyear?>년
						<?=$season->ccenter->month?>월
						<?=$season->ccenter->day?>일
						<?=$season->ccenter->hour?>시
						<?=$season->ccenter->min?>분<br>
 			<div class="stitle b">다음달 중기:</div> <?=$season->nenter->name?>
						<?=$season->nenter->hyear?>년
						<?=$season->nenter->month?>월
						<?=$season->nenter->day?>일
						<?=$season->nenter->hour?>시
						<?=$season->nenter->min?>분<br><br>

			<div class="stitle b">합삭 (New Moon):</div>
						<?=$moon->new->hyear?>년
						<?=$moon->new->month?>월
						<?=$moon->new->day?>일
						<?=$moon->new->hour?>시
						<?=$moon->new->min?>분<br>
			<div class="stitle b">망 (Full Moon):</div>
						<?=$moon->full->hyear?>년
						<?=$moon->full->month?>월
						<?=$moon->full->day?>일
						<?=$moon->full->hour?>시
						<?=$moon->full->min?>분<br>

			<div class="stitle b">합삭 (New Moon):</div>
						<?=$moon1->new->hyear?>년
						<?=$moon1->new->month?>월
						<?=$moon1->new->day?>일
						<?=$moon1->new->hour?>시
						<?=$moon1->new->min?>분<br>
			<div class="stitle b">망 (Full Moon):</div>
						<?=$moon1->full->hyear?>년
						<?=$moon1->full->month?>월
						<?=$moon1->full->day?>일
						<?=$moon1->full->hour?>시
						<?=$moon1->full->min?>분<br>


			<a name="desc"></a>

			<hr>

			<h5>* About 진짜 만세력 PHP version</h5>

			<p>
			<span class="label label-warning">진짜만세력</span> PHP Api 1.0.0은
			<a href="mailto:kohyc@chollian.net">고영창</a>님의
			<span class="label label-warning">진짜만세력</span> 0.92 Perl 버전을
			PHP로 포팅한 것이다.
			</p>

			<p>
			이 API의 유효기간은 다음과 같다.
			</p>

			<ul>
				<li>32bit:
					<ul>
						<li>양력 BC 2086(-2087)년 2월 9일 ~ AD 6078년 1월 29일</li>
						<li>음력 BC 2086(-2087)년 1월 1일 ~ AD 6077년 12월 29일</li>
						<li>BC 2087년 7월 5일부터는 계산이 급속하게 느려진다.</li>
					</ul>
				</li>
				<li>64bit:
					<ul>
						<li>BC 9998(-9999)년 1월 1일 ~ AD 9999년 12월 31일</li>
						<li>API의 연도 체크가 4자리 까지임..</li>
						<li>64bit 계산이 가능한 시점까지 가능할 듯..</li>
					</ul>
				</li>
			</ul>

			<p>
			현 페이지의 달력은 BC 2399년 부터 AD 2300년 까지만 지원을 한다. 그 이유는
			이 시점을 벋어날 경우, <u>계산 속도가 급속하게 느려지기 때문이다</u>.
			</p>

			<p>
			왠만하면, 과거 2000년과 미래 100년 구간 이내에서 사용하는 것을 권장한다.
			</p>

			<p>
			<span class="label label-warning">진짜만세력</span>에 대해서는 다음의 URL을
			참고 하기 바란다.<br><br>

			<span class="label">Original Site</span>
			<a href="http://afnmp3.homeip.net:81/~kohyc/calendar/index.cgi">http://afnmp3.homeip.net:81/~kohyc/calendar/index.cgi</a><br>
			<span class="label">PHP Source</span>
			<a href="http://svn.oops.org/wsvn/PHP.pear_Lunar/trunk/">OOPS.org SVN</a><br>
			<span class="label">Download &amp; Installation</span>
			<a href="http://pear.oops.org/">pear.oops.org</a>
			</p>

			<hr>

			<div class="copy">Copyright &copy; 2013 <a href="//oops.org">OOPS.org</a></div>
		</div>

	</div>

</div>
</body>
</html>
