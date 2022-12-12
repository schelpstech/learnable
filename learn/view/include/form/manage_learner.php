<div class="main_content_iner overly_inner ">
    <div class="container-fluid p-0 ">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!--View Profile--->
                <div class="col-xl-12" id="view_profile">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0"><?php echo $learner_profile['fname'] ?>'s Profile</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="card-profile"><img src="
                                <?php
                                if (isset($learner_profile['picture'])) {
                                    echo '../../asset/img/passport/'. $learner_profile['picture'] . '"';  
                                } else {
                                    echo '../../asset/img/staff/instructor.jpg';
                                }
                                ?>" alt="" data-original-title="" title="">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Fullname</span>
                                </div>
                                <input type="text" disabled class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['fname'] ?>">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="classid">Learner's Class</label>
                                </div>
                                <select class="form-select" disabled tabindex="1" required="yes">
                                    <option value="<?php echo $learner_profile['classid'] ?>"><?php echo $learner_profile['classname'] ?></option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="subject">Learner's Gender</label>
                                </div>
                                <select class="form-select" disabled tabindex="2" required="yes">
                                    <option value="<?php echo $learner_profile['gender'] ?>"><?php echo $learner_profile['gender'] ?? "" ?></option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Date of Birth</span>
                                </div>
                                <input type="date" disabled class="form-control" value="<?php echo $learner_profile['dob'] ?? "" ?>" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Phonenumber</span>
                                </div>
                                <input type="text" disabled class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['numb'] ?? "" ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Email</span>
                                </div>
                                <input type="text" disabled class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['email'] ?? "" ?>">
                            </div>
                            <button tabindex="5" class="btn_1 full_width text-center" onclick="toggle_modify()">Modify Learner Details</button>
                        </div>
                    </div>
                </div>
                <div class="media_thumb ml_25" id="loader" style="display: none;">
                    <img src="../../asset/img/app/giphy.gif" alt="">
                </div>
                <!--Edit Profile--->
                <div class="col-xl-12" id="edit_profile" style="display: none;">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Edit <?php echo $learner_profile['fname'] ?>'s Profile</h3>
                                </div>
                            </div>
                        </div>

                        <div class="white_card_body">
                            <div class="card-profile" id="mypassport">
                                <img src="<?php
                                            if (!empty($learner_profile['picture'])) {
                                                echo '../../asset/img/passport/'. $learner_profile['picture'];
                                            }else {
                                                    echo ' " style="display: none;" ';
                                            }
                                            ?> " width="200" />

                            </div>
                            <div class="card-profile" id="preview" style="display: none;">
                                <canvas id="myCanvas" width="275" height="314"></canvas>
                            </div>
                            <div class="input-group mb-3">
                                <input type="file" id="imageInput" max-size="1000" accept="image/png, image/jpg, image/jpeg" class="form-control" tabindex="4" aria-label="Passport" aria-describedby="basic-addon1" value="">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Passport</span>
                                </div>
                            </div>
                            <script>
                                let imgInput = document.getElementById('imageInput');
                                imgInput.addEventListener('change', function(e) {
                                    if (e.target.files) {
                                        let imageFile = e.target.files[0]; //here we get the image file
                                        var reader = new FileReader();
                                        reader.readAsDataURL(imageFile);
                                        reader.onloadend = function(e) {
                                            var myImage = new Image(); // Creates image object
                                            myImage.src = e.target.result; // Assigns converted image to image object
                                            myImage.onload = function(ev) {
                                                var myCanvas = document.getElementById("myCanvas"); // Creates a canvas object
                                                var myContext = myCanvas.getContext("2d"); // Creates a contect object
                                                myContext.drawImage(myImage, 0, 0, 275, 314); // Draws the image on canvas
                                            }
                                        }
                                    }
                                    $("#mypassport").hide();
                                    $("#preview").show();
                                });
                            </script>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Fullname</span>
                                </div>
                                <input type="text" id="fullname" class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['fname'] ?>">
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="classid">Learner's Class</label>
                                </div>
                                <select class="form-select" id="classid" tabindex="1" required="yes">
                                    <option value="<?php echo $learner_profile['classid'] ?>"><?php echo $learner_profile['classname'] ?></option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <label class="" for="subject">Learner's Gender</label>
                                </div>
                                <select class="form-select" tabindex="2" id="gender" required="yes">
                                    <option value="<?php echo $learner_profile['gender'] ?>"><?php echo $learner_profile['gender'] ?? "" ?></option>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Date of Birth</span>
                                </div>
                                <input type="date" id="date_of_birth" class="form-control" value="<?php echo $learner_profile['dob'] ?? "" ?>" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Phonenumber</span>
                                </div>
                                <input type="tel" id="phone" class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['numb'] ?? "" ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-text">
                                    <span class="" id="basic-addon1">Learner's Email</span>
                                </div>
                                <input type="email" id="email" class="form-control" tabindex="4" aria-label="Fullname" aria-describedby="basic-addon1" value="<?php echo $learner_profile['email'] ?? "" ?>">
                            </div>
                            <button tabindex="5" class="btn_1 full_width text-center" onclick="modify_learner()">Modify Learner Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row " id="response">

        </div>
    </div>
</div>