<?php
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

        return json_decode($response, true);

    } catch (\Exception $exception) {
        return  $exception->getMessage();
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