<?php

/**
 *
 * Number to chinese function.
 *
 * @license MIT License (MIT)
 *
 * @author Weilong Wang  <wilonx@163.com> https://github.com/wilon
 *
 */

if (! function_exists('number2chinese')) {

    /**
     * number2chinese description
     *
     * · 個，十，百，千，萬，十萬，百萬，千萬，億，十億，百億，千億，萬億，十萬億，
     *   百萬億，千萬億，兆；此函數億乘以億為兆
     *
     * · 以「十」開頭，如十五，十萬，十億等。兩位數以上，在數字中部出現，則用「一十幾」，
     *   如一百一十，一千零一十，一萬零一十等
     *
     * · 「二」和「兩」的問題。兩億，兩萬，兩千，兩百，都可以，但是20只能是二十，
     *   200用二百也更好。22,2222,2222是「二十二億兩千二百二十二萬兩千二百二十二」
     *
     * · 關於「零」和「〇」的問題，數字中一律用「零」，只有頁碼、年代等編號中數的空位
     *   才能用「〇」。數位中間無論多少個0，都讀成一個「零」。2014是「兩千零一十四」，
     *   20014是「二十萬零一十四」，201400是「二十萬零一千四百」
     *
     * 參考：https://jingyan.baidu.com/article/636f38bb3cfc88d6b946104b.html
     *
     * 人民幣寫法參考：[正確填寫票據和結算憑證的基本規定](http://bbs.chinaacc.com/forum-2-35/topic-1181907.html)
     *
     * @param  minx  $number
     * @param  boolean $isCurrency
     * @return string
     */
    function number2chinese($number, $isCurrency = false)
    {
        // 判斷正確數字
        if (!preg_match('/^-?\d+(\.\d+)?$/', $number)) {
            throw new Exception('number2chinese() wrong number', 1);
        }
        list($integer, $decimal) = explode('.', $number . '.0');

        // 檢測是否為負數
        $symbol = '';
        if (substr($integer, 0, 1) == '-') {
            $symbol = '負';
            $integer = substr($integer, 1);
        }
        if (preg_match('/^-?\d+$/', $number)) {
            $decimal = null;
        }
        $integer = ltrim($integer, '0');

        // 準備參數
        $numArr  = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '.' => '點'];
        $descArr = ['', '十', '百', '千', '萬', '十', '百', '千', '億', '十', '百', '千', '萬億', '十', '百', '千', '兆', '十', '百', '千'];
        if ($isCurrency) {
            $number = substr(sprintf("%.5f", $number), 0, -1);
            $numArr  = ['', '壹', '貳', '叁', '肆', '伍', '陸', '柒', '捌', '玖', '.' => '點'];
            $descArr = ['', '拾', '佰', '仟', '萬', '拾', '佰', '仟', '億', '拾', '佰', '仟', '萬億', '拾', '佰', '仟', '兆', '拾', '佰', '仟'];
            $rmbDescArr = ['角', '分', '厘', '毫'];
        }

        // 整數部分拼接
        $integerRes = '';
        $count = strlen($integer);
        if ($count > max(array_keys($descArr))) {
            throw new Exception('number2chinese() number too large.', 1);
        } else if ($count == 0) {
            $integerRes = '零';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $n = $integer[$i];      // 位上的數
                $j = $count - $i - 1;   // 單位數組 $descArr 的第幾位
                // 零零的讀法
                $isLing = $i > 1                    // 去除首位
                    && $n !== '0'                   // 本位數字不是零
                    && $integer[$i - 1] === '0';    // 上一位是零
                $cnZero = $isLing ? '零': '';
                $cnNum  = $numArr[$n];
                // 單位讀法
                $isEmptyDanwei = ($n == '0' && $j % 4 != 0)     // 是零且一斷位上
                    || substr($integer, $i - 3, 4) === '0000';  // 四個連續0
                $descMark = isset($cnDesc) ? $cnDesc : '';
                $cnDesc = $isEmptyDanwei ? '' : $descArr[$j];
                // 第一位是一十
                if ($i == 0 && $cnNum == '一' && $cnDesc == '十') $cnNum = '';
                // 二兩的讀法
                $isChangeEr = $n > 1 && $cnNum == '二'       // 去除首位
                    && !in_array($cnDesc, ['', '十', '百'])  // 不讀兩\兩十\兩百
                    && $descMark !== '十';                   // 不讀十兩
                if ($isChangeEr ) $cnNum = '兩';
                $integerRes .=  $cnZero . $cnNum . $cnDesc;
            }
        }

        // 小數部分拼接
        $decimalRes = '';
        $count = strlen($decimal);
        if ($decimal === null) {
            $decimalRes = $isCurrency ? '整' : '';
        } else if ($decimal === '0') {
            $decimalRes = $isCurrency ? '' : '零';
        } else if ($count > max(array_keys($descArr))) {
            throw new Exception('number2chinese() number too large.', 1);
        } else {
            for ($i = 0; $i < $count; $i++) {
                if ($isCurrency && $i > count($rmbDescArr) - 1) break;
                $n = $decimal[$i];
                if (!$isCurrency) {
                    $cnZero = $n === '0' ? '零' : '';
                    $cnNum  = $numArr[$n];
                    $cnDesc = '';
                    $decimalRes .=  $cnZero . $cnNum . $cnDesc;
                } else {
                    // 零零的讀法
                    $isLing = $i > 0                        // 去除首位
                        && $n !== '0'                       // 本位數字不是零
                        && $decimal[$i - 1] === '0';        // 上一位是零
                    $cnZero = $isLing ? '零' : '';
                    $cnNum  = $numArr[$n];
                    $cnDesc = $cnNum ? $rmbDescArr[$i] : '';
                    $decimalRes .=  $cnZero . $cnNum . $cnDesc;
                }
            }
        }
        // 拼接結果
        $res = $symbol . (
            $isCurrency
            ? $integerRes . ($decimalRes === '' ? '元整' : "元$decimalRes")
            : $integerRes . ($decimalRes ==='' ? '' : "點$decimalRes")
        );
        return $res;
    }
}