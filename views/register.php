<?php include_once("header.php") ?>

<div class="container">
  <h2 class="my-3">Register new account</h2>

  <!-- Create auction form -->
  <form method="POST" action="process_registration.php" onsubmit="return formCheck()">
    <div class="form-group row">
      <label for="accountType" class="col-sm-2 col-form-label text-right">Registering as a:</label>
      <div class="col-sm-10">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" onclick="toggleRadioRequired()">
          <label class="form-check-label" for="accountBuyer">Buyer</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller" onclick="toggleRadioRequired()">
          <label class="form-check-label" for="accountSeller">Seller</label>
        </div>
        <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required.</span></small>
      </div>
    </div>
    <div class="form-group row">
      <label for="userEmail" class="col-sm-2 col-form-label text-right">Email</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="userEmail" placeholder="Email" oninput="toggleRequired('emailHelp', this)">
        <small id="emailHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
      </div>
    </div>
    <div class="form-group row">
      <label for="initialPassword" class="col-sm-2 col-form-label text-right">Password</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="initialPassword" placeholder="Password" oninput="toggleRequired('passwordHelp', this)">
        <small id="passwordHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
      </div>
    </div>
    <div class="form-group row">
      <label for="repeatPassword" class="col-sm-2 col-form-label text-right">Repeat password</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="repeatPassword" placeholder="Enter password again" oninput="toggleRequired('repeatPasswordHelp', this)">
        <small id="repeatPasswordHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
      </div>
    </div>
    <div class="form-group row">
      <button type="submit" class="btn btn-primary form-control">Register</button>
    </div>
  </form>

  <div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>

  </div>

  <?php include_once("footer.php") ?>

  <script>
    function formCheck() {

      // Read inputs
      const email = document.getElementById("userEmail").value;
      const initialPassword = document.getElementById("initialPassword").value;
      const repeatPassword = document.getElementById("repeatPassword").value;

      // Email check (regex might not be the best way)
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email)) {
        alert("Please enter a valid email.");
        return false;
      }

      // Password matching check
      if (initialPassword !== repeatPassword) {
        alert("Passwords do not match.");
        return false;
      }

      // Continue form submission if all checks pass
      return true;
    }

    // Required text visibility toggle for text inputs
    function toggleRequired(helpId, inputField) {
      const helpText = document.getElementById(helpId);
      if (inputField.value) {
        helpText.style.display = "none";
      } else {
        helpText.style.display = "inline";
      }
    }

    // Required text visibility toggle for radio buttons
    function toggleRadioRequired() {
      const accountTypeHelp = document.getElementById("accountTypeHelp");
      accountTypeHelp.style.display = "none";
    }
  </script>