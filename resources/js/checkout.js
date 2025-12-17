import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.getElementById("checkout-form");
  const errorBox = document.getElementById("checkout-error");

  if (!form) return;

  const stripeKey = document
    .querySelector('meta[name="stripe-key"]')
    ?.getAttribute("content");

  const stripe = await loadStripe(stripeKey);
  let elements;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.classList.add("hidden");

    const payload = {
      address: document.getElementById("address").value,
      city: document.getElementById("city").value,
      postal_code: document.getElementById("postal_code").value,
      country: document.getElementById("country").value,
    };

    try {
      const res = await fetch("/checkout/pay", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();

      if (!res.ok || !data.payment_intents) {
        throw new Error(data.error || "Payment initialization failed.");
      }

      if (!elements) {
        elements = stripe.elements({
          clientSecret: data.payment_intents[0].client_secret,
        });

        const paymentElement = elements.create("payment");
        paymentElement.mount("#payment-element");
      }

      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: `${window.location.origin}/checkout/success?order_id=${encodeURIComponent(data.order_id)}`,
        },
      });

      if (error) throw error;

    } catch (err) {
      errorBox.textContent = err.message || "Payment failed.";
      errorBox.classList.remove("hidden");
    }
  });
});
