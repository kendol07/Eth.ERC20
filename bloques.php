<?php

require(__DIR__.'/../../vendor/autoload.php');

use Binance\Utils;

class Ethereum
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function toHexValue($value)
    {
        $hexValue = $this->utils->toHex($value);
        return $hexValue;
    }
}

function getTransactionsInBlock($blockNumber)
{
    try {
        $url = 'https://mainnet.infura.io/v3/a0beb9b1b25945cdb29181e0f14c9f83';
        $data = [
            'jsonrpc' => '2.0',
            'method' => 'eth_getBlockByNumber',
            'params' => [$blockNumber, true],
            'id' => 1,
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    } catch (\Exception $exception) {
        echo "<p style='color: red;'>Se produjo un problema:<br />";
        echo $exception->getMessage() . "</p>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    }
}

function getBalance($address) {
    $url = 'https://mainnet.infura.io/v3/a0beb9b1b25945cdb29181e0f14c9f83';
    $data = [
        'jsonrpc' => '2.0',
        'method' => 'eth_getBalance',
        'params' => [$address, 'latest'],
        'id' => 1,
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    if (isset($result['result'])) {
        $balanceInWei = $result['result'];
        
        return $balanceInWei;
    } else {
        return null;
    }
}


function getTokenBalance($contractAddress, $tokenAddress)
{
    $url = 'https://mainnet.infura.io/v3/a0beb9b1b25945cdb29181e0f14c9f83';
    $data = [
        'jsonrpc' => '2.0',
        'method' => 'eth_call',
        'params' => [
            [
                'to' => $contractAddress,
                'data' => '0x70a08231000000000000000000000000' . substr($tokenAddress, 2)
            ],
            'latest'
        ],
        'id' => 1,
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    if (isset($result['result'])) {
        $balance = hexdec($result['result']);
        return $balance;
    } else {
        return null;
    }
}


echo $balance = getTokenBalance('0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48', '0xcba0074a77A3aD623A80492Bb1D8d932C62a8bab')/1000000;



//echo $balance = hexdec(getBalance('0xcba0074a77A3aD623A80492Bb1D8d932C62a8bab'))/1000000000000000000;



/*
$Eth = new Ethereum();

$blockNumber = $Eth->toHexValue(17262109); // Especifica el número de bloque en formato hexadecimal

$transactions = json_decode(getTransactionsInBlock('0x'.$blockNumber), true);

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
else
{
    echo print_r($transactions);
}
