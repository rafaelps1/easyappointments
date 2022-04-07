<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| AWS - Internal Configuration
|--------------------------------------------------------------------------
|
| Declare some of the global config values of the Google Calendar
| synchronization feature.
|
*/

$config['aws_sns_region']           = Config::AWS_SNS_REGION;
$config['aws_sns_version']          = Config::AWS_SNS_VERSION;
$config['aws_sns_key_id']           = Config::AWS_SNS_KEY_ID;
$config['aws_sns_secret_key']       = Config::AWS_SNS_SECRET_KEY;
$config['aws_sns_topic_appoint']    = CONFIG::AWS_SNS_TOPIC_APPOINT;
