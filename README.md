# FRITZ!Box Control Interface

Ein PHP-basiertes Interface, um grundlegende Steuerungen und Informationen von einer FRITZ!Box über die TR-064-API abzurufen. Diese Anwendung ermöglicht das Abrufen von Geräteinformationen, die Steuerung der Internetverbindung, WLAN-Management und mehr.

## Funktionen

- **Geräteinformationen abrufen**: Holen Sie grundlegende Informationen zu Ihrer FRITZ!Box ab.
- **Neustart der FRITZ!Box**: Startet die FRITZ!Box neu.
- **WLAN-Status abrufen und steuern**: Sehen Sie den aktuellen WLAN-Status und aktivieren/deaktivieren Sie das WLAN.
- **Verbindungsgeschwindigkeit abrufen**: Erhalten Sie die Internetverbindungsgeschwindigkeit.
- **Internetverbindung neu starten**: Trennt und stellt die Internetverbindung wieder her.

## Voraussetzungen

- Ausführung im gleichen lokal-Netzwerk.
- PHP-Server unter Windows mit cURL-Unterstützung
- Zugriff auf die FRITZ!Box und Aktivierung der TR-064-API (Zugangsdaten erforderlich)
- Konfiguration der FRITZ!Box für den Zugriff über TR-064:
  - Aktivieren Sie die **Heimnetzfreigabe** und die **API**-Nutzung unter "Heimnetz > Netzwerk > Netzwerkeinstellungen > Zugriff für Anwendungen"

## Installation

1. Klonen Sie das Repository:

   ```bash
   git clone https://github.com/xeonios-studio/avm_fritzbox_php/.git
   cd fritzbox-control-interface
