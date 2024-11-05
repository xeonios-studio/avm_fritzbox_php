<?php
// Funktion zur Ausführung von Aktionen auf der FRITZ!Box über TR-064
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

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "Fehler: " . curl_error($ch);
    }
    curl_close($ch);

    return $response;
}

// Funktion: Gerätinformationen abrufen
function getDeviceInfo($host, $username, $password) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:GetInfo xmlns:u="urn:dslforum-org:service:DeviceInfo:1"/>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:DeviceInfo:1#GetInfo";
    
    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Funktion: FRITZ!Box neustarten
function rebootDevice($host, $username, $password) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:Reboot xmlns:u="urn:dslforum-org:service:DeviceConfig:1"/>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:DeviceConfig:1#Reboot";

    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Funktion: WLAN-Informationen abrufen
function getWLANInfo($host, $username, $password) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:GetInfo xmlns:u="urn:dslforum-org:service:WLANConfiguration:1"/>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:WLANConfiguration:1#GetInfo";

    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Funktion: WLAN aktivieren
function enableWLAN($host, $username, $password, $enable = true) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:SetEnable xmlns:u="urn:dslforum-org:service:WLANConfiguration:1">
                    <NewEnable>' . ($enable ? '1' : '0') . '</NewEnable>
                </u:SetEnable>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:WLANConfiguration:1#SetEnable";

    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Funktion: Internetverbindungsgeschwindigkeit abrufen
function getConnectionSpeed($host, $username, $password) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:GetCommonLinkProperties xmlns:u="urn:dslforum-org:service:WANCommonInterfaceConfig:1"/>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:WANCommonInterfaceConfig:1#GetCommonLinkProperties";

    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Funktion: Internetverbindung neu starten
function reconnectInternet($host, $username, $password) {
    $xml = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <s:Body>
                <u:ForceTermination xmlns:u="urn:dslforum-org:service:WANIPConnection:1"/>
            </s:Body>
        </s:Envelope>';
    $action = "urn:dslforum-org:service:WANIPConnection:1#ForceTermination";

    return sendTR064Request($host, $username, $password, $action, $xml);
}

// Beispiel zur Nutzung der Funktionen
// $host = 'fritz.box'; // oder IP-Adresse
// $username = 'admin';
// $password = 'passwort';

// Gerätinformationen abrufen
// $deviceInfo = getDeviceInfo($host, $username, $password);
// echo "<pre>" . htmlspecialchars($deviceInfo) . "</pre>";

// FRITZ!Box neustarten
// rebootDevice($host, $username, $password);

// WLAN-Status abrufen
// $wlanInfo = getWLANInfo($host, $username, $password);
// echo "<pre>" . htmlspecialchars($wlanInfo) . "</pre>";

// WLAN aktivieren/deaktivieren
// enableWLAN($host, $username, $password, true);

// Verbindungsgeschwindigkeit abrufen
// $speed = getConnectionSpeed($host, $username, $password);
// echo "<pre>" . htmlspecialchars($speed) . "</pre>";

// Internetverbindung neu starten
// reconnectInternet($host, $username, $password);
?>
