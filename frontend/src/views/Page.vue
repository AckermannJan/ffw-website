<template>
  <div>
    <template v-if="!isLoading">
      <Report>
        <template #headline>
          {{ pageDetails.post_title }}
        </template>
        <template #content>
          <div class="report__content" v-html="pageDetails.post_content" />
        </template>
      </Report>
      <v-carousel
        v-if="
          Object.keys(pageDetails.attached_images).length > 0 &&
          parseInt(pageDetails.any_attached_images) === 1
        "
        show-arrows="hover"
      >
        <v-carousel-item
          v-for="(item, i) in pageDetails.attached_images"
          :key="i"
        >
          <v-img :src="item" height="100%"></v-img>
        </v-carousel-item>
      </v-carousel>
    </template>
    <Loader v-else />
  </div>
</template>

<script>
import Loader from "../components/partials/Loader.vue";
import { mapState, mapActions } from "pinia";
import { usePageStore } from "@/store/modules/page";
import { useHead } from "@unhead/vue";
import { computed } from "vue";
import Report from "../components/partials/Report/Report.vue";

export default {
  components: {
    Report,
    Loader,
  },
  props: {
    slug: {
      default: null,
    },
  },
  setup() {
    const pageStore = usePageStore();
    useHead({
      title: computed(
        () =>
          "Feuerwehr Mühltal Traisa | " +
          (pageStore.pageDetails.post_title || ""),
      ),
    });
  },
  computed: {
    ...mapState(usePageStore, ["pageDetails", "isLoading"]),
  },
  mounted() {
    this.getPageById(this.slug || this.$route.params.pageSlug);
  },
  methods: {
    ...mapActions(usePageStore, ["getPageById"]),
  },
};
</script>

<style>
img {
  max-width: 100%;
  height: auto;
}
.v-image__image--cover {
  background-size: 100% auto !important;
}
</style>
