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

    //Load Stripe
    const stripe = await loadStripe(stripeKey);

    //Create PaymentIntent
    let clientSecret;
    try {
        const intentRes = await fetch('/checkout/intent', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .content,
            },
        });

        const intentData = await intentRes.json();

        if (!intentData.client_secret) {
            throw new Error('Failed to initialize payment');
        }

        clientSecret = intentData.client_secret;
    } catch (err) {
        console.error(err);
        errorBox.innerText = 'Payment initialization failed.';
        errorBox.classList.remove('hidden');
        return;
    }

    //Mount Stripe Payment Element
    const elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    //Confirm payment on submit
    checkoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorBox.classList.add('hidden');
        payButton.disabled = true;
        payButton.innerText = 'Processing...';

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: `${window.location.origin}/checkout/success`,
            },
        });

        if (error) {
            errorBox.innerText = error.message || 'Payment failed.';
            errorBox.classList.remove('hidden');
            payButton.disabled = false;
            payButton.innerText = 'Pay again';
        }
    });
});
