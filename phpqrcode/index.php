<html>  
<head>  
    <title>Qr Generation Form</title>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>  
    <link rel="stylesheet" type="text/css" href="css/style.css/">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">
    </script>
</head>
<body>  
  <div class="container">          
   <div class="table-responsive">  
    <h3 align="center">QR Generation Form</h3><br/>
    <div class="box">
     <form method="post" action="qrcode.php" > 
      <div class="form-group">
         <label>QR Text</label>
         <input type="text" name="qrtext" id="qrtext" placeholder="Enter QR Text" required data-parsley-pattern="^[a-zA-Z]+$" data-parsley-trigger="keyup" class="form-control" />
      </div>
      <div class="form-group">
       <input type="submit" name="sbt-btn" value="QR Generate" class="btn btn-success" />
      </div>
     </form>
    </div>
   </div>  
  </div>
 </body>  
</html> 