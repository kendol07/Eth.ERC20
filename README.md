# Eth.ERC20
Procesar transacciones de Ethereum y tokens ERC20

// Crear y procesar una transacción ERC20
$Erc20RawSigned= '0x'.$ethereumTransaction->createErc20Transaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0x1F6Da4454F8395D652a69F6487438BAce3C2e28c', '975.70', '0xdAC17F958D2ee523a2206206994597C13D831ec7', '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

// Crear y procesar una transacción ETH
$EthRawSigned='0x'.$ethereumTransaction->createEthTransaction('0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', '0xE618C28c20a8F114a6d9515eDb1bDF9398aA9CF1', 0.01, '0x5e823d3e06d82242a3ffff2c3a18bf42bd9f97a5b95ca7e5db9c630467adebf3');

//Push transacion
echo json_encode($ethereumTransaction->processTransaction($Erc20RawSigned),true);
echo json_encode($ethereumTransaction->processTransaction($EthRawSigned),true);
