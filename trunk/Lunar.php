<?php
/**
 * Project: Lunar :: 양력/음력 변환 클래스<br>
 * File:    Lunar.php
 *
 * 이 패키지는 양력/음력간의 변환을 제공하는 API로, 고영창님의 '진짜만세력'
 * 0.92 버전을 PHP Class화 한 후 front end API를 추가한 것이다.
 *
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -9999-01-01 ~ 9999-12-31
 *     + API 날자 입력 형식떄문에 연도를 4자리로 제한. 아마 64bit 계산이
 *       가능한 지점까지 가능할 듯..
 * </pre>
 *
 * 계산 처리 시간상, 과거 2000년전과 미래 100년후의 시간은 왠만하면 웹에서는
 * 사용하는 것을 권장하지 않음!
 *
 * 주의!
 *
 * 이 API를 사용하기 전에 '진짜 만세력'을 검색하여 '진짜 만세력'의 특징을
 * 잘 알고 사용하기 바란다. '진짜 만세력'은 계산에 의한 값이기 때문에 DB화
 * 된 만세력과는 약간의 차이가 발생할 수 있다!
 *
 * @category    Calendar
 * @package     Lunar
 * @author      JoungKyun.Kim <http://oops.org>
 * @copyright   (c) 1997-2013 OOPS.org
 * @license     http://afnmp3.homeip.net/~kohyc/calendar/index.cgi
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
 * 이 변환 API의 유효기간은 다음과 같다.
 *
 * <pre>
 *   * 32bit
 *     + -2087-02-09(음력 -2087-01-01) ~ 6078-01-29(음 6077-12-29)
 *     + -2087-07-05(음력 -2087-05-29) 이전은 계산이 무지 느려짐..
 *
 *   * 64bit
 *     + -9999-01-01 ~ 9999-12-31
 *     + API 날자 입력 형식떄문에 연도를 4자리로 제한. 아마 64bit 계산이
 *       가능한 지점까지 가능할 듯..
 * </pre>
 *
 * 계산 처리 시간상, 과거 2000년전과 미래 100년후의 시간은 왠만하면 웹에서는
 * 사용하는 것을 권장하지 않음!
 *
 * @package     Lunar
 */
Class Lunar extends Lunar_API {
	// {{{ +-- public (array) toargs ($v)
	/**
	 * @access public
	 * @return array
	 * @param string|int 날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
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
					throw new Exception ('Invalid Date Format', E_USER_WARNING);
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
					throw new Exception ('Invalid Date Format', E_USER_WARNING);
					return false;
				}
			}
		}
		$v = sprintf ('%d-%d-%d', $y, $m, $d);

		return array ($y, $m, $d);
	}
	// }}}

	// {{{ +-- public (string) human_year ($y)
	/**
	 * 연도를 human readable하게 표시
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

	// {{{ +-- public (bool) is_yoon ($y)
	/**
	 * 윤년 체크
	 *
	 * @access public
	 * @return bool
	 * @param int 년도
	 */
	public function is_yoon ($y) {
		if ( ! ($y % 400) )
			return true;

		if ( ! ($y % 4 ) && ($y % 100) )
			return true;

		return false;
	}
	// }}}

	// {{{ +-- public (object) tolunar ($v = null)
	/**
	 * 양력 날자를 음력으로 변환
	 *
	 * @access public
	 * @return object .
	 *
	 *   - date => YYYY-MM-DD 형식의 음력 날자
	 *   - dangi => 단기
	 *   - hyear => AD/BC 형식 년도
	 *   - year => 년도
	 *   - month => 월
	 *   - day => 일
	 *   - moonyoon => 윤년 여부
	 *   - largemonth => 평달/큰달 여부
	 *   - week => 요일
	 *   - hweek => 요일 (한자)
	 *   - unixstamp => unixstamp (양력)
	 *   - ganji => 간지
	 *   - hganji => 간지 (한자)
	 *   - gan => 10간
	 *   - hgan => 10간 (한자)
	 *   - ji => 12지
	 *   - hji => 12지 (한자)
	 *   - ddi => 띠
	 *
	 * @param int|string   날자형식
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 */
	public function tolunar ($v = null) {
		list ($y, $m, $d) = $this->toargs ($v);

		$r = $this->solartolunar ($y, $m, $d);
		list ($year, $month, $day, $myoon, $lmonth) = $r;

		$w = $this->getweekday ($y, $m, $d);

		$k1 = ($year + 6) % 10;
		$k2 = ($year + 8) % 12;

		if ( $k1 < 0 ) $k1 += 10;
		if ( $k2 < 0 ) $k2 += 12;

		return (object) array (
			'date'       => $this->regdate ($r),
			'dangi'      => $year + 2333,
			'hyear'      => $this->human_year ($year),
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
	 *
	 *   - date => YYYY-MM-DD 형식의 양력 날자
	 *   - dangi => 단기
	 *   - hyear => AD/BC 형식 년도
	 *   - year => 년도
	 *   - month => 월
	 *   - day => 일
	 *   - week => 요일
	 *   - hweek => 요일 (한자)
	 *   - unixstamp => unixstamp (양력)
	 *   - ganji => 간지
	 *   - hganji => 간지 (한자)
	 *   - gan => 10간
	 *   - hgan => 10간 (한자)
	 *   - ji => 12지
	 *   - hji => 12지 (한자)
	 *   - ddi => 띠
	 *
	 * @param int|string 날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
	 *
	 * @param bool 윤달여부
	 */
	public function tosolar ($v = null, $yoon = false) {
		list ($y, $m, $d) = $this->toargs ($v);

		$r = $this->lunartosolar ($y, $m, $d, $yoon);
		list ($year, $month, $day) = $r;

		$w = $this->getweekday ($year, $month, $day);

		$k1 = ($y + 6) % 10;
		$k2 = ($y + 8) % 12;

		if ( $k1 < 0 ) $k1 += 10;
		if ( $k2 < 0 ) $k2 += 12;

		return (object) array (
			'date'       => $this->regdate ($r),
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
	 * @access public
	 * @return object .
	 *
	 *    - data => YYYY-MM-DD 형식의 양력 날자
	 *    - year => 세차
	 *    - month => 월건 (태양력)
	 *    - day => 일진
	 *    - hyear => 한자 세차
	 *    - hmonth => 한자 월건 (태양력)
	 *    - hday => 한자 일진
	 *
	 * @param int|string 날자형식
	 *
	 *    - unixstmap (1970년 12월 15일 이후부터 가능)
	 *    - Ymd or Y-m-d
	 *    - null data (현재 시간)
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
	 * @return object .
	 *
	 *    - data => index number
	 *    - k => 한글 28수
	 *    - h => 한자 28수
	 *
	 * @param int|string   날자형식
	 *
	 *    - unixstmap (1970년 12월 15일 이후부터 가능)
	 *    - Ymd or Y-m-d
	 *    - null data (현재 시간)
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
	 * @return object    현달 초입/중기와 다음달 초입 데이터 반환
	 *
	 * * center (현달 초입)
	 *  - name  => 절기 이름
	 *  - hname => 절기 한자 이름
	 *  - hyear => AD/BC 형식 연도
	 *  - year  => 절기 연도
	 *  - month => 절기 월
	 *  - day   => 절기 일
	 *  - hour  => 절기 시
	 *  - min   => 절기 분
	 * * ccenter (현달 중기)
	 *  - name  => 절기 이름
	 *  - hname => 절기 한자 이름
	 *  - hyear => AD/BC 형식 연도
	 *  - year  => 절기 연도
	 *  - month => 절기 월
	 *  - day   => 절기 일
	 *  - hour  => 절기 시
	 *  - min   => 절기 분
	 * * ncenter (다음달 초입)
	 *  - name  => 절기 이름
	 *  - hname => 절기 한자 이름
	 *  - hyear => AD/BC 형식 연도
	 *  - year  => 절기 연도
	 *  - month => 절기 월
	 *  - day   => 절기 일
	 *  - hour  => 절기 시
	 *  - min   => 절기 분
	 *
	 * @param int|string   날자형식
	 *
	 *  - unixstmap (1970년 12월 15일 이후부터 가능)
	 *  - Ymd or Y-m-d
	 *  - null data (현재 시간
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
				'hname' => $this->hmonth_st[$inginame],
				'hyear' => $this->human_year ($ingiyear),
				'year'  => $ingiyear,
				'month' => $ingimonth,
				'day'   => $ingiday,
				'hour'  => $ingihour,
				'min'   => $ingimin
			),
			'ccenter' => (object) array (
				'name'  => $this->month_st[$midname],
				'hname' => $this->hmonth_st[$midname],
				'hyear' => $this->human_year ($midyear),
				'year'  => $midyear,
				'month' => $midmonth,
				'day'   => $midday,
				'hour'  => $midhour,
				'min'   => $midmin
			),
			'nenter'  => (object) array (
				'name'  => $this->month_st[$outginame],
				'hname' => $this->hmonth_st[$outginame],
				'hyear' => $this->human_year ($outgiyear),
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
	 * @return object	. 합삭/망 object
	 * 
	 *   * new : 합삭
	 *
	 *     - hyear => 합삭 AD/BC 형식 연도
	 *     - year  => 합삭 연도
	 *     - month => 합삭 월
	 *     - day   => 합삭 일
	 *     - hour  => 합삭 시
	 *     - min   => 합삭 분
	 *        
	 *   * full : 망
	 *
	 *     - hyear => 망 AD/BC 형식 연도
	 *     - year  => 망 연도
	 *     - month => 망 월
	 *     - day   => 망 일
	 *     - hour  => 망 시
	 *     - min   => 망 분
	 *
	 * @param int|string   날자형식
	 *
	 *   - unixstmap (1970년 12월 15일 이후부터 가능)
	 *   - Ymd or Y-m-d
	 *   - null data (현재 시간)
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
				'hyear' => $this->human_year ($y1),
				'year' => $y1,
				'month' => $mo1,
				'day' => $d1,
				'hour' => $h1,
				'min' => $mi1
			),   // 합삭 (New Moon)
			'full' => (object) array (
				'hyear' => $this->human_year ($ym),
				'year' => $ym,
				'month' => $mom,
				'day' => $dm,
				'hour' => $hm,
				'min' => $mim
			)    // 망 (Full Moon)
		);
	}
	// }}}

	// {{{ +-- public (string) ganji_ref ($no, $mode = false)
	/**
	 * dayfortune method의 ganji index 반환값을 이용하여, ganji
	 * 값을 구함
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
