// Builds a one-event iCalendar file in the browser, so a duty can be added
// to a phone or desktop calendar without any backend.

const pad = (n) => String(n).padStart(2, "0");

// Floating local time ("20261104T190000"): calendar apps show it at that
// clock time, which is what the roster means.
const localStamp = (date) =>
  `${date.getFullYear()}${pad(date.getMonth() + 1)}${pad(date.getDate())}` +
  `T${pad(date.getHours())}${pad(date.getMinutes())}00`;

const escapeText = (text = "") =>
  text
    .replace(/\\/g, "\\\\")
    .replace(/\n/g, "\\n")
    .replace(/,/g, "\\,")
    .replace(/;/g, "\\;");

export function buildIcsEvent({ title, start, end, location, description }) {
  const now = new Date();
  const stamp =
    `${now.getUTCFullYear()}${pad(now.getUTCMonth() + 1)}${pad(now.getUTCDate())}` +
    `T${pad(now.getUTCHours())}${pad(now.getUTCMinutes())}${pad(now.getUTCSeconds())}Z`;
  const lines = [
    "BEGIN:VCALENDAR",
    "VERSION:2.0",
    "PRODID:-//Feuerwehr Traisa//Website//DE",
    "BEGIN:VEVENT",
    `UID:${localStamp(start)}-${Math.random().toString(36).slice(2)}@feuerwehr-traisa.de`,
    `DTSTAMP:${stamp}`,
    `DTSTART:${localStamp(start)}`,
    end ? `DTEND:${localStamp(end)}` : null,
    `SUMMARY:${escapeText(title)}`,
    location ? `LOCATION:${escapeText(location)}` : null,
    description ? `DESCRIPTION:${escapeText(description)}` : null,
    "END:VEVENT",
    "END:VCALENDAR",
  ].filter(Boolean);
  return lines.join("\r\n");
}

export function downloadIcs(filename, content) {
  const blob = new Blob([content], { type: "text/calendar;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(url);
}
