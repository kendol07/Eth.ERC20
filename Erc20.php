<?php
require(__DIR__.'/../../vendor/autoload.php');

use Web3p\EthereumTx\Transaction;
use Bezhanov\Ethereum\Converter;
use Binance\Utils;
use Binance\Formatter;

//$bigInteger = new BigInteger();
$utils = new Utils();
$Formatter=new Utils();


class EthereumTransaction
{  
    //convertir cantidades de Ethereum, WEI, GWEI
    private $converter;
    public function __construct()
    {
        $this->converter = new Converter();
    }
    
    private $utils;
    public function toHexValue($value)
    {
        $hexValue = Utils::toHex($value);
        return $hexValue;
    }
    //Metodo para codificar Hex
    /*public function encodeNumberToHex($number)
    {
        $bigInteger = new BigInteger($number);
        $hexadecimal = $bigInteger->toHex();
        return $hexadecimal;
    }

    // Función para calcular el hash keccak256
    private function keccak256(string $input): string
    {
        return hash('sha3-256', $input);
    }

    // Función para codificar los parámetros ABI
    private function abiEncode(string $functionSignature, array $parameters): string
    {
        $encodedFunctionSignature = substr($this->keccak256($functionSignature), 0, 8);
        $encodedParameters = "";

        foreach ($parameters as $parameter) {
            $encodedParameters .= str_pad(substr($parameter, 2), 64, "0", STR_PAD_LEFT);
        }

        return "0x" . $encodedFunctionSignature . $encodedParameters;
    }*/

    // Función para obtener el precio del gas
    private function getGasPrice(): string
    {
        $jsonFee = json_decode(file_get_contents('https://api.etherscan.io/api?module=gastracker&action=gasoracle&apikey=VPCX7N6WHNC8XDYYBD7UWSWGDK8ANJUQDH'), true);
        $gasPrice = $this->converter->toWei($jsonFee['result']['SafeGasPrice'], 'gwei');
        return $gasPrice;
    }

    // Función para obtener el nonce de una dirección
    private function getNonce($address): int
    {
        $httpGet = json_decode(file_get_contents("https://api.etherscan.io/api?module=account&action=txlist&address=$address&sort=desc&apikey=VPCX7N6WHNC8XDYYBD7UWSWGDK8ANJUQDH"), true);

        if ($httpGet['status'] == 0) {
            return 0;
        } else {
            $nonce = 0;
            foreach ($httpGet['result'] as $indice => $valor) {
                if (strtolower($valor['from']) == strtolower($address)) {
                    $nonce = $valor['nonce'] + 1;
                    break;
                }
            }
            return $nonce;
        }
    }

    // Función para crear una transacción ERC20
    public function createErc20Transaction($fromAddress, $toAddress, $amount, $contractAddress, $privateKey)
    {
        $gasPrice = $this->getGasPrice();
        $nonce = $this->getNonce($fromAddress);
        //$amount = Utils::toMinUnitByDecimals("$value", $decimales); //decimales del token, ejemplo usdt tiene 6

        $params = [
            'nonce' => '0x'.$this->toHexValue($nonce),
            'from' =>  $fromAddress,
            'to' =>    $contractAddress,
            'gas' =>   '0x'.$this->toHexValue(21050),
            'gasPrice' => '0x'.$gasPrice,
            'value' => '0x0',
            'gasLimit' => '0x'.$this->toHexValue(21050),
            'chainId' => 1
        ];

        /*$functionSignature = "transfer(address,uint256)";
        $parameters = [
            $toAddress,
            "0x" . $this->encodeNumberToHex($amount/0.000001)
        ];
        $params['data'] = $this->abiEncode($functionSignature, $parameters);*/
        
        $method = 'transfer(address,uint256)';
        $formatMethod = Formatter::toMethodFormat($method);
        $formatAddress = Formatter::toAddressFormat($toAddress);
        $formatInteger = Formatter::toIntegerFormat($amount/0.000001);
        $params['data'] = "0x{$formatMethod}{$formatAddress}{$formatInteger}";
        
        $transaction = new Transaction($params);

        return $transaction->sign($privateKey);
    }

    // Función para procesar una transacción enviándola a la red Ethereum
    public function processTransaction($signedTransaction)
    {
        $ch = curl_init('https://api.etherscan.io/api?module=proxy&action=eth_sendRawTransaction');
        $data = array('hex' => '0x'.$signedTransaction,'apikey' => 'VPCX7N6WHNC8XDYYBD7UWSWGDK8ANJUQDH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $resultado = json_decode(curl_exec($ch), true);
        curl_close($ch);
    }
    
    //funcion para crear transaccion de Ethereum
    public function createEthTransaction($fromAddress, $toAddress, $amount, $privateKey)
    {
       $gasPrice = $this->getGasPrice();
       $nonce = $this->getNonce($fromAddress);
       $monto_ethTowei = $this->converter->toWei($amount, 'ether');

       $transaction = new Transaction([
        'nonce' => '0x'.$this->toHexValue($nonce),
        'from' => $fromAddress,
        'to' => $toAddress,
        'gas' => '0x'.$this->toHexValue(21050),
        'gasPrice' => '0x'.$this->toHexValue($gasPrice),
        'value' => '0x'.$this->toHexValue($monto_ethTowei),
        'gasLimit' => '0x'.$this->toHexValue(21050),
        'chainId' => 1
      ]);

      return $transaction->sign($privateKey);
    }


}

// Crear una instancia de la clase EthereumTransaction
$ethereumTransaction = new EthereumTransaction();


// Crear y procesar una transacción ERC20
$Erc20RawSigned= '0x'.$ethereumTransaction->createErc20Transaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0x1F6Da4454F8395D652a69F6487438BAce3C2e28c', '975.70', '0xdAC17F958D2ee523a2206206994597C13D831ec7', '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

// Crear y procesar una transacción ETH
$EthRawSigned='0x'.$ethereumTransaction->createEthTransaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', 975.70, '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

//Push transacion
echo json_encode($ethereumTransaction->processTransaction($Erc20RawSigned),true);
#echo json_encode($ethereumTransaction->processTransaction($EthRawSigned),true);

?>