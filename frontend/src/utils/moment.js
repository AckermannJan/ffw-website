import moment from "moment";
export const momentInstance = inp => {
  moment.locale("de");
  return moment(inp);
};
