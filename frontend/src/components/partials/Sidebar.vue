<template>
  <div class="sidebar">
    <section v-if="!isLoading && latestAlarm">
      <div class="ffw-section-title">
        <h2>Letzter Einsatz</h2>
        <router-link to="/einsatzabteilung/einsatze" class="ffw-link"
          >alle Einsätze</router-link
        >
      </div>
      <router-link
        :to="'/einsatzabteilung/einsatze/' + latestAlarm.post_name"
        class="alarm-card"
      >
        <div class="ffw-eyebrow alarm-card__meta">
          <v-icon size="16">mdi-alarm-light-outline</v-icon>
          <span v-if="alarm.number">Einsatz {{ alarm.number }} · </span>
          <span v-if="latestAlarm.alarmTime">{{
            formatShortDate(latestAlarm.alarmTime.timestamp)
          }}</span>
        </div>
        <div class="alarm-card__title">{{ alarm.label }}</div>
        <div v-if="latestAlarm.einsatzart" class="alarm-card__type">
          {{ latestAlarm.einsatzart }}
        </div>
      </router-link>
    </section>

    <section v-if="!isLoading && nextThreeMeetings">
      <div class="ffw-section-title">
        <h2>Termine</h2>
        <router-link to="/termine" class="ffw-link">zu allen Terminen</router-link>
      </div>
      <ul class="meetings">
        <li v-for="meeting in nextThreeMeetings" :key="meeting.ID" class="meeting">
          <div class="ffw-date-badge" aria-hidden="true">
            <div class="ffw-date-badge__top">
              {{ formatWeekDay(meeting.date.timestamp) }}
            </div>
            <div class="ffw-date-badge__bottom">
              {{ formatNumberDay(meeting.date.timestamp) }}
            </div>
          </div>
          <div class="meeting__text">
            <div class="meeting__title">{{ meeting.post_title }}</div>
            <div class="meeting__when">
              {{ formatDayMonth(meeting.date.timestamp) }} ·
              {{ formatTime(meeting.date.timestamp) }} Uhr
            </div>
          </div>
        </li>
      </ul>
    </section>

    <section v-for="sideBarPost in sideBarPosts" :key="sideBarPost.ID">
      <div class="ffw-section-title">
        <h2>{{ sideBarPost.post_title }}</h2>
      </div>
      <component
        :is="sideBarPost.goto ? 'a' : 'div'"
        :href="sideBarPost.goto || undefined"
        class="sidebar-post"
      >
        <img
          :src="sideBarPost.sideimg"
          :alt="sideBarPost.sideimgalt || sideBarPost.post_title"
        />
      </component>
    </section>

    <!-- Excerpts of the Wichtelwehr / Jugendfeuerwehr / Einsatzabteilung
         pages in WordPress ("Textauszug"). -->
    <section v-if="mitmachen.length" class="join">
      <div class="ffw-notch join__head">Mitmachen</div>
      <ul class="join__list">
        <li v-for="entry in mitmachen" :key="entry.title">
          <router-link :to="entry.to" class="join__title">{{
            entry.title
          }}</router-link>
          <p>{{ entry.text }}</p>
        </li>
      </ul>
    </section>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useSideBarStore } from "@/store/modules/sideBar";
import { parseAlarmTitle } from "@/utils/alarms";
import {
  formatDayMonth,
  formatNumberDay,
  formatShortDate,
  formatTime,
  formatWeekDay,
} from "@/utils/dateFilters";

export default {
  name: "Sidebar",
  computed: {
    ...mapState(useSideBarStore, {
      isLoading: "isLoading",
      sideBarPosts: "sideBarPosts",
      latestAlarm: "latestAlarm",
      nextThreeMeetings: "nextThreeMeetings",
      cmsMitmachen: "mitmachen",
    }),
    // A group whose page has no excerpt yet is left out.
    mitmachen() {
      return (this.cmsMitmachen || []).filter((entry) => entry.text);
    },
    alarm() {
      return parseAlarmTitle(this.latestAlarm?.post_title);
    },
  },
  methods: {
    formatDayMonth,
    formatNumberDay,
    formatShortDate,
    formatTime,
    formatWeekDay,
  },
};
</script>

<style scoped lang="scss">
@import "Sidebar";
</style>
