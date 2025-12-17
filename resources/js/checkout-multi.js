import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const payBtn = document.getElementById("pay-btn");
  const payError = document.getElementById("pay-error");
  const cardMount = document.getElementById("card-element");

  if (!payBtn || !cardMount) return;

  const publishableKey = document.querySelector('meta[name="stripe-key"]')?.content;
  if (!publishableKey) {
    payError.textContent = "Missing Stripe publishable key.";
    return;
  }

  const stripe = await loadStripe(publishableKey);
  const elements = stripe.elements();
  const card = elements.create("card");
  card.mount("#card-element");

  payBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    payError.textContent = "";
    payBtn.disabled = true;

    try {
      const address = document.querySelector('[name="address"]').value;
      const city = document.querySelector('[name="city"]').value;
      const postal_code = document.querySelector('[name="postal_code"]').value;
      const country = document.querySelector('[name="country"]').value;

      const res = await fetch("/checkout/pay", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "Accept": "application/json",
        },
        body: JSON.stringify({ address, city, postal_code, country }),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data?.error || "Payment initialization failed.");
      }

      const orderId = data.order_id;
      const paymentIntents = data.payment_intents || [];
      if (!orderId || paymentIntents.length === 0) {
        throw new Error("Missing payment intents.");
      }

      for (const pi of paymentIntents) {
        const clientSecret = pi.client_secret;

        const result = await stripe.confirmCardPayment(clientSecret, {
          payment_method: { card },
        });

        if (result.error) {
          throw new Error(result.error.message || "Payment failed.");
        }

        if (result.paymentIntent && result.paymentIntent.status !== "succeeded") {
          throw new Error("Payment not completed.");
        }
      }

      window.location.href = `/checkout/success?order_id=${encodeURIComponent(orderId)}`;
    } catch (err) {
      payError.textContent = err.message || "Payment failed.";
      payBtn.disabled = false;
    }
  });
});
