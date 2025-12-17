import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.getElementById("checkout-form");
  const errorBox = document.getElementById("checkout-error");

  if (!form) return;

  const stripeKey = document
    .querySelector('meta[name="stripe-key"]')
    ?.getAttribute("content");

  if (!stripeKey) {
    errorBox.textContent = "Stripe configuration error.";
    errorBox.classList.remove("hidden");
    return;
  }

  const stripe = await loadStripe(stripeKey);
  let elements;

  try {

    const res = await fetch("/checkout/pay", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document
          .querySelector('meta[name="csrf-token"]')
          .content,
      },
      body: JSON.stringify({
        address: "__pending__",
        city: "__pending__",
        postal_code: "__pending__",
        country: "__pending__",
      }),
    });

    const data = await res.json();

    if (!res.ok || !data.client_secret) {
      throw new Error(data.error || "Payment initialization failed.");
    }
    
    elements = stripe.elements({ clientSecret: data.client_secret });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

  } catch (err) {
    errorBox.textContent = err.message || "Payment initialization failed.";
    errorBox.classList.remove("hidden");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.classList.add("hidden");

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `${window.location.origin}/checkout/success`,
      },
    });

    if (error) {
      errorBox.textContent = error.message;
      errorBox.classList.remove("hidden");
    }
  });
});
