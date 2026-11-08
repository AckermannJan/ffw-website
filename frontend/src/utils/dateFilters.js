import { momentInstance } from "./moment";

// Used in Sidebar.vue, Termine.vue, singleAlarm.vue
export function formatDate(date) {
  return (
    momentInstance(parseInt(date) * 1000)
      .utc()
      .format("DD MMMM") +
    " um " +
    momentInstance(parseInt(date) * 1000)
      .utc()
      .format("HH:mm")
  );
}

// Used in Sidebar.vue (redesign): "15. November"
export function formatDayMonth(date) {
  return momentInstance(parseInt(date) * 1000)
    .utc()
    .format("D. MMMM");
}

// Used in Sidebar.vue (redesign): "14:00"
export function formatTime(date) {
  return momentInstance(parseInt(date) * 1000)
    .utc()
    .format("HH:mm");
}

// Used in Sidebar.vue (redesign): "06.10.2026"
export function formatShortDate(date) {
  return momentInstance(parseInt(date) * 1000)
    .utc()
    .format("DD.MM.YYYY");
}

export function formatWeekDay(date) {
  return momentInstance(parseInt(date) * 1000)
    .utc()
    .format("dd")
    .toUpperCase();
}

export function formatNumberDay(date) {
  return momentInstance(parseInt(date) * 1000)
    .utc()
    .format("DD");
}

// Used in alarmTable.vue (different format)
export function formatAlarmDate(date) {
  return momentInstance(parseInt(date) * 1000).format("DD.MM.YYYY");
}

// Used in Roster.vue (date string in DD.MM.YYYY format)
export function rosterWeekDay(date) {
  if (!date) return;
  const tmpDate = date.split(".");
  return momentInstance(`${tmpDate[1]}.${tmpDate[0]}.${tmpDate[2]}`)
    .format("dd")
    .toUpperCase();
}

export function rosterNumberDay(date) {
  if (!date) return;
  const tmpDate = date.split(".");
  return momentInstance(`${tmpDate[1]}.${tmpDate[0]}.${tmpDate[2]}`).format(
    "DD",
  );
}

export function rosterNumberMonth(date) {
  if (!date) return;
  const tmpDate = date.split(".");
  return momentInstance(`${tmpDate[1]}.${tmpDate[0]}.${tmpDate[2]}`).format(
    "MM",
  );
}
