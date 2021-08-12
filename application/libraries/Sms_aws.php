<?php defined('BASEPATH') or exit('No direct script access allowed');

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use EA\Engine\Types\Text;
use EA\Engine\Types\Url;

class Sms_aws {

    const COUNTRY_CODE = '+55';
    protected $CI, $sns_client;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('aws');
        $this->sns_client = new SnsClient($this->aws_settings());
    }

    // $this->sms_aws->publishSms($message, $phone);
    public function publishSms($message = '', $endpoint = '') 
    {
        
        $message_params = array(
            "Message"           => $message,
            "PhoneNumber"       => sefl::COUNTRY_CODE . $endpoint,
            "MessageAttributes" => $this->messageAttributes()
        );

        try {
            $result = $this->sns_client->publish($message_params);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        } 
    }

    // $this->sms_aws->publishSmsToTopic($message);
    public function publishSmsToTopic($message)
    {
        try {
            $result = $this->sns_client->publish([
                'Message' => $message,
                'TopicArn' => config('aws_sns_topic_appoint'),
            ]);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

    // $this->sns_client->subscribeToTopicSMS($endpoint)
    public function subscribeToTopicSMS($endpoint)
    {
        try {
            $result = $this->sns_client->subscribe([
                'Protocol' => 'sms',
                'TopicArn' => config('aws_sns_topic_appoint'),
                'Endpoint' => self::COUNTRY_CODE . $endpoint,
                'ReturnSubscriptionArn' => true
            ]);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

    // $this->sms_aws->listSubscriptions();
    public function listSubscriptions()
    {
        try {
            $result = $this->sns_client->listSubscriptions([]);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

    // $this->sms_aws->listTopics();
    public function listTopics()
    {
        try {
            $result = $this->sns_client->listTopics([]);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }

    // $this->sms_aws->listPhoneNumbersOptedOut();
    public function listPhoneNumbersOptedOut()
    {
        try {
            $result = $this->sns_client->listPhoneNumbersOptedOut([]);
            var_dump($result);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        } 
    }

    public function __call($name, $arguments=null)
    {
        if (!property_exists($this, $name)) {
            return call_user_func_array([$this->sns_client, $name], $arguments);
        }
    }

    protected function aws_settings()
    {
        return [
            'credentials' => array(
                'key' => config('aws_sns_key_id'),
                'secret' => config('aws_sns_secret_key')
            ),
            'region' => config('aws_sns_region'),
            'version' => config('aws_sns_version')
        ];
    }

    protected function messageAttributes()
    {
        return [
            // You can put your senderId here. but first you have to verify the senderid by customer support of AWS then you can use your senderId.
            // If you don't have senderId then you can comment senderId 
            // 'AWS.SNS.SMS.SenderID' => [
            //     'DataType' => 'String',
            //     'StringValue' => ''
            // ],
            'AWS.SNS.SMS.SMSType' => [
                'DataType' => 'String',
                'StringValue' => 'Transactional'
            ]
        ];
    }
}
