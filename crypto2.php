<?php

$json = [
  [
    "Name_main" => "Quicksilver",
    "Name_to_card" => "quicksilver"
  ],
  [
    "Name_main" => "Humans",
    "Name_to_card" => "humans-ai"
  ],
  [
    "Name_main" => "Meme",
    "Name_to_card" => "meme-network"
  ],
  [
    "Name_main" => "Aura",
    "Name_to_card" => "aura-network"
  ],
  [
    "Name_main" => "Solana",
    "Name_to_card" => "solana"
  ],
  [
    "Name_main" => "Lambda",
    "Name_to_card" => "lambda"
  ],
  [
    "Name_main" => "Gitopia",
    "Name_to_card" => "gitopia"
  ],
  [
    "Name_main" => "Arable",
    "Name_to_card" => "arable-protocol"
  ],
  [
    "Name_main" => "Stride",
    "Name_to_card" => "stride"
  ],
  [
    "Name_main" => "Rebus",
    "Name_to_card" => "rebus"
  ],
  [
    "Name_main" => "Umee",
    "Name_to_card" => "umee"
  ],
  [
    "Name_main" => "Evmos",
    "Name_to_card" => "evmos"
  ],
  [
    "Name_main" => "Gravity",
    "Name_to_card" => "gravity-bridge-dai"
  ],
  [
    "Name_main" => "Canto",
    "Name_to_card" => "canto"
  ]
];

$conn = new mysqli("localhost", "sasha7ab_db", "1234sS", "sasha7ab_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

foreach ($json as $item) {
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=' . $item['Name_to_card'] . '&vs_currencies=usd';

    curl_setopt($ch, CURLOPT_URL, $url);

    $result = curl_exec($ch);

    if ($result === false) {
        echo "Error executing cURL request: " . curl_error($ch);
        continue;
    }

    $apiData = json_decode($result, true);

    if ($apiData === null) {
        echo "Error decoding API response.";
        continue;
    }

    $item_name = $item['Name_to_card'];

    if (isset($apiData[$item_name]['usd'])) {
        $price = $apiData[$item_name]['usd'];

        // Get the current date and time
        $currentDateTime = date('Y-m-d H:i:s');

        // Update the query to add the current date
        $stmt = $conn->prepare("UPDATE MainnetPrice SET price2 = ?, date_price2 = ? WHERE token = ?");
        $stmt->bind_param("dss", $price, $currentDateTime, $item_name);

        if ($stmt->execute()) {
            echo "Record updated successfully. Token: " . $item_name . ", Price: " . $price . "\n";
        } else {
            echo "Error updating record: " . $conn->error . "\n";
        }

        $stmt->close();
    } else {
        echo "Invalid API response for token: " . $item_name . "\n";
    }
}

curl_close($ch);

$conn->close();

?>
