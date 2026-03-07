const fs = require("fs");
const path = require("path");

const ART_MAP = {
  "Unterricht und Praxis": "U/P",
  "Unterricht": "U",
  "Praxis": "P",
  "Sonstige Veranstaltungen": "S",
  "Katastrophenschutz": "KatS",
};

const parseDateTime = (value) => {
  // Strip TZID param: "TZID=Europe/Berlin:20260107T190000" or bare "20260107T190000"
  const raw = value.includes(":") ? value.split(":").pop() : value;
  const date = raw.slice(0, 8);   // "20260107"
  const time = raw.slice(9, 15);  // "190000"
  return { date, time };
};

const formatDate = (icsDate) => {
  // "20260107" → "07.01.2026"
  const y = icsDate.slice(0, 4);
  const m = icsDate.slice(4, 6);
  const d = icsDate.slice(6, 8);
  return `${d}.${m}.${y}`;
};

const formatTimeVon = (icsTime) => {
  // "190000" → "19:00:00"
  return `${icsTime.slice(0, 2)}:${icsTime.slice(2, 4)}:${icsTime.slice(4, 6)}`;
};

const formatTimeBis = (icsTime) => {
  // "213000" → "21:30" (no seconds)
  return `${icsTime.slice(0, 2)}:${icsTime.slice(2, 4)}`;
};

const calcDauer = (startTime, endTime) => {
  // Both in "HHMMSS" format
  const toMinutes = (t) => parseInt(t.slice(0, 2)) * 60 + parseInt(t.slice(2, 4));
  const diff = toMinutes(endTime) - toMinutes(startTime);
  if (diff <= 0) return "";
  const h = Math.floor(diff / 60);
  const m = diff % 60;
  return `${h}:${String(m).padStart(2, "0")}`;
};

const parseDescription = (desc) => {
  // "FwDV 1 | Art: Praxis" or "Art: Sonstige Veranstaltungen"
  let fwdv = "";
  let art = "";

  if (desc.includes("|")) {
    const [left, right] = desc.split("|").map((s) => s.trim());
    if (left.startsWith("FwDV")) fwdv = left;
    const artMatch = right.match(/Art:\s*(.+)/);
    if (artMatch) art = artMatch[1].trim();
  } else {
    const artMatch = desc.match(/Art:\s*(.+)/);
    if (artMatch) art = artMatch[1].trim();
  }

  return { fwdv, art: ART_MAP[art] ?? art };
};

const parseIcs = (icsFilePath) => {
  const raw = fs.readFileSync(icsFilePath, "utf-8");

  // Unfold RFC 5545 line continuations (CRLF + whitespace)
  const unfolded = raw.replace(/\r?\n[ \t]/g, "");
  const lines = unfolded.split(/\r?\n/);

  const events = [];
  let current = null;

  for (const line of lines) {
    if (line === "BEGIN:VEVENT") {
      current = {};
      continue;
    }
    if (line === "END:VEVENT") {
      if (current) events.push(current);
      current = null;
      continue;
    }
    if (!current) continue;

    // Split on first ":" — property name may include params separated by ";"
    const colonIdx = line.indexOf(":");
    if (colonIdx === -1) continue;
    const key = line.slice(0, colonIdx);
    const value = line.slice(colonIdx + 1);

    // Normalize key: use base name (before ";")
    const baseName = key.split(";")[0];
    current[baseName] = value;
  }

  return events;
};

const convertIcsToRoster = (icsFilePath) => {
  const events = parseIcs(icsFilePath);
  const roster = [];

  for (const ev of events) {
    const dtstart = ev["DTSTART"] || "";
    const dtend = ev["DTEND"] || "";
    if (!dtstart || !dtend) continue;

    const start = parseDateTime(dtstart);
    const end = parseDateTime(dtend);
    const { fwdv, art } = parseDescription(ev["DESCRIPTION"] || "");

    roster.push({
      Datum: formatDate(start.date),
      von: formatTimeVon(start.time),
      bis: formatTimeBis(end.time),
      Thema: ev["SUMMARY"] || "",
      FwDV: fwdv,
      Art: art,
      Dauer: calcDauer(start.time, end.time),
      "Ortsteil-Feuerwehr": ev["LOCATION"] || "",
    });
  }

  // Sort by date ascending
  roster.sort((a, b) => {
    const toIso = (d) => d.split(".").reverse().join("-");
    return toIso(a.Datum).localeCompare(toIso(b.Datum));
  });

  return roster;
};

// --- CLI ---
const inputFile = process.argv[2];
const outputFile =
  process.argv[3] ||
  path.resolve(__dirname, "../src/utils/rosters/eAbtRoster.json");

if (!inputFile) {
  console.error("Usage: node icsToJsonConverter.js <input.ics> [output.json]");
  process.exit(1);
}

const roster = convertIcsToRoster(inputFile);
fs.writeFileSync(outputFile, JSON.stringify(roster, null, 2));
console.log(`Converted ${roster.length} events → ${outputFile}`);
