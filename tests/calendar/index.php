<?php

require_once 'Lunar.php';

function get_microtime ($old, $new) {
	$start = explode(" ", $old);
	$end = explode(" ", $new);
	return sprintf("%.2f", ($end[1] + $end[0]) - ($start[1] + $start[0]));
}

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

if ( $year < -2400 || $year > 2200 ) {
	readfile ('./error.html');
	exit;
}

$gregorian_check = $year . $month;

$lday = array (0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$wday = array ('일' => 1, '월' => 2, '화' => 3, '수' => 4, '목' => 5, '금' => 6, '토' => 7);

$old = microtime ();

$lunar = new oops\Lunar;

$cdate = $year . '-' . $month . '-01';
$fday = $lunar->tolunar ($cdate);
$tune = $lunar->dayfortune ($cdate);
$moon = $lunar->moonstatus ($cdate);
$moon1 = $lunar->moonstatus ($year . '-' . $month . '-28');
$s28  = $lunar->s28day ($cdate);
$season = $lunar->seasondate ($cdate);

/*
 * pear_Lunar 1.0.0에서는 is_leap method가 연도만으로 율리우스력
 * 판단을 하지 않으며로 아래과 같이 조건을 체크해야 한다.
 * if ( $lunar->is_leap ($year, $year < 1583 ? true : false) )
 * pear_Lunar 1.0.1 부터는 2번째 인자가 없어도, 연도만으로
 * 율리우스력 판단을 한다.
 */
if ( $lunar->is_leap ($year) )
	$lday[2] = 29;
$lastday = $lday[(int) $month];

if ( $fday->leap )
	$fday->month = '(閏)' . $fday->month;

$tdstart = $wday[$fday->week];
$tdend = $tdstart + $lastday;

if ( $gregorian_check == 158210 ) {
	require_once '158210.php';
} else {
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

		if ( $fday->largemonth ) {
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

		if ( $chk ) {
			if ( ($i + 2) > $lday[(int) $month] ) {
				$cmon = $month + 1;
				$cday = ($i + 2) - $lday[(int) $month];
			} else {
				$cmon = $month;
				$cday = $i + 2;
			}
			$cdate = '';
			$cdate = $year . '-' . $cmon . '-' . $cday;
			#echo $cdate . ' : ' . $lday[(int) $month] . ' : ' . $i . "<br>\n";
			$r = $lunar->tolunar ($cdate);
			/*
			echo "<pre>\n";
			print_r ($r);
			echo "</pre>\n";
			 */

			if ( $season->center->month != $month )
				$season = $lunar->seasondate ($cdate);

			$mbuf = $r->leap ? '(閏)' : '';
			$mbuf .= $r->month;

			if ( $r->largemonth )
				$fday->largemonth = true;
			$chk == false;
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
}

$np = prev_next ($year, $month);

$hyear = $lunar->human_year ($year);

$new = microtime ();
$ptime = get_microtime ($old, $new);
?><!doctype html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title>Lunar/Solar Pear package</title>
	<link rel="stylesheet" type="text/css" href="//cdn.oops.org/bootstrap/2.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="manse.css">
	<!--[if IE]><link rel="stylesheet" type="text/css" href="manse-ie.css"><![endif]-->
	<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="manse-ie7.css"><![endif]-->

	<script type="text/javascript" src="//cdn.oops.org/jquery/1.10.2.min.js"></script>
	<script type="text/javascript" src="//cdn.oops.org/bootstrap/2.3.2/js/bootstrap.min.js"></script>

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
				[ ],
				[ , , , , , , , , , , , , , , , '광복절' ],
				[ ],
				[ , , , '개천절', , , , , , '한글날' ],
				[ ],
				[ , , , , , , , , , , , , , , , , , , , , , , , , , '성탄절' ]
			],
			cmon:   <?=(int) $month?>,
			lmonth: <?=$lm?>,
			lday:   <?=$ld?>,
			liljin: <?=$liljin?>,
			l28s:   <?=$l28s?>,

			fixtable: function () {
<?php
echo <<<EOF
				var season = new Object ();
				season.m{$season->center->month}d{$season->center->day} = '{$season->center->name}';
				season.m{$season->ccenter->month}d{$season->ccenter->day} = '{$season->ccenter->name}';
				season.m{$season->nenter->month}d{$season->nenter->day} = '{$season->nenter->name}';


EOF;
?>
				for ( td=1; td<this.tdstart; td++ ) {
					$( '#td' + td ).empty ();
				}

				var i = 1;
				for ( ; td<this.tdend; td++,i++ ) {
					if ( this.tdend == 23 )
						dayv = (i > 4) ? i + 10 : i;
					else
						dayv = i;

					var julip = '';

					eval ('var jv = season.m' + this.cmon + 'd' + dayv);
					if ( jv != undefined )
						julip = '<span class="julip">' + jv + '</span>';

					var data = '<span class="day">' + dayv + '</span>'
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
		Lunar/Solar Pear Calendar
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
			<div class="stitle b">Processing Time:</div> <?=$ptime?> sec<br>
			<div class="stitle b">Valid Period:</div> BC 2401(-2400)년 ~ AD 2300<br><br>

	 		<div class="stitle b">이번달 절입:</div> <?=$season->center->name?>
						<?=$season->center->hyear?>년
						<?=$season->center->month?>월
						<?=$season->center->day?>일
						<?=$season->center->hour?>시
						<?=$season->center->min?>분
						<span class="label label-success">Julian Date</span>  <?=$season->center->julian?><br>
 			<div class="stitle b">이번달 중기:</div> <?=$season->ccenter->name?>
						<?=$season->ccenter->hyear?>년
						<?=$season->ccenter->month?>월
						<?=$season->ccenter->day?>일
						<?=$season->ccenter->hour?>시
						<?=$season->ccenter->min?>분
						<span class="label label-success">Julian Date</span>  <?=$season->ccenter->julian?><br>
 			<div class="stitle b">다음달 절입:</div> <?=$season->nenter->name?>
						<?=$season->nenter->hyear?>년
						<?=$season->nenter->month?>월
						<?=$season->nenter->day?>일
						<?=$season->nenter->hour?>시
						<?=$season->nenter->min?>분
						<span class="label label-success">Julian Date</span>  <?=$season->nenter->julian?><br><br>

			<div class="stitle b">합삭 (New Moon):</div>
						<?=$moon->new->hyear?>년
						<?=$moon->new->month?>월
						<?=$moon->new->day?>일
						<?=$moon->new->hour?>시
						<?=$moon->new->min?>분
						<span class="label label-success">Julian Date</span>  <?=$moon->new->julian?><br>
			<div class="stitle b">망 (Full Moon):</div>
						<?=$moon->full->hyear?>년
						<?=$moon->full->month?>월
						<?=$moon->full->day?>일
						<?=$moon->full->hour?>시
						<?=$moon->full->min?>분
						<span class="label label-success">Julian Date</span>  <?=$moon->full->julian?><br>

			<div class="stitle b">합삭 (New Moon):</div>
						<?=$moon1->new->hyear?>년
						<?=$moon1->new->month?>월
						<?=$moon1->new->day?>일
						<?=$moon1->new->hour?>시
						<?=$moon1->new->min?>분
						<span class="label label-success">Julian Date</span>  <?=$moon1->new->julian?><br>
			<div class="stitle b">망 (Full Moon):</div>
						<?=$moon1->full->hyear?>년
						<?=$moon1->full->month?>월
						<?=$moon1->full->day?>일
						<?=$moon1->full->hour?>시
						<?=$moon1->full->min?>분
						<span class="label label-success">Julian Date</span>  <?=$moon1->full->julian?><br>


			<a name="desc"></a>

			<hr>

			<h5>* About Lunar/Solar Pear Package</h5>

			<p>
			<span class="label label-warning">Lunar</span> Pear package는
			<a href="mailto:kohyc@chollian.net">고영창</a>님의
			<span class="label label-warning">진짜만세력</span> 0.92 Perl 버전을
			PHP로 포팅한 것이다.
			</p>

			<p>
			먼저 달력을 보기전에 다음 사항을 꼭 인지를 해야 한다.
			</p>

			<ol>
				<li>우리가 사용하는 그레고리력은 1582년 10월 15일 부터 존재한다.</li>
				<li>1582년 10월 15일 이전은 율리우스력으로 표기한다.</li>
				<li>1582년 10월 5일 부터 1582년 10월 14일은 calender상에 존재하지 않는다.</li>
			</ol>

			<p>
			그럼 고영창님의 <span class="label label-warning">진짜만세력</span>과 다른
			달력들과의 차이가 발생하는 이유를 살펴 보자면 다음의 특징들이 있다.
			</p>

			<ol>
				<li>대부분의 calendar들은 1582년 10월 15일 이전을 율리우스력으로 표기한다.</li>
				<li><span class="label label-warning">진짜만세력</span>은 모든 표시를
					그레고리력으로 표기한다.</li>
				<li>심지어 <span class="label label-warning">진짜만세력</span>은
					존재하지 않는 1582.10.5~1582.10.14 기간을 표시한다.</span>
			</ol>

			<p>
			이런 이유로 고영장님의 <span class="label label-warning">진짜만세력</span>은
			1582년 10월 15일 이전의 데이터에 대해서는 다른 달력들과 많은 차이를 보이게
			된다. 하지만, 그렇다고 해서 고영창님의 달력이 잘못되었다고 할 수는 없으며,
			율리우스 적일(Julian date)의 경우에는 정확한 표기를 하고 있는 것으로 보인다.
			</p>

			<p>
			또한, 기존의 달력들이 database를 구축하여 사용하는 방식이나, 고영창님의
			<span class="label label-warning">진짜만세력</span>은 계산에 의하여 사용
			되므로, 음력 윤달의 기준이 조금 다르기 때문에 일간이나 음력윤달의 차이로
			인하여 음력날자가 1~2일 정도가 차이가 발생할 수 있다. 이에 대해서는 고영창님의
			<a href="http://afnmp3.homeip.net/~kohyc/calendar/index.cgi">홈페이지</a>를
			참조하기 바란다.

			<p>
			<span class="label label-warning">진짜만세력</span>과 여기서 배포하는
			진짜만세력 PHP API의 차이는 다음과 같다.
			</p>

			<ol>
				<li>모든 계산은 original <span class="label label-warning">진짜만세력</span>의
					계산 방식을 따른다. (이는 다른 calendar들과 음력 날자가 1~2일의 차이가
					발생할 수 있고, 음력 윤달이 다를 수 있다.)</li>
				<li>1582년 10월 15일 이전의 표기를 율리우스력을 사용한다. (다른
					calender들과 역사 기록과 맞추기 위해서...)</li>
				<li>율리우스력을 사용하기 때문에 기원전 calender는 BC 4713년
					2월 8일 부터 가능하다.</li>
				<li>위의 이유는 <span class="label label-warning">진짜만세력</span>의
					계산은 그레고리력으로 하기 때문에 율리우스력을 그레고리력으로
					변환하기 위한 알고리즘의 제약 때문이다.</li>
				<li>2.0 부터는 <a href="http://pear.oops.org/">oops/KASI_Lunar pear package</a>가
					설치되어 있을 경우, 1391-02-05 ~ 2050-12-31 까지는 한국천문연구원의 음양력
					DB를 지원한다.</li>
			</ol>

			<p>
			고영창님의 <span class="label label-warning">진짜만세력</span>과 동일하게
			포팅한 버전을 원할 경우,
			<a href="//oops.org/project/manse/original/">http://oops.org/project/manse/original</a>을
			참고 하도록 한다.
			</p>

			<p style="text-decoration: underline;">
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
						<li>BC 4713(-4712)년 2월 8일 ~ AD 9999년 12월 31일</li>
						<li>API의 연도 체크가 4자리 까지이므로 10000년 이상은 확인 못함</li>
						<li>64bit 계산이 가능한 시점까지 가능할 듯..</li>
						<li>기원전의 경우 율리우스 적일이 BC 4713년 1월 1일 부터이므로
							그레고리력 변환이 가능한  BC 4713년 2월 8일부터 가능하다.</li>
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
