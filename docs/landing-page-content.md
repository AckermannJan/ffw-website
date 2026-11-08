# Landing page: where the content comes from

Everything on the start page comes from WordPress through
`wpTheme/functions.php`, except the founding year and the Wichtelwehr year
(`frontend/src/utils/constants.js`).

## Maintained in wp-admin

| On the page | Where to change it |
|---|---|
| Hero photo | Private page **"Startseite"** (ID 4428) › Types field "Start Bild" (or its "Beitragsbild"). Alt text: in the media library. |
| "Mitmachen" box (sidebar) | **"Textauszug"** of the pages Wichtelwehr (`info`, ID 66), Jugendfeuerwehr (`info-2`, ID 90) and Einsatzabteilung (`einsatzabteilung`, ID 58). A page without an excerpt is left out of the box. |
| News cards (Aktuelles) | Posts/pages with "Auf der Startseite sichtbar?" ticked. Picture: "Start Bild". Teaser: "Textauszug", or else the first words of the content. |
| Date on a news card | Types field **"Veranstaltungsdatum"** (group "Startseiten Infos"). Empty → the publish date is shown. |
| Label on a news card ("VEREIN") | Optional Types field `kategorie`; otherwise the title of the parent page. |
| Alt text of news pictures | Media library › image › "Alternativtext". |

The excerpt box only shows in the editor if it is enabled under
"Ansicht anpassen" (top right).

## Calculated automatically

| On the page | Source |
|---|---|
| "Letzter Einsatz" incl. Einsatzart | Newest alarm post, Types field `einsatzart` |
| "Einsatz 27" in the sidebar | Number taken from the alarm title ("Einsatz 27 (2026) …") |
| "… Einsätze bisher im Jahr" | Alarm posts whose `alarmierungszeitpunkt` is in the current year. An alarm without a post (e.g. Einsatz 8 in 2026) is not counted. |
| Termine | Next three "Termin" posts (`zeitpunkt`) |

## API fields (wpTheme)

- `getIndexInfo`, per post: `startBildAlt`, `excerpt`, `kategorie`, `veranstaltungsdatum`
- `getSidebarInfo`: `latestAlarm.einsatzart`, `alarmCountThisYear`, `hero`, `mitmachen`

Every Types lookup in these fields is guarded (`ffw_field()`), so an
unconfigured field returns `''` instead of a fatal error. Types is the free,
unmaintained version, so it's used as little as possible.
