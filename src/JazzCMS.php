<?php

namespace NsTechNs\JazzCMS;

class JazzCMS
{
    private $config;
    private $response;
    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function __construct(array $config = [])
    {
        $this->response = new \stdClass;
        $this->setConfigParams($config);
    }

    private function setConfigParams($config)
    {
        $this->resetConfig();
        foreach ($config as $key => $value) {
            $this->config[$key] = $config[$key];
        }
    }

    private function resetConfig()
    {
        $this->config = [
            'base_url' => config('jazz-cms.base_url'),
            'username' => config('jazz-cms.username'),
            'password' => config('jazz-cms.password'),
            'from_mask' => config('jazz-cms.from'),
            'is_urdu' => config('jazz-cms.is_urdu', false),
            'show_status' => config('jazz-cms.show_status', true),
            'short_code' => config('jazz-cms.short_code'),
            'user_agent' => request()->header('User-Agent'),
            ];
    }

    public function setIsUrdu($value = true)
    {
        $this->config['is_urdu'] = $value;
    }

    public function setShowStatus($value = true)
    {
        $this->config['show_status'] = $value;
    }

    private function sendRequest($type, $end_point, $data = [], $headers = [], $file = false)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->config['base_url'].$end_point);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->config['user_agent']);

            if (! empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

            if ($type == 'POST' && ! empty($data)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                if (! empty($file)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
            }

            if (! empty($file)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
            }

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            $result = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $requestSize = curl_getinfo($ch, CURLINFO_REQUEST_SIZE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

            $this->response->request = ['request_size' => $requestSize, 'curl_error' => $curlError, 'base_url' => $this->config['base_url'], 'content_type' => $contentType, 'redirect_count' => $redirectCount, 'effective_url' => $effectiveUrl, 'total_time' => $totalTime];
            $this->response->http_code = $httpCode;

            if (! empty($data) && ! $file) {
                $this->response->data = $this->parseXmlData($result);
            } else {
                $this->response->data["statusmessage"] = $result;
            }

            if ($httpCode == "200" && (isset($this->response->data["statusmessage"]) &&
                in_array(
                    $this->response->data["statusmessage"],
                    ["Message Sent Successfully!","In Process.. and check your campaign logs.", "Your Campaign runs successfully. Please check your campaign logs."]
                ))) {
                $this->response->status = 'success';
            } else {
                $this->response->status = 'failed';
            }
            curl_close($ch);
        } catch (\Exception $ex) {
            $this->response->request = ['request_size' => null, 'base_url' => $this->config['base_url'], 'curl_error' => null, 'content_type' => null, 'redirect_count' => null, 'effective_url' => null, 'total_time' => null];
            $this->response->http_code = null;
            $this->response->status = 'failed';
            $this->response->data = $ex;
        }
    }

    private function parseXmlData($result): array
    {
        $xml_utf8 = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $result);
        $simple_xml = simplexml_load_string($xml_utf8);
        $xml_to_json = json_encode($simple_xml);

        return !empty($result)?json_decode($xml_to_json, true):[];
    }

    public function sendSMS($to, $message, $identifier = null, $unique_id = null, $product_id = null, $channel = null, $transaction_id = null): \stdClass
    {
        $xmldoc = "<SMSRequest><Username>{$this->config['username']}</Username><Password>{$this->config['password']}</Password><From>{$this->config['from_mask']}</From><To>{$to}</To><Message>{$message}</Message><urdu>{$this->config['is_urdu']}</urdu><statuscode>{$this->config['show_status']}</statuscode><Identifier>{$identifier}</Identifier><UniqueId>{$unique_id}</UniqueId><ProductId>{$product_id}</ProductId><Channel>{$channel}</Channel><TransactionId>{$transaction_id}</TransactionId></SMSRequest>";
        $this->sendRequest('POST', '/sendsms_xml.html', ['xmldoc' => $xmldoc]);

        return $this->response;
    }

    public function directSendSMS($to, $message, $identifier = null, $unique_id = null, $product_id = null, $channel = null, $transaction_id = null): \stdClass
    {
        $parameters = ['Username' => $this->config['username'], 'Password' => $this->config['password'], 'From' => $this->config['from_mask'], 'To' => $to, 'Message' => $message, 'Identifier' => $identifier, 'UniqueId' => $unique_id, 'ProductId' => $product_id, 'Channel' => $channel, 'TransactionId' => $transaction_id];
        $this->sendRequest('GET', '/sendsms_url.html?'.http_build_query($parameters));

        return $this->response;
    }

    public function receivingSMS($from_date = null, $to_date = null): \stdClass
    {
        $filter_date = "";
        if (! empty($from_date) && ! empty($to_date)) {
            $filter_date = "<FromDate>{$from_date}</FromDate><ToDate>{$to_date}</ToDate>";
        }

        $xmldoc = "<SMSRequest><Username>{$this->config['username']}</Username><Password>{$this->config['password']}</Password><Shortcode>{$this->config['short_code']}</Shortcode><urdu>{$this->config['is_urdu']}</urdu><statuscode>{$this->config['show_status']}</statuscode>{$filter_date}</SMSRequest>";
        $this->sendRequest('POST', '/receivesms_xml.html', ['xmldoc' => $xmldoc]);

        return $this->response;
    }

    public function balanceInquiry(): \stdClass
    {
        $parameters = ['Username' => $this->config['username'], 'Password' => $this->config['password']];
        $this->sendRequest('GET', '/request_sms_check.html?'.http_build_query($parameters));

        return $this->response;
    }

    public function sendSMSGroups($group_name, $message): \stdClass
    {
        $parameters = ['Username' => $this->config['username'], 'Password' => $this->config['password'], 'From' => $this->config['from_mask'],'Group' => $group_name, 'Message' => $message];
        $this->sendRequest('GET', '/sendsms_group.html?'.http_build_query($parameters));

        return $this->response;
    }

    public function sendSMSInternational($to, $message): \stdClass
    {
        $parameters = ['Username' => $this->config['username'], 'Password' => $this->config['password'], 'From' => $this->config['from_mask'], 'To' => $to, 'Message' => $message];
        $this->sendRequest('GET', '/int_api.html?'.http_build_query($parameters));

        return $this->response;
    }

    public function scheduleJob($contacts_file, $message, $schedule_date_time = null): \stdClass
    {
        $curl_file = new \CURLFile(realpath($contacts_file));
        $parameters = ['Username' => $this->config['username'], 'Password' => $this->config['password'], 'From' => $this->config['from_mask'], 'Message' => $message, 'ScheduleDateTime' => $schedule_date_time, 'file_contents' => $curl_file];
        $this->sendRequest('POST', '/upload_txt.html', $parameters, [], true);

        return $this->response;
    }
}
