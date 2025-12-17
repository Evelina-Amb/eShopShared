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
  let orderId;

  try {
    // 🔥 CREATE PAYMENT INTENTS ON PAGE LOAD
    const res = await fetch("/checkout/pay", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document
          .querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        address: "pending",
        city: "pending",
        postal_code: "pending",
        country: "pending",
      }),
    });

    const data = await res.json();

    if (!res.ok || !data.payment_intents?.length) {
      throw new Error(data.error || "Payment initialization failed.");
    }

    orderId = data.order_id;

    elements = stripe.elements({
      clientSecret: data.payment_intents[0].client_secret,
    });

    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

  } catch (err) {
    errorBox.textContent = err.message || "Failed to load payment form.";
    errorBox.classList.remove("hidden");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.classList.add("hidden");

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `${window.location.origin}/checkout/success?order_id=${encodeURIComponent(orderId)}`,
      },
    });

    if (error) {
      errorBox.textContent = error.message || "Payment failed.";
      errorBox.classList.remove("hidden");
    }
  });
});
