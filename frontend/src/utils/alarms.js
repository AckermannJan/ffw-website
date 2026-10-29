// Alarm titles follow "Einsatz 27 (2026) Stromausfall". Split them so the
// redesign can show number, year and label separately.
// The alarm number has no field of its own in Types, so the title is the
// only source.
export function parseAlarmTitle(title = "") {
  const match = title.match(/^Einsatz\s+(\d+)\s*\((\d{4})\)\s*(.*)$/);
  if (!match) return { number: null, year: null, label: title };
  return {
    number: Number(match[1]),
    year: Number(match[2]),
    label: match[3] || title,
  };
}
