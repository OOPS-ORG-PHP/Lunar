<?php
/**
 * Project: Lunar :: 양력/음력 변환 클래스<br>
 * File:    Lunar.php
 *
 * 이 패키지는 양력/음력간의 변환을 제공하는 API로, 고영창님의 '진짜만세력'
 * 0.92 버전을 PHP Class화 한 후 front end API를 추가한 것이다.
 *
 * 이 변환 API는 -2085(BC 2086년) 부터 2298년까지 유효하다.
 *
 * @category    Calendar
 * @package     Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 1997-2013 OOPS.org
 * @license     http://afnmp3.homeip.net:81/~kohyc/calendar/index.cgi
 * @version     SVN: $Id$
 * @link        http://pear.oops.org/package/Lunar
 * @since       File available since release 0.0.1
 * @example     pear_Lunar/tests/test.php Sample code
 * @filesource
 */

/**
 * import Lunar API
 */
require_once 'Lunar/Lunar_API.php';

/**
 * 양력/음력 변환 클래스
 *
 * 이 패키지는 양력/음력간의 변환을 제공하는 API로, 고영창님의 '진짜만세력'
 * 0.92 버전을 PHP Class화 한 후 front end API를 추가한 것이다.
 *
 * 이 변환 API는 -2085(BC 2086년) 부터 2298년까지 유효하다.
 *
 * @package     Lunar
 */
Class Lunar extends Lunar_API {

	// {{{ +-- public (array) toargs ($v)
	/**
	 * @access public
	 * @return array
	 * @param string|int 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function toargs (&$v) {
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
					throw new myException ('Invalid Date Format', E_USER_WARNING);
					return false;
				}
			}

			if ( $y > 1969 && $y < 2038 ) {
				$fixed = mktime (0, 0, 0, $m, $d, $y);
				$y = (int) date ('Y', $fixed);
				$m = (int) date ('m', $fixed);
				$d = (int) date ('d', $fixed);
			} else {
				if ( $m > 12 || $d > 31 ) {
					throw new myException ('Invalid Date Format', E_USER_WARNING);
					return false;
				}
			}
		}
		$v = sprintf ('%d-%d-%d', $y, $m, $d);

		return array ($y, $m, $d);
	}
	// }}}

	// {{{ +-- private (string) regdate ($v)
	/**
	 * YYYY-MM-DD 형식의 날자를 반환
	 *
	 * @access private
	 * @return string
	 * @param array
	 */
	private function regdate ($v) {
		list ($year, $month, $day) = $v;

		return sprintf (
			'%d-%s%d-%s%d',
			$year,
			($month < 10 ) ? '0' : '',
			$month,
			($day < 10 ) ? '0' : '',
			$day
		);
	}
	// }}}

	// {{{ +-- public (object) tolunar ($v = null)
	/**
	 * 양력 날자를 음력으로 변환
	 *
	 * @access public
	 * @return object
	 *    <ul>
	 *        <li>date => YYYY-MM-DD 형식의 음력 날자</li>
	 *        <li>dangi => 단기</li>
	 *        <li>year => 년도</li>
	 *        <li>month => 월</li>
	 *        <li>day => 일</li>
	 *        <li>moonyoon => 윤년 여부</li>
	 *        <li>largemonth => 평달/큰달 여부</li>
	 *        <li>week => 요일</li>
	 *        <li>hweek => 요일 (한자)</li>
	 *        <li>unixstamp => unixstamp (양력)</li>
	 *        <li>ganji => 간지</li>
	 *        <li>hganji => 간지 (한자)</li>
	 *        <li>gan => 10간</li>
	 *        <li>hgan => 10간 (한자)</li>
	 *        <li>ji => 12지</li>
	 *        <li>hji => 12지 (한자)</li>
	 *        <li>ddi => 띠</li>
	 *    </ul>
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function tolunar ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);

		$r = $this->solartolunar ($y, $m, $d);
		list ($year, $month, $day, $myoon, $lmonth) = $r;

		$w = $this->getweekday ($y, $m, $d);

		$k1 = ($year + 6) % 10;
		$k2 = ($year + 8) % 12;

		return (object) array (
			'date'       => $this->regdate ($r),
			'dangi'      => $year + 2333,
			'year'       => $year,
			'month'      => $month,
			'day'        => $day,
			'moonyoon'   => $myoon,
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

	// {{{ +-- public (object) tosolar ($v = null, $yoon = false)
	/**
	 * 음력 날자를 양력으로 변환
	 *
	 * @access public
	 * @return object
	 *    <ul>
	 *        <li>date => YYYY-MM-DD 형식의 양력 날자</li>
	 *        <li>dangi => 단기</li>
	 *        <li>year => 년도</li>
	 *        <li>month => 월</li>
	 *        <li>day => 일</li>
	 *        <li>week => 요일</li>
	 *        <li>hweek => 요일 (한자)</li>
	 *        <li>unixstamp => unixstamp (양력)</li>
	 *        <li>ganji => 간지</li>
	 *        <li>hganji => 간지 (한자)</li>
	 *        <li>gan => 10간</li>
	 *        <li>hgan => 10간 (한자)</li>
	 *        <li>ji => 12지</li>
	 *        <li>hji => 12지 (한자)</li>
	 *        <li>ddi => 띠</li>
	 *    </ul>
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 * @param bool 윤달여부
	 */
	public function tosolar ($v = null, $yoon = false) {
		list ($y, $m, $d) = $this->toargs ($v);

		$r = $this->lunartosolar ($y, $m, $d, $yoon);
		list ($year, $month, $day) = $r;

		$w = $this->getweekday ($year, $month, $day);

		$k1 = ($y + 6) % 10;
		$k2 = ($y + 8) % 12;

		return (object) array (
			'date'       => $this->regdate ($r),
			'dangi'      => $year + 2333,
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
	 * 일진 데이터를 구한다.
	 *
	 * @access public
	 * @return object
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function dayfortune ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);

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
	 * @access public
	 * @return object
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function s28day ($v = null) {
		if ( is_object ($v) ) {
			$r = $v->data + 1;
			if ( $r >= 28 )
				$r %= 28;

			goto return_data;
		}

		list ($y, $m, $d) = $this->toargs ($v);
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
	 * 절기 시간 구하기
	 *
	 * @access public
	 * @return array
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function seasondate ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);

		list (
			$inginame, $ingiyear, $ingimonth, $ingiday, $ingihour, $ingimin,
			$midname, $midyear, $midmonth, $midday, $midhour, $midmin,
			$outginame, $outgiyear, $outgimonth, $outgiday, $outgihour, $outgimin
		) = $this->solortoso24 ($y, $m, 20, 1, 0);

		return (object) array (
			'center'  => (object) array (
				'name'  => $this->month_st[$inginame],
				'hname'  => $this->hmonth_st[$inginame],
				'year'  => $ingiyear,
				'month' => $ingimonth,
				'day'   => $ingiday,
				'hour'  => $ingihour,
				'min'   => $ingimin
			),
			'ccenter' => (object) array (
				'name'  => $this->month_st[$midname],
				'hname'  => $this->hmonth_st[$midname],
				'year'  => $midyear,
				'month' => $midmonth,
				'day'   => $midday,
				'hour'  => $midhour,
				'min'   => $midmin
			),
			'nenter'  => (object) array (
				'name'  => $this->month_st[$outginame],
				'hname'  => $this->hmonth_st[$outginame],
				'year'  => $outgiyear,
				'month' => $outgimonth,
				'day'   => $outgiday,
				'hour'  => $outgihour,
				'min'   => $outgimin
			)
		);
	}
	// }}}

	// {{{ +-- public (object) moonstatus ($v = null)
	/**
	 * 합삭/망 데이터 구하기
	 *
	 * @access public
	 * @return object
	 * @param int|string 날자형식
	 *    <ul>
	 *        <li>unixstmap (1970년 12월 15일 이후부터 가능)</li>
	 *        <li>Ymd or Y-m-d</li>
	 *        <li>null data (현재 시간<li>
	 *    </ul>
	 */
	public function moonstatus ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);

		list (
			$y1, $mo1, $d1, $h1, $mi1,
			$ym, $mom, $dm, $hm, $mim,
			$y2, $m2, $d2, $h2, $mi2
		) = $this->getlunarfirst ($y, $m, $d);

		return (object) array (
			'new' => (object) array (
				'year' => $y1, 'month' => $mo1, 'day' => $d1, 'hour' => $h1, 'min' => $mi1
			),   // 합삭 (New Moon)
			'full' => (object) array (
				'year' => $ym, 'month' => $mom, 'day' => $dm, 'hour' => $hm, 'min' => $mim
			)    // 망 (Full Moon)
		);
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
