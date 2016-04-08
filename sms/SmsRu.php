<?php
namespace extpoint\yii2\sms;

use extpoint\yii2\Exception;

class SmsRu extends \yii\base\Component {

    public $apiId;
    public $from = null;

    /**
     * @param string $to
     * @param string $text
     * @param string [$from]
     * @return mixed
     * @throws Exception
     */
    public function send($to, $text, $from = null)
    {
        if ($from === null) {
            $from = $this->from;
        }

        if ($this->apiId === 'debug') {
            $r = file_put_contents(
                \Yii::$app->runtimePath . '/sms.ru ' . date('Y-m-d H-i-s ').$to.'.txt',
                'From: '.$from . "\n\n" . $text
            );

            if (!$r) {
                throw new Exception('Cannot save SMS.RU debug file');
            }

            return array();
        }

        $ch = curl_init("http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $post = [
            "api_id" => $this->apiId,
            "to"     => $to,
            "text"   => $text,
        ];
        // check from
        if ($from) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $from) || preg_match('/^[0-9]+$/', $from)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $from;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        if (is_string($result)) {
            $result = explode("\n", $result);

            if ($result[0] == 100) {
                unset($result[0]);
                return $result;
            }
        }

        ob_start();
        var_dump($result);
        $result = ob_get_clean();

        throw new Exception('SMS.RU request failed: '.$result);
    }

}
