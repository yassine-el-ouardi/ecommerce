<?php

declare(strict_types=1);

namespace Flutterwave\EventHandlers;

class PayoutSubaccoutEventHandler implements EventHandlerInterface
{
    use EventTracker;

    public function onSuccessful($transactionData): void
    {
        // TODO: Implement onSuccessful() method.
    }

    public function onFailure($transactionData): void
    {
        // TODO: Implement onFailure() method.
    }

    public function onRequery($transactionReference): void
    {
        // TODO: Implement onRequery() method.
    }

    public function onRequeryError($requeryResponse): void
    {
        // TODO: Implement onRequeryError() method.
    }

    public function onCancel($transactionReference): void
    {
        // TODO: Implement onCancel() method.
    }

    public function onTimeout($transactionReference, $data): void
    {
        // TODO: Implement onTimeout() method.
    }
}
