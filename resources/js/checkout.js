import { loadStripe } from '@stripe/stripe-js';

document.addEventListener('DOMContentLoaded', async () => {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    const payButton = document.getElementById('pay-button');
    const errorBox = document.getElementById('checkout-error');
    const stripeKey = document.querySelector('meta[name="stripe-key"]')?.content;

    if (!stripeKey) {
        console.error('Missing Stripe public key');
        return;
    }

    const stripe = await loadStripe(stripeKey);
    let elements;

    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorBox.classList.add('hidden');
        payButton.disabled = true;
        payButton.innerText = 'Initializing payment...';

        try {
            // 🔹 Create PaymentIntent ONLY when user clicks Pay
            const response = await fetch('/checkout/pay', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                body: JSON.stringify({
                    address: document.getElementById('address').value,
                    city: document.getElementById('city').value,
                    postal_code: document.getElementById('postal_code').value,
                    country: document.getElementById('country').value,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to initialize payment');
            }

            const data = await response.json();

            elements = stripe.elements({
                clientSecret: data.client_secret,
            });

            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            payButton.innerText = 'Processing...';

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: `${window.location.origin}/checkout/success`,
                },
            });

            if (error) {
                throw error;
            }

        } catch (err) {
            console.error(err);
            errorBox.innerText = err.message || 'Payment failed.';
            errorBox.classList.remove('hidden');
            payButton.disabled = false;
            payButton.innerText = 'Pay again';
        }
    });
});
