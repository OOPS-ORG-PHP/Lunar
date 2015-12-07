<?php
/**
 * Project: Lunar :: 양력/음력 변환 클래스<br>
 * File:    Lunar.php
 *
 * 이 패키지는 양력/음력간의 변환을 제공한다.
 *
 * 1852년 10월 15일 이전의 양력 날자는 율리우스력으로 취급을 하며, 내부
 * 계산시에 그레고리력으로 변환을 하여 계산을 한다.
 *
 * 제공 되는 기능은 다음과 같다.
 *
 * 1. 양력/음력 변환 API
 * 2. 절기 API
 * 3. 합삭/막 정보 API
 * 4. 세차/월간/일진 API 등
 *
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -4712-02-08 ~ 9999-12-31
 *     + API의 연도 체크가 4자리 까지이므로 10000년 이상은 확인 못함
 *     + 64bit 계산이 가능한 시점까지 가능할 듯..
 *     + 기원전의 경우 Julian date가 BC 4713년 1월 1일 부터이므로
 *       Gregorian calendar 변환이 가능한 BC 4713년 2월 8일부터 가능
 * </pre>
 *
 * 계산 처리 시간상, 과거 2000년전과 미래 100년후의 시간은 왠만하면 웹에서는
 * 사용하는 것을 권장하지 않음!
 *
 * 참고!
 *
 * oops\KASI_Lunar pear package를 설치한 후에, KASI_Lunar.php 를 include 하면,
 * 내부적으로 1391-02-05 ~ 2050-12-31 기간은 한국천문연구원의 음양력 DB를 이용하여
 * 처리를 한다.
 *
 * 주의!
 *
 * pear_Lunar package는 2가지 라이센스를 가지고 있다. pear_Lunar 패키지의
 * Core API (Lunar/Lunar_API.php)는 고영창님의 '진짜만세력' 코드를 PHP로
 * 포팅한 것으로, 고영창님에게 라이센스가 있으며, front end API(Lunar.php)는
 * 김정균이 작성한 코드들로 BSD license를 따른다.
 *
 * @category    Calendar
 * @package     oops\Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2015 OOPS.org
 * @license     BSD (Lunar.php) And 고영창(Lunar/Lunar_API.php)
 * @version     SVN: $Id$
 * @link        http://pear.oops.org/package/Lunar
 * @since       File available since release 0.0.1
 * @example     pear_Lunar/tests/test.php Sample code
 * @filesource
 */

/**
 * Namespace oops
 */
namespace oops;

/**
 * import myException class
 */
require_once 'myException.php';
set_error_handler('myException::myErrorHandler');


/**
 * import Lunar API
 */
require_once 'Lunar/Lunar_API.php';

/**
 * 양력/음력 변환 클래스
 *
 * 이 패키지는 양력/음력간의 변환을 제공한다.
 *
 * 1852년 10월 15일 이전의 양력 날자는 율리우스력으로 취급을 하며, 내부
 * 계산시에 그레고리력으로 변환을 하여 계산을 한다.
 *
 * 제공 되는 기능은 다음과 같다.
 *
 * 1. 양력/음력 변환 API
 * 2. 절기 API
 * 3. 합삭/막 정보 API
 * 4. 세차/월간/일진 API 등
 *
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -4712-02-08 ~ 9999-12-31
 *     + API의 연도 체크가 4자리 까지이므로 10000년 이상은 확인 못함
 *     + 64bit 계산이 가능한 시점까지 가능할 듯..
 *     + 기원전의 경우 Julian date가 BC 4713년 1월 1일 부터이므로
 *       Gregorian calendar 변환이 가능한 BC 4713년 2월 8일부터 가능
 * </pre>
 *
 * 계산 처리 시간상, 과거 2000년전과 미래 100년후의 시간은 왠만하면 웹에서는
 * 사용하는 것을 권장하지 않음!
 *
 * 참고!
 *
 * oops\KASI_Lunar pear package를 설치한 후에, KASI_Lunar.php 를 include 하면,
 * 내부적으로 1391-02-05 ~ 2050-12-31 기간은 한국천문연구원의 음양력 DB를 이용하여
 * 처리를 한다.
 *
 * @package     oops\Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 2015 OOPS.org
 * @license     BSD (Lunar.php) And 고영창(Lunar/Lunar_API.php)
 * @version     SVN: $Id$
 * @example     pear_Lunar/tests/test.php Sample code
 */
Class Lunar extends Lunar_API {
	private $KASI = null;

	// {{{ +-- public (array) toargs ($v, $lunar = fasle)
	/**
	 * 입력된 날자 형식을 연/월/일의 멤버를 가지는 배열로 반환한다.
	 * 입력된 변수 값은 YYYY-MM-DD 형식으로 변환 된다.
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 30 25}
	 *
	 * @access public
	 * @return array
	 *   <pre>
	 *       Array
	 *       (
	 *           [0] => 2013
	 *           [1] => 7
	 *           [2] => 16
	 *       )
	 *   </pre>
	 * @param string|int 날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 */
	public function toargs (&$v, $lunar = false) {
		if ( $v == null ) {
			$y = (int) date ('Y');
			$m = (int) date ('m');
			$d = (int) date ('d');
		} else {
			if ( is_numeric ($v) && $v > 30000000 ) {
				// unit stamp ?
				$y = (int) date ('Y', $v);
				$m = (int) date ('m', $v);
				$d = (int) date ('d', $v);
			} else {
				if ( preg_match ('/^(-?[0-9]{1,4})[\/-]?([0-9]{1,2})[\/-]?([0-9]{1,2})$/', trim ($v), $match) ) {
					array_shift ($match);
					list ($y, $m, $d) = $match;
				} else {
					throw new \myException ('Invalid Date Format', E_USER_WARNING);
					return false;
				}
			}

			// 넘어온 날자가 음력일 경우 아래가 실행되면 측정 날자가 달라질 수 있다.
			if ( ! $lunar && $y > 1969 && $y < 2038 ) {
				$fixed = mktime (0, 0, 0, $m, $d, $y);
				$y = (int) date ('Y', $fixed);
				$m = (int) date ('m', $fixed);
				$d = (int) date ('d', $fixed);
			} else {
				if ( $m > 12 || $d > 31 ) {
					throw new \myException ('Invalid Date Format', E_USER_WARNING);
					return false;
				}
			}
		}
		$v = $this->regdate (array ($y, $m, $d));

		return array ($y, $m, $d);
	}
	// }}}

	// {{{ +-- public (string) human_year ($y)
	/**
	 * 연도를 human readable하게 표시
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 56 11}
	 *
	 * @access public
	 * @return string   AD/BC type의 연도
	 * @param int 연도
	 */
	public function human_year ($y) {
		if ( $y < 1 ) {
			$y = ($y * -1) + 1;
			$t = 'BC';
		} else
			$t = 'AD';

		return sprintf ('%s %d', $t, $y);
	}
	// }}}

	// {{{ +-- public (array) split_date ($date)
	/**
	 * YYYY-MM-DD 또는 array ((string) YYYY, (string) MM, (string) DD)
	 * 입력값을 * array ((int) $y, (int) $m, (int) $d)으로 변환
	 *
	 * @access public
	 * @return array array ((int) $y, (int) $m, (int) $d)
	 * @param array|string
	 *     - YYYY-MM-DD
	 *     - array ((string) YYYY, (string) MM, (stirng) DD)
	 */
	public function split_date ($date) {
		if ( is_array ($date) )
			$date = $this->regdate ($date);

		$minus = ($date[0] == '-') ? true : false;
		$date = $minus ? substr ($date, 1) : $date;

		$r = preg_split ('/-/', $date);
		if ( $minus )
			$r[0] *= -1;

		foreach ($r as $k => $v )
			$r[$k] = (int) $v;

		return $r;
	}
	// }}}

	// {{{ +-- private (string) regdate ($v)
	/**
	 * YYYY-MM-DD 형식의 날자를 반환
	 *
	 * @access private
	 * @return string   YYYY-MM-DD 형식으로 반환
	 * @param array     년월일 배열 - array ($year, $month, $day)
	 */
	private function regdate ($v) {
		list ($year, $month, $day) = $v;

		return sprintf (
			'%d-%s%d-%s%d',
			$year,
			($month < 10 ) ? '0' : '',
			(int) $month,
			($day < 10 ) ? '0' : '',
			(int) $day
		);
	}
	// }}}

	// {{{ +-- public (bool) is_leap ($y, $julian = false)
	/**
	 * 윤년 체크
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 68 14}
	 *
	 * @access public
	 * @return bool
	 * @param int 년도
	 * @param bool Julian 여부
	 * <p>
	 * 1582년 이전은 Julian calender로 판단하여 이 값이
	 * false라도 율리우스력으로 간주하여 판단한다. (sinse 1.0.1)
	 * </p>
	 */
	public function is_leap ($y, $julian = false) {
		// Julian의 윤년은 4로 나누어지면 된다.
		if ( $julian || $y < 1583 )
			return ($y % 4) ? false : true;

		if ( ($y % 400) == 0 )
			return true;

		if ( ($y % 4) == 0 && ($y % 100) != 0 )
			return true;

		return false;
	}
	// }}}

	// {{{ +-- public (bool) is_gregorian ($y, $m, $d = 1)
	/**
	 * 해당 날자가 gregorian 범위인지 체크
	 *
	 * @access public
	 * @return bool
	 * @param int 연도
	 * @param int 월
	 * @param int 일
	 */
	public function is_gregorian ($y, $m, $d = 1) {
		if ( (int) $m < 10 )
			$m = '0' . (int) $m;
		if ( (int) $d < 10 )
			$d = '0' . (int) $d;

		$chk = $y . $m . $d;

		if ( $chk < 15821015 )
			return false;

		return true;
	}
	// }}}

	// {{{ +-- private (string) gregorian2julian ($v)
	/**
	 * 그레고리력을 율리안력으로 변환
	 *
	 * @access private
	 * @return stdClass
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [fmt] => 2013-06-09          // YYYY-MM-DD 형식의 Julian 날자
	 *       [year] => 2013               // 연도
	 *       [month] => 6                 // 월
	 *       [day] => 9                   // 일
	 *       [week] => 화                 // 요일
	 *   )
	 *   </pre>
	 *
	 * @param array|int Gregorian 연월일 배열 or Julian date count
	 */
	private function gregorian2julian ($v) {
		if ( is_array ($v) ) {
			$d = $this->regdate ($v);
			list ($y, $m, $d) = $this->split_date ($d);

			$v = $this->cal2jd (array ($y, $m, $d));
		}

		if ( extension_loaded ('calendar') ) {
			$r = (object) cal_from_jd ($v, CAL_JULIAN);
			if ( $r->year < 0 )
				$r->year++;

			return (object) array (
				'fmt'   => $this->regdate (array ($r->year, $r->month, $r->day)),
				'year'  => $r->year,
				'month' => $r->month,
				'day'   => $r->day,
				'week'  => $r->dow
			);
		}

		if ( is_float ($v) ) {
			list ($Z, $F) = preg_split ('/\./', $v);
		} else {
			$Z = $v;
			$F = 0;
		}

		if ( $v < 2299161 )
			$A = $Z;
		else {
			$alpha = (int) ($Z - 1867216.25 / 36524.25);
			$A = $Z + 1 + $alpha - (int) ($alpha / 4);
		}

		$B = $A + 1524;
		$C = (int) (($B - 122.1) / 365.25);
		$D = (int) (365.25 * $C);
		$E = (int) ( ($B - $D) / 30.6001);

		$day = $B - $D - (int) (30.6001 * $E) + $F;
		$month = ($E < 14) ? $E - 1 : $E - 13;
		$year = $C - 4715;
		if ( $month > 2 )
			$year--;

		$week = ($v + 1.5) % 7;

		return (object) array (
			'fmt'   => $this->regdate (array ($year, $month, $day)),
			'year'  => $year,
			'month' => $month,
			'day'   => $day,
			'week'  => $week
		);
	}
	// }}}

	// {{{ +-- private mod ($x, $y)
	private function mod ($x, $y) {
		return ($x % $y + $y) % $y;
	}
	// }}}

	// {{{ +-- private (string) julian2gregorian ($v)
	/**
	 * 율리안력을 그레고리안역으로 변환
	 *
	 * @access private
	 * @return stdClass
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [fmt]   => 2013-06-09        // YYYY-MM-DD 형식의 Julian 날자
	 *       [year]  => 2013              // 연도
	 *       [month] => 6                 // 월
	 *       [day]   => 9                 // 일
	 *       [week]  => 화                // 요일
	 *   )
	 *   </pre>
	 *
	 * @param array|int Julian 연월일 배열 or Julian date count
	 */
	private function julian2gregorian ($jd, $pure = false) {
		if ( is_array ($jd) ) {
			list ($y, $m, $d) = $this->split_date ($jd);
			$jd = $this->cal2jd (array ($y, $m, $d), true);
		}

		if ( extension_loaded ('calendar') && $pure == false ) {
			$r = (object) cal_from_jd ($jd, CAL_GREGORIAN);
			if ( $r->year < 0 )
				$r->year++;

			return (object) array (
				'fmt'   => $this->regdate (array ($r->year, $r->month, $r->day)),
				'year'  => $r->year,
				'month' => $r->month,
				'day'   => $r->day,
				'week'  => $r->dow
			);
		}

		// https://en.wikipedia.org/wiki/Julian_day#Gregorian_calendar_from_Julian_day_number
		// 01-01-02 부터 이전은 맞지 않는다 --;
		#$f = (int) $jd + 1401;
		#$f = (int) ($f + (((4 * $jd + 274277) / 146097) * 3) / 4 - 38);
		#$e = 4 * $f + 3;
		#$g = (int) (($e % 1461) / 4);
		#$h = 5 * $g + 2;
		#$day = (int) (($h % 153) / 5 + 1);
		#$month = (int) ((($h / 153 + 2) % 12) + 1);
		#$year = (int) ($e / 1461 - 4716 + (12 + 2 - $month) / 12);

		// http://www.fourmilab.ch/documents/calendar/
		$wjd = floor ($jd -  0.5) + 0.5;
		# GREGORIAN_EPOCH 1721425.5
		$depoch = $wjd - 1721425.5;
		$quadricent = floor ($depoch / 146097);
		$dqc = $this->mod ($depoch, 146097);
		$cent = floor ($dqc / 36524);
		$dcent = $this->mod ($dqc, 36524);
		$quad = floor ($dcent / 1461);
		$dquad = $this->mod ($dcent, 1461);
		$yindex = floor ($dquad / 365);

		$year = ($quadricent * 400) + ($cent * 100) + ($quad * 4) + $yindex;
		if ( ! ($cent == 4 || $yindex == 4) )
			$year++;
	
		$yearday = $wjd - $this->cal2jd (array ($year, 1, 1));
		$leapadj = (($wjd < $this->cal2jd (array ($year, 3, 1)))
					? 0 : ($this->is_leap ($year) ? 1 : 2));
		$month = floor (((($yearday + $leapadj) * 12) + 373) / 367);
		$day = ceil ($wjd - $this->cal2jd (array ($year, $month, 1))) + 1;

		$week = ($jd + 1.5) % 7;

		return (object) array (
			'fmt'   => $this->regdate (array ($year, $month, $day)),
			'year'  => $year,
			'month' => $month,
			'day'   => $day,
			'week'  => $week
		);
	}
	// }}}

	// {{{ +-- private (int) cal2jd_pure ($v, $julian = false)
	/**
	 * Gregorian 날자를 Julian date로 변환 (by PURE PHP CODE)
	 *
	 * http://new.astronote.org/bbs/board.php?bo_table=prog&wr_id=29929
	 * 1. Y는 해당년도, M는 월(1월=1,2월=2), D는 해당 월의 날짜이다.
	 *    D는 시간값도 포함한 소수값으로 생각하자. 가령 3일 12시 UT라면
	 *    D=3.5이다.
	 * 2. M>2인 경우 Y,M은 변경하지 않는다. M = 1 또는 2인 경우 Y=Y-1,
	 *    M=M+12로 계산한다.
	 * 3. 그레고리력(Gregorian Calendar)의 경우 아래처럼 계산한다.
	 *    A = INT(Y/100), B = 2 – A + INT(A/4)
	 *    여기서 INT는 ()안에 들어간 값을 넘지않는 가장 큰 정수이다.
	 *    율리우스력(Julian Calendar)의 경우 B=0이다.
	 * 4. JD는 다음과 같이 계산된다.
	 *    JD = INT(365.25(Y+4716)) + INT(30.6001(M+1)) + D + B – 1524.5
	 *    여기서 30.6001은 정확히는 30.6을 써야한다. 하지만 컴퓨터 계산시
	 *    10.6이여 하는데 10.599999999 이런식으로 표현되는 경우가 발생하면
	 *    INT(10.6)과 INT(10.5999..)의 결과가 달라진다. 이 문제 대해 대처
	 *    하기 위해 30.6001을 사용한 것이다. 이러한 에러를 Round-off Error
	 *    라고 불린다.
	 *
	 * @access private
	 * @return int Julian date
	 * @param array 연월일 배열 : array ($y, $m, $d)
	 */
	private function cal2jd_pure ($v, $julian = false) {
		list ($y, $m, $d) = $v;

		if ( $m <= 2 ) {
			$y--;
			$m += 12;
		}

		$A = (int) ($y / 100);
		$B = $julian ? 0 : 2 - $A + (int) ($A / 4);
		$C = (int) (365.25 * ($y + 4716));
		$D = (int) (30.6001 * ($m + 1));
		return ceil ($C + $D + $d + $B - 1524.5);
	}
	// }}}

	// {{{ +-- private (int) cal2jd_ext ($v, $julian = false)
	/**
	 * Gregorian 날자를 Julian date로 변환 (by Calendar Extension)
	 *
	 * @access private
	 * @return int Julian date
	 * @param array 연월일 배열 : array ($y, $m, $d)
	 */
	private function cal2jd_ext ($v, $julian = false) {
		list ($y, $m, $d) = $v;

		$old = date_default_timezone_get ();
		date_default_timezone_set ('UTC');

		$func = $julian ? 'JulianToJD' : 'GregorianToJD';
		if ( $y < 1 )
			$y--;
		$r = $func ((int) $m, (int) $d, (int) $y);

		date_default_timezone_set ($old);
		return $r;
	}
	// }}}

	// {{{ +-- public (int) cal2jd ($v)
	/**
	 * Gregorian 날자를 Julian date로 변환
	 *
	 * @access public
	 * @return int Julian date
	 * @param array 연월일 배열 : array ($y, $m, $d)
	 */
	public function cal2jd ($v, $julian = false) {
		if ( extension_loaded ('calendar') )
			return $this->cal2jd_ext ($v, $julian);

		return $this->cal2jd_pure ($v, $julian);
	}
	// }}}

	// {{{ +-- private (string) toutc ($v)
	/**
	 * Localtime을 UTC로 변환
	 *
	 * @access private
	 * @return string YYYY-MM-DD-HH-II-SS
	 * @param string date format (YYYY-MM-DD HH:II:SS)
	 */
	private function toutc ($v) {
		$t = strtotime ($v);
		$old = date_default_timezone_get ();
		date_default_timezone_set ('UTC');
		$r = date ('Y-m-d-H-i-s', $t);
		date_default_timezone_set ($old);

		return $r;
	}
	// }}}

	// {{{ +-- private (int) to_utc_julian ($v)
	/**
	 * 합삭/망 절기 시간을 UTC로 변환 후, Julian date로 표현
	 *
	 * @access private
	 * @return int
	 * @param string data format (YYYY-MM-DD HH:II:SS)
	 */
	private function to_utc_julian ($v) {
		$buf = $this->toutc ($v);
		list ($y, $m, $d, $h, $i, $s) = $this->split_date ($buf);

		$chk = $y . $m . $d;

		//$julian = ( $chk < 18451015 ) ? $true : false;
		$j = $this->cal2jd (array ($y, $m, $d), $julian);

		if ( ($h - 12) < 0 ) {
			$h = 11 - $h;
			$i = 60 - $i;
			$buf = (($h * 3600 + $i * 60) / 86400) * -1;
		} else
			$buf = (($h - 12) * 3600 + $i * 60) / 86400;

		return $j + $buf;
	}
	// }}}

	// {{{ +-- private (array) fix_calendar ($y, $m, $d)
	/**
	 * 1582년 10월 15일 이전의 date를 julian calendar로 변환
	 *
	 * @access private
	 * @return array 년월일 배열 (array ($y, $m, $d))
	 * @param int year
	 * @param int month
	 * @param int day
	 */
	private function fix_calendar ($y, $m, $d) {
		if ( $m < 10 )
			$m = '0' . $m;
		if ( $d < 10 )
			$d = '0' . $d;

		# 15821005 ~ 15821014 까지는 gregorian calendar에서는 존재
		# 하지 않는다. 그러므로, 이 기간의 날자는 julian calendar
		# 와 매치되는 날자로 변경한다. (10씩 빼준다.
		$chk = $y . $m . $d;
		if ( $chk > 15821004 && $chk < 15821015 ) {
			$julian = $this->cal2jd (array ($y, (int) $m, (int) $d));
			$julian -= 10;
			$r = $this->julian2gregorian ($julian);
			list ($y, $m, $d) = array ($r->year, $r->month, $r->day);
		}

		# 15821005 보다 과거의 날자는 gregorian calendar가 없다.
		# 그러므로 julian calendar로 표현한다.
		if ( $this->is_gregorian ($y, (int) $m, (int) $d) === false ) {
			$r = $this->julian2gregorian (array ($y, (int) $m, (int) $d));
			list ($y, $m, $d) = array ($r->year, $r->month, $r->day);
		}

		return array ($y, $m, $d);
	}
	// }}}

	// {{{ +-- public (object) tolunar ($v = null)
	/**
	 * 양력 날자를 음력으로 변환
	 *
	 * 진짜 만세력은 1582/10/15(Gregorian calendar의 시작) 이전의 날자
	 * 역시 Gregorian으로 표기를 한다. 그러므로 Calendar의 오류로 보일
	 * 수도 있다. (실제로는 계산상의 오류는 없다고 봐야 한다.)
	 *
	 * 이런 부분을 보정하기 위하여, tolunar method는 1582/10/04 까지의
	 * 날자는 julian calendar로 변환을 하여 음력날자를 구한다. 이로 인
	 * 하여 1582/10/15 이전의 음력 날자는 original 진짜 만세력과 다른
	 * 값을 가지게 된다.)
	 *
	 * 이렇게 표현될 경우, 천문우주 지식정보의 값과 비슷하게 나올 수는
	 * 있으나, 평달/큰달 계산은 진짜 만세력의 것을 이용하므로 오차는
	 * 발생할 수 있다.
	 * http://astro.kasi.re.kr/Life/ConvertSolarLunarForm.aspx?MenuID=115
	 *
	 * 2.0 부터는 이러한 오차를 줄이기 위하여 oops\KASI_Lunar package가
	 * 설치되어 있을 경우, 1392-02-05 ~ 2050-12-31 기간에 대해서는
	 * 천문과학연구원의 데이터를 이용할 수 있도록 지원한다.
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 83 35}
	 *
	 * @access public
	 * @return stdClass    음력 날자 정보 반환
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [fmt] => 2013-06-09          // YYYY-MM-DD 형식의 음력 날자
	 *       [dangi] => 4346              // 단기
	 *       [hyear] => AD 2013           // AD/BC 형식의 연도
	 *       [year] => 2013               // 연도
	 *       [month] => 6                 // 월
	 *       [day] => 9                   // 일
	 *       [leap] =>                    // 음력 윤달 여부
	 *       [largemonth] => 1            // 평달/큰달 여부
	 *       [week] => 화                 // 요일
	 *       [hweek] => 火                // 한자 요일
	 *       [unixstamp] => 1373900400    // unixstamp (양력 날자)
	 *       [ganji] => 계사              // 세차(년)
	 *       [hganji] => 癸巳             // 한자 세차
	 *       [gan] => 계                  // 세차 10간
	 *       [hgan] => 癸                 // 세차 한자 10간
	 *       [ji] => 사                   // 세차 12지
	 *       [hji] => 巳                  // 세차 한자 12지
	 *       [ddi] => 뱀                  // 띠
	 *   )
	 *   </pre>
	 *
	 * @param int|string   날자형식
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 *   - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
	 */
	public function tolunar ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);
		#printf ("** %4s.%2s.%2s ... ", $y, $m, $d);

		$kasi = false;
		$cdate = preg_replace ('/-/', '', $v);
		// 1391-02-05 ~ 2050-12-31 까지는 KASI data로 처리를 한다.
		if ( $cdate > 13910204 && $cdate < 20510101 ) {
			if ( class_exists ('oops\KASI\Lunar') ) {
				$kasi = true;
				if ( $this->KASI == null )
					$this->KASI = new \oops\KASI\Lunar;
				$r = $this->KASI->tolunar ($v);
				if ( $r === false )
					return false;

				$year   = $r->year;
				$month  = $r->month;
				$day    = $r->day;
				$leap   = $r->leap;
				$lmonth = $r->lmoon;
				$w      = $r->week;

				unset ($r);
				$r = array ($year, $month, $day);
			}
		}	

		if ( $kasi === false ) {
			list ($y, $m, $d) = $this->fix_calendar ($y, $m, $d);
			#printf ("%4s.%2s.%2s<br>", $y, $m, $d);

			$r = $this->solartolunar ($y, $m, $d);
			list ($year, $month, $day, $leap, $lmonth) = $r;

			$w = $this->getweekday ($y, $m, $d);
		}

		$k1 = ($year + 6) % 10;
		$k2 = ($year + 8) % 12;

		if ( $k1 < 0 ) $k1 += 10;
		if ( $k2 < 0 ) $k2 += 12;

		return (object) array (
			'fmt'        => $this->regdate ($r),
			'dangi'      => $year + 2333,
			'hyear'      => $this->human_year ($year),
			'year'       => $year,
			'month'      => $month,
			'day'        => $day,
			'leap'       => $leap,
			'largemonth' => $lmonth,
			'week'       => $this->week[$w],
			'hweek'      => $this->hweek[$w],
			'unixstamp'  => mktime (0, 0, 0, $m, $d, $y),
			'ganji'      => $this->gan[$k1] . $this->ji[$k2],
			'hganji'     => $this->hgan[$k1] . $this->hji[$k2],
			'gan'        => $this->gan[$k1],
			'hgan'       => $this->hgan[$k1],
			'ji'         => $this->ji[$k2],
			'hji'        => $this->hji[$k2],
			'ddi'        => $this->ddi[$k2]
		);
	}
	// }}}

	// {{{ +-- public (object) tosolar ($v = null, $leap = false)
	/**
	 * 음력 날자를 양력으로 변환.
	 *
	 * 구하는 음력월이 윤달인지 여부를 알 수 없을 경우, tosolar method
	 * 를 실행하여 얻은 양력 날자를 다시 tolunar로 변환하여 비교하여
	 * 동일하지 않다면, 윤달 파라미터 값을 주고 다시 구해야 한다!
	 *
	 * 진짜 만세력은 Gregorian으로 표기를 하기 때문에, 양력 1582-10-15
	 * 이전의 경우에는 return object의 julian member 값으로 비교를 해야
	 * 한다.
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 119 42}
	 *
	 * @access public
	 * @return stdClass    양력 날자 정보 object 반환
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [jd] => 2456527             // Julian Date Count
	 *       [fmt] => 2013-07-16         // YYYY-MM-DD 형식의 양력 날자 (15821015 이전은 율리우스력)
	 *       [gregory] => 2013-07-16     // Gregory Calendar
	 *       [julian] => 2013-08-09      // Julian Calendar
	 *       [dangi] => 4346             // 단기 (양력)
	 *       [hyear] => AD 2013          // AD/BC 형식 년도
	 *       [year] => 2013              // 양력 연도
	 *       [month] => 7                // 월
	 *       [day] => 16                 // 일
	 *       [week] => 화                // 요일
	 *       [hweek] => 火               // 한자 요일
	 *       [unixstamp] => 1373900400   // unixstamp (양력)
	 *       [ganji] => 계사             // 세차
	 *       [hganji] => 癸巳            // 세차 한자
	 *       [gan] => 계                 // 세차 10간
	 *       [hgan] => 癸                // 세차 한자 10간
	 *       [ji] => 사                  // 세차 12지
	 *       [hji] => 巳                 // 세차 한자 12지
	 *       [ddi] => 뱀                 // 띠
	 *   )
	 *   </pre>
	 *
	 * @param int|string 날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 *
	 * @param bool 윤달여부
	 */
	public function tosolar ($v = null, $leap = false) {
		list ($y, $m, $d) = $this->toargs ($v, true);

		$kasi = false;
		$cdate = preg_replace ('/-/', '', $v);
		// 1391-01-01 ~ 2050-12-31 까지는 KASI data로 처리를 한다.
		if ( $cdate > 13910101 && $cdate < 20501119 ) {
			if ( class_exists ('oops\KASI\Lunar') ) {
				$kasi = true;
				if ( $this->KASI == null )
					$this->KASI = new \oops\KASI\Lunar;
				$r = $this->KASI->tosolar ($v, $leap);
				if ( $r === false )
					return false;

				$year  = $r->year;
				$month = $r->month;
				$day   = $r->day;
				$w     = $r->week;

				$jdate = $r->jd; 
				$fmt   = $r->fmt;

				if ( $r->jd > 2299160) {
					$j    = $this->gregorian2julian ($r->jd);
					$jfmt = $j->fmt;
					$gfmt = $r->fmt;
				} else {
					$jfmt = $r->fmt;
					$g    = $this->julian2gregorian ($r->jd);
					$gfmt = $g->fmt;
				}
			}
		}

		if ( $kasi === false ) {
			$r = $this->lunartosolar ($y, $m, $d, $leap);
			list ($year, $month, $day) = $r;

			$w = $this->getweekday ($year, $month, $day);

			$jdate = $this->cal2jd ($r);
			//$julian = $this->gregorian2julian ($r);
			$julian = $this->gregorian2julian ($jdate);
			$jfmt   = $julian->fmt;
			$gfmt   = $this->regdate ($r);
			$fmt = ($jdate < 2299161) ? $jfmt : $gfmt;
		}

		$k1 = ($y + 6) % 10;
		$k2 = ($y + 8) % 12;

		if ( $k1 < 0 ) $k1 += 10;
		if ( $k2 < 0 ) $k2 += 12;

		return (object) array (
			'jd'         => $jdate,
			'fmt'        => $fmt,
			'gregory'    => $gfmt,
			'julian'     => $jfmt,
			'dangi'      => $year + 2333,
			'hyear'      => $this->human_year ($year),
			'year'       => $year,
			'month'      => $month,
			'day'        => $day,
			'week'       => $this->week[$w],
			'hweek'      => $this->hweek[$w],
			'unixstamp'  => mktime (0, 0, 0, $month, $day, $year),
			'ganji'      => $this->gan[$k1] . $this->ji[$k2],
			'hganji'     => $this->hgan[$k1] . $this->hji[$k2],
			'gan'        => $this->gan[$k1],
			'hgan'       => $this->hgan[$k1],
			'ji'         => $this->ji[$k2],
			'hji'        => $this->hji[$k2],
			'ddi'        => $this->ddi[$k2]
		);
	}
	// }}}

	// {{{ +-- public (object) dayfortune ($v = null)
	/**
	 * 세차(년)/월건(월)/일진(일) 데이터를 구한다.
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 163 56}
	 *
	 * @access public
	 * @return stdClass
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [data] => stdClass Object
	 *           (
	 *                [y] => 29           // 세차 index
	 *                [m] => 55           // 월건 index
	 *                [d] => 19           // 일진 index
	 *           )
	 *
	 *       [year] => 계사               // 세차 값
	 *       [month] => 기미              // 월건 값
	 *       [day] => 계미                // 일진 값
	 *       [hyear] => 癸巳              // 한자 세차 값
	 *       [hmonth] => 己未             // 한자 월건 값
	 *       [hday] => 癸未               // 한자 일진 값
	 *   )
	 *   </pre>
	 *
	 * @param int|string 날자형식
	 *
	 *    - unixstmap (1970년 12월 15일 이후부터 가능)
	 *    - Ymd or Y-m-d
	 *    - null data (현재 시간)
	 *    - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
	 */
	public function dayfortune ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);
		list ($y, $m, $d) = $this->fix_calendar ($y, $m, $d);

		list ($so24, $year, $month, $day, $hour)
			= $this->sydtoso24yd ($y, $m, $d, 1, 0);

		return (object) array (
			'data' => (object) array ('y' => $year, 'm' => $month, 'd' => $day),
			'year' => $this->ganji[$year],
			'month' => $this->ganji[$month],
			'day' => $this->ganji[$day],
			'hyear' => $this->hganji[$year],
			'hmonth' => $this->hganji[$month],
			'hday' => $this->hganji[$day],
		);
	}
	// }}}

	// {{{ +-- public (object) s28day ($v = null)
	/**
	 * 특정일의 28수를 구한다.
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 221 35}
	 *
	 * @access public
	 * @return stdClass
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [data] => 5    // 28수 index
	 *       [k] => 미      // 한글 28수 값
	 *       [h] => 尾      // 한자 28수 값
	 *   )
	 *   </pre>
	 *
	 * @param int|string   날자형식
	 *
	 *    - unixstmap (1970년 12월 15일 이후부터 가능)
	 *    - Ymd or Y-m-d
	 *    - null data (현재 시간)
	 *    - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
	 *    - Recursion s28day return value:<br>
	 *      loop에서 s28day method를 반복해서 호출할 경우 return value를 이용할
	 *      경우, return value의 index값을 이용하여 계산을 하지 않아 속도가 빠름.
	 */
	public function s28day ($v = null) {
		if ( is_object ($v) ) {
			$r = $v->data + 1;
			if ( $r >= 28 )
				$r %= 28;

			goto return_data;
		}

		list ($y, $m, $d) = $this->toargs ($v);
		list ($y, $m, $d) = $this->fix_calendar ($y, $m, $d);
		$r = $this->get28sday ($y, $m, $d);

		return_data:

		return (object) array (
			'data' => $r,
			'k' => $this->s28days_hangul[$r],
			'h' => $this->s28days[$r]
		);
	}
	// }}}

	// {{{ +-- public (array) seasondate ($v = null)
	/**
	 * 해당 양력일에 대한 음력 월의 절기 시간 구하기
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 257 52}
	 *
	 * @access public
	 * @return stdClass   현달 초입/중기와 다음달 초입 데이터 반환
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [center] => stdClass Object      // 이번달 초입 데이터
	 *           (
	 *               [name] => 소서              // 절기 이름
	 *               [hname] => 小暑             // 절기 한자 이름
	 *               [hyear] => AD 2013          // AD/BC 형식 연도
	 *               [year] => 2013              // 초입 연도
	 *               [month] => 7                // 초입 월
	 *               [day] => 7                  // 초입 일
	 *               [hour] => 7                 // 초입 시간
	 *               [min] => 49                 // 초입 분
	 *               [julian] => 2456480.4506944 // Julian date (UTC)
	 *           )
	 *
	 *       [ccenter] => stdClass Object     // 이번달 중기 데이터
	 *           (
	 *               [name] => 대서              // 절기 이름
	 *               [hname] => 大暑             // 절기 한자 이름
	 *               [hyear] => AD 2013          // AD/BC 형식 연도
	 *               [year] => 2013              // 중기 연도
	 *               [month] => 7                // 중기 월
	 *               [day] => 23                 // 중기 일
	 *               [hour] => 1                 // 중기 시간
	 *               [min] => 11                 // 중기 분
	 *               [julian] => 2456496.1743056 // Julian date (UTC)
	 *           )
	 *
	 *       [nenter] => stdClass Object      // 다음달 초입 데이터
	 *           (
	 *               [name] => 입추              // 절기 이름
	 *               [hname] => 立秋             // 절기 한자 이름
	 *               [hyear] => AD 2013          // AD/BC 형식 연도
	 *               [year] => 2013              // 초입 연도
	 *               [month] => 8                // 초입 월
	 *               [day] => 7                  // 초입 일
	 *               [hour] => 17                // 초입 시간
	 *               [min] => 36                 // 초입 분
	 *               [julian] => 2456511.8583333 // Julian date (UTC)
	 *           )
	 *   )
	 *   </pre>
	 *
	 * @param int|string   날자형식
	 *
	 *  - unixstmap (1970년 12월 15일 이후부터 가능)
	 *  - Ymd or Y-m-d
	 *  - null data (현재 시간
	 *  - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
	 */
	public function seasondate ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);
		list ($y, $m, $d) = $this->fix_calendar ($y, $m, $d);

		list (
			$inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			$midname, $midyear, $midmonth, $midday, $midhour, $midmin,
			$outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin
		) = $this->solortoso24 ($y, $m, 20, 1, 0);

		$j_ce = $this->to_utc_julian (
			sprintf (
				'%s %s:%s:00',
				$this->regdate (array ($ingiyear, $ingimonth, $ingiday)),
				$ingihour < 10 ? '0' . $ingihour : $ingihour,
				$ingimin < 10 ? '0' . $ingimin : $ingimin
			)
		);

		// 1852-10-15 이전이면 julian으로 변경
		if ( $this->is_gregorian ($ingiyear, $ingimonth, $ingiday) === false ) {
			$r = $this->gregorian2julian (array ($ingiyear, $ingimonth, $ingiday));
			$ingiyear  = $r->year;
			$ingimonth = $r->month;
			$ingiday   = $r->day;
		}

		$j_cc = $this->to_utc_julian (
			sprintf (
				'%s %s:%s:00',
				$this->regdate (array ($midyear, $midmonth, $midday)),
				$midhour < 10 ? '0' . $midhour : $midhour,
				$midmin < 10 ? '0' . $midmin : $midmin
			)
		);

		// 1852-10-15 이전이면 julian으로 변경
		if ( $this->is_gregorian ($midyear, $midmonth, $midday) === false ) {
			$r = $this->gregorian2julian (array ($midyear, $midmonth, $midday));
			$midyear  = $r->year;
			$midmonth = $r->month;
			$midday   = $r->day;
		}

		$j_ne = $this->to_utc_julian (
			sprintf (
				'%s %s:%s:00',
				$this->regdate (array ($outgiyear, $outgimonth, $outgiday)),
				$outgihour < 10 ? '0' . $outgihour : $outgihour,
				$outgimin < 10 ? '0' . $outgimin : $outgimin
			)
		);

		// 1852-10-15 이전이면 julian으로 변경
		if ( $this->is_gregorian ($outgiyear, $outgimonth, $outgiday) === false ) {
			$r = $this->gregorian2julian (array ($outgiyear, $outgimonth, $outgiday));
			$outgiyear  = $r->year;
			$outgimonth = $r->month;
			$outgiday   = $r->day;
		}

		return (object) array (
			'center'  => (object) array (
				'name'   => $this->month_st[$inginame],
				'hname'  => $this->hmonth_st[$inginame],
				'hyear'  => $this->human_year ($ingiyear),
				'year'   => $ingiyear,
				'month'  => $ingimonth,
				'day'    => $ingiday,
				'hour'   => $ingihour,
				'min'    => $ingimin,
				'julian' => $j_ce
			),
			'ccenter' => (object) array (
				'name'   => $this->month_st[$midname],
				'hname'  => $this->hmonth_st[$midname],
				'hyear'  => $this->human_year ($midyear),
				'year'   => $midyear,
				'month'  => $midmonth,
				'day'    => $midday,
				'hour'   => $midhour,
				'min'    => $midmin,
				'julian' => $j_cc
			),
			'nenter'  => (object) array (
				'name'   => $this->month_st[$outginame],
				'hname'  => $this->hmonth_st[$outginame],
				'hyear'  => $this->human_year ($outgiyear),
				'year'   => $outgiyear,
				'month'  => $outgimonth,
				'day'    => $outgiday,
				'hour'   => $outgihour,
				'min'    => $outgimin,
				'julian' => $j_ne
			)
		);
	}
	// }}}

	// {{{ +-- public (object) moonstatus ($v = null)
	/**
	 * 양력일에 대한 음력월 합삭/망 데이터 구하기
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 311 56}
	 *
	 * @access public
	 * @return stdClass	합삭/망 object
	 *
	 *   <pre>
	 *   stdClass Object
	 *   (
	 *       [new] => stdClass Object      // 합삭 (New Moon) 데이터
	 *           (
	 *               [hyear] => AD 2013          // 합삭 AD/BC 형식 연도
	 *               [year] => 2013              // 합삭 연도
	 *               [month] => 7                // 합삭 월
	 *               [day] => 8                  // 합삭 일
	 *               [hour] => 16                // 합삭 시간
	 *               [min] => 15                 // 합삭 분
	 *               [julian] => 2456481.8020833 // Julian date (UTC)
	 *           )
	 *
	 *       [full] => stdClass Object     // 망 (Full Moon) 데이터
	 *           (
	 *               [hyear] => AD 2013          // 망 AD/BC 형식 연도
	 *               [year] => 2013              // 망 연도
	 *               [month] => 7                // 망 월
	 *               [day] => 23                 // 망 일
	 *               [hour] => 2                 // 망 시간
	 *               [min] => 59                 // 망 분
	 *               [julian] => 2456496.2493056 // Julian date (UTC)
	 *           )
	 *   )
	 *   </pre>
	 *
	 * @param int|string   날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 *   - 1582년 10월 15일 이전의 날자는 율리우스력의 날자로 취급함.
	 */
	public function moonstatus ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);
		list ($y, $m, $d) = $this->fix_calendar ($y, $m, $d);

		list (
			$y1, $mo1, $d1, $h1, $mi1,
			$ym, $mom, $dm, $hm, $mim,
			$y2, $m2, $d2, $h2, $mi2
		) = $this->getlunarfirst ($y, $m, $d);

		$j_new = $this->to_utc_julian (
			sprintf (
				'%s %s:%s:00',
				$this->regdate (array ($y1, $mo1, $d1)),
				$h1 < 10 ? '0' . $h1 : $h1,
				$mi1 < 10 ? '0' . $mi1 : $mi1
			)
		);

		// 1852-10-15 이전이면 julian으로 변경
		if ( $this->is_gregorian ($y1, $mo1, $d1) === false ) {
			$r = $this->gregorian2julian (array ($y1, $mo1, $d1));
			$y1 = $r->year;
			$mo1 = $r->month;
			$d1 = $r->day;
		}

		$j_full = $this->to_utc_julian (
			sprintf (
				'%s %s:%s:00',
				$this->regdate (array ($ym, $mom, $dm)),
				$hm < 10 ? '0' . $hm : $hm,
				$mim < 10 ? '0' . $mim : $mim
			)
		);

		// 1852-10-15 이전이면 julian으로 변경
		if ( $this->is_gregorian ($ym, $mom, $dm) === false ) {
			$r = $this->gregorian2julian (array ($ym, $mom, $dm));
			$ym = $r->year;
			$mom = $r->month;
			$dm = $r->day;
		}

		return (object) array (
			'new' => (object) array (
				'hyear'  => $this->human_year ($y1),
				'year'   => $y1,
				'month'  => $mo1,
				'day'    => $d1,
				'hour'   => $h1,
				'min'    => $mi1,
				'julian' => $j_new
			),   // 합삭 (New Moon)
			'full' => (object) array (
				'hyear'  => $this->human_year ($ym),
				'year'   => $ym,
				'month'  => $mom,
				'day'    => $dm,
				'hour'   => $hm,
				'min'    => $mim,
				'julian' => $j_full
			)    // 망 (Full Moon)
		);
	}
	// }}}

	// {{{ +-- public (string) ganji_ref ($no, $mode = false)
	/**
	 * dayfortune method의 ganji index 반환값을 이용하여, ganji
	 * 값을 구함
	 *
	 * 예제:
	 * {@example pear_Lunar/tests/sample.php 163 56}
	 *
	 * @access public
	 * @return string
	 * @param int ganji index number
	 * @param bool 출력 모드 (false => 한글, true => 한자)
	 */
	public function ganji_ref ($no, $mode = false) {
		if ( $no > 59 )
			$no -= 60;

		$m = $mode ? 'hganji' : 'ganji';
		return $this->{$m}[$no];
	}
	// }}}

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim: set filetype=php noet sw=4 ts=4 fdm=marker:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
?>
