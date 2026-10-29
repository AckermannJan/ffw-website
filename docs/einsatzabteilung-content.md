# Einsatzabteilung: Zahlen und Kontakt im CMS pflegen

Die Seite `/einsatzabteilung` zeigt Kennzahlen und die Wehrführung aus
WordPress. Gepflegt werden sie in **Seiten → Einsatzabteilung**, im
Feldbereich **Einsatzabteilung** unter dem Editor.

| Feld | Slug | Typ | Anzeige auf der Website |
|---|---|---|---|
| Aktive Mitglieder | `abt-aktive` | Zahl | „37 ehrenamtliche Aktive“ |
| Feuerwehrfrauen | `abt-feuerwehrfrauen` | Zahl | „14 Feuerwehrfrauen“ |
| Einsätze pro Jahr | `abt-einsaetze-pro-jahr` | Einzelne Zeile | Leer lassen: wird aus den Einsätzen der letzten drei vollen Jahre berechnet (z. B. „37–45“). Ausfüllen überschreibt den Wert. |
| Wehrführung | `abt-wehrfuehrung` | Einzelne Zeile | Namen, z. B. „Nicolas Richter & Daniel Funk“ |
| Wehrführung E-Mail | `abt-wehrfuehrung-email` | E-Mail | Kontakt-Button |
| Wehrführung Foto | `abt-wehrfuehrung-bild` | Bild | Hochformat, wird 4:5 zugeschnitten. Alternativtext in der Mediathek pflegen. |

Leere Felder: die Website zeigt dann ihre eingebauten Werte. „17–60 Jahre
aktiver Dienst“ ist gesetzlich festgelegt und steht fest im Code.

API: `GET /wp-json/types/v1/getEinsatzabteilung/` (siehe `wpTheme/functions.php`).
Die Einsatzzahlen pro Jahr werden 12 Stunden zwischengespeichert und beim
Speichern eines Einsatzes neu berechnet.

## Nach dem Deploy (einmalig)

Toolset Types (kostenlos) kann eine Feldgruppe nicht auf eine einzelne Seite
beschränken, wohl aber auf eine Seitenvorlage. Bis dahin erscheint die
Gruppe auf allen Seiten.

1. **Seiten → Einsatzabteilung → Seiten-Attribute → Template:**
   „Einsatzabteilung“ wählen und aktualisieren.
2. **Toolset → Beitragsfeldgruppen → Einsatzabteilung → Erscheint auf → Edit
   → Vorlagen:** „Einsatzabteilung“ ankreuzen, übernehmen, Feldgruppe
   speichern.
