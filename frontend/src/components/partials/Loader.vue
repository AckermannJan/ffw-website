<template>
  <div class="report-loader">
    <div class="report-loader__headline">
      <v-skeleton-loader
        type="heading"
        color="transparent"
        class="report-loader__headline-bone"
      ></v-skeleton-loader>
    </div>
    <div class="report-loader__content">
      <v-skeleton-loader
        type="paragraph"
        color="transparent"
      class="report-loader__content-bone"
      ></v-skeleton-loader>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style>
.report-loader__headline {
  background-color: #af4a45;
  max-height: 50px;
  padding: 10px 20px;
  display: flex;
  align-items: center;
  position: relative;
}

/* Arrow tip matching Report.scss */
.report-loader__headline::after {
  position: absolute;
  width: 0;
  height: 0;
  bottom: -15px;
  content: '';
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 16px solid #af4a45;
  left: 40px;
}

/* Stretch the skeleton loader across the full red bar */
.report-loader__headline-bone {
  width: 100%;
}

/* Heading bone: visible white shimmer on red */
.report-loader__headline-bone .v-skeleton-loader__heading {
  background-color: rgba(255, 255, 255, 0.4) !important;
  width: 55%;
}

.report-loader__content-bone .v-skeleton-loader__paragraph {
  background-color: rgba(255, 255, 255, 0) !important;
  width: 55%;

  &::after {
    display: none;
  }
}

.report-loader__content {
  background-color: rgba(59, 59, 59, 0.34);
  padding: 20px 20px 20px;
}

/* Content bones: subtle dark shimmer */
.report-loader__content .v-skeleton-loader__text {
  background-color: rgba(0, 0, 0, 0.12) !important;
}
</style>
