# Backend Bewerbung

## Willkommen

Vielen Dank für deine Bewerbung bei uns! Um die praktischen Fähigkeiten unserer Bewerber zu prüfen, muss jeder unserer Bewerber die anschließende
Aufgabe bearbeiten. Diese beschreibt einen Ablauf von Aufgaben mit welchen wir uns jeden Tag beschäftigen. Neben den meisten unserer Wettbewerber
zeichnen wir uns dadurch aus, dass wir nicht nur einen Onlineshop, sondern auch direkt unsere eigene Produktion betreiben.

Dadurch ergeben sich für uns immense Vorteile in der Optimierung unserer Produktionsanbindung, jedoch auch eine deutlich größere Komplexität. In der
folgenden Aufgabe beschäftigen wir uns mit einer sehr abstrakten Form eines Produktlebenszyklus.

Die Aufgabe ist so ausgelegt, dass sie in ungefähr 3-4 Stunden abgeschlossen werden kann. Bitte nimm dir hierfür ausreichend Zeit, um dein
bestmögliches Ergebnis einzureichen.

Um deine vollendete Aufgabe einzureichen, schicke uns bitte eine `.zip` Datei, welche den gesamten Quellcode beinhaltet.

## Aufgabenbeschreibung

Die Aufgabe besteht aus insgesamt vier Teilen mit unterschiedlicher Komplexität. Zu Beginn wird ein Prozessmanager implementiert, welcher die Zustände
eines Produktes während der Produktion verwaltet. Im zweiten Teil wird ein Logger implementiert, welcher für den Prozessmanager genutzt werden kann.
Im dritten Schritt wird eine Adressvalidierung für den Versand des Produktes implementiert und im vierten Teil wird ein Datenbankschema optimiert.

Für alle Aufgaben gilt:

* Die Grundstruktur der Aufgaben sollte beibehalten werden
* Es können alle mitgelieferten Dateien bearbeitet werden
* Es können neue Dateien hinzugefügt werden
* Alle mitgelieferten Klassen, Methoden, Variablen, usw. können erweitert oder angepasst werden.
* Unit Tests können erweitert oder angepasst werden

### Teil 1 - Prozessmanager

Jedes Produkt, das bei uns im Onlineshop bestellt wird, muss durch unsere Produktion laufen, bis es am Ende zum Kunden verschickt werden kann. Für die
meisten Artikel besteht der Produktionsprozess aus mehreren Schritten, die in einer bestimmten Reihenfolge aufeinander folgen und nur wenn ein Schritt
erfolgreich abgeschlossen wurde, kann der darauffolgende Schritt auch starten.

Für diese Aufgabe schauen wir uns beispielhaft zwei Produkte an:

- Gerahmtes Poster
- Bedruckte Glasplatte

Zusätzlich kann der Kunde bei der Bestellung für beide Produkte wählen, ob diese mit einer Geschenkverpackung versehen werden sollen oder nicht.
Sollte der Kunde diese Option wählen, so wird im Produktionsablauf ein weiterer (optionaler) Schritt ausgeführt. Damit sehen die Produktionsabläufe
der beiden Produkte wie folgt aus:

| Produkt              | Zustände                                                                                 |
|----------------------|------------------------------------------------------------------------------------------|
| Gerahmtes Poster     | - Ordered<br>- Printed<br>- Sliced<br>- Framed<br>- Gift-Wrapped (Optional)<br>- Shipped |
| Bedruckte Glasplatte | - Ordered<br>- Printed<br>- Gift-Wrapped (Optional)<br>- Shipped                         |

Die Produkte werden manuell von einem Schritt zum nächsten befördert, wodurch es vorkommen kann, dass ein Artikel einen falschen Zustand erhält. Die
Einhaltung der korrekten Reihenfolge der Zustände ist jedoch verpflichtend, weshalb eine entsprechende Fehlermeldung geworfen werden muss.

Für diese Aufgabe ist bereits eine Grundstruktur im Ordner `src/Production/*` gegeben. Im Ordner `tests/Production` befindet sich die
Datei `ArticleTest.php` um den Prozessmanager zu testen.

### Teil 2 - Logger

Für den Fall, dass ein Artikel nicht wie gewünscht produziert wurde, ist es notwendig nachverfolgen zu können was bisher passiert ist. Hierfür möchten
wir einen Logger implementieren, der entsprechend die Änderungen mitschreibt und uns alle notwendigen Informationen gibt einen entsprechenden Fehler
zu finden. Der Logger soll im Prozessmanager aus Teil 1 genutzt werden.

Wenn für einen Artikel die Option "Geschenkverpackung" aktiviert wird, soll ein `debug` Eintrag geloggt werden. Für den Fall, dass ein Artikel in
einem invaliden Zustand ist, soll zusätzlich zur Exception auch ein entsprechender `error` Eintrag erstellt werden.

Anforderungen für den Logger:

- Der Logger soll verschiedene Prioritäten unterstützen (debug, info, warn, error, ...)
- Eine Exception soll mit mehr Informationen geloggt werden als die normalen Log-Einträge, um späteres debuggen zu ermöglichen
- Für jeden Logeintrag soll zusätzlich die aktuelle Uhrzeit und Priorität mitgespeichert werden
- Der Logger soll mindestens zwei verschiedene Speicherarten unterstützen, z.B. Konsole, Datenbank oder Filesystem.
- Der Logger soll zur Laufzeit jeweils nur eine der beiden Speicherarten verwenden
- Das Abspeichern selbst muss nicht implementiert werden (Testen ob Datenbank/Datei existiert usw.)
- Die Funktionalität des Loggers soll durch PHPUnit-Tests abgedeckt sein. Die Speicherarten müssen nicht getestet werden, da deren Implementierung
  nicht notwendig ist.
- Die Grundprinzipien von OOP sollen eingehalten werden.

### Teil 3 - Versand

Für den Versand unserer Produkte ist es notwendig die Adressen unserer Kunden zu validieren. Sind die Adressen nicht korrekt, können die Pakete nicht
zugestellt werden und werden vom Versanddienstleister zurückgesendet.

Hierfür werden von einer API entsprechend die Kundendaten bezogen um sie danach validieren zu können.

Implementiere in der Klasse `Shop\Shipping\AddressValidator` eine Methode um die Adressdaten von der Klasse `Shop\API\CustomerDataApiMock` zu
beziehen. Danach soll jeweils die Straße des Kunden in Straßenname und Hausnummer getrennt werden. Die Straßen sollen wie folgt getrennt werden:

```
Name: Einsteinstr., Hausnummer: 7
Name: Einsteinstrasse, Hausnummer: 7
Name: Curd-Jürgens-Str., Hausnummer: 30
...
```

Eine weitere Validierung der Adressen ist für diese Aufgabe nicht notwendig. Im Ordner `tests/Shipping` befindet sich die Datei `AddressTest.php` um
die geteilten Adressen zu prüfen.

### Teil 4 - Datenbank

In der Datei `database\database.sqlite` bzw. `database\database.sql` befindet sich eine Datenbank mit einer Tabelle `orders` und Beispieldaten. Diese
beinhaltet Bestellungen von Kunden und die dazugehörigen Daten. Mit der aktuellen Datenbankstruktur lassen sich nur schwer Statistiken über
Bestellungen berechnen.

| Spaltenname      | Beschreibung                                                        |
|------------------|---------------------------------------------------------------------|
| customer_id      | Die Kundenummer des Kunden                                          |
| customer_name    | Der Name des Kunden                                                 |
| delivery_address | Die Adresse wo die Bestellung hingeschickt werden soll              |
| invoice_address  | Die Adresse für die Rechnung                                        |
| order            | Eine Übersicht über alle bestellten Produkte und ihre Eigenschaften |
| order_date       | Das Datum der Bestellung                                            |
| order_status     | Der aktuelle Status der Bestellung                                  |

Ziel dieser Aufgabe ist es, die Datenbankstruktur soweit wie möglich nach aktuellen Datenbankstandards zu optimieren, sodass zusammengehörige Daten in
entsprechenden Tabellen zu finden sind. Für diese Aufgabe kann entweder auf Basis der `*.sqlite` oder der `*.sql` Datei gearbeitet werden.

Zusätzlich zur neuen Struktur sollen hier auch die vorhandenen Daten vom alten in das neue Format migriert werden.
