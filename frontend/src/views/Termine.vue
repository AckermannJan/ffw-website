<template>
  <div>
    <v-row
      class="calendar__entry mb-3"
      v-for="(meeting, index) in termine"
      :key="index"
    >
      <v-col class="calendar__date">
        <span>{{ meeting.zeitpunkt.timestamp | weekDay }}</span>
        <span>{{ meeting.zeitpunkt.timestamp | numberDay }}</span>
      </v-col>
      <v-col class="calender__info" style="padding: 0; position: relative">
        <div>{{ meeting.post_title }}</div>
        <div class="subtitle-2 calendar__detail-date">
          {{ meeting.zeitpunkt.timestamp | date }}
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { momentInstance } from "@/utils/moment";

export default {
  name: "Termine",
  filters: {
    date(date) {
      return (
        momentInstance(parseInt(date) * 1000)
          .utc()
          .format("DD MMMM") +
        " um " +
        momentInstance(parseInt(date) * 1000)
          .utc()
          .format("HH:mm")
      );
    },
    weekDay(date) {
      return momentInstance(parseInt(date) * 1000)
        .utc()
        .format("dd")
        .toUpperCase();
    },
    numberDay(date) {
      return momentInstance(parseInt(date) * 1000)
        .utc()
        .format("DD");
    }
  },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | Termine",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Termine"
        }
      ]
    };
  },
  computed: {
    ...mapGetters("termine", {
      termine: "termine",
      isLoading: "isLoading"
    })
  },
  mounted() {
    this.getAllMeetings();
  },
  methods: {
    ...mapActions("termine", {
      getAllMeetings: "getAllMeetings"
    })
  }
};
</script>

<style scoped lang="scss">
.calendar {
  &__entry {
    margin: 0px;
    border-bottom: dashed 1px #af4a45;
    padding-bottom: 10px;
  }
  &__body {
    border-top: solid 2px #af4a45;
    padding: 10px 15px;
  }
  &__date {
    margin-right: 15px;
    height: 90px;
    box-shadow: 0 0 5px 1px rgba(0, 0, 0, 0.1);
    max-width: 70px;
    border: 5px solid #af4a45;
    text-align: center;
    padding-top: 10px;
    background: linear-gradient(
      to bottom,
      transparent,
      transparent 50%,
      #af4a45 50%,
      #af4a45
    );
    span:first-child {
      margin-top: -7px;
      font-size: 1.5rem;
      font-weight: 700;
      display: block;
      color: #af4a45;
    }
    span:last-child {
      margin-top: 7px;
      font-size: 1.5rem;
      font-weight: 700;
      display: block;
    }
  }
}
</style>
