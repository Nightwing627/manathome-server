
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   </head>
   <style>
      /* header start */
      .container
      {
      margin-right: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      margin-left: 0 !important;
      width: 100% !important;
      max-width: 100%;
      }
      .bgcolor
      {
      background-image: -webkit-linear-gradient( 0deg, rgb(52,82,255) 0%, rgb(255,16,83) 100%);
      height:100px;
      }
      .textcenter
      {
      text-align:center;
      }
      .whitecolor
      {
      color:white;
      }
      .padding25
      {
      padding:12px;
      }
      /* end header */
      /* start main content */
      .centerimage
      {
      width:100px;
      }
      .mauto_dblock
      {
      margin:auto;
      display:block;
      }
      .mymarg
      {
      margin: -28px;
      /* padding: 0; */
      margin-right: 0;
      margin-left: 0;
      }
      .card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background: #fff !important;
      background-clip: border-box !important;;
      border-radius: 6px !important;
      border: 0 !important;
      margin-bottom: 1.3rem !important;;
      box-shadow: 6px 11px 41px -28px #796eb1 !important;
      -webkit-box-shadow: 6px 11px 41px -28px #796eb1 !important;
      }
      .margintop40
      {
      margin-top:40px;
      }
      .fontsize18
      {
      font-size:18px;
      }
      .padding35
      {
      padding:35px;
      }
      .fontweight600
      {
      font-weight: 600;
      }
      .greycolor
      {
      color:grey;
      }
      .fontweight400
      {
      font-weight:400 !important;
      }
      .cologreen
      {
      color:#10ff00 !important;
      }
      /*end main content */
      /* start footer */
      .bluecolor
      {
      color:#6767ff !important;
      }
      .paddingrl
      {
      padding-right: 124px;
      padding-left: 124px;
      }
      .marginbotom39
      {
      margin-bottom:39px;
      }
      .imageer{
    height: 96px;
    width: 227px;
      }
      .imagetoper{
          margin-bottom: 10px !important;
      }
      /* end footer */
      /* a4 size css */
      #page-wrap { width: 800px; margin: 0 auto; }
      p{
      font-weight:600;
      }
      #header {  width: 100%; margin: 20px 0;  color:black; font: Helvetica, Sans-Serif; text-decoration: uppercase;  padding: 8px 0px; }
      .delete-wpr { position: relative; }
      .delete { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }
      /*end */
   </style>
   <body>
      <div id="page-wrap">
         <div id="header">
            <div class="container">
               <div class="bgcolor">
                  <div class="welcomediv padding25">
                     <span class="textcenter whitecolor imagetoper">
                         <img src="<?php echo base_url(); ?>uploads/logo_image/skillsafari_logo1.png"  class="centerimage mauto_dblock imageer"></span>
                  </div>
               </div>
            </div>
            <!-- end right column -->
            <!-- end row -->
         </div>
         <!-- end header -->
         <div class="card mymarg">
            <div class="columncenter padding35">
               <div class="imagediv margintop40">
                  
               </div>
               <div class="centercontentdiv margintop40">
                  <h1 class="fontsize18 fontweight600">Hey <?php echo $username;?></h1>
                  
                  <p class="greycolor fontweight400">This is your OTP <?php echo $optdata; ?>
                   
                  </p>
                    <br>
                  <p class="greycolor fontweight400">Before we get started, we'll need to verify your email</p>
                  <br>
                 
               </div>
            </div>
         </div>
        
        
   </body>
</html>