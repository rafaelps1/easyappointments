<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cron_jobs extends EA_Controller {
    
    public function __construct() {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');

        $this->load->library('sms_aws');
    }

    public function sendSms($message = '', $phone = '') {
        $date = 'now';
        $datetime = new DateTimeZone('America/Sao_Paulo');
        $tomorrow = new DateTime($date, $datetime);
        $afterTomorrow = clone($tomorrow);
        $tomorrow->add(new DateInterval('P1D'));
        $afterTomorrow->add(new DateInterval('P2D'));

        $appointments = $this->appointments_model
            ->get_batch([
                'date(start_datetime) >=' => $tomorrow->format('Y-m-d'), 
                'date(start_datetime) <' => $afterTomorrow->format('Y-m-d')
            ]);

        foreach ($appointments as $key => $appoint) {
            $customer = $this->customers_model->get_row($appoint['id_users_customer']);
            $service = $this->services_model->get_row($appoint['id_services']);

            if (empty($customer)) {
                echo "[{$key}] Customer not found. </br>";
                continue; 
            }
            if (empty($customer['mobile_number']) && empty($customer['phone_number'])) {
                echo "[{$key}] Phone number undefined. </br>";
                continue; 
            }

            $number = empty($customer['mobile_number']) ? $customer['phone_number'] : $customer['mobile_number'];
            $number = preg_replace('/\D{1,15}/', '', $number);
            if (!empty($number) && strlen($number) >= 11) {
                $appointHour = (new DateTime($appoint['start_datetime'], $datetime));
                $message = "Agendamento/compromisso de {$service['name']} foi confirmado para o dia {$appointHour->format('d/m/Y')} às {$appointHour->format('H:m')}. Favor chegar 30min antes";
                echo "[{$key}]; {$appoint['hash']}; {$number}; Sending: {$message}.</br>";
                // $this->sms_aws->publishSms($message, $number);
            }
        }

        echo "Done!".PHP_EOL;
    }
    
}
