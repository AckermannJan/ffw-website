import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Home",
    component: () => import("../views/Home.vue")
  },
  {
    path: "/seite/:pageSlug",
    name: "Page",
    component: () => import("../views/Page.vue")
  },
  {
    path: "/termine",
    name: "Termine",
    component: () => import("../views/Termine.vue")
  },
  {
    path: "/archiv",
    name: "Archive",
    component: () => import("../views/Archive.vue")
  },
  {
    path: "/kindergruppen",
    name: "Kindergruppen",
    component: () => import("../views/Kindergruppen.vue")
  },
  {
    path: "/kindergruppen/wichtelwehr",
    name: "Wichtelwehr",
    redirect: { path: "/kindergruppen/wichtelwehr/info" }
  },
  {
    path: "/kindergruppen/wichtelwehr/:pageSlug",
    name: "WichtelwehrSeiten",
    component: () => import("../views/Page.vue")
  },
  {
    path: "/kindergruppen/jugendfeuerwehr",
    name: "Jugendfeuerwehr",
    redirect: { path: "/kindergruppen/jugendfeuerwehr/info-2" }
  },
  {
    path: "/kindergruppen/jugendfeuerwehr/:pageSlug",
    name: "JugendfeuerwehrSeiten",
    component: () => import("../views/Page.vue")
  },
  {
    path: "/technik",
    name: "Technik",
    component: () => import("../views/Technik.vue")
  },
  {
    path: "/technik/:pageSlug",
    name: "Technik-Gerät",
    component: () => import("../views/Page.vue")
  },
  {
    path: "/kontakt",
    name: "Kontakt",
    component: () => import("../views/Page.vue"),
    props: { slug: "kontakt" }
  },
  {
    path: "/impressum",
    name: "Impressum",
    component: () => import("../views/Page.vue"),
    props: { slug: "impressum" }
  },
  {
    path: "/einsatzabteilung",
    name: "Einsatzabteilung",
    component: () => import("../views/Page.vue"),
    props: { slug: "einsatzabteilung" }
  },
  {
    path: "/einsatzabteilung/dienstplan",
    name: "EinsatzabteilungDienstplan",
    component: () => import("../views/Roster.vue"),
    props: { roster: "eAbtRoster" }
  },
  {
    path: "/einsatzabteilung/einsatze",
    name: "Einsätze",
    component: () => import("../views/alarms/alarmTable.vue")
  },
  {
    path: "/einsatzabteilung/einsatze/:pageSlug",
    name: "Einsatz",
    component: () => import("../views/alarms/singleAlarm.vue")
  }
];

const router = new VueRouter({
  routes,
  mode: "history",
  scrollBehavior() {
    return { x: 0, y: 0 };
  }
});

export default router;
