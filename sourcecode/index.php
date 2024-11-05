<?php
// Fehlerbericht aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Funktion zur Ausführung von Aktionen auf der FRITZ!Box
function sendTR064Request($host, $username, $password, $action, $xml) {
    $url = "http://$host:49000/upnp/control/deviceconfig";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'SOAPACTION: ' . $action,
        'Content-Type: text/xml; charset="utf-8"',
        'Content-Length: ' . strlen($xml),
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

    // SSL-Zertifikatprüfung deaktivieren (nur für lokale Netzwerke oder Testzwecke)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "Fehler: " . curl_error($ch);
    }
    curl_close($ch);

    return $response;
}

// Initialisiert Variablen
$response = "";
$host = "";
$username = "";
$password = "";

// Falls eine Funktion ausgewählt wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $action = $_POST['action'];

    switch ($action) {
        case 'getDeviceInfo':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetDeviceInfo xmlns:u="urn:dslforum-org:service:DeviceInfo:1"/>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:DeviceInfo:1#GetDeviceInfo";
            break;

        case 'reboot':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:Reboot xmlns:u="urn:dslforum-org:service:DeviceConfig:1"/>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:DeviceConfig:1#Reboot";
            break;

        case 'getWANStatus':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetWANCommonInterfaceConfig xmlns:u="urn:dslforum-org:service:WANCommonInterfaceConfig:1"/>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:WANCommonInterfaceConfig:1#GetWANCommonInterfaceConfig";
            break;

        case 'getWLANConfig':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetWLANConfiguration xmlns:u="urn:dslforum-org:service:WLANConfiguration:1">
                            <NewEnable>1</NewEnable>
                        </u:GetWLANConfiguration>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:WLANConfiguration:1#GetWLANConfiguration";
            break;

        case 'getHosts':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetHostListByType xmlns:u="urn:dslforum-org:service:Hosts:1">
                            <NewType>2</NewType> <!-- 1=Local, 2=Remote, 3=All -->
                        </u:GetHostListByType>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:Hosts:1#GetHostListByType";
            break;

        // Weitere Funktionen hinzufügen
        case 'getWLANStatus':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetWLANStatus xmlns:u="urn:dslforum-org:service:WLANConfiguration:1"/>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:WLANConfiguration:1#GetWLANStatus";
            break;

        case 'getConnectedDevices':
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <s:Body>
                        <u:GetListOfEntries xmlns:u="urn:dslforum-org:service:Host:1"/>
                    </s:Body>
                </s:Envelope>';
            $soapAction = "urn:dslforum-org:service:Host:1#GetListOfEntries";
            break;

        default:
            $response = "Unbekannte Aktion ausgewählt.";
            break;
    }

    if (isset($soapAction) && isset($xml)) {
        // Anfrage senden und Antwort erhalten
        $response = sendTR064Request($host, $username, $password, $soapAction, $xml);
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>FRITZ!Box Verwaltung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .panel {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 20px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .panel h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .panel form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .panel label {
            font-weight: bold;
            margin: 10px 0 5px;
            text-align: left;
        }
        .panel input[type="text"],
        .panel input[type="password"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .panel button {
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 10px;
        }
        .panel button:hover {
            background-color: #0056b3;
        }
        .response-panel {
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            text-align: left;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .response-panel h2 {
            font-size: 18px;
            color: #007bff;
            margin-top: 0;
        }
        .functions {
            margin-top: 20px;
        }
        .functions button {
            width: 100%;
            background-color: #28a745;
            margin-bottom: 10px;
        }
        .functions button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="panel">
        <h1>Login zur FRITZ!Box</h1>
        <form method="post">
            <label for="host">FRITZ!Box-Adresse:</label>
            <input type="text" id="host" name="host" required placeholder="z.B. 192.168.178.1" value="<?= htmlspecialchars($host ?? '') ?>">

            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">

            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="action" value="login">Einloggen</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] != 'login'): ?>
            <div class="response-panel">
                <h2>Antwort der FRITZ!Box</h2>
                <pre><?= htmlspecialchars($response) ?></pre>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login'): ?>
            <div class="response-panel">
                <h2>Login-Status</h2>
                <?php
                // Prüfe, ob die Antwort eine erfolgreiche Authentifizierung enthält
                if (strpos($response, 'SOAP-ENV:Envelope') !== false) {
                    echo "<p>Login erfolgreich!</p>";
                } else {
                    echo "<p>Login fehlgeschlagen. Bitte überprüfe deine Zugangsdaten.</p>";
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] != 'login'): ?>
            <div class="functions">
                <form method="post" action="fritzbox_functions.php">
                    <input type="hidden" name="host" value="<?= htmlspecialchars($host ?? '') ?>">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($username ?? '') ?>">
                    <input type="hidden" name="password" value="<?= htmlspecialchars($password ?? '') ?>">
                    
                    <button type="submit" name="action" value="getDeviceInfo">Geräteinformationen abrufen</button>
                    <button type="submit" name="action" value="reboot">FRITZ!Box neu starten</button>
                    <button type="submit" name="action" value="getWANStatus">WAN-Status abrufen</button>
                    <button type="submit" name="action" value="getWLANConfig">WLAN-Konfiguration abrufen</button>
                    <button type="submit" name="action" value="getHosts">Verbunden Geräte anzeigen</button>
                    <button type="submit" name="action" value="getWLANStatus">WLAN-Status abrufen</button>
                    <button type="submit" name="action" value="getConnectedDevices">Verbunden Geräte abrufen</button>
                    <!-- Weitere Buttons für zusätzliche Funktionen -->
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
