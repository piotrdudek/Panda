<?php

namespace Panda5\CarRent\Infrastructure;

class DotpayCompletePayment
{
    /**
     * Ok transaction status.
     */
    const STATUS_OK = 'OK';
    /**
     * Fail transaction status.
     */
    const STATUS_FAIL = 'FAIL';
    /**
     * Completed transaction status from Dotpay response.
     */
    const OPERATION_STATUS_COMPLETED = 'completed';
    /**
     * @var array
     */
    private $data;
    private $pin;
    /**
     * DotpayCompletePurchase constructor.
     * @param $data
     * @param $pin
     */
    public function __construct(array $data, $pin)
    {
        $this->data = $data;
        $this->pin = $pin;
    }
    /**
     * Validate signature from Dotpay response
     * and return transaction status.
     *
     * @param array $data
     * @return OK | FAIL string
     */
    private function validateSignature()
    {
        $string = implode(
            '',
            [
                $this->pin,
                $this->paramValue('id'),
                $this->paramValue('operation_number'),
                $this->paramValue('operation_type'),
                $this->paramValue('operation_status'),
                $this->paramValue('operation_amount'),
                $this->paramValue('operation_currency'),
                $this->paramValue('operation_withdrawal_amount'),
                $this->paramValue('operation_commission_amount'),
                $this->paramValue('operation_original_amount'),
                $this->paramValue('operation_original_currency'),
                $this->paramValue('operation_datetime'),
                $this->paramValue('operation_related_number'),
                $this->paramValue('control'),
                $this->paramValue('description'),
                $this->paramValue('email'),
                $this->paramValue('p_info'),
                $this->paramValue('p_email'),
                $this->paramValue('channel'),
                $this->paramValue('channel_country'),
                $this->paramValue('geoip_country'),
            ]
        );
        if ($this->paramValue('operation_status') !== self::OPERATION_STATUS_COMPLETED) {
            return self::STATUS_FAIL;
        }
        if (hash('sha256', $string) !== $this->paramValue('signature') ) {
            return self::STATUS_FAIL;
        }
        return self::STATUS_OK;
    }
    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->validateSignature() === self::STATUS_OK ? true : false;
    }
	public function getNumber(){
		return $this->paramValue('operation_number');
	}
	public function getStatus(){
		return $this->paramValue('operation_status');
	}
	public function getAmount(){
		return $this->paramValue('operation_amount');
	}
	public function getControl(){
		return $this->paramValue('control');
	}

	public function getMail(){
		return $this->paramValue('email');
	}

    private function paramValue($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }
}