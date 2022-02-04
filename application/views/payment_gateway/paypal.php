<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
  </head>

  <body>
      
    
      
    <!-- Include the PayPal JavaScript SDK; replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this
            ->Api_model
            ->get_type_name_by_id('general_settings', '98', 'value'); ?>&currency=USD"></script>

    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
   

    <script>
      
      paypal.Buttons({

        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: <?php echo $grandtotal; ?> // Can reference variables or functions. Example: `value: document.getElementById('...').value`
              }
            }]
          });
        },

        // Finalize the transaction after payer approval
         onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
            console.log(details)
            window.location.replace("<?php echo base_url(); ?>index.php/Paypal/success")
        })
    },
      }).render('#paypal-button-container');
        
     

    </script>
      
     
  </body>
</html>