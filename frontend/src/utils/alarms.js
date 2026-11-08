// Alarm categories, keyed by the Types field "einsatzicon" that editors set
// on every alarm post.
export const ALARM_CATEGORIES = {
  1: { label: "Brand", plural: "Brandeinsätze", icon: "mdi-fire" },
  2: {
    label: "Technische Hilfe",
    plural: "Hilfeleistungen",
    icon: "mdi-hammer",
  },
  3: {
    label: "Brandmeldealarm",
    plural: "Brandmeldealarme",
    icon: "mdi-alarm-bell",
  },
  4: {
    label: "Sicherheitsdienst",
    plural: "Sicherheitsdienste",
    icon: "mdi-binoculars",
  },
};

// Vehicles, keyed by the Types checkbox values of the field "fahrzeuge".
// The LF 8 was replaced by the MLF and has no Technik page any more.
const CARS = "https://wordpress.feuerwehr-traisa.de/wp-content";
export const ALARM_VEHICLES = {
  1: {
    name: "MTF",
    to: "/technik/mtf",
    image: `${CARS}/uploads/2016/08/Vorsschaukleinnn.png`,
  },
  2: {
    name: "LF 8",
    to: null,
    image: `${CARS}/themes/FFW/imgs/cars/lf86.jpg`,
  },
  3: {
    name: "LF 10",
    to: "/technik/lf-106",
    image: `${CARS}/themes/FFW/imgs/cars/lf106.jpg`,
  },
  4: {
    name: "Anhänger",
    to: "/technik/anhaenger",
    image: `${CARS}/themes/FFW/imgs/cars/anhaenger.jpg`,
  },
  5: {
    name: "KDOW",
    to: "/technik/kdow",
    image: `${CARS}/themes/FFW/imgs/cars/kdow.jpg`,
  },
  6: {
    name: "MLF",
    to: "/technik/mlf",
    image: `${CARS}/uploads/2025/05/MLF_Icon.png`,
  },
};

// Alarm titles follow "Einsatz 27 (2026) Stromausfall". Split them so the
// redesign can show number, year and label separately.
// The alarm number has no field of its own in Types, so the title is the
// only source.
// The number can also be a range ("Einsatz 21-22 (2024) …") when one post
// covers several alarms; it is returned as text, e.g. "21–22".
export function parseAlarmTitle(title = "") {
  const match = title.match(
    /^Einsatz\s+(\d+(?:\s*[-–]\s*\d+)?)\s*\((\d{4})\)\s*(.*)$/,
  );
  if (!match) return { number: null, year: null, label: title };
  return {
    number: match[1].replace(/\s*[-–]\s*/, "–"),
    year: Number(match[2]),
    label: match[3] || title,
  };
}
