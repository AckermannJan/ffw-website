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
