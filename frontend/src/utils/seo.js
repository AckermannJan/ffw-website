import { computed, unref } from "vue";
import { useHead } from "@unhead/vue";
import { useRoute } from "vue-router";

// One canonical address for every page. The site also answers on
// feuerwehr-traisa.de and ff-traisa.de; canonical links point search
// engines to this host.
export const SITE_URL = "https://www.feuerwehr-traisa.de";
export const SITE_NAME = "Freiwillige Feuerwehr Traisa";
export const DEFAULT_DESCRIPTION =
  "Freiwillige Feuerwehr Mühltal-Traisa: Einsätze, Termine, Jugendfeuerwehr, Wichtelwehr und Verein. Ehrenamtlich für Ihre Sicherheit in Traisa seit 1880.";
export const DEFAULT_IMAGE =
  "https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2016/02/1-fwhaus-aussen.jpg";

// Uploads are stored with http:// URLs; the site runs on https.
const https = (url) => (url ? url.replace(/^http:\/\//, "https://") : url);

// Plain text for meta descriptions: no tags, one line, cut at a word
// boundary to about 155 characters (what search results show).
export function toDescription(text = "", max = 155) {
  const clean = String(text)
    .replace(/<[^>]*>/g, " ")
    .replace(/&nbsp;/g, " ")
    .replace(/&#8222;|&#8220;/g, '"')
    .replace(/&amp;/g, "&")
    .replace(/\s+/g, " ")
    .trim();
  if (clean.length <= max) return clean;
  return clean.slice(0, clean.lastIndexOf(" ", max)) + " …";
}

// Base data for every page: the fire brigade as an organisation.
export const ORGANIZATION_LD = {
  "@context": "https://schema.org",
  "@type": "FireStation",
  "@id": `${SITE_URL}/#organization`,
  name: SITE_NAME,
  alternateName: "Feuerwehr Mühltal Traisa",
  url: `${SITE_URL}/`,
  image: DEFAULT_IMAGE,
  telephone: "+49 6151 145400",
  foundingDate: "1880",
  address: {
    "@type": "PostalAddress",
    streetAddress: "Im Rosengarten 19",
    postalCode: "64367",
    addressLocality: "Mühltal",
    addressRegion: "Hessen",
    addressCountry: "DE",
  },
  parentOrganization: {
    "@type": "Organization",
    name: "Freiwillige Feuerwehr Mühltal",
    url: "https://www.feuerwehr-muehltal.de/",
  },
};

/**
 * Title, description, canonical URL, link previews (Open Graph / Twitter)
 * and optional structured data for the current page. Every option may be a
 * value, ref or getter, so pages that load their content can pass computed
 * values.
 *
 *   usePageSeo({ title: "Termine", description: "…" })
 *   usePageSeo({ title: () => page.title, image: () => page.image,
 *                type: "article", jsonLd: () => ({...}) })
 */
export function usePageSeo(options = {}) {
  const route = useRoute();
  const read = (value) =>
    typeof value === "function" ? value() : unref(value);

  const title = computed(() => {
    const page = read(options.title);
    return page ? `${page} | ${SITE_NAME}` : `${SITE_NAME} – Mühltal`;
  });
  const description = computed(
    () => toDescription(read(options.description)) || DEFAULT_DESCRIPTION,
  );
  // Query strings (?jahr=, ?gruppe=) only change a view of the same page.
  const url = computed(
    () => SITE_URL + (route.path === "/" ? "/" : route.path),
  );
  const image = computed(() => https(read(options.image)) || DEFAULT_IMAGE);
  const robots = computed(() =>
    read(options.noindex) ? "noindex, follow" : "index, follow",
  );
  const type = computed(() => read(options.type) || "website");
  const scripts = computed(() => {
    const data = read(options.jsonLd);
    return data
      ? [
          {
            key: "page-ld",
            type: "application/ld+json",
            innerHTML: JSON.stringify(data),
          },
        ]
      : [];
  });

  useHead({
    title,
    link: [{ rel: "canonical", href: url }],
    meta: [
      { name: "description", content: description },
      { name: "robots", content: robots },
      { property: "og:site_name", content: SITE_NAME },
      { property: "og:locale", content: "de_DE" },
      { property: "og:type", content: type },
      { property: "og:title", content: title },
      { property: "og:description", content: description },
      { property: "og:url", content: url },
      { property: "og:image", content: image },
      { name: "twitter:card", content: "summary_large_image" },
      { name: "twitter:title", content: title },
      { name: "twitter:description", content: description },
      { name: "twitter:image", content: image },
    ],
    script: scripts,
  });
}
