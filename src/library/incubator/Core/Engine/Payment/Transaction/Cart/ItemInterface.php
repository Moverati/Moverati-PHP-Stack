<?php
namespace Core\Engine\Payment\Transaction\Cart;

interface ItemInterface {
    public function getPaymentTransactionUid();
    public function getPaymentTransactionName();
    public function getPaymentTransactionAmount();
}