<template>
  <div class="ffw-eyebrow post-meta">
    <!-- "kategorie": Types field, or else the parent page title (wpTheme). -->
    <template v-if="page.kategorie">
      <span>{{ page.kategorie }}</span>
      <span aria-hidden="true">·</span>
    </template>
    <!-- "veranstaltungsdatum" (Types date field) when set; otherwise the
         WordPress publish date. -->
    <time :datetime="isoDate">{{ displayDate }}</time>
  </div>
</template>

<script>
import { momentInstance } from "@/utils/moment";

export default {
  name: "PostMeta",
  props: {
    page: { type: Object, required: true },
  },
  computed: {
    date() {
      return momentInstance(
        this.page.veranstaltungsdatum || this.page.post_date,
      );
    },
    isoDate() {
      return this.date.format("YYYY-MM-DD");
    },
    displayDate() {
      return this.date.format("DD.MM.YYYY");
    },
  },
};
</script>

<style scoped>
.post-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
</style>
