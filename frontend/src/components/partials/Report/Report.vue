<template>
  <div class="report">
    <div class="report__headline">
      <h1>
        <slot name="headline" />
      </h1>
    </div>
    <div class="report__content">
      <slot name="content"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "Report"
};
</script>

<style scoped lang="scss">
@import "Report";
</style>
