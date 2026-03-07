<template>
  <Report>
    <template v-slot:headline>
      <v-skeleton-loader class="pt-3" type="heading"></v-skeleton-loader>
    </template>
    <template v-slot:content>
      <v-skeleton-loader type="paragraph"></v-skeleton-loader>
    </template>
  </Report>
</template>

<script>
import Report from "./Report/Report";
export default {
  components: { Report }
};
</script>
