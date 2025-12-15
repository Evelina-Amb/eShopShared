import { loadStripe } from '@stripe/stripe-js';

document.addEventListener('DOMContentLoaded', async () => {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    const payButton = document.getElementById('pay-button');
    const errorBox = document.getElementById('payment-error');

    try {
        //Create PaymentIntent on backend
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

        const data = await response.json();

        if (!data.client_secret) {
            throw new Error('Payment initialization failed.');
        }

        //Initialize Stripe
        const stripe = await loadStripe(
            document.querySelector('meta[name="stripe-key"]').content
        );

        const elements = stripe.elements({
            clientSecret: data.client_secret,
        });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#payment-element');

        //Submit payment
        checkoutForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            payButton.disabled = true;
            payButton.innerText = 'Processing...';

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: `${window.location.origin}/checkout/success`,
                },
            });

            if (error) {
                errorBox.innerText = error.message;
                errorBox.classList.remove('hidden');
                payButton.disabled = false;
                payButton.innerText = 'Pay now';
            }
        });

    } catch (err) {
        console.error(err);
        errorBox.innerText = err.message || 'Payment error.';
        errorBox.classList.remove('hidden');
    }
});
