# Writing CMS pages (Verein, Einsatzabteilung, Nachwuchs, …)

Every WordPress page shown under `/seite/…` and the other CMS routes goes
through `frontend/src/components/cms/CmsContent.vue`. It turns normal editor
content into the site's layout, as long as a few conventions are followed.
No Types fields are involved.

Work in the editor's **"Text"** tab when restructuring; inline fonts and sizes
(`tahoma`, `10pt`, colours) are removed by the frontend anyway, so they don't
need to be set.

| In the editor | On the site |
|---|---|
| Text before the first heading | Intro. On pages with H2 sections, the first paragraph is a large italic lead. |
| **Überschrift 2** (`<h2>`) | Starts a section: bold heading with a dotted red line. |
| **Überschrift 3** (`<h3>`), two or more inside one section | Each H3 and the text below it becomes a card; the cards sit side by side. |
| An image with a caption (`[caption]`) in a section that also has text | Picture on the left, text on the right (e.g. Vorstand). |
| Numbered list (`<ol>`) | Red numbered steps. |
| A paragraph that only contains links to PDFs | Download buttons. Text in the same paragraph shows above (if it comes first) or below them. |
| A PDF link inside running text | Link with a small download icon. |
| `IBAN: DE.. .... ...` in its own paragraph, optionally followed by `BIC: ...` | Bank box with a "Kopieren" button. |

Pages without H2 headings are shown as one block of clean text, so existing
pages work without changes.

## Example: Verein (page 59)

```html
Der Verein "Freiwillige Feuerwehr Traisa e.V." unterstützt …

<h2>Was wir fördern</h2>
<h3>Ausrüstung &amp; Fahrzeuge</h3>
Dies geschieht zum einen durch …
<h3>Nachwuchsarbeit</h3>
Zum anderen legen wir …

<h2>Mitglied werden</h2>
Werden Sie Mitglied …
<ol>
  <li><a href="…/Beitrittserklärung-FFW-Traisa.pdf">Beitrittserklärung herunterladen (PDF)</a></li>
  <li><a href="…/SEPA-Basislastschrift-Mandat-FFW-Traisa.pdf">Sepa Einzugsermächtigung herunterladen (PDF)</a></li>
  <li>Beide Dokumente ausgefüllt in den Briefkasten der Feuerwehr werfen …</li>
</ol>

<h2>Spenden</h2>
IBAN: DE46 5085 0150 0022 0003 73

BIC: HELADEF1DAS

<h2>Vorstand</h2>
[caption …]<img … /> Vereinsvorsitzende: Daniela Richter &amp; Dieter Scheider[/caption]
Kontakt über <a href="mailto:verein@feuerwehr-traisa.de">verein@feuerwehr-traisa.de</a>
```

The Verein page in WordPress was restructured this way on 28.10.2026 (wording
unchanged; the previous version is in the page's WordPress revisions).

**Note:** the site no longer shows that WordPress content. `/seite/verein` is
a static page (`frontend/src/views/Verein.vue`) so it can match the design
exactly; Vorstand, forms and bank details are changed there in code.
