# PHP number2chinese

### 源專案

[wilon/php-number2chinese](https://github.com/wilon/php-number2chinese)

PHP 數字轉為漢字描述，人民幣大寫方法。

 * 個，十，百，千，萬，十萬，百萬，千萬，億，十億，百億，千億，萬億，十萬億，百萬億，千萬億，兆；此函數億乘以億為兆
 
 * 以「十」開頭，如十五，十萬，十億等。兩位數以上，在數字中部出現，則用「一十幾」，如一百一十，一千零一十，一萬零一十等

 * 「二」和「兩」的問題。兩億，兩萬，兩千，兩百，都可以，但是20只能是二十，200用二百也更好。22,2222,2222是「二十二億兩千二百二十二萬兩千二百二十二」
 
 * 關於「零」和「〇」的問題，數字中一律用「零」，只有頁碼、年代等編號中數的空位才能用「〇」。數位中間無論多少個0，都讀成一個「零」。2014是「兩千零一十四」，200014是「二十萬零一十四」，201400是「二十萬零一千四百」
 
 * 參考：https://jingyan.baidu.com/article/636f38bb3cfc88d6b946104b.html

 * 人民幣寫法參考：[正確填寫票據和結算憑證的基本規定](http://bbs.chinaacc.com/forum-2-35/topic-1181907.html)

### 使用方法

> #string number2chinese ( mixed $number [, bollen $isCurrency] )#

*將$number轉為漢字念法*

* mixed $number

    輸入數字或字符串。
    當數字過大或過小時，請輸入string
    支持負數

* bollen $isCurrency

    默認為false，當為true時返回人民幣大寫漢字
    人民幣最大單位[仟兆]，最小單位[毫]

### 範例程式碼

```php
$num1 = 0.1234567890;
echo number2chinese($num1);    // 零點一二三四五六七八九
echo number2chinese($num1, true);    // 零元壹角貳分叁厘肆毫
$num2 = 20000000000000000;
echo number2chinese($num2);    // 兩兆
echo number2chinese($num2, true);    // 貳兆元整
$num3 = -1202030;
echo number2chinese($num3);    // 負一百二十萬零兩千零三十
echo number2chinese($num3, true);    // 負壹佰貳拾萬零貳仟零叁拾元整
```

當數字過大時，請輸入string
```php
$num2 = 1234567890.0123456789;
echo number2chinese($num2);    // 十二億三千四百五十六萬七千八百九十點零一二三
echo number2chinese($num2, true);    // 壹拾貳億叁仟肆佰伍拾陸萬柒仟捌佰玖拾元零壹分貳厘叁毫
$num2 = '1234567890.0123456789';
echo number2chinese($num2);    // 十二億三千四百五十六萬七千八百九十點零一二三四五六七八九
echo number2chinese($num2, true);    // 壹拾貳億叁仟肆佰伍拾陸萬柒仟捌佰玖拾元壹分貳厘叁毫
```

 若想精確小數點後兩位，請先處理$num1
```php
$num1 = 0.1234567890;
echo number2chinese(number_format($num1, 2));    // 零點一二
echo number2chinese(number_format($num1, 2), true);    // 零元壹角貳分
```
