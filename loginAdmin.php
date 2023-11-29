<!DOCTYPE html>
<html>
<link rel="stylesheet" href="CSS/logindesign.css">
    <title>Staff - Login</title>
    <header>
</header>
<body>
<div class="split-background">
      
        <div class="content">
        <div class="header">
  <class="logo">AUTOMATED CAR PARKING</a>
 
  </div>
</div>

<div style="padding-left: 500px">

</div>

</body>
<style>
    <style>
* {box-sizing: border-box;}

body { 
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.header {
  overflow: hidden;
  background-color: #f1f1f1;
  padding: 30px 20px;
  
}

.header a {
  float: left;
  color: black;
  text-align: center;
  padding: 12px;
  text-decoration: none;
  font-size: 18px; 
  line-height: 25px;
  border-radius: 4px;
}

.header a.logo {
  font-size: 25px;
  font-weight: bold;
}







</style>
    </style>

<div class="form">
    <form class="sub-form" method ="post" action="loginAdminForm.php">
        <div class="upper-form">
            <h2>ADMIN LOGIN</h2>
            <?php if(isset($_GET['error'])){ ?>
            <div class="alert alert-danger" role="alert">
            <?=$_GET['error']?>
            </div>
            <?php } ?>
            <label>Username</label> <br>
            <input type="text" name="username"> <br>
            <label>Password</label> <br>
            <input type="password" name="password"> <br>
            <button type="submit">Login</button> <br>
        </div>
    </form>
</div>
</html>