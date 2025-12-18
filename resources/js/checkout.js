import { loadStripe } from "@stripe/stripe-js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.getElementById("checkout-form");
  const errorBox = document.getElementById("checkout-error");
  const payButton = document.getElementById("pay-button");

  if (!form) return;

  const stripeKey = document.querySelector('meta[name="stripe-key"]')?.getAttribute("content");
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

  if (!stripeKey || !csrf) {
    errorBox.textContent = "Stripe configuration error.";
    errorBox.classList.remove("hidden");
    return;
  }

  const stripe = await loadStripe(stripeKey);

  let elements;
  let orderId;

  try {
    const res = await fetch("/checkout/intent", {
      method: "POST",
      headers: {
        "Accept": "application/json",
        "X-CSRF-TOKEN": csrf,
      },
    });

    const data = await res.json();

    if (!res.ok || !data.client_secret || !data.order_id) {
      throw new Error(data?.error || "Failed to initialize payment");
    }

    orderId = data.order_id;

    /* ---------- RENDER BREAKDOWN ---------- */
    if (data.breakdown) {
      const format = (cents) => `€${(cents / 100).toFixed(2)}`;

      document.getElementById("items-total").textContent =
        format(data.breakdown.items_total_cents);

      if (data.breakdown.small_order_fee_cents > 0) {
        document.getElementById("small-order-fee").textContent =
          format(data.breakdown.small_order_fee_cents);

        document.getElementById("small-order-row").classList.remove("hidden");
      }

      document.getElementById("order-total").textContent =
        format(data.breakdown.total_cents);
    }

    /* ---------- STRIPE ELEMENT ---------- */
    elements = stripe.elements({ clientSecret: data.client_secret });
    elements.create("payment").mount("#payment-element");

  } catch (err) {
    errorBox.textContent = err.message || "Failed to initialize payment";
    errorBox.classList.remove("hidden");
    return;
  }

  /* ---------- SUBMIT ---------- */
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.classList.add("hidden");

    payButton.disabled = true;
    payButton.textContent = "Processing…";

    try {
      const address = document.getElementById("address").value;
      const city = document.getElementById("city").value;
      const postal_code = document.getElementById("postal_code").value;
      const country = document.getElementById("country").value;

      const shipRes = await fetch("/checkout/shipping", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify({
          order_id: orderId,
          address,
          city,
          postal_code,
          country,
        }),
      });

      if (!shipRes.ok) {
        const d = await shipRes.json().catch(() => ({}));
        throw new Error(d?.error || "Failed to save shipping");
      }

      const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: `${window.location.origin}/checkout/success?order_id=${encodeURIComponent(orderId)}`,
        },
      });

      if (error) throw error;

    } catch (err) {
      errorBox.textContent = err.message || "Payment failed.";
      errorBox.classList.remove("hidden");
      payButton.disabled = false;
      payButton.textContent = "Pay again";
    }
  });
});
