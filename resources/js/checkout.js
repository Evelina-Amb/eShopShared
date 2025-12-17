import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.getElementById("checkout-form");
  const errorBox = document.getElementById("checkout-error");

  if (!form) return;

  const stripeKey = document.querySelector('meta[name="stripe-key"]').content;
  const stripe = await loadStripe(stripeKey);

  let elements;

  try {
    const res = await fetch("/checkout/pay", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        address: "pending",
        city: "pending",
        postal_code: "pending",
        country: "pending",
      }),
    });

    const data = await res.json();
    if (!data.client_secret) throw new Error("Payment init failed");

    elements = stripe.elements({ clientSecret: data.client_secret });
    elements.create("payment").mount("#payment-element");

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: `/checkout/success?order_id=${data.order_id}`,
        },
      });

      if (error) {
        errorBox.textContent = error.message;
        errorBox.classList.remove("hidden");
      }
    });
  } catch (err) {
    errorBox.textContent = err.message;
    errorBox.classList.remove("hidden");
  }
});
