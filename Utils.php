<?php

namespace extpoint\yii2;

use yii\db\ActiveQuery;

class Utils {

    const NBSP_UTF8 = "\xc2\xa0";

    static function dump()
    {
        echo '<pre>';
        call_user_func_array('var_dump', func_get_args());
        die;
    }

    static public function pluralRu($num, $one, $some, $many = false) {

        $num = abs($num);
        if ($many === false)
            $many = $some;

        $teen_factor = $num % 100;
        if ($teen_factor < 10 or $teen_factor > 20)
        {
            $num = $num % 10;
            if ($num == 1) return $one;
            if ($num >= 2 and $num <= 4) return $some;
        }

        return $many;
    }

    public static function humanFriendlyDateRange($since, $till) {

        if (is_string($since)) {
            $since = strtotime($since);
        }
        if (is_string($till)) {
            $till = strtotime($till);
        }

        $sinceYear = date('Y', $since);
        $sinceMonth = self::date('f', $since);
        $sinceDay = date('j', $since);
        $tillYear = date('Y', $till);
        $tillMonth = self::date('f', $till);
        $tillDay = date('j', $till);

        if ($sinceYear != $tillYear) {
            return "$sinceDay $sinceMonth $sinceYear - $tillDay $tillMonth $tillYear";
        }

        if ($sinceMonth != $tillMonth) {
            return "$sinceDay $sinceMonth - $tillDay $tillMonth $tillYear";
        }

        return "$sinceDay - $tillDay $tillMonth $tillYear";
    }

    static public function arrayPluck($key, $a)
    {
        $r = array();
        foreach ($a as $idx => $record)
            $r[$idx] = is_object($record) ? $record->$key : $record[$key];

        return $r;
    }

    static public function arrayPluckIds($key, $a)
    {
        $r = array();
        foreach ($a as $record) {
            $v = is_object($record) ? $record->$key : $record[$key];
            if ($v !== null) {
                $r[] = $v;
            }
        }

        return array_unique($r, SORT_NUMERIC);
    }

    static public function arrayPluckToHash($keyKey, $valueKey, $a)
    {
        $r = array();
        foreach ($a as $record)
            $r[is_object($record) ? $record->$keyKey : $record[$keyKey]] =
                is_object($record) ? $record->$valueKey : $record[$valueKey];

        return $r;
    }

    static public function mapModelResults($a)
    {
        $r = array();

        foreach ($a as $record)
            $r[$record->id] = $record;

        return $r;
    }

    static public function mapArrayOfRecords($key, $a)
    {
        $r = array();

        foreach ($a as $record)
            $r[is_array($record) ? $record[$key] : $record->$key] = $record;

        return $r;
    }

    static public function bunchArrayOfRecords($key, $a)
    {
        $r = array();

        foreach ($a as $record) {
            $r[(string)(is_array($record) ? $record[$key] : $record->$key)][] = $record;
        }

        return $r;
    }

    static public function arrayOfPairsToHash($a)
    {
        $r = array();

        foreach ($a as $subArray)
            $r[$subArray[0]] = $subArray[1];

        return $r;
    }

    static public function trimNumber($min, $max, $number) {
        return max($min, min($max, (float)$number));
    }

    static public function formatNumber($number, $digits = 2)
    {
        return number_format(round((float)$number, $digits), $digits, ',', ' '); // round() helps avoid "-0"
    }

    /**
     * @param string $string
     * @return float|null
     */
    static public function inputNumber($string) {
        $string = str_replace(' ', '', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace(',', '.', $string);
        return is_numeric($string) ? (float)$string : null;
    }

    static public function formatDelta($number, $digits = 2)
    {
        $number = (float)$number;
        return ($number > 0 ? '+' : '') . number_format($number, $digits, ',', ' ');
    }

    static public function formatPercent($value, $digits = 1)
    {
        return number_format($value * 100, $digits, ',', ' ');
    }

    static public function formatTimeFromHours($hours)
    {
        $intHours = floor($hours);
        $fraction = $hours - $intHours;

        $minutes = floor($fraction * 60);
        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }

        return $intHours . ':' . $minutes;
    }

    /**
     * @param int|null $moment
     * @return int
     */
    static public function getDayStart($moment = null) {
        return strtotime(date('Y-m-d', $moment ?: time()));
    }

    /**
     * @param int|null $moment
     * @return int
     */
    static public function getWeekStart($moment = null) {
        $moment = self::getDayStart($moment);
        return strtotime('-'.(date('N', $moment)-1).'days', $moment);
    }

    /**
     * @param string|null $date
     * @return string
     */
    static public function getWeekStartDate($date = null) {
        $moment = $date === null ? time() : strtotime($date);
        return date('Y-m-d', strtotime('-'.(date('N', $moment)-1).'days', $moment));
    }

    /**
     * @param string|int $since
     * @param string|int $till
     * @return int
     */
    static public function getDaysBetween($since, $till) {

        if (is_numeric($since)) {
            $since = date("Y-m-d", $since);
        }
        if (is_numeric($till)) {
            $till = date("Y-m-d", $till);
        }

        $date1 = new \DateTime($since);
        $date2 = new \DateTime($till);

        return (int)$date2->diff($date1)->format("%a");
    }

    static protected function colorToInt($c) {
        $c = ltrim($c, '#');
        if (strlen($c) == 3) {
            $c = $c{0}.$c{0}.$c{1}.$c{1}.$c{2}.$c{2};
        }
        return hexdec($c);
    }

    /**
     * @param string $color1
     * @param string $color2
     * @param float $preferFirst
     * @return string Average color
     */
    static public function mixColors($color1, $color2, $preferFirst = 0.5) {

        $c1 = self::colorToInt($color1);
        $c2 = self::colorToInt($color2);

        $o1 = (object)[
            'r' => ($c1 & 0xff0000) >> 16,
            'g' => ($c1 & 0xff00) >> 8,
            'b' => $c1 & 0xff,
        ];
        $o2 = (object)[
            'r' => ($c2 & 0xff0000) >> 16,
            'g' => ($c2 & 0xff00) >> 8,
            'b' => $c2 & 0xff,
        ];

        $r = (object)[
            'r' => round($o2->r * (1-$preferFirst) + $o1->r * $preferFirst),
            'g' => round($o2->g * (1-$preferFirst) + $o1->g * $preferFirst),
            'b' => round($o2->b * (1-$preferFirst) + $o1->b * $preferFirst),
        ];

        return sprintf('#%06x', ($r->r << 16) | ($r->g << 8) | $r->b );
    }

    /**
     * @param string $baseBackColor
     * @param string $currentBackColor
     * @param string $foreColor
     * @return string $foreColor adjusted by $currentBackColor
     */
    static public function applyAltBackColor($baseBackColor, $currentBackColor, $foreColor) {

        $cBaseBack = self::colorToInt($baseBackColor);
        $cCurrentBack = self::colorToInt($currentBackColor);
        $cForeColor = self::colorToInt($foreColor);

        $oBaseBack = (object)[
            'r' => ($cBaseBack & 0xff0000) >> 16,
            'g' => ($cBaseBack & 0xff00) >> 8,
            'b' => $cBaseBack & 0xff,
        ];
        $oCurrentBack = (object)[
            'r' => ($cCurrentBack & 0xff0000) >> 16,
            'g' => ($cCurrentBack & 0xff00) >> 8,
            'b' => $cCurrentBack & 0xff,
        ];
        $oForeColor = (object)[
            'r' => ($cForeColor & 0xff0000) >> 16,
            'g' => ($cForeColor & 0xff00) >> 8,
            'b' => $cForeColor & 0xff,
        ];

        $r = (object)[
            'r' => self::replaceBackColorEntry($oBaseBack->r, $oCurrentBack->r, $oForeColor->r),
            'g' => self::replaceBackColorEntry($oBaseBack->g, $oCurrentBack->g, $oForeColor->g),
            'b' => self::replaceBackColorEntry($oBaseBack->b, $oCurrentBack->b, $oForeColor->b),
        ];

        return sprintf('#%06x', ($r->r << 16) | ($r->g << 8) | $r->b );
    }

    static private function replaceBackColorEntry($baseBack, $currentBack, $fore) {

        $scale = $baseBack > $fore ?
            $fore / $baseBack : // Light back
            1; // Dark back

        return self::trimNumber(0, 255, round($fore + ($currentBack - $baseBack) * $scale));
    }

    static public function getColorByHash($string, $orMask = 0, $andMask = 0xffffff) {
        return '#'.sprintf('%06x', crc32($string) & $andMask | $orMask);
    }

    static public function getLightColorByHash($string, $orMask = 0, $andMask = 0xffffff) {
        return self::getColorByHash($string, 0x808080);
    }

    static public function getDarkColorByHash($string, $orMask = 0, $andMask = 0xffffff) {
        return self::getColorByHash($string, 0, 0x7f7f7f);
    }

    /**
     * @param string|\yii\db\ActiveQuery $sql
     * @param array $params
     * @return array
     */
    static public function queryHash($sql, $params = []) {

        if ($sql instanceof ActiveQuery) {
            $query = $sql->asArray()->all();
        }
        else {
            $query = \Yii::$app->db->createCommand($sql)->query($params);
        }

        $result = [];

        foreach ($query as $row) {
            $row = array_values($row);
            $result[$row[0]] = $row[1];
        }

        return $result;
    }

    /**
     * @param string[]|null[]|int[]|float[] $a
     * @param string $ifEmpty
     * @return string
     */
    static public function arrayToSql($a, $ifEmpty = 'NULL') {

        if (count($a) == 0) {
            return $ifEmpty;
        }

        return implode(', ', array_map([\Yii::$app->db, 'quoteValue'], $a));
    }

    static public function needMarkNonProduction() {
        return !YII_ENV_PROD && empty($_GET['forceProductionLook']);
    }

    static public function dateNext($date) {
        return date('Y-m-d', strtotime('+1day', strtotime($date)));
    }

    static public function dateNextMonth($date) {
        return date('Y-m-d', strtotime('+1month', strtotime($date)));
    }

    static public function dateMath($expression, $date = null) {
        return date('Y-m-d', strtotime($expression, strtotime($date === null ? date('Y-m-d') : $date)));
    }

    static public function textToHtml($text) {

        $html = nl2br(htmlspecialchars($text));

        // Match URIs
        $html = preg_replace('#https?://\S+#', '<a href="$0">$0</a>', $html);

        // emails
        $html = preg_replace('#[\w\-+\.]+@[\w\-+\.]+#', '<a href="https://mail.google.com/mail/?view=cm&fs=1&to=$0" target="_blank">$0</a>', $html);

        // tickets
        static $issueLink;
        if (!$issueLink) {
            $issueLink = \yii\helpers\Url::to(['/pm/issues/view', 'issueId' => '9999']);
            $issueLink = str_replace('9999', '$1', $issueLink);
        }

        $html = preg_replace('/#(\d+)/', '<a href="' . $issueLink . '">' . $issueLink . '</a>', $html);

        return $html;
    }

    /**
     * @param string|int $date1
     * @param string|int $date2
     * @param string $fullFormat
     * @param string $dayMonthFormat
     * @param string $dayFormat
     * @return string
     * @throws Exception
     */
    static public function formatDatesRange($date1, $date2, $fullFormat = 'j f y', $dayMonthFormat = 'j f', $dayFormat = 'j') {

        if (is_string($date1)) {
            $date1 = strtotime($date1);
        }
        if (is_string($date2)) {
            $date2 = strtotime($date2);
        }

        // Same day
        if (date('Y-m-d', $date1) === date('Y-m-d', $date2)) {
            return self::date($fullFormat, $date2);
        }

        // Same month
        if (date('Y-m', $date1) === date('Y-m', $date2)) {
            return self::date($dayFormat, $date1) . ' – ' . self::date($fullFormat, $date2);
        }

        // Same year
        if (date('Y', $date1) === date('Y', $date2)) {
            return self::date($dayMonthFormat, $date1) . ' – ' . self::date($fullFormat, $date2);
        }

        return self::date($fullFormat, $date1) . ' – ' . self::date($fullFormat, $date2);
    }

    /**
     * Used to suppress PHP notice on empty array keys with no turning notices off
     * @param mixed $argument
     * @param mixed $item
     */
    public static function accumulate(&$argument, $item = 1) {

        $argument += $item;
    }

    /**
     * Used to suppress PHP notice on empty array keys with no turning notices off
     * @param array $argument
     * @param array $item
     */
    public static function accumulateMerge(&$argument, $item) {

        $argument = array_merge($argument ?: [], $item);
    }

    /**
     * Used to suppress PHP notice on empty array keys with no turning notices off
     * @param mixed $argument
     * @param mixed $item
     */
    public static function accumulateMax(&$argument, $item) {

        if ($argument === null || $item > $argument) {
            $argument = $item;
        }
    }

    /**
     * Used to suppress PHP notice on empty array keys with no turning notices off
     * @param mixed $argument
     * @param mixed $value
     * @return mixed
     */
    public static function init(&$argument, $value) {

        if ($argument === null) {
            $argument = $value;
        }

        return $value;
    }

    public static function escapeScriptTagInJs($js) {
        return str_ireplace('</script>', '<\\u002fscript>', $js);
    }

    /**
     * @param string $format
     * @param string|int|null $value
     * @return string
     * @throws Exception
     */
    public static function date($format, $value = null) {

        if ($value === null) {
            $value = time();
        }
        elseif (is_string($value)) {
            $value = is_numeric($value) ? (int)$value : strtotime($value);
            if (!$value) {
                throw new Exception('Wrong date input');
            }
        }

        $r = '';
        while (mb_ereg('[\\\\aADFlMf]', $format, $regs))
        {
            $pos = mb_strpos($format, $regs[0]);
            if ($regs[0] === "\\")
            {
                $r .= mb_substr($format, 0, $pos+2);
                $format = mb_substr($format, $pos+2);
            }
            else
            {
                $r .= mb_substr($format, 0, $pos) .
                    mb_ereg_replace('.', '\\\\0', self::getDateStringRu($regs[0], $value));
                $format = mb_substr($format, $pos+1);
            }
        }
        $format = $r . $format;

        return date($format, $value);
    }

    public static function parseTimeZone($timeZone)
    {
        $tz = new \DateTimeZone($timeZone);
        $date = new \DateTime('now', $tz);
        $offset = $tz->getOffset($date) . ' seconds';
        $dateOffset = clone $date;
        $dateOffset->sub(\DateInterval::createFromDateString($offset));

        $interval = $dateOffset->diff($date);
        return $interval->format('%R%H:%I');
    }

    /**
     * @param string $param
     * @param int $time
     * @return string
     */
    protected static function getDateStringRu($param, $time)
    {
        switch ($param)
        {
            case 'a': return date('G', $time) < 12 ? 'дп' : 'пп';
            case 'A': return date('G', $time) < 12 ? 'ДП' : 'ПП';


            case 'D':
                // Для русского языка более привычно двубуквенное представление
                $x = array('вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб');
                return $x[date('w', $time)];

            case 'l':
                $x = array('Воскресенье', 'Понедельник', 'Вторник', 'Среда',
                    'Четверг', 'Пятница', 'Суббота');
                return $x[date('w', $time)];


            case 'F':
                $x = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                    'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
                return $x[date('n', $time)-1];

            case 'f':
                $x = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
                    'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
                return $x[date('n', $time)-1];

            case 'M':
                // Для русского языка более привычно сокращение с маленькой буквы
                $x = array('янв', 'фев', 'мар', 'апр', 'май', 'июн',
                    'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
                return $x[date('n', $time)-1];
        }
        return '';
    }

}
