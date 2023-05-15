Instalacion
composer require fenguoz/bsc-php


Ejemplos:

require(__DIR__.'/Transacciones.php');
require(__DIR__.'/bloques.php');

$ethereumTransaction = new EthereumTransaction();


// Crear y procesar una transacción ERC20
$Erc20RawSigned= $ethereumTransaction->createErc20Transaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0x1F6Da4454F8395D652a69F6487438BAce3C2e28c', '975.70', '0xdAC17F958D2ee523a2206206994597C13D831ec7', '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

// Crear y procesar una transacción ETH
$EthRawSigned=$ethereumTransaction->createEthTransaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', 975.70, '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

//Push transacion
echo json_encode($ethereumTransaction->processTransaction($Erc20RawSigned),true);
echo json_encode($ethereumTransaction->processTransaction($EthRawSigned),true);

#Balance de un Token, especificar los decimales.  1 seguido de cantidad de ceros = decimales.  Contrato: 0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48
echo $balance = getTokenBalance('0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48', '0xcba0074a77A3aD623A80492Bb1D8d932C62a8bab')/1000000;

#balance de una cuenta ETH
echo $balance = hexdec(getBalance('0xcba0074a77A3aD623A80492Bb1D8d932C62a8bab'))/1000000000000000000;

$blockNumber = $ethereumTransaction->toHexValue(17262109); // Especifica el número de bloque
$transactions = getTransactionsInBlock('0x'.$blockNumber);

if (isset($transactions['result']['transactions'])) {

    foreach ($transactions['result']['transactions'] as $clave => $transaction) {
        $from = $transaction['from'];
        $to = $transaction['to'];
        $input = $transaction['input'];
        $hash = $transaction['hash'];
        $value = $transaction['value'];

        echo "From: $from<br>";
        echo "To: $to<br>";
        echo "Input: $input<br>";
        echo "Hash: $hash<br>";
        echo "Value: ".hexdec($value)."<br>";
        echo "<br><hr>";
    }
}


