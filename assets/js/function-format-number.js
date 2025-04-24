function formatNumberInd(
  number,
  locale = "id-ID",
  options = { style: "decimal" }
) {
  return new Intl.NumberFormat(locale, options).format(number);
}
