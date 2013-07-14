<?php
/*
 * Test code for pear_Lunar
 * $Id$
 *
 * 1841-01-0 ~ 2041-01-01
 * 양-음력 사이의 변환하여 요일이 동일한지 검증
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

require_once 'Lunar.php';

$lunar = new Lunar;

echo "== 음력-> 양력 요일 검증 ====================================\n";
$z = array ();
for ( $i=1842; $i<=2040; $i++ ) {
	$z = $lunar->tolunar ($i . '0101');
	$p[] = array ($z->date, $z->tday);
	#echo sprintf ("\tarray ('%d', '%s'),\n", preg_replace ('/-/', '', $p->date), $p->tday);
}

foreach ( $p as $v ) {
	$y = substr ($v[0], 0, 4);
	$m = substr ($v[0], 5, 2);
	$d = substr ($v[0], 8, 2);

	$td = date ('D', mktime (0, 0, 0, $m, $d, $y));

	$buf = $lunar->tosolar ($v[0]);
	if ( $v[1] != $buf->tday ) {
		printf (
			"%s (%s - $td) : %s (%s)\n",
			$v[0], $v[1], $buf->date, $buf->tday
		);
	}
}


echo "==================================================\n";
echo "== 음력-> 음력 요일 검증 ====================================\n";

$z = array ();
for ( $i=1841; $i<=2040; $i++ ) {
	$z = $lunar->tosolar ($i . '0101');
	$p[] = array ($z->date, $z->tday);
	#echo sprintf ("\tarray ('%d', '%s'),\n", preg_replace ('/-/', '', $p->date), $p->tday);
}

foreach ( $p as $v ) {
	$y = substr ($v[0], 0, 4);
	$m = substr ($v[0], 5, 2);
	$d = substr ($v[0], 8, 2);

	$td = date ('D', mktime (0, 0, 0, $m, $d, $y));

	$buf = $lunar->tolunar($v[0]);
	if ( $v[1] != $buf->tday ) {
		printf (
			"%s (%s - $td) : %s (%s)\n",
			$v[0], $v[1], $buf->date, $buf->tday
		);
	}
}