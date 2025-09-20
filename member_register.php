<?php
  include 'header.php';
?>
     <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-7 col-lg-7 col-md-12">
                 <h2 class="text-center text-dark mt-5">Member Register Form</h2>
    
                <div class="card o-hidden my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                             <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                    </div>
                                    <form class="user">
                                        <div class="form-group row mb-3">
                                            <div class="col-sm-6">
                                                <label for="First Name"><b>First Name</b></label>
                                                <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                                    placeholder="First Name">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="Last Name"><b>Last Name</b></label>
                                                <input type="text" class="form-control form-control-user" id="exampleLastName"
                                                    placeholder="Last Name">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="Email Address"><b>Email Address</b></label>
                                            <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                                                placeholder="Email Address">
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-sm-6">
                                                <label for="Password"><b>Password</b></label>
                                                <input type="password" class="form-control form-control-user"
                                                    id="exampleInputPassword" placeholder="Password">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="Password"><b>Repeat Password</b></label>
                                                <input type="password" class="form-control form-control-user"
                                                    id="exampleRepeatPassword" placeholder="Repeat Password">
                                            </div>
                                        </div>
                                        <a href="login.html" class="btn btn-primary w-100 mb-3">
                                            Register Account
                                        </a>
                                    </form>
                                    <hr>
                                    <div class="text-center mt-3">
                                        <a href="#" class="text-dark fw-bold">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a href="member_login.php"  class="text-dark fw-bold">Already have an account? Login!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?php
  include 'footer.php';
?>