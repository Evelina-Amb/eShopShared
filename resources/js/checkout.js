import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.getElementById("checkout-form");
  const errorBox = document.getElementById("checkout-error");

  if (!form) return;

  const stripeKey = document.querySelector('meta[name="stripe-key"]').content;
  const stripe = await loadStripe(stripeKey);

  let elements;
  let orderId;

  try {
    const res = await fetch("/checkout/intent", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({})
});
  }

    const data = await res.json();
    if (!data.client_secret) throw new Error("Failed to initialize payment");

    orderId = data.order_id;

    elements = stripe.elements({ clientSecret: data.client_secret });
    elements.create("payment").mount("#payment-element");
  } catch (err) {
    errorBox.textContent = err.message;
    errorBox.classList.remove("hidden");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `/checkout/success?order_id=${orderId}`,
      },
    });

    if (error) {
      errorBox.textContent = error.message;
      errorBox.classList.remove("hidden");
    }
  });
});
