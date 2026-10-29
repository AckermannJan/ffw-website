// Turns WordPress page HTML (classic editor) into a structure the CMS page
// view can lay out. Editors only follow a few conventions:
//
//   text before the first H2  -> intro
//   H2                        -> a section
//   H3 inside a section       -> cards side by side (2 or more)
//   image with caption        -> figure; next to the section text
//   paragraph of PDF links    -> download buttons
//   IBAN (+ BIC) in the text  -> bank box with a copy button
//
// Pages without H2 headings come out as one intro, so existing pages keep
// working. See docs/cms-page-conventions.md.

const IBAN_PATTERN =
  /\b([A-Z]{2}\d{2}(?: ?[A-Z0-9]{4}){3,7}(?: ?[A-Z0-9]{1,3})?)\b/;
const BIC_PATTERN = /\bBIC:?\s*([A-Z]{6}[A-Z0-9]{2}(?:[A-Z0-9]{3})?)\b/;

// Inline formatting from the WordPress editor (tahoma, 10pt, colours) is
// dropped so the site's own typography applies.
function cleanNode(root) {
  root.querySelectorAll("[style]").forEach((el) => el.removeAttribute("style"));
  root
    .querySelectorAll("font")
    .forEach((el) => el.replaceWith(...el.childNodes));
  // Spans without attributes carry nothing once the styles are gone.
  root
    .querySelectorAll("span:not([class]):not([id])")
    .forEach((el) => el.replaceWith(...el.childNodes));
  // Layout-only wrapper divs (e.g. <div style="text-align: left">): a div
  // around paragraphs is flattened, a div that only holds text becomes a
  // paragraph (some pages use divs instead of paragraphs).
  root.querySelectorAll("div:not([class]):not([id])").forEach((el) => {
    if (el.querySelector(BLOCK_SELECTOR)) {
      el.replaceWith(...el.childNodes);
    } else {
      const p = el.ownerDocument.createElement("p");
      p.append(...el.childNodes);
      el.replaceWith(p);
    }
  });
  wrapInlineRuns(root);
}

const BLOCK_SELECTOR =
  "p, div, h1, h2, h3, h4, h5, h6, ul, ol, table, figure, blockquote, hr, iframe";

// Text and inline elements left directly under <body> are grouped into
// paragraphs, so "jeden <strong>zweiten</strong> Dienstag" stays one line.
function wrapInlineRuns(root) {
  let run = null;
  for (const node of [...root.childNodes]) {
    const isBlock =
      node.nodeType === Node.ELEMENT_NODE && node.matches(BLOCK_SELECTOR);
    if (isBlock) {
      run = null;
      continue;
    }
    if (!run) {
      if (node.nodeType === Node.TEXT_NODE && !node.textContent.trim())
        continue;
      run = root.ownerDocument.createElement("p");
      node.before(run);
    }
    run.append(node);
  }
}

function isEmptyParagraph(node) {
  return (
    node.nodeType === Node.ELEMENT_NODE &&
    node.tagName === "P" &&
    !node.querySelector("img, iframe") &&
    node.textContent.trim() === ""
  );
}

function isPdfLink(a) {
  return /\.pdf($|[?#])/i.test(a.getAttribute("href") || "");
}

function toFigure(node) {
  const img = node.querySelector("img");
  if (!img) return null;
  const caption = node.querySelector(".wp-caption-text, figcaption");
  const hasOtherText =
    node.textContent.trim() !== (caption ? caption.textContent.trim() : "");
  // A paragraph with an image *and* running text stays normal text.
  if (!caption && hasOtherText) return null;
  return {
    type: "figure",
    src: img.getAttribute("src"),
    alt: img.getAttribute("alt") || (caption ? caption.textContent.trim() : ""),
    caption: caption ? caption.textContent.trim() : "",
  };
}

function toDownloads(node) {
  if (node.tagName !== "P") return null;
  const links = [...node.querySelectorAll("a")];
  if (!links.length || !links.every(isPdfLink)) return null;
  const clone = node.cloneNode(true);
  clone.querySelectorAll("a").forEach((a) => a.remove());
  const note = clone.textContent.replace(/\s+/g, " ").trim();
  // "Bitte beachten Sie unsere <a>…</a>" reads as an introduction, so the
  // note goes above the buttons when it comes first in the paragraph.
  const fullText = node.textContent;
  const noteFirst =
    !!note &&
    fullText.indexOf(links[0].textContent) >
      fullText.indexOf(note.slice(0, 10));
  return {
    type: "downloads",
    links: links.map((a) => ({
      href: a.getAttribute("href"),
      label: a.textContent.replace(/\s+/g, " ").trim(),
    })),
    note,
    noteFirst,
  };
}

function textOf(node) {
  // \s also matches non-breaking spaces from the editor (&nbsp;).
  return node.textContent.replace(/\s/g, " ");
}

// Groups a run of nodes into blocks. Consecutive plain nodes are merged into
// one HTML block; IBAN/BIC paragraphs merge into one bank block.
function toBlocks(nodes) {
  const blocks = [];
  let html = [];

  const flushHtml = () => {
    if (html.length) {
      blocks.push({ type: "html", html: html.join("") });
      html = [];
    }
  };

  for (const node of nodes) {
    // Inline runs are already wrapped in paragraphs (wrapInlineRuns).
    if (node.nodeType !== Node.ELEMENT_NODE || isEmptyParagraph(node)) continue;

    const isCaption =
      node.classList.contains("wp-caption") || node.tagName === "FIGURE";
    const onlyImage =
      node.tagName === "P" && node.querySelector("img") && !textOf(node).trim();
    const figure = isCaption || onlyImage ? toFigure(node) : null;
    if (figure) {
      flushHtml();
      blocks.push(figure);
      continue;
    }

    const downloads = toDownloads(node);
    if (downloads) {
      flushHtml();
      blocks.push(downloads);
      continue;
    }

    const text = textOf(node);
    const iban = text.match(IBAN_PATTERN);
    const bic = text.match(BIC_PATTERN);
    const lastBlock = blocks[blocks.length - 1];
    if (iban) {
      flushHtml();
      blocks.push({ type: "bank", iban: iban[1], bic: bic ? bic[1] : "" });
      continue;
    }
    if (bic && !html.length && lastBlock?.type === "bank" && !lastBlock.bic) {
      lastBlock.bic = bic[1];
      continue;
    }

    html.push(node.outerHTML);
  }
  flushHtml();
  return blocks;
}

// Splits a section's nodes into a lead (before the first H3) and cards
// (one per H3). Fewer than two H3s are not worth a card grid.
function splitCards(nodes) {
  const h3Count = nodes.filter((n) => n.tagName === "H3").length;
  if (h3Count < 2) return { lead: toBlocks(nodes), cards: [] };

  const lead = [];
  const cards = [];
  let current = null;
  for (const node of nodes) {
    if (node.tagName === "H3") {
      current = { title: node.textContent.trim(), nodes: [] };
      cards.push(current);
    } else if (current) {
      current.nodes.push(node);
    } else {
      lead.push(node);
    }
  }
  return {
    lead: toBlocks(lead),
    cards: cards.map((c) => ({ title: c.title, blocks: toBlocks(c.nodes) })),
  };
}

export function parseCmsSections(html) {
  const doc = new DOMParser().parseFromString(html || "", "text/html");
  cleanNode(doc.body);

  const intro = [];
  const sections = [];
  let current = null;
  for (const node of [...doc.body.childNodes]) {
    if (node.tagName === "H2") {
      current = { title: node.textContent.trim(), nodes: [] };
      sections.push(current);
    } else if (current) {
      current.nodes.push(node);
    } else {
      intro.push(node);
    }
  }

  return {
    intro: toBlocks(intro),
    sections: sections.map((s) => ({ title: s.title, ...splitCards(s.nodes) })),
  };
}
