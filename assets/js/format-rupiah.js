// Format Nominal format rupiah
const inputBudget = document.getElementById("inputBudget");

inputBudget.addEventListener("input", () => {
  // Remove any non-digit characters
  let input = inputBudget.value.replace(/[^\d]/g, "");
  // Convert to a number and format with "Rp" prefix and "." and "," separator
  let formattedInput = Number(input).toLocaleString("id-ID", {
    style: "currency",
    currency: "IDR",
  });
  // Remove trailing ",00" if present
  formattedInput = formattedInput.replace(",00", "");
  // Update the input value with the formatted number
  inputBudget.value = formattedInput;
});
