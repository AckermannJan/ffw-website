<template>
  <div>
    <div v-if="!isLoading" class="report">
      <div class="report__headline">
        <h1>
          {{ alarm.post_title }}
        </h1>
      </div>
      <div class="report__content">
        <v-row class="report__red pt-5" no-gutters>
          <v-col cols="3" style="text-align: center"
            ><v-icon size="60" color="#fff">mdi-hammer</v-icon></v-col
          >
          <v-col>
            <div class="headline mb-4"><b>Einsatzart</b></div>
            <p style="margin-left: 10px">{{ alarm.einsatzart }}</p>
            <div class="headline mb-4"><b>Einsatzort</b></div>
            <p style="margin-left: 10px">{{ alarm.einsatzort }}</p>
            <div class="headline mb-4"><b>Alarmierungsart</b></div>
            <p style="margin-left: 10px">{{ alarm.alarmierungsart }}</p>
            <div class="headline mb-4"><b>Alarmierungszeitpunkt</b></div>
            <p style="margin-left: 10px">
              {{ alarm.alarmierungszeitpunkt.timestamp | date }}
            </p>
          </v-col>
        </v-row>
        <v-row class="mt-4" no-gutters>
          <v-col cols="3" style="text-align: center"
            ><v-icon size="60" color="#000">mdi-fire-truck</v-icon></v-col
          >
          <v-col>
            <div class="car" v-for="car in alarm.fahrzeuge" :key="car">
              <img :src="carImages[car].img" :alt="carImages[car].alt" />
            </div>
            <div class="headline mb-4"><b>EINSATZPERSONAL</b></div>
            <p style="margin-left: 10px">{{ alarm.einsatzpersonal }}</p>
            <div class="headline mb-4"><b>WEITERE KRÄFTE</b></div>
            <p style="margin-left: 10px">{{ alarm.weitereKraefte }}</p>
          </v-col>
        </v-row>
        <v-row class="report__red pt-5 report__red--no" no-gutters>
          <v-col cols="3" style="text-align: center"
            ><v-icon size="60" color="#fff"
              >mdi-clipboard-text-outline</v-icon
            ></v-col
          >
          <v-col v-html="alarm.post_content_html" class="mb-3" />
        </v-row>
        <v-carousel
          v-if="
            Object.keys(alarm.medienbilder).length > 0 &&
              Object.values(alarm.medienbilder)[0] !== ''
          "
          height="430"
          show-arrows-on-hover
        >
          <v-carousel-item
            v-for="(item, i) in alarm.medienbilder"
            :key="i"
            :src="item"
          ></v-carousel-item>
        </v-carousel>
      </div>
    </div>
    <Loader v-else />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { momentInstance } from "@/utils/moment";
import Loader from "../../components/partials/Loader";

export default {
  name: "singleAlarm",
  components: { Loader },
  data() {
    return {
      carImages: {
        1: {
          alt: "MTF",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2016/08/Vorsschaukleinnn.png"
        },
        2: {
          alt: "LF/8",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/lf86.jpg"
        },
        3: {
          alt: "LF/10",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/lf106.jpg"
        },
        4: {
          alt: "Anhänger",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/anhaenger.jpg"
        },
        5: {
          alt: "KdoW",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/themes/FFW/imgs/cars/kdow.jpg"
        },
        6: {
          alt: "MLF",
          img:
            "https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2025/05/MLF_Icon.png"
        }
      }
    };
  },
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
    }
  },
  computed: {
    ...mapGetters("alarms", {
      alarm: "alarm",
      isLoading: "isAlarmLoading"
    })
  },
  mounted() {
    this.getAlarm(this.$route.params.pageSlug);
  },
  methods: {
    ...mapActions("alarms", {
      getAlarm: "getAlarm"
    })
  },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | " + this.alarm.post_title,
      meta: [
        {
          name: "robots",
          content: "nofollow"
        }
      ]
    };
  }
};
</script>

<style scoped lang="scss">
.car {
  display: inline-block;
  margin-right: 10px;
  width: 70px;
  height: 70px;
  background-color: #fff;
  border-radius: 60px;
  overflow: hidden;
  border: solid 5px rgba(175, 74, 69, 0.4);
  img {
    margin-top: 7px;
    width: 60px;
  }
}
.report {
  background-color: rgba(59, 59, 59, 0.34);
  &__headline {
    color: #fff;
    padding-left: 20px;
    position: relative;
    background-color: #af4a45;
    height: 50px;
    &::after {
      position: absolute;
      width: 0;
      height: 0;
      top: 50px;
      content: "";
      border-left: 8px solid transparent;
      border-right: 8px solid transparent;
      border-top: 16px solid #af4a45;
      left: 40px;
    }
  }
  &__red {
    color: #fff;
    position: relative;
    background-color: rgb(157, 66, 62);
    &::after {
      position: absolute;
      width: 0;
      height: 0;
      bottom: -14px;
      content: "";
      border-left: 8px solid transparent;
      border-right: 8px solid transparent;
      border-top: 16px solid rgb(157, 66, 62);
      left: 40px;
    }
    &--no {
      &::after {
        position: relative;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 16px solid transparent;
        left: 40px;
      }
    }
  }
}
</style>
