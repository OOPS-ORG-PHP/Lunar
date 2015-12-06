<?php
/*
 * Test code for pear_Lunar_KASI
 * $Id$
 */

$iniget = function_exists ('___ini_get') ? '___ini_get' : 'ini_get';
$iniset = function_exists ('___ini_set') ? '___ini_set' : 'ini_set';

$cwd = getcwd ();
$ccwd = basename ($cwd);
if ( $ccwd == 'tests' ) {
	$oldpath = $iniget ('include_path');
	$newpath = preg_replace ("!/{$ccwd}!", '', $cwd);
	$iniset ('include_path', $newpath . ':' . $oldpath);
}

/*
 * Lunar_KASI API import
 */
require_once 'Lunar.php';

$lunar = new Lunar;

$days = array (
	'1391-02-05',
	'1582-10-04',
	'1582-10-05',
	'1582-10-15',
	'1583-03-03', // leap
	'1583-04-03', // leap
	'2050-12-31',
	'2051-12-31',
);

$ment = array (
	'유효범위 시작날자',
	'율리우스력 마지막 날자',
	'율리우스력과 그레고리력 사이의 존재하지 않는 날',
	'그레고리력 시작 날자',
	'윤달 체크(아님)',
	'윤달 체크(맞음)',
	'유효범위 마지막 날자',
	'유효범위 밖',
);

echo "-----------------------------------------------------------------------\n";

$i = 0;
foreach ( $days as $day ) {
	$l = $lunar->tolunar ($day);

	$leap  = $l->leap ? 'true' : 'false';
	$lmoon = $l->lmoon ? 'true' : 'false';

	$s = $lunar->tosolar ($l->date, $l->leap);

	echo <<<EOF
지정 날자: $day - {$ment[$i++]}
음력 변환: {$l->date}, leap: {$leap}, LargeMoon: {$lmoon}
양력 변환: {$s->date} {$s->fmt}
-----------------------------------------------------------------------

EOF;
}

