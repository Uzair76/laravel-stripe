<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>

    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        #card-element {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<form id="payment-form">
    @csrf

    <!-- Name -->
    <input type="text" id="card-holder-name" placeholder="Card Holder Name">
    <br><br>

    <!-- Stripe Card Element -->
    <div id="card-element"></div>
    <br>

    <button type="submit">Submit Payment</button>
</form>

<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}'); // from Stripe dashboard

    const elements = stripe.elements();

    const cardElement = elements.create("card");
    cardElement.mount("#card-element");

    const form = document.getElementById("payment-form");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: "card",
            card: cardElement,
            billing_details: {
                name: document.getElementById("card-holder-name").value
            }
        });

        if (error) {
            alert(error.message);
        } else {
            console.log("PaymentMethod ID:", paymentMethod.id);

            // Send this ID to Laravel backend
        }
    });
</script>

</body>
</html>