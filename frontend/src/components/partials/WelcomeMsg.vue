<template>
  <div class="text-center dottedBorder mb-5">
    <h1 class="headline font-italic font-weight-bold mb-5">
      Unsere Freizeit für Ihre Sicherheit -<br />
      Seit {{ yearsInExistence }} Jahren
    </h1>
    <p>
      Wir freuen uns, dass Sie den Weg auf unsere Webseite gefunden haben.<br />
      Schauen Sie sich um und entdecken Sie die Vielfalt der Feuerwehr Traisa.
    </p>
  </div>
</template>

<script>
import { momentInstance } from "@/utils/moment";

export default {
  name: "WelcomeMsg",
  computed: {
    yearsInExistence() {
      return momentInstance().diff("1880-01-01", "years", false);
    }
  }
};
</script>

<style scoped lang="scss">
.dottedBorder:after {
  height: 1px;
  width: 100%;
  content: " ";
  display: block;
  background: repeating-linear-gradient(
    to right,
    transparent,
    transparent 5px,
    #af4a45 5px,
    #af4a45 8px
  );
}
</style>
