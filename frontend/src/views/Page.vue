<template>
  <div>
    <template v-if="!isLoading">
      <Report>
        <template #headline>
          {{ pageDetails.post_title }}
        </template>
        <template #content>
          <CmsContent :html="pageDetails.post_content" />
          <PhotoGallery
            v-if="photos.length"
            :photos="photos"
            class="page-gallery"
          />
        </template>
      </Report>
    </template>
    <Loader v-else />
  </div>
</template>

<script>
import Loader from "../components/partials/Loader.vue";
import { mapState, mapActions } from "pinia";
import { usePageStore } from "@/store/modules/page";
import { usePageSeo } from "@/utils/seo";
import Report from "../components/partials/Report/Report.vue";
import CmsContent from "../components/cms/CmsContent.vue";
import PhotoGallery from "../components/partials/PhotoGallery.vue";

export default {
  components: {
    Report,
    Loader,
    CmsContent,
    PhotoGallery,
  },
  props: {
    slug: {
      default: null,
    },
  },
  setup() {
    const pageStore = usePageStore();
    usePageSeo({
      title: () => pageStore.pageDetails.post_title,
      description: () =>
        pageStore.pageDetails.post_excerpt ||
        pageStore.pageDetails.post_content,
      image: () => pageStore.pageDetails.startBild,
    });
  },
  computed: {
    ...mapState(usePageStore, ["pageDetails", "isLoading"]),
    pageSlug() {
      return this.slug || this.$route.params.pageSlug;
    },
    // "any_attached_images" is a Types radio field: "1" shows the gallery.
    photos() {
      if (String(this.pageDetails.any_attached_images) !== "1") return [];
      const urls = Object.values(this.pageDetails.attached_images || {}).filter(
        Boolean,
      );
      return urls.map((url, i) => ({
        // Uploads are stored with http:// URLs; the site runs on https.
        src: url.replace(/^http:\/\//, "https://"),
        alt: `Bild ${i + 1} von ${urls.length}`,
      }));
    },
  },
  watch: {
    // Moving between pages of the same route (e.g. /technik/mtf →
    // /technik/lf-106) reuses this component, so load the new page.
    pageSlug() {
      this.getPageById(this.pageSlug);
    },
  },
  mounted() {
    this.getPageById(this.pageSlug);
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
</style>

<style scoped>
.page-gallery {
  margin-top: 32px;
}
</style>
