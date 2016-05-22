# Lunar Calendar PHP pear package

## License

Lunar Pear class의 라이센스는 복합 라이센스 이므로, 주의가 필요하다.

* ***Lunar.php*** : BSD
* ***진짜 만세력 포팅 class*** :
  * Lunar/Lunar_API.php
  * 고영창
* 진짜 만세력이 open source 이기는 하지만, 정확한 license가 표기되어 있지않다. 그러므로, 홈페이지 같은 곳에서
사용하는 것에는 크게 문제가 없으나, 상용 패키지 제품에 포함을 시키는 것은 주의 하는 것이 좋다. 이 패키지를
사용함에 있어 아무런 개런티를 하지 않는다.

## Description

***Lunar*** Pear package는 [고영창](mailto:kohyc@chollian.net)님의 ***진짜만세력*** 0.92 Perl 버전을 PHP로 포팅한 것이다.

고영창님의 ***진짜만세력***은 절기시간과 합삭시간을 정밀하게 계산하여 만든 만세력입니다. (DB를 가지고 있지 않습니다.) 유효기간은 -10000년부터 10000년까지의 기간입니다.

### 달력 개발에 대한 주의 사항

1. 우리가 사용하는 그레고리력은 1582년 10월 15일 부터 존재한다.
2. 1582년 10월 15일 이전은 율리우스력으로 표기한다.
3. 1582년 10월 5일 부터 1582년 10월 14일은 calender상에 존재하지 않는다.

### original 진짜만세력 정보
* 개발자: [고영창](mailto:kohyc@chollian.net)
* Homepage: http://afnmp3.homeip.net:81/~kohyc/calendar/index.cgi

### original 진짜만세력의 이슈

1. 대부분의 calendar들은 1582년 10월 15일 이전을 율리우스력으로 표기한다.
1. ***진짜만세력***은 모든 표시를 그레고리력으로 표기한다.
1. 심지어 진짜만세력은 존재하지 않는 1582.10.5~1582.10.14 기간을 표시한다.
1. 상당히 거친(개발자의 주관이 강하게 들어간) 근삿식을 사용하기 때문에 한국 천문 연구원의 데이터와 약간의 오차가 발생한다.
 * [음력 날자가 다른 달](http://cal20000.blogspot.kr/2013/09/blog-post_3003.html)
 * [진짜만세력 오류라는데..](http://cal20000.blogspot.kr/2013/09/blog-post_327.html)
 * [음력 2015년과 2016년, 그리고 2017년의 자료가 약간 차이가 있는 것 같습니다.](http://cal20000.blogspot.kr/2013/09/2015-2016-2017.html)
 * [음력달력 오류 투성이…吉日·凶日이 뒤죽박죽 (세계일보)](http://cal20000.blogspot.kr/2013/09/re.html)
 * [진짜만세력 검증 후기](http://m.blog.naver.com/skyntel/60205518266)
 * [진짜만세력 검증 1900-2298](http://m.blog.naver.com/skyntel/60205466423)

이런 이유로 고영장님의 ***진짜만세력***은 1582년 10월 15일 이전의 데이터에 대해서는 율리안력으로 표기를 하고 있지 않기 때문에 완전히 다른 달력 처럼 보이고 있으며, 2000년 이후에서 시간이 경과 될 수록 JPL 천체력 DE431과 다소 차이를 보이게 된다.

하지만, 그렇다고 해서 고영창님의 달력이 잘못되었다고 할 수는 없으며(물론 맞다고 할 수도 없다), 율리우스 적일(Julian date)의 경우에는 정확한 표기를 하고 있는 것으로 보인다.

또한, 기존의 달력들이 database를 구축하여 사용하는 방식이나, 고영창님의 ***진짜만세력***은 계산에 의하여 사용 되므로,
음력 윤달의 기준이 조금 다르기 때문에 일간이나 음력윤달의 차이로 인하여 음력날자가 1~2일 정도가 차이가 발생할 수
있다. 이에 대해서는 [고영창님의 홈페이지](http://afnmp3.homeip.net/~kohyc/calendar/index.cgi)를 참조하기 바란다.

### 진짜 만세력과 Lunar Pear 패키지의 차이

1. 모든 계산은 original ***진짜만세력***의 계산 방식을 따른다. (이는 다른 calendar들과 음력 날자가 1~2일의 차이가 발생할 수 있고, 음력 윤달이 다를 수 있다.)
1. 1582년 10월 15일 이전의 표기를 율리우스력을 사용한다. (다른 calender들과 역사 기록과 맞추기 위해서...)
1. 율리우스력을 사용하기 때문에 기원전 calender는 BC 4713년 2월 8일 부터 가능하다.
1. 위의 이유는 ***진짜만세력***의 계산은 그레고리력으로 하기 때문에 율리우스력을 그레고리력으로 변환하기 위한 알고리즘의 제약 때문이다.
1. 2.0 부터는 [KASI-Lunar package](https://github.com/OOPS-ORG-PHP/KASI-Lunar) pear package가 설치되어 있을 경우, 1391-02-05 ~ 2050-12-31 까지는 한국천문연구원의 음양력 DB를 지원한다.

고영창님의 진짜만세력과 동일하게 포팅한 버전을 원할 경우, http://oops.org/project/manse/original 문서를 참고 하도록 한다.

### Calendar 유효 기간

* 32bit:
  * 양력 BC 2086(-2087)년 2월 9일 ~ AD 6078년 1월 29일
  * 음력 BC 2086(-2087)년 1월 1일 ~ AD 6077년 12월 29일
  * BC 2087년 7월 5일부터는 계산이 급속하게 느려진다.
* 64bit:
  * BC 4713(-4712)년 2월 8일 ~ AD 9999년 12월 31일
  * API의 연도 체크가 4자리 까지이므로 10000년 이상은 확인 못함
  * 64bit 계산이 가능한 시점까지 가능할 듯..
  * 기원전의 경우 율리우스 적일이 BC 4713년 1월 1일 부터이므로 그레고리력 변환이 가능한 BC 4713년 2월 8일부터 가능하다.
  
## Installation

```bash
[root@host ~]$ pear channel-discover pear.oops.org
Adding Channel "pear.oops.org" succeeded
Discovery of channel "pear.oops.org" succeeded
[root@host ~]$ pear install oops/Lunar
```

## dependency
  * PHP >= 5.1.0
  * PHP extensions
    * calendar
  * pear packages
    * [myException](https://github.com/OOPS-ORG-PHP/myException/) >= 1.0.1
    * [KASI-Lunar](https://github.com/OOPS-ORG-PHP/KASI-Lunar/) (optional) >= 1.0.0

## Reference

http://pear.oops.org/docs/oops-Lunar/Lunar/Lunar.html

## Sample codes

```php
<?php
/*
 * Lunar API import
 */
/*
 * oops/KASI_Lunar pcakge가 설치 되어 있으면, 1391-02-05 ~ 2050-12-31 까지의 음력 데이터를
 * 한국 천문 연구소의 데이터를 이용한다.
 */
require_once 'KASI_Lunar.php';
require_once 'Lunar.php';


$target = $argv[1] ? $argv[1] : date ('Ymd', time ());

$lunar = new oops\Lunar;


/*
 * $lunar->toargs (&$v)
 *
 * input:
 *        2013-07-13
 *        2013-7-13
 *        20130713
 *        1373641200
 *        Null
 *
 * output:
 *       Array
 *       (
 *           [0] => 2013
 *           [1] => 7
 *           [2] => 13
 *       )
 *
 * and reference variavble $v is changed to '2013-07-13'
 */

$v = '2013-7-13';
$r = $lunar->toargs ($v);
echo "### $v\n";
print_r ($r);

/*
 * $lunar->human_year ($y)
 *
 * input:
 *        -2334
 *
 * output:
 *        BC 2333
 */

echo $lunar->human_year (-2333);

/*
 * $lunar->is_reap ($y)
 *
 * input:
 *        1992 (양력)
 *
 * output:
 *        true
 */

if ( $lunar->is_leap (1992) )
    echo "This is Yoon Year\n";
else
    echo "This is not Yoon Year\n";

/*
 * $lunar->tolunar ($date)
 *
 * input:
 *        2013-07-16 or
 *        2013-7-16  or
 *        20130716   or
 *        1373900400 or
 *        NULL
 *
 * output
 *        stdClass Object
 *        (
 *            [fmt] => 2013-06-09
 *            [dangi] => 4346
 *            [hyear] => AD 2013
 *            [year] => 2013
 *            [month] => 6
 *            [day] => 9
 *            [leap] =>
 *            [largemonth] => 1
 *            [week] => 화
 *            [hweek] => 火
 *            [unixstamp] => 1373900400
 *            [ganji] => 계사
 *            [hganji] => 癸巳
 *            [gan] => 계
 *            [hgan] => 癸
 *            [ji] => 사
 *            [hji] => 巳
 *            [ddi] => 뱀
 *        )
 */

print_r ($lunar->tolunar ('2013-07-16'));

/*
 * $lunar->tosolar ($date)
 *
 * input:
 *        2013-06-09 or
 *        2013-6-09  or
 *        20130609   or
 *        NULL
 *
 * output
 *        stdClass Object
 *        (
 *            [fmt] => 2013-07-16
 *            [dangi] => 4346
 *            [hyear] => AD 2013
 *            [year] => 2013
 *            [month] => 7
 *            [day] => 16
 *            [week] => 화
 *            [hweek] => 火
 *            [unixstamp] => 1373900400
 *            [ganji] => 계사
 *            [hganji] => 癸巳
 *            [gan] => 계
 *            [hgan] => 癸
 *            [ji] => 사
 *            [hji] => 巳
 *            [ddi] => 뱀
 *        )
 */

print_r ($lunar->tosolar ('2013-06-09'));

/*
 * 구하는 음력월의 윤달 여부를 모른다면 다음과 같이 확인
 * 과정이 필요하다.
 */
$lun = '2013-06-09';
$solv = $lunar->tosolar ($lun);
$lunv = $lunar->tolunar ($sol->fmt);
if ( $lun != $lunv->fmt )
    $solv = $lunar->tosolar ($lun, true);


/*
 * $lunar->dayfortuen ($date)
 *
 * input:
 *        2013-07-16 or
 *        2013-7-16  or
 *        20130716   or
 *        1373900400 or
 *        NULL
 *
 * output:
 *        stdClass Object
 *        (
 *            [data] => stdClass Object
 *                (
 *                     [y] => 29           // 세차 index
 *                     [m] => 55           // 월건 index
 *                     [d] => 19           // 일진 index
 *                )
 *   
 *            [year] => 계사               // 세차 값
 *            [month] => 기미              // 월건 값
 *            [day] => 계미                // 일진 값
 *            [hyear] => 癸巳              // 한자 세차 값
 *            [hmonth] => 己未             // 한자 월건 값
 *            [hday] => 癸未               // 한자 일진 값
 *        )
 */

print_r ($lunar->dayfortune ('2013-07-16'));

/*
 * 7월 1일 부터 30일 까지의 일진을 구할 경우
 * 다음은 아주 안좋은 방법이다.
 */

for ( $i=1; $i<31; $i++ ) {
    $r = $lunar->dayfortune ('2013-07-' . $i);
    $iljin[$i] = $r->day;
}

/*
 * 위의 경우는 아래와 같이 $lunar->ganji_ref method를 이용하여
 * 성능을 높일 수 있다.
 */

$r = $lunar->dayfortune ('2013-07-01');
$iljin[$i] = $r->day;
$gindex = $r->data->d;

for ( $i=2; $i<31; $i++ ) {
    $gindex++;
    if ( $gindex >= 60 )
        $gindex -= 60;
    $iljin[$i] = $lunar->ganji_ref[$gindex];
}


/*
 * $lunar->s28day ($date)
 *
 * input:
 *        2013-07-16 or
 *        2013-7-16  or
 *        20130716   or
 *        1373900400 or
 *        NULL
 *
 * output:
 *        stdClass Object
 *        (
 *            [data] => 5
 *            [k] => 미
 *            [h] => 尾
 *        )
 */

print_r ($lunar->s28day ('2013-07-16'));

/*
 * 역시 7/1 부터 7/30 까지의 일진을 구할 경우에는 다음과 같이
 * 하면 성능이 매우 좋아진다.
 */

$s28 = null;
for ( $i=0; $i<30; $i++ ) {
    if ( $s28 === null )
        $s28 = $lunar->s28day ('2013-07-01');
    else
        $s28 = $lunar->s28day ($s28);

    $s28v[$i] = $s28->k;
}

/*
 * $lunar->seasondate ($date)
 *
 * input:
 *        2013-07-16 or
 *        2013-7-16  or
 *        20130716   or
 *        1373900400 or
 *        NULL
 *
 * output:
 *        stdClass Object
 *        (
 *            [center] => stdClass Object
 *                (
 *                    [name] => 소서
 *                    [hname] => 小暑
 *                    [hyear] => AD 2013
 *                    [year] => 2013
 *                    [month] => 7
 *                    [day] => 7
 *                    [hour] => 7
 *                    [min] => 49
 *                )
 *  
 *            [ccenter] => stdClass Object
 *                (
 *                    [name] => 대서
 *                    [hname] => 大暑
 *                    [hyear] => AD 2013
 *                    [year] => 2013
 *                    [month] => 7
 *                    [day] => 23
 *                    [hour] => 1
 *                    [min] => 11
 *                )
 *  
 *            [nenter] => stdClass Object
 *                (
 *                    [name] => 입추
 *                    [hname] => 立秋
 *                    [hyear] => AD 2013
 *                    [year] => 2013
 *                    [month] => 8
 *                    [day] => 7
 *                    [hour] => 17
 *                    [min] => 36
 *                )
 *        )
 */

print_r ($lunar->seasondate ('2013-07-16'));


/*
 * $lunar->moonstatus ($date)
 *
 * input:
 *        2013-07-16 or
 *        2013-7-16  or
 *        20130716   or
 *        1373900400 or
 *        NULL
 *
 * output:
 *   stdClass Object
 *   (
 *       [new] => stdClass Object
 *           (
 *               [hyear] => AD 2013
 *               [year] => 2013
 *               [month] => 7
 *               [day] => 8
 *               [hour] => 16
 *               [min] => 15
 *           )
 *
 *       [full] => stdClass Object
 *           (
 *               [hyear] => AD 2013
 *               [year] => 2013
 *               [month] => 7
 *               [day] => 23
 *               [hour] => 2
 *               [min] => 59
 *           )
 *   )
 */

print_r ($lunar->moonstatus ('2013-07-01'));

/*
 * 합삭/망 정보의 경우, 한달에 음력월이 2개가 있으므로,
 * 1일의 정보만 얻어서는 합삭/망 중에 1개의 정보만 나올 수 있다.
 * 그러므로, 1일의 데이터를 얻은 다음, 음력 1일의 정보까지 구하면
 * 한달의 합삭/망 정보를 모두 표현할 수 있다.
 */

$lun = $lunar->tolunar ('2013-07-01');

if ( $lun->largemonth ) // 평달의 경우 마지막이 29일이고 큰달은 30일이다.
    $plus = 29 - $lun->day;
else
    $plus = 30 - $lun->day;

$r1 = $lunar->moonstatus ('2013-07-01');           // 음력 2013-05-23
$r2 = $lunar->moonstatus ('2013-07-' . 1 + $plus); // 음력 2013-06-01

print_r ($r1);
print_r ($r2);

?>
```
