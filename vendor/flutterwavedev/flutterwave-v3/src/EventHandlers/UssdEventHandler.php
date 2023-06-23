<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

class UssdEventHandler implements EventHandlerInterface
{
    use EventTracker;

    /**
     * This is called only when a transaction is successful
     * */
    public function onSuccessful($transactionData): void
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Comfirm that the transaction is successful
        // Confirm that the chargecode is 00 or 0
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here

        self::sendAnalytics('Initiate-USSD-transfer');
    }

    /**
     * This is called only when a transaction failed
     * */
    public function onFailure($transactionData): void
    {
        self::sendAnalytics('Initiate-USSD-transfer-error');
        // Get the transaction from your DB using the transaction reference (txref)
        // Update the db transaction record (includeing parameters that didn't exist before the transaction is completed. for audit purpose)
        // You can also redirect to your failure page from here
    }

    /**
     * This is called when a transaction is requeryed from the payment gateway
     * */
    public function onRequery($transactionReference): void
    {
        // Do something, anything!
    }

    /**
     * This is called a transaction requery returns with an error
     * */
    public function onRequeryError($requeryResponse): void
    {
        // Do something, anything!
    }

    /**
     * This is called when a transaction is canceled by the user
     * */
    public function onCancel($transactionReference): void
    {
        // Do something, anything!
        // Note: Somethings a payment can be successful, before a user clicks the cancel button so proceed with caution
    }

    /**
     * This is called when a transaction doesn't return with a success or a failure response. This can be a timedout transaction on the Rave server or an abandoned transaction by the customer.
     * */
    public function onTimeout($transactionReference, $data): void
    {
        // Get the transaction from your DB using the transaction reference (txref)
        // Queue it for requery. Preferably using a queue system. The requery should be about 15 minutes after.
        // Ask the customer to contact your support and you should escalate this issue to the flutterwave support team. Send this as an email and as a notification on the page. just incase the page timesout or disconnects
    }

    /**
     * @throws \Exception
     */
    public function onAuthorization(\stdClass $response, ?array $resource = null): array
    {
        if (! property_exists($response, 'meta')) {
            throw new \Exception('USSD service: An error occurred. please retry the transaction');
        }

        if (property_exists($response, 'data')) {
            $transactionId = $response->data->id;
            $tx_ref = $response->data->tx_ref;
            $flw_ref = $response->data->flw_ref;

            $data = [
                'transactionId' => $transactionId,
                'tx_ref' => $tx_ref,
                'flw_ref' => $flw_ref,
                'code' => $response->data->payment_code,
            ];
        }

        $data['instruction'] = $response->meta->authorization->note;

        $data['mode'] = $response->meta->authorization->mode;

        if (is_array($resource) && ! empty($resource)) {
            $logger = $resource['logger'];
            $logger->notice('Ussd Authorization Mode: '.$data['mode']);
        }

        return $data;
    }
}
