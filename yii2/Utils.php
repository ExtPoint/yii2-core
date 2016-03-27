<?php

namespace extpoint\yii;

class Utils {

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
