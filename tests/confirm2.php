<?php
/*
 * Test code for pear_Lunar
 * $Id$
 *
 * 1842년 부터 2041년까지의 매월 1일을 양력->음력->양력으로
 * 변환하여 변환전의 양력과 변환후의 양력을 비교 검증
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

#require_once 'KASI_Lunar.php';
require_once 'Lunar.php';

try {
	$lunar = new oops\Lunar;

	$z = array ();
	for ( $i=1842; $i<=2041; $i++ ) {
		for ( $j=1; $j<=12; $j++ ) {
			$j = ($j < 10) ? '0' . $j : $j;

			echo "- $i.$j.01\n";
			$z = $lunar->tolunar ($i . $j . '01');
			$z1 = $lunar->tosolar ($z->fmt, $z->leap);

			if ( $z1->fmt != "{$i}-{$j}-01" || $z->week != $z1->week ) {
				printf ("** %d-%s-01 %s(양) %s(음) %s %s\n", $i, $j, $z1->fmt, $z->fmt, $z1->week, $z->week);
			}
		}

	}
} catch ( Exception $e ) {
	echo $e->Message () . "\n";
	print_r ($e->TraceAsArray ()) . "\n";
	$e->finalize ();
}

