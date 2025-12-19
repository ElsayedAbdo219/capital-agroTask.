<?php

namespace Modules\Payment\Enums;

enum PaymentMethod {
  const CARD = 'card';
  const TRANSFER = 'transfer';
  const CASH = 'cash';
  const WALLET = 'wallet';
  const MANUAL = 'manual';

}
