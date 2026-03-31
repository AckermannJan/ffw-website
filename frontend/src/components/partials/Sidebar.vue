<template>
  <div>
    <div v-if="!isLoading && latestAlarm" class="sideBarEntry mb-3">
      <div class="headline font-weight-bold">Letzter Einsatz</div>
      <div class="sideBarEntry__body">
        <router-link
          class="link"
          :to="'/einsatzabteilung/einsatze/' + latestAlarm.post_name"
          >{{ latestAlarm.post_title }}</router-link
        >
      </div>
    </div>
    <div v-if="!isLoading && nextThreeMeetings" class="calendar">
      <div class="headline calendar__headline font-weight-bold">
        Termine
        <router-link to="/termine" class="link">zu allen Terminen</router-link>
      </div>
      <div class="calendar__body">
        <v-row
          v-for="(meeting, index) in nextThreeMeetings"
          :key="index"
          class="calendar__entry mb-3"
        >
          <v-col class="calendar__date">
            <span>{{ formatWeekDay(meeting.date.timestamp) }}</span>
            <span>{{ formatNumberDay(meeting.date.timestamp) }}</span>
          </v-col>
          <v-col class="calender__info" style="padding: 0; position: relative">
            <div>{{ meeting.post_title }}</div>
            <div class="text-subtitle-2 calendar__detail-date">
              {{ formatDate(meeting.date.timestamp) }}
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
import { mapState } from "pinia";
import { useSideBarStore } from "@/store/modules/sideBar";
import {
  formatDate,
  formatWeekDay,
  formatNumberDay,
} from "@/utils/dateFilters";

export default {
  name: "Sidebar",
  computed: {
    ...mapState(useSideBarStore, [
      "isLoading",
      "sideBarPosts",
      "latestAlarm",
      "nextThreeMeetings",
    ]),
  },
  methods: {
    formatDate,
    formatWeekDay,
    formatNumberDay,
  },
};
</script>

<style scoped lang="scss">
@import "Sidebar";
</style>
