// Writes dist/sitemap.xml after `vite build` (see "build" in package.json).
//
// Lists the fixed pages of the Vue app plus every news report and alarm
// from the WordPress API. When WordPress cannot be reached during the
// build, the sitemap only contains the fixed pages; the build never fails
// because of it.
const fs = require("fs");
const path = require("path");

const SITE_URL = "https://www.feuerwehr-traisa.de";
const API = "https://wordpress.feuerwehr-traisa.de/wp-json/types/v1/";
const FIRST_ALARM_YEAR = 2013;
const OUT = path.join(__dirname, "..", "dist", "sitemap.xml");

// Pages of the Vue app (src/router/index.js) and the WordPress pages they
// link to permanently.
const STATIC_PAGES = [
  ["/", "weekly", "1.0"],
  ["/einsatzabteilung/einsatze", "weekly", "0.9"],
  ["/termine", "weekly", "0.8"],
  ["/seite/verein", "monthly", "0.8"],
  ["/einsatzabteilung", "monthly", "0.8"],
  ["/einsatzabteilung/dienstplan", "monthly", "0.6"],
  ["/kindergruppen", "monthly", "0.8"],
  ["/kindergruppen/wichtelwehr/info", "monthly", "0.6"],
  ["/kindergruppen/wichtelwehr/uebungsplanwichtel", "monthly", "0.5"],
  ["/kindergruppen/jugendfeuerwehr/info-2", "monthly", "0.6"],
  ["/kindergruppen/jugendfeuerwehr/uebungsplan", "monthly", "0.5"],
  ["/technik", "monthly", "0.7"],
  ["/technik/kdow", "yearly", "0.5"],
  ["/technik/mtf", "yearly", "0.5"],
  ["/technik/lf-106", "yearly", "0.5"],
  ["/technik/mlf", "yearly", "0.5"],
  ["/technik/anhaenger", "yearly", "0.5"],
  ["/technik/rettungsboot", "yearly", "0.5"],
  ["/technik/feuerwehrhaus", "yearly", "0.5"],
  ["/archiv", "weekly", "0.6"],
  ["/kontakt", "yearly", "0.5"],
  ["/impressum", "yearly", "0.2"],
];

async function getJson(endpoint) {
  const response = await fetch(API + endpoint, {
    signal: AbortSignal.timeout(20000),
  });
  if (!response.ok) throw new Error(`${endpoint}: HTTP ${response.status}`);
  return response.json();
}

// "2026-10-07 15:59:41" -> "2026-10-07"
const day = (wpDate) => (wpDate ? String(wpDate).slice(0, 10) : null);

async function dynamicPages() {
  const pages = new Map();
  const add = (loc, lastmod, changefreq, priority) => {
    if (!pages.has(loc)) pages.set(loc, { loc, lastmod, changefreq, priority });
  };

  // News reports: the ones on the start page and the archived ones.
  const [current, archive] = await Promise.all([
    getJson("getIndexInfo/"),
    getJson("getArchive/"),
  ]);
  for (const post of [...current, ...archive]) {
    add(`/seite/${post.post_name}`, day(post.post_modified), "yearly", "0.6");
  }

  // Alarms, year by year.
  const thisYear = new Date().getFullYear();
  for (let year = thisYear; year >= FIRST_ALARM_YEAR; year--) {
    const alarms = await getJson(`getAllAlarmsFromYear/?year=${year}`);
    if (!Array.isArray(alarms)) continue;
    for (const alarm of alarms) {
      add(
        `/einsatzabteilung/einsatze/${alarm.post_name}`,
        day(alarm.post_modified),
        "yearly",
        "0.4",
      );
    }
  }
  return [...pages.values()];
}

const escapeXml = (text) =>
  text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

function toXml(entries) {
  const urls = entries
    .map(({ loc, lastmod, changefreq, priority }) =>
      [
        "  <url>",
        `    <loc>${escapeXml(SITE_URL + loc)}</loc>`,
        lastmod ? `    <lastmod>${lastmod}</lastmod>` : null,
        `    <changefreq>${changefreq}</changefreq>`,
        `    <priority>${priority}</priority>`,
        "  </url>",
      ]
        .filter(Boolean)
        .join("\n"),
    )
    .join("\n");
  return (
    '<?xml version="1.0" encoding="UTF-8"?>\n' +
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
    urls +
    "\n</urlset>\n"
  );
}

async function main() {
  const entries = STATIC_PAGES.map(([loc, changefreq, priority]) => ({
    loc,
    lastmod: null,
    changefreq,
    priority,
  }));
  try {
    entries.push(...(await dynamicPages()));
  } catch (error) {
    console.warn(
      `Sitemap: WordPress not reachable (${error.message}); writing the fixed pages only.`,
    );
  }
  fs.mkdirSync(path.dirname(OUT), { recursive: true });
  fs.writeFileSync(OUT, toXml(entries));
  console.log(`Sitemap: ${entries.length} URLs written to dist/sitemap.xml`);
}

main();
