<?php

/**
 * SAMPLE - Displays the current saldo of the first found account.
 * If an SSL certificate is not available (e.g. at remote development environments) you
 * can provide an alternate adapter. The adapter must implement
 * the Fhp\Adapter\AdapterInterface.
 */

require '../vendor/autoload.php';

use Fhp\Adapter\CurlNoSsl;
use Fhp\FinTs;

define('FHP_BANK_URL', '');                 # HBCI / FinTS Url can be found here: https://www.hbci-zka.de/institute/institut_auswahl.htm (use the PIN/TAN URL)
define('FHP_BANK_PORT', 443);               # HBCI / FinTS Port can be found here: https://www.hbci-zka.de/institute/institut_auswahl.htm
define('FHP_BANK_CODE', '');                # Your bank code / Bankleitzahl
define('FHP_ONLINE_BANKING_USERNAME', '');  # Your online banking username / alias
define('FHP_ONLINE_BANKING_PIN', '');       # Your online banking PIN (NOT! the pin of your bank card!)

$fints = new FinTs(
    FHP_BANK_URL,
    FHP_BANK_PORT,
    FHP_BANK_CODE,
    FHP_ONLINE_BANKING_USERNAME,
    FHP_ONLINE_BANKING_PIN
);

// Use FinTs::setAdapter to assign a custom adapter.
$noSslAdapter = new CurlNoSsl(FHP_BANK_URL, FHP_BANK_PORT);
$fints->setAdapter($noSslAdapter);

$accounts = $fints->getSEPAAccounts();

$oneAccount = $accounts[0];
$saldo = $fints->getSaldo($oneAccount);
print_r($saldo);

