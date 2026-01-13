const PRODUCTS = {
  "four-post": {
    title: "Four-Post Lift",
    img: "public/rentals/four-post.png",
    desc: "Heavy-duty four-post lift for stable vehicle support, perfect for long-hour repairs and maintenance.",
    features: [
      "High stability & safety",
      "Ideal for long bookings",
      "Easy drive-on access",
    ],
    price: "$45 / Hour",
    note: "Packages available: 10 Hours & 20 Hours",
  },

  "two-post": {
    title: "Two-Post Lift",
    img: "public/rentals/two-post.png",
    desc: "Fast access lift designed for mechanical repair work with compact footprint.",
    features: [
      "Quick underbody access",
      "Space efficient",
      "Perfect for repairs",
    ],
    price: "$45 / Hour",
    note: "Packages available: 10 Hours & 20 Hours",
  },

  scissor: {
    title: "Scissor Lift",
    img: "public/rentals/scissor.png",
    desc: "Low profile scissor lift, great for quick services like tire, brake, and detailing work.",
    features: [
      "Low-profile design",
      "Fast lifting operation",
      "Great for tire & brake work",
    ],
    price: "$45 / Hour",
    note: "Packages available: 10 Hours & 20 Hours",
  },

  "engine-hoist": {
    title: "Engine Hoist",
    img: "public/rentals/engine-hoist.png",
    desc: "Reliable engine hoist for safe engine lifting and workshop handling.",
    features: [
      "Strong lifting capacity",
      "Easy movement",
      "Workshop grade build",
    ],
    price: "$—",
    note: "Contact us for pricing",
  },

  "diag-scanner": {
    title: "Diagnostic Scanner",
    img: "public/rentals/diag-scanner.png",
    desc: "Professional diagnostic scanner for checking and clearing vehicle codes.",
    features: [
      "Fast fault detection",
      "Code clear & data reading",
      "Modern vehicle support",
    ],
    price: "$—",
    note: "Contact us for pricing",
  },

  "ac-r134a": {
    title: "AC Machine (R134a)",
    img: "public/rentals/ac-machine-r134a.png",
    desc: "AC service machine for R134a refrigerant systems with safe recovery and recharge.",
    features: [
      "Recovery & recharge",
      "Workshop professional use",
      "Safe & accurate",
    ],
    price: "$—",
    note: "Contact us for pricing",
  },

  "ac-r1234yf": {
    title: "AC Machine (R1234yf)",
    img: "public/rentals/ac-machine-r1234yf.png",
    desc: "AC service machine designed for modern R1234yf systems for reliable performance.",
    features: [
      "Modern vehicle compatible",
      "Recovery & recharge",
      "Accurate performance",
    ],
    price: "$—",
    note: "Contact us for pricing",
  },
  "tool-rental": {
    title: "Tool Rentals",
    img: "public/rentals/tool-rentals.png",
    desc: "Wide range of professional automotive tools available for rent, suitable for DIY repairs and workshop-level jobs.",
    features: [
      "Professional-grade tools",
      "Wide variety for all repairs",
      "Well-maintained & ready to use",
    ],
    price: "$—",
    note: "Contact us for pricing",
  },
};

function getQueryParam(name) {
  const url = new URL(window.location.href);
  return url.searchParams.get(name);
}

const itemKey = getQueryParam("item");
const product = PRODUCTS[itemKey];

if (!product) {
  // fallback
  document.getElementById("detailsTitle").textContent = "Item not found";
  document.getElementById("detailsDesc").textContent =
    "Please go back and select a rental item.";
  document.getElementById("detailsBookBtn").style.display = "none";
} else {
  document.getElementById("detailsTitle").textContent = product.title;
  document.getElementById("detailsDesc").textContent = product.desc;
  document.getElementById("detailsImg").src = product.img;

  document.getElementById("detailsFeatures").innerHTML = product.features
    .map((f) => `<li>${f}</li>`)
    .join("");

  document.getElementById("detailsPrice").textContent = product.price;
  document.getElementById("detailsNote").textContent = product.note;

  document.getElementById("detailsBookBtn").addEventListener("click", () => {
    // ✅ Go to your booking page with item key
    window.location.href = `/booking`;
  });
}
