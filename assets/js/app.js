var routes = {
  home: "/anasayfa",
  login: "/login",
  forgotPassword: "/sifremi-unuttum",
  register: "/register",
  quick: "/kayit",
  normal: "/normal-kayit",
  detailed: "/detayli-kayit",
  fields: "/tarlalar",
  account: "/hesap"
};

function go(path) {
  window.location.href = path;
}

function setMessage(element, text, type) {
  element.textContent = text;
  element.style.color = type === "error" ? "#ff5b5b" : "#39d873";
}

function numberValue(id) {
  return parseFloat(document.getElementById(id).value) || 0;
}

function textValue(id) {
  return document.getElementById(id).value.trim();
}

function formatMoney(value) {
  return `${value.toFixed(2)} TL`;
}
