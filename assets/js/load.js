$(document).ready(function () {
  $(".loader")
    .delay(800)
    .fadeOut(function () {
      document.documentElement.style.overflow = "auto";
    });
});

$(document).ready(function () {
  $(".loader-br").fadeOut(800);
});
