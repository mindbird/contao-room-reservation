# Contao Room Reservation

Eine Contao-Erweiterung zur Reservierung von Raeumen. Buchungen werden als
Kalenderereignisse gespeichert und stehen dadurch unmittelbar als Belegung im
zugeordneten Kalenderarchiv zur Verfuegung.

## Voraussetzungen

- PHP 8.3 oder hoeher
- Contao 5.7
- Contao Calendar Bundle
- Terminal42 Notification Center 2.7 oder hoeher

## Installation

Die Erweiterung im Stammverzeichnis der Contao-Installation installieren:

```bash
composer require mindbird/contao-room-reservation
```

Anschliessend den Contao-Installationsvorgang ausfuehren, damit die
Datenbankstruktur aktualisiert wird:

```bash
vendor/bin/contao-console contao:migrate
```

## Einrichtung

1. Im Backend unter **Inhalte > Kalender** ein Kalenderarchiv fuer den Raum anlegen. Jede erfolgreiche Buchung wird dort als Kalenderereignis gespeichert.
2. Eine Zielseite anlegen, auf die nach einer Buchung weitergeleitet wird. Sie sollte ein Kalendermodul enthalten, das dasselbe Kalenderarchiv ausgibt.
3. Ein Frontend-Modul vom Typ **Raumreservierung** anlegen und auf der gewuenschten Seite einbinden.
4. Im Modul das Kalenderarchiv, die Buchungszeiten, die Mindestbuchungsdauer und die Zielseite festlegen.
5. Optional eine AGB-Seite und eine Notification-Center-Benachrichtigung auswaehlen.

## Moduloptionen

| Option | Beschreibung |
| --- | --- |
| Kalenderarchiv | Kalender, in dem die Buchungen als Ereignisse angelegt werden. |
| Buchungszeitraum | Frueheste Start- und spaeteste Endzeit; das Formular bietet Viertelstunden-Schritte an. |
| Zeit zwischen Buchungen | Sperrzeit in Minuten nach einer bestehenden Buchung. |
| Mindestbuchungsdauer | Kleinste zulaessige Buchungsdauer in Minuten. |
| Zielseite | Seite, auf die nach einer erfolgreichen Buchung weitergeleitet wird. |
| AGB-Seite | Optionaler Link, den Buchende im Formular bestaetigen muessen. |
| Wiederholung | Buchungen koennen woechentlich wiederholt werden. |
| Preisoptionen | Konfiguriert Preise fuer Stunde, Tag, halbe Stunde, halben Tag und Abend im Formular. |

Das Formular prueft die Verfuegbarkeit gegen vorhandene Kalenderereignisse im
ausgewaehlten Archiv. Nicht verfuegbare Termine werden nicht gespeichert.

## Benachrichtigungen

Fuer eine Buchungsbestaetigung im Notification Center den Typ
`room_reservation_booking_confirmation` verwenden. Folgende Tokens stehen zur
Verfuegung:

| Token | Inhalt |
| --- | --- |
| `##room_start_date##` | Beginn der Buchung |
| `##room_end_date##` | Ende der Buchung |
| `##room_repeat##` | Ob es sich um eine wiederholte Buchung handelt |
| `##room_repeat_times##` | Anzahl der woechentlichen Wiederholungen |
| `##room_event_title##` | Titel der Veranstaltung |
| `##member_email##` | E-Mail-Adresse des angemeldeten Frontend-Mitglieds, sofern vorhanden |


## Lizenz

LGPL-3.0-or-later. Siehe [GNU LGPL 3.0](https://www.gnu.org/licenses/lgpl-3.0.html).
