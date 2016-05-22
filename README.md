# Lunar PHP pear package

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

이런 이유로 고영장님의 ***진짜만세력***은 1582년 10월 15일 이전의 데이터에 대해서는 다른 달력들과 많은 차이를 보이게 된다.
하지만, 그렇다고 해서 고영창님의 달력이 잘못되었다고 할 수는 없으며, 율리우스 적일(Julian date)의 경우에는 정확한
표기를 하고 있는 것으로 보인다.

또한, 기존의 달력들이 database를 구축하여 사용하는 방식이나, 고영창님의 ***진짜만세력***은 계산에 의하여 사용 되므로,
음력 윤달의 기준이 조금 다르기 때문에 일간이나 음력윤달의 차이로 인하여 음력날자가 1~2일 정도가 차이가 발생할 수
있다. 이에 대해서는 [고영창님의 홈페이지](http://afnmp3.homeip.net/~kohyc/calendar/index.cgi)를 참조하기 바란다.

### 진짜 만세력과 Lunar Pear 패키지의 차이

1. 모든 계산은 original ***진짜만세력***의 계산 방식을 따른다. (이는 다른 calendar들과 음력 날자가 1~2일의 차이가 발생할 수 있고, 음력 윤달이 다를 수 있다.)
1. 1582년 10월 15일 이전의 표기를 율리우스력을 사용한다. (다른 calender들과 역사 기록과 맞추기 위해서...)
1. 율리우스력을 사용하기 때문에 기원전 calender는 BC 4713년 2월 8일 부터 가능하다.
1. 위의 이유는 ***진짜만세력***의 계산은 그레고리력으로 하기 때문에 율리우스력을 그레고리력으로 변환하기 위한 알고리즘의 제약 때문이다.
1. 2.0 부터는 oops/KASI_Lunar pear package가 설치되어 있을 경우, 1391-02-05 ~ 2050-12-31 까지는 한국천문연구원의 음양력 DB를 지원한다.

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
