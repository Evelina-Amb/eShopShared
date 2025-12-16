import { loadStripe } from '@stripe/stripe-js';

document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('checkout-form');
    const paymentElementContainer = document.getElementById('payment-element');
    const payButton = document.getElementById('pay-button');
    const errorBox = document.getElementById('checkout-error');

    if (!form || !paymentElementContainer) return;

    const stripeKey = document
        .querySelector('meta[name="stripe-key"]')
        ?.getAttribute('content');

    const stripe = await loadStripe(stripeKey);
    let elements;

    //Create PaymentIntent
    try {
        const response = await fetch('/checkout/pay', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
            },
            body: JSON.stringify({
                address: 'pending',
                city: 'pending',
                postal_code: 'pending',
                country: 'pending',
            }),
        });

        if (!response.ok) {
            throw new Error('Payment initialization failed');
        }

        const { client_secret } = await response.json();

        elements = stripe.elements({ clientSecret: client_secret });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

    } catch (err) {
        errorBox.textContent = err.message;
        errorBox.classList.remove('hidden');
        return;
    }

    // Confirm payment on submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        payButton.disabled = true;
        payButton.textContent = 'Processing…';

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: `${window.location.origin}/checkout/success`,
            },
        });

        if (error) {
            errorBox.textContent = error.message;
            errorBox.classList.remove('hidden');
            payButton.disabled = false;
            payButton.textContent = 'Pay again';
        }
    });
});
