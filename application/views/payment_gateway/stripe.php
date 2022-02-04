<?php



require(APPPATH.'libraries/vendor/autoload.php');
\Stripe\Stripe::setApiKey($this ->Api_model ->get_type_name_by_id('general_settings', '101', 'value'));

$session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price_data' => [
      'currency' => 'usd',
      'product_data' => [
        'name' => 'Product',
      ],
      'unit_amount' =>  $grandtotal * 100,
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => base_url().'index.php/Stripe/success',
  'cancel_url' =>  base_url().'index.php/Stripe/cancel',
]);

?>

<style>
.button {
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}

.button1 {background-color: #797dff;} /* Green */
 /* Blue */
</style>
<html>
    
  <head>
    <title>Buy cool new product</title>
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body>
    <button  class="button button1"id="checkout-button">Stripe Checkout</button>
    <script>
      var stripe = Stripe('<?php echo $this ->Api_model ->get_type_name_by_id('general_settings', '100', 'value'); ?>');
      const btn = document.getElementById("checkout-button")
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        stripe.redirectToCheckout({
          sessionId: "<?php echo $session->id; ?>"
        });
      });
        
       
    </script>
  </body>
</html>