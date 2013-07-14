<?php
/*
 * Test code for pear_Lunar
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

require_once 'Lunar.php';


$target = $argv[1] ? $argv[1] : date ('Ymd', time ());

$lunar = new Lunar;

$target = $argv[1];
$lunar->toargs ($target);

echo "**\n";
printf ("** target date is %s\n", $target);
echo "**\n";
echo "\n";

$z = $lunar->tolunar ($target);
$tune = $lunar->dayfortune ($target);
$moon = $lunar->moonstatus ($target);
$s28  = $lunar->s28day ($target);
$season = $lunar->seasondate ($target);

$yoon = $z->moonyoon ? ', 윤달' : '';
$bmon = $z->largemonth ? '큰달' : '평달';

echo <<<EOF
-- 음력 변환 --------------------------------

날자   {$z->date} {$z->week} ({$z->hweek})
연     {$z->year}
월     {$z->month} ($bmon$yoon) {$tune->month}({$tune->hmonth})월
일     {$z->day} {$tune->day}({$tune->hday})일
간지   {$z->ganji} ({$z->hganji}) ({$tune->year})
띠     {$z->ddi}
28수   {$s28->k} ({$s28->h})

합삭 (New Moon)   {$moon->new->year}년 {$moon->new->month}월 {$moon->new->day}일 {$moon->new->hour}시 {$moon->new->min}분
망   (Full Moon)  {$moon->full->year}년 {$moon->full->month}월 {$moon->full->day}일 {$moon->full->hour}시 {$moon->full->min}분


EOF;

foreach ( $season as $v )
	printf ("%s(%s) %d년 %d월 %d일\n", $v->name, $v->hname, $v->year, $v->month, $v->day);


$z = $lunar->tosolar ($z->date, $z->moonyoon);

echo <<<EOF

-- 양력 재변환 ------------------------------
날자   {$z->date} {$z->week} ({$z->hweek})
연     {$z->year}
월     {$z->month}
일     {$z->day}
간지   {$z->ganji} ({$z->hganji})
띠     {$z->ddi}

EOF;

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
