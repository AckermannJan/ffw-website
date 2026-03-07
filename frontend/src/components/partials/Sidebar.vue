<template>
  <div>
    <div class="sideBarEntry mb-3" v-if="!isLoading">
      <div class="headline font-weight-bold">Letzter Einsatz</div>
      <div class="sideBarEntry__body">
        <router-link
          class="link"
          :to="'/einsatzabteilung/einsatze/' + latestAlarm.post_name"
          >{{ latestAlarm.post_title }}</router-link
        >
      </div>
    </div>
    <div class="calendar" v-if="!isLoading">
      <div class="headline calendar__headline font-weight-bold">
        Termine
        <router-link to="/termine" class="link">zu allen Terminen</router-link>
      </div>
      <div class="calendar__body">
        <v-row
          class="calendar__entry mb-3"
          v-for="(meeting, index) in nextThreeMeetings"
          :key="index"
        >
          <v-col class="calendar__date">
            <span>{{ meeting.date.timestamp | weekDay }}</span>
            <span>{{ meeting.date.timestamp | numberDay }}</span>
          </v-col>
          <v-col class="calender__info" style="padding: 0; position: relative">
            <div>{{ meeting.post_title }}</div>
            <div class="subtitle-2 calendar__detail-date">
              {{ meeting.date.timestamp | date }}
            </div>
          </v-col>
        </v-row>
      </div>
    </div>
    <div
      v-for="sideBarPost in sideBarPosts"
      :key="sideBarPost.ID"
      class="sideBarEntry mb-3"
    >
      <div class="headline font-weight-bold">{{ sideBarPost.post_title }}</div>
      <div
        class="sideBarEntry__body sideBarEntry__body--noBg sideBarEntry__body--noPadding"
      />
      <img
        class="sideBarEntry"
        :src="sideBarPost.sideimg"
        :alt="sideBarPost.sideimgalt || 'Bild für ein Event'"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { momentInstance } from "@/utils/moment";

export default {
  name: "Sidebar",
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
  computed: {
    ...mapGetters("sideBar", {
      isLoading: "isLoading",
      sideBarPosts: "sideBarPosts",
      latestAlarm: "latestAlarm",
      nextThreeMeetings: "nextThreeMeetings"
    })
  }
};
</script>

<style scoped lang="scss">
@import "Sidebar";
</style>
