(function(){const r=document.createElement("link").relList;if(r&&r.supports&&r.supports("modulepreload"))return;for(const e of document.querySelectorAll('link[rel="modulepreload"]'))a(e);new MutationObserver(e=>{for(const o of e)if(o.type==="childList")for(const l of o.addedNodes)l.tagName==="LINK"&&l.rel==="modulepreload"&&a(l)}).observe(document,{childList:!0,subtree:!0});function n(e){const o={};return e.integrity&&(o.integrity=e.integrity),e.referrerPolicy&&(o.referrerPolicy=e.referrerPolicy),e.crossOrigin==="use-credentials"?o.credentials="include":e.crossOrigin==="anonymous"?o.credentials="omit":o.credentials="same-origin",o}function a(e){if(e.ep)return;e.ep=!0;const o=n(e);fetch(e.href,o)}})();function g(){const t=document.querySelector("[data-site-header]");t&&(t.className="site-header",t.innerHTML=`
    <a class="brand" href="./index.html" aria-label="Oakter home">
      <img src="./assets/oakter-logo-280.png" alt="Oakter" />
    </a>
    <button class="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <nav class="primary-nav" aria-label="Primary navigation">
      <a href="./index.html">Studio AC</a>
      <a href="./mini-ups.html">Mini UPS</a>
      <a href="./gan-charger.html">GaN Charger</a>
      <a href="./b2b.html">B2B Products</a>
    </nav>
  `)}const y='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.43.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.43.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.43-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.43-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16Zm0 1.62c-3.15 0-3.52.01-4.76.07-.95.04-1.47.2-1.81.34-.46.18-.78.39-1.12.73-.34.34-.55.66-.73 1.12-.13.34-.3.86-.34 1.81-.06 1.24-.07 1.61-.07 4.76s.01 3.52.07 4.76c.04.95.2 1.47.34 1.81.18.46.39.78.73 1.12.34.34.66.55 1.12.73.34.13.86.3 1.81.34 1.24.06 1.61.07 4.76.07s3.52-.01 4.76-.07c.95-.04 1.47-.2 1.81-.34.46-.18.78-.39 1.12-.73.34-.34.55-.66.73-1.12.13-.34.3-.86.34-1.81.06-1.24.07-1.61.07-4.76s-.01-3.52-.07-4.76c-.04-.95-.2-1.47-.34-1.81a3 3 0 0 0-.73-1.12 3 3 0 0 0-1.12-.73c-.34-.13-.86-.3-1.81-.34-1.24-.06-1.61-.07-4.76-.07Zm0 2.76a5.3 5.3 0 1 1 0 10.6 5.3 5.3 0 0 1 0-10.6Zm0 8.74a3.44 3.44 0 1 0 0-6.88 3.44 3.44 0 0 0 0 6.88Zm5.5-8.94a1.24 1.24 0 1 1-2.48 0 1.24 1.24 0 0 1 2.48 0Z"/></svg>',w='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.01 10.13 11.93v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.79-4.7 4.54-4.7 1.32 0 2.7.24 2.7.24v2.97h-1.52c-1.5 0-1.96.93-1.96 1.89v2.26h3.34l-.53 3.49h-2.81V24C19.61 23.08 24 18.1 24 12.07Z"/></svg>',L='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 6.19C0 8.07 0 12 0 12s0 3.93.5 5.81a3.02 3.02 0 0 0 2.12 2.14c1.88.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14C24 15.93 24 12 24 12s0-3.93-.5-5.81ZM9.55 15.57V8.43L15.82 12l-6.27 3.57Z"/></svg>',M='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6Zm-2 0-8 5-8-5h16Zm0 12H4V8l8 5 8-5v10Z"/></svg>',C='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.39-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.61-.92-2.21-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37s-1.04 1.02-1.04 2.48 1.07 2.88 1.21 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35ZM12.05 21.79h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26C2.16 6.45 6.6 2.01 12.05 2.01c2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.44 9.89-9.88 9.89ZM20.52 3.45A11.78 11.78 0 0 0 12.04 0C5.46 0 .1 5.36.1 11.89c0 2.1.55 4.14 1.59 5.95L0 24l6.3-1.65a11.88 11.88 0 0 0 5.69 1.45h.01c6.58 0 11.94-5.36 11.94-11.89 0-3.18-1.24-6.17-3.49-8.42Z"/></svg>';function S(){const t=document.querySelector("[data-site-footer]");t&&(t.className="site-footer",t.id="contact",t.innerHTML=`
    <div class="footer-top">
      <div class="footer-brand">
        <img src="./assets/oakter-logo-220.png" alt="Oakter" />
        <p>Made in India power, cooling and smart technology products.</p>
        <div class="footer-social" aria-label="Oakter on social media">
          <a href="https://www.instagram.com/oyeoakter/" target="_blank" rel="noopener" aria-label="Instagram">${y}</a>
          <a href="https://www.facebook.com/oakter/" target="_blank" rel="noopener" aria-label="Facebook">${w}</a>
          <a href="https://www.youtube.com/channel/UC3h_V9-78yWVbtTi5eNWvZQ" target="_blank" rel="noopener" aria-label="YouTube">${L}</a>
          <a href="mailto:oye@oakter.com" aria-label="Email">${M}</a>
          <a href="https://wa.me/917575040506" target="_blank" rel="noopener" aria-label="WhatsApp">${C}</a>
        </div>
      </div>
      <nav class="footer-col" aria-label="Products">
        <strong>Products</strong>
        <a href="./index.html">Studio AC</a>
        <a href="./mini-ups.html">Mini UPS</a>
        <a href="./gan-charger.html">GaN Charger</a>
        <a href="./b2b.html">B2B Products</a>
      </nav>
      <div class="footer-col">
        <strong>Company</strong>
        <a href="./about.html">About us</a>
        <a href="./contact-us.html">Contact us</a>
        <a href="tel:+917575040506">+91 75750-40506</a>
        <a href="mailto:oye@oakter.com">oye@oakter.com</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2026 Oakter. Made in India.</span>
      <a href="./privacy-policy.html">Privacy policy</a>
    </div>
  `)}g();S();const p=document.querySelector(".site-header"),s=document.querySelector(".menu-toggle"),x=document.querySelectorAll(".primary-nav a");s==null||s.addEventListener("click",()=>{const t=p.classList.toggle("is-open");s.setAttribute("aria-expanded",String(t)),s.setAttribute("aria-label",t?"Close menu":"Open menu")});x.forEach(t=>{t.addEventListener("click",()=>{p.classList.remove("is-open"),s==null||s.setAttribute("aria-expanded","false"),s==null||s.setAttribute("aria-label","Open menu")})});const v=window.matchMedia("(prefers-reduced-motion: reduce)").matches;if(!v&&"IntersectionObserver"in window){const t=document.querySelectorAll([".section",".feature-grid article",".pdp-section > *",".product-band > *",".cards > *",".detail-columns > *",".gan-details > *",".section-heading"].join(", ")),r=new IntersectionObserver(n=>{n.forEach(a=>{(a.isIntersecting||a.boundingClientRect.top<0)&&(a.target.classList.add("in-view"),r.unobserve(a.target))})},{threshold:.12,rootMargin:"0px 0px -8% 0px"});t.forEach((n,a)=>{n.classList.add("reveal"),n.style.setProperty("--reveal-delay",`${Math.min(a%4,3)*60}ms`),r.observe(n)})}const I=window.matchMedia("(max-width: 620px)");document.querySelectorAll(".carousel-shell").forEach(t=>{const r=t.querySelector("[data-carousel]"),n=t.querySelector(".carousel-dots"),a=r?Array.from(r.children):[];let e=0,o;if(!r||!n||a.length<2)return;const l=a.map((c,i)=>{const d=document.createElement("button");return d.type="button",d.setAttribute("aria-label",`Go to slide ${i+1}`),n.append(d),d}),u=()=>{l.forEach((c,i)=>{c.classList.toggle("is-active",i===e)})},h=c=>{e=(c+a.length)%a.length,r.scrollTo({left:a[e].offsetLeft-r.offsetLeft,behavior:"smooth"}),u()},f=()=>{v||!I.matches||(window.clearInterval(o),o=window.setInterval(()=>h(e+1),3600))};l.forEach((c,i)=>{c.addEventListener("click",()=>{h(i),f()})}),r.addEventListener("scroll",()=>{const c=a.reduce((i,d,b)=>{const m=Math.abs(r.scrollLeft-(d.offsetLeft-r.offsetLeft));return m<i.distance?{index:b,distance:m}:i},{index:e,distance:Number.POSITIVE_INFINITY});c.index!==e&&(e=c.index,u())}),u(),f()});
