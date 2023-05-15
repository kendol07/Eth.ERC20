<?php
require(__DIR__.'/../../vendor/autoload.php');

use Web3p\EthereumTx\Transaction;
use Bezhanov\Ethereum\Converter;
use Binance\Utils;
use Binance\Formatter;

$utils = new Utils();

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


}?>